<?php

namespace App\Libraries\WhatsApp;

interface WhatsAppProviderInterface
{
    /**
     * @param array{media_url?: string, media_type?: string}|null $media
     *
     * @return array{success: bool, provider_message_id: string|null, provider_status: string|null, provider_response: array, error_message: string|null}
     */
    public function send(string $to, string $message, ?array $media = null): array;
}
