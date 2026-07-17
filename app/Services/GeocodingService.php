<?php

namespace App\Services;

use Config\Geocoding;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Resolves latitude/longitude into a human-readable address.
 *
 * Uses an OpenStreetMap Nominatim-compatible endpoint. The lookup is
 * best-effort: any failure (disabled, offline, rate-limited, malformed
 * response) returns null so the caller can gracefully fall back to raw
 * coordinates. Results are cached by rounded coordinates to respect the
 * provider's usage policy and keep visit saves fast.
 */
class GeocodingService
{
    private readonly Geocoding $config;
    private readonly LoggerInterface $logger;

    public function __construct(?Geocoding $config = null, ?LoggerInterface $logger = null)
    {
        $this->config = $config ?? config(Geocoding::class);
        $this->logger = $logger ?? \Config\Services::logger();
    }

    /**
     * @return string|null Human-readable address, or null when unavailable.
     */
    public function reverseGeocode(float $latitude, float $longitude): ?string
    {
        if (!$this->config->enabled) {
            return null;
        }

        if ($latitude < -90.0 || $latitude > 90.0 || $longitude < -180.0 || $longitude > 180.0) {
            return null;
        }

        // Round to ~11 m so repeat visits from the same spot reuse a cached address.
        $cacheKey = sprintf('geocode_%s_%s', number_format($latitude, 4, '.', ''), number_format($longitude, 4, '.', ''));
        $cache = \Config\Services::cache();
        $cached = $cache->get($cacheKey);
        if (is_string($cached)) {
            return $cached !== '' ? $cached : null;
        }

        $address = $this->requestAddress($latitude, $longitude);

        // Cache both hits and misses (empty string) to avoid hammering the provider.
        $cache->save($cacheKey, (string) $address, $this->config->cacheTtl);

        return $address;
    }

    private function requestAddress(float $latitude, float $longitude): ?string
    {
        $query = [
            'format' => 'jsonv2',
            'lat' => number_format($latitude, 7, '.', ''),
            'lon' => number_format($longitude, 7, '.', ''),
            'zoom' => 18,
            'addressdetails' => 1,
            'accept-language' => $this->config->language,
        ];

        if ($this->config->email !== '') {
            $query['email'] = $this->config->email;
        }

        try {
            $client = \Config\Services::curlrequest([
                'timeout' => $this->config->timeout,
                'http_errors' => false,
            ]);

            $response = $client->get($this->config->endpoint, [
                'query' => $query,
                'headers' => [
                    'User-Agent' => $this->config->userAgent,
                    'Accept' => 'application/json',
                ],
            ]);

            if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
                $this->logger->warning('[Geocoding] Non-2xx response [' . $response->getStatusCode() . '] for ' . $latitude . ',' . $longitude);
                return null;
            }

            $decoded = json_decode((string) $response->getBody(), true);
            if (!is_array($decoded)) {
                return null;
            }

            return $this->formatAddress($decoded);
        } catch (Throwable $exception) {
            $this->logger->error('[Geocoding] Exception: ' . $exception->getMessage());
            return null;
        }
    }

    /**
     * Builds a concise, readable address from a Nominatim response, preferring
     * the most meaningful components over the very long default display_name.
     */
    private function formatAddress(array $data): ?string
    {
        $parts = $data['address'] ?? null;
        if (is_array($parts)) {
            $ordered = [
                $parts['road'] ?? ($parts['neighbourhood'] ?? null),
                $parts['suburb'] ?? ($parts['village'] ?? ($parts['town'] ?? ($parts['city'] ?? null))),
                $parts['county'] ?? ($parts['state_district'] ?? null),
                $parts['state'] ?? null,
                $parts['postcode'] ?? null,
            ];

            $filtered = array_values(array_unique(array_filter(array_map(
                static fn ($value): string => trim((string) $value),
                $ordered
            ), static fn (string $value): bool => $value !== '')));

            if ($filtered !== []) {
                return $this->clip(implode(', ', $filtered));
            }
        }

        $display = trim((string) ($data['display_name'] ?? ''));

        return $display !== '' ? $this->clip($display) : null;
    }

    private function clip(string $address): string
    {
        // Column is VARCHAR(255); keep a safe margin and avoid mid-character cuts.
        return mb_substr($address, 0, 255);
    }
}
