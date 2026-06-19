<?php

namespace App\Libraries\WhatsApp;

use CodeIgniter\HTTP\CURLRequest;
use Config\WhatsApp;
use Psr\Log\LoggerInterface;
use Throwable;

class RestWhatsAppProvider implements WhatsAppProviderInterface
{
    private readonly LoggerInterface $logger;

    public function __construct(
        private readonly WhatsApp $config,
        private readonly CURLRequest $client,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? \Config\Services::logger();
    }

    public function send(string $to, string $message, ?array $media = null): array
    {
        $to = $this->normalizeNumber($to);
        $mediaUrl = trim((string) ($media['media_url'] ?? ''));
        $payload = [
            $this->config->recipientParam => $to,
            $this->config->messageParam => $message,
        ];

        if ($this->config->messageTypeParam !== '' && $this->config->messageType !== '') {
            $payload[$this->config->messageTypeParam] = $mediaUrl !== ''
                ? $this->config->imageMessageType
                : $this->config->messageType;
        }

        if ($mediaUrl !== '' && $this->config->mediaUrlParam !== '') {
            $payload[$this->config->mediaUrlParam] = $mediaUrl;
        }

        if ($this->config->instanceId !== '') {
            $payload[$this->config->instanceParam] = $this->config->instanceId;
        }

        $options = [
            'timeout' => 30,
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        if ($this->config->tokenLocation === 'bearer' && $this->config->apiToken !== '') {
            $options['headers']['Authorization'] = 'Bearer ' . $this->config->apiToken;
        } elseif ($this->config->tokenLocation === 'body' && $this->config->apiToken !== '') {
            $payload[$this->config->tokenParam] = $this->config->apiToken;
        }

        if ($this->config->httpMethod === 'GET') {
            $options['query'] = $this->withQueryToken($payload);
        } else {
            if ($this->config->bodyFormat === 'json') {
                $options['json'] = $payload;
            } else {
                $options['form_params'] = $payload;
            }

            if ($this->config->tokenLocation === 'query' && $this->config->apiToken !== '') {
                $options['query'] = [$this->config->tokenParam => $this->config->apiToken];
            }
        }

        $this->logger->info('[WhatsApp] Sending to ' . $to . ' via ' . $this->config->httpMethod . ' ' . $this->config->apiUrl);
        $this->logger->debug('[WhatsApp] Payload: ' . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        try {
            $response = $this->client->request($this->config->httpMethod, $this->config->apiUrl, $options);
            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();
            $decoded = json_decode($body, true);
            $providerResponse = is_array($decoded) ? $decoded : ['raw' => $body];
            $success = $statusCode >= 200 && $statusCode < 300 && !$this->looksFailed($providerResponse);

            $this->logger->info('[WhatsApp] Response [' . $statusCode . ']: ' . $body);

            return [
                'success' => $success,
                'provider_message_id' => (string) ($providerResponse['message_id'] ?? $providerResponse['id'] ?? ''),
                'provider_status' => (string) ($providerResponse['status'] ?? $statusCode),
                'provider_response' => $providerResponse,
                'error_message' => $success ? null : ($providerResponse['message'] ?? $providerResponse['error'] ?? 'WhatsApp API request failed.'),
            ];
        } catch (Throwable $exception) {
            $this->logger->error('[WhatsApp] Exception: ' . $exception->getMessage());

            return [
                'success' => false,
                'provider_message_id' => null,
                'provider_status' => 'exception',
                'provider_response' => ['exception' => $exception->getMessage()],
                'error_message' => $exception->getMessage(),
            ];
        }
    }

    private function withQueryToken(array $payload): array
    {
        if ($this->config->apiToken !== '') {
            $payload[$this->config->tokenParam] = $this->config->apiToken;
        }

        return $payload;
    }

    private function normalizeNumber(string $number): string
    {
        $digits = preg_replace('/\D+/', '', $number) ?? '';

        if (strlen($digits) === 10 && $this->config->defaultCountryCode !== '') {
            return $this->config->defaultCountryCode . $digits;
        }

        return $digits;
    }

    private function looksFailed(array $response): bool
    {
        $status = strtolower((string) ($response['status'] ?? $response['success'] ?? ''));

        return in_array($status, ['false', 'failed', 'error'], true);
    }
}
