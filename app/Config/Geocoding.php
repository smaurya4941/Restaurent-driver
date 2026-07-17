<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Reverse-geocoding configuration.
 *
 * Turns latitude/longitude captured during a visit into a human-readable
 * address. Defaults to the free OpenStreetMap Nominatim service; every value
 * can be overridden from the environment (.env) using the `geocoding.*` keys.
 */
class Geocoding extends BaseConfig
{
    /** Master switch. When false, visits store coordinates only. */
    public bool $enabled = true;

    /** Reverse-geocoding provider identifier. Currently: 'nominatim'. */
    public string $provider = 'nominatim';

    /** Reverse-geocoding endpoint (Nominatim-compatible). */
    public string $endpoint = 'https://nominatim.openstreetmap.org/reverse';

    /**
     * Nominatim's usage policy requires a descriptive User-Agent that
     * identifies the application. Change this to your own app/contact.
     */
    public string $userAgent = 'HawaHawaiDriverApp/1.0 (+driver-management)';

    /** Optional contact e-mail forwarded to Nominatim as good practice. */
    public string $email = '';

    /** Preferred language for the returned address. */
    public string $language = 'en';

    /** Request timeout in seconds. Kept short so a slow lookup never stalls a save. */
    public int $timeout = 5;

    /** How long (seconds) to cache a resolved address for nearby coordinates. */
    public int $cacheTtl = 2592000; // 30 days

    public function __construct()
    {
        parent::__construct();

        $this->enabled = filter_var(env('geocoding.enabled', $this->enabled), FILTER_VALIDATE_BOOL);
        $this->provider = (string) env('geocoding.provider', $this->provider);
        $this->endpoint = (string) env('geocoding.endpoint', $this->endpoint);
        $this->userAgent = (string) env('geocoding.userAgent', $this->userAgent);
        $this->email = (string) env('geocoding.email', $this->email);
        $this->language = (string) env('geocoding.language', $this->language);
        $this->timeout = (int) env('geocoding.timeout', $this->timeout);
        $this->cacheTtl = (int) env('geocoding.cacheTtl', $this->cacheTtl);
    }
}
