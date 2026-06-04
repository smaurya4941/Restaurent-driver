<?php

namespace App\Libraries\WhatsApp;

use Psr\Log\LoggerInterface;

class LogWhatsAppProvider implements WhatsAppProviderInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function send(string $to, string $message, ?array $media = null): array
    {
        $providerMessageId = 'log_' . uniqid('', true);

        $this->logger->info('WhatsApp message queued for simulated delivery.', [
            'to' => $to,
            'provider_message_id' => $providerMessageId,
            'message' => $message,
            'media' => $media,
        ]);

        return [
            'success' => true,
            'provider_message_id' => $providerMessageId,
            'provider_status' => 'accepted',
            'provider_response' => ['mode' => 'log', 'accepted' => true],
            'error_message' => null,
        ];
    }
}
