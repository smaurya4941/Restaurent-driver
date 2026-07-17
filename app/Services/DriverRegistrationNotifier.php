<?php

namespace App\Services;

use App\Models\WhatsAppMessageModel;
use CodeIgniter\I18n\Time;
use Config\WhatsApp;
use Throwable;

/**
 * Queues the automated "welcome" WhatsApp message that is sent to a driver
 * immediately after they are registered successfully.
 *
 * The message is not sent inline: it is enqueued into the `whatsapp_messages`
 * table and later dispatched by the `whatsapp:worker` command through the
 * configured provider. This keeps registration fast and gives the message the
 * same retry / delivery-tracking guarantees as every other WhatsApp message.
 */
class DriverRegistrationNotifier
{
    private WhatsApp $config;

    public function __construct(
        private readonly WhatsAppMessageModel $messageModel = new WhatsAppMessageModel(),
        ?WhatsApp $config = null,
    ) {
        $this->config = $config ?? config(WhatsApp::class);
    }

    /**
     * Build and queue the welcome message for a freshly registered driver.
     *
     * Never throws: a notification problem must not roll back or break the
     * registration flow. Returns true when a message was queued.
     *
     * @param array      $driver      Driver row (as stored, incl. full_name, mobile_number, whatsapp_number, city).
     * @param array|null $vehicle     Primary vehicle row (vehicle_number, vehicle_type), or null when none.
     * @param string     $branchLabel Human readable branch name the driver was registered under.
     */
    public function sendWelcomeMessage(array $driver, ?array $vehicle, string $branchLabel = ''): bool
    {
        try {
            if (!$this->config->sendWelcomeOnRegistration) {
                return false;
            }

            $recipient = $this->resolveRecipient($driver);
            if ($recipient === null) {
                log_message('info', '[DriverWelcome] Skipped: driver #{id} has no valid WhatsApp/mobile number.', [
                    'id' => $driver['id'] ?? '?',
                ]);
                return false;
            }

            $messageBody = $this->buildMessageBody($driver, $vehicle, $branchLabel);
            $queuedAt = Time::now()->toDateTimeString();

            $this->messageModel->insert([
                'campaign_id'      => null,
                'driver_id'        => (int) ($driver['id'] ?? 0),
                'recipient_number' => $recipient,
                'template_id'      => null,
                'template_type'    => 'driver_welcome',
                'message_body'     => $messageBody,
                'media_type'       => null,
                'media_url'        => null,
                'status'           => 'queued',
                'queue_status'     => 'queued',
                'delivery_status'  => 'pending',
                'attempt_count'    => 0,
                'max_attempts'     => 3,
                'queued_at'        => $queuedAt,
                'provider_status'  => 'queued_on_registration',
            ]);

            log_message('info', '[DriverWelcome] Queued welcome message for driver #{id} to {number}.', [
                'id'     => $driver['id'] ?? '?',
                'number' => $recipient,
            ]);

            return true;
        } catch (Throwable $exception) {
            // Registration already succeeded — swallow and log so the user flow is never affected.
            log_message('error', '[DriverWelcome] Failed to queue welcome message for driver #{id}: {msg}', [
                'id'  => $driver['id'] ?? '?',
                'msg' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Prefer the dedicated WhatsApp number, fall back to the mobile number.
     * Returns null when neither is a usable number.
     */
    private function resolveRecipient(array $driver): ?string
    {
        foreach (['whatsapp_number', 'mobile_number'] as $field) {
            $value = trim((string) ($driver[$field] ?? ''));
            if ($value !== '' && preg_replace('/\D+/', '', $value) !== '') {
                return $value;
            }
        }

        return null;
    }

    private function buildMessageBody(array $driver, ?array $vehicle, string $branchLabel): string
    {
        $tokens = $this->buildTokens($driver, $vehicle, $branchLabel);

        $template = trim($this->config->welcomeMessageTemplate);
        if ($template === '') {
            $template = $this->defaultTemplate($vehicle !== null);
        }

        $rendered = preg_replace_callback('/{{\s*([a-zA-Z0-9_\s]+)\s*}}/', static function (array $matches) use ($tokens): string {
            $key = strtolower(str_replace(' ', '_', trim($matches[1])));
            return $tokens[$key] ?? $matches[0];
        }, $template);

        return trim((string) ($rendered ?? $template));
    }

    /**
     * @return array<string, string>
     */
    private function buildTokens(array $driver, ?array $vehicle, string $branchLabel): array
    {
        $restaurant = trim((string) $this->config->restaurantName);
        $branchLabel = trim($branchLabel);
        $restaurantFull = $restaurant;
        if ($branchLabel !== '' && strcasecmp($branchLabel, $restaurant) !== 0) {
            $restaurantFull = $restaurant . ' (' . $branchLabel . ')';
        }

        return [
            'driver_name'     => trim((string) ($driver['full_name'] ?? 'Driver')) ?: 'Driver',
            'restaurant'      => $restaurant,
            'restaurant_full' => $restaurantFull,
            'branch'          => $branchLabel,
            'mobile_number'   => trim((string) ($driver['mobile_number'] ?? '')),
            'whatsapp_number' => trim((string) ($driver['whatsapp_number'] ?? '')),
            'city'            => trim((string) ($driver['city'] ?? '')),
            'vehicle_number'  => strtoupper(trim((string) ($vehicle['vehicle_number'] ?? ''))),
            'vehicle_type'    => ucwords(trim((string) ($vehicle['vehicle_type'] ?? ''))),
            'has_vehicle'     => $vehicle !== null ? '1' : '0',
        ];
    }

    private function defaultTemplate(bool $hasVehicle): string
    {
        $lines = [
            'Namaste {{driver_name}}! 🙏',
            '',
            'You have successfully registered with *{{restaurant_full}}*.',
            '',
            '👤 *Your Details*',
            '• Name: {{driver_name}}',
            '• Mobile: {{mobile_number}}',
        ];

        if ($hasVehicle) {
            $lines[] = '';
            $lines[] = '🚗 *Vehicle Details*';
            $lines[] = '• Number: {{vehicle_number}}';
            $lines[] = '• Type: {{vehicle_type}}';
        }

        $lines[] = '';
        $lines[] = 'Thank you for joining us. We look forward to working with you!';

        return implode("\n", $lines);
    }
}
