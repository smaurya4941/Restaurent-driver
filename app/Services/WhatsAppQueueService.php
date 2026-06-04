<?php

namespace App\Services;

use App\Models\WhatsAppMessageModel;
use CodeIgniter\I18n\Time;
use Config\WhatsApp;

class WhatsAppQueueService
{
    public function __construct(
        private readonly WhatsAppMessageModel $messageModel = new WhatsAppMessageModel(),
        private readonly WhatsAppProviderFactory $providerFactory = new WhatsAppProviderFactory(),
    ) {
    }

    public function processQueuedMessages(?int $limit = null): array
    {
        $config = config(WhatsApp::class);
        $batchSize = $limit ?? $config->workerBatchSize;
        $now = Time::now()->toDateTimeString();

        $messages = $this->messageModel
            ->groupStart()
                ->where('queue_status', 'queued')
                ->orGroupStart()
                    ->where('queue_status', 'retry')
                    ->where('next_retry_at <=', $now)
                ->groupEnd()
            ->groupEnd()
            ->orderBy('id', 'ASC')
            ->findAll($batchSize);

        $provider = $this->providerFactory->make();
        $processed = 0;
        $sent = 0;
        $failed = 0;

        foreach ($messages as $message) {
            $processed++;
            $messageId = (int) $message['id'];
            $attemptCount = ((int) $message['attempt_count']) + 1;

            $this->messageModel->update($messageId, [
                'queue_status' => 'processing',
                'processing_started_at' => $now,
                'attempt_count' => $attemptCount,
                'error_message' => null,
            ]);

            $media = null;
            if (!empty($message['media_url'])) {
                $media = [
                    'media_type' => (string) ($message['media_type'] ?? 'image'),
                    'media_url' => (string) $message['media_url'],
                ];
            }

            $result = $provider->send((string) $message['recipient_number'], (string) $message['message_body'], $media);

            if ($result['success'] === true) {
                $sent++;
                $deliveryStatus = $config->simulateDelivery ? 'delivered' : 'sent';

                $this->messageModel->update($messageId, [
                    'status' => 'sent',
                    'queue_status' => 'processed',
                    'delivery_status' => $deliveryStatus,
                    'sent_at' => $now,
                    'delivered_at' => $deliveryStatus === 'delivered' ? $now : null,
                    'provider_message_id' => $result['provider_message_id'],
                    'provider_status' => $result['provider_status'],
                    'provider_response' => json_encode($result['provider_response'], JSON_UNESCAPED_UNICODE),
                    'failed_at' => null,
                    'next_retry_at' => null,
                ]);
            } else {
                $failed++;
                $canRetry = $attemptCount < (int) $message['max_attempts'];
                $nextRetry = $canRetry ? Time::now()->addMinutes($config->retryDelayMinutes)->toDateTimeString() : null;

                $this->messageModel->update($messageId, [
                    'status' => 'failed',
                    'queue_status' => $canRetry ? 'retry' : 'failed',
                    'delivery_status' => 'failed',
                    'failed_at' => $now,
                    'next_retry_at' => $nextRetry,
                    'provider_message_id' => $result['provider_message_id'],
                    'provider_status' => $result['provider_status'],
                    'provider_response' => json_encode($result['provider_response'], JSON_UNESCAPED_UNICODE),
                    'error_message' => $result['error_message'],
                ]);
            }
        }

        return [
            'processed' => $processed,
            'sent' => $sent,
            'failed' => $failed,
        ];
    }
}
