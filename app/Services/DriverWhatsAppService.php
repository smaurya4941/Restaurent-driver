<?php

namespace App\Services;

use App\Models\WhatsAppMessageModel;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\I18n\Time;
use Config\Database;

class DriverWhatsAppService
{
    public function __construct(
        private readonly WhatsAppMessageModel $messageModel = new WhatsAppMessageModel(),
        private readonly BranchContext $branchContext = new BranchContext(),
    ) {
    }

    public function buildDriverGroupReport(array $filters): array
    {
        if (!$this->validateFilterRange($filters)) {
            return [];
        }

        return $this->baseBuilder($filters)
            ->orderBy('visit_count', 'DESC')
            ->orderBy('total_guests', 'DESC')
            ->orderBy('drivers.full_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function queueGroupedMessage(array $filters, string $messageBody, int $createdBy, ?string $mediaUrl = null): int
    {
        $rows = $this->buildDriverGroupReport($filters);
        return $this->queueRows($rows, $messageBody, $createdBy, $mediaUrl);
    }

    public function queueSelectedMessage(array $filters, array $driverIds, string $messageBody, int $createdBy, ?string $mediaUrl = null): int
    {
        $driverIds = array_values(array_unique(array_filter(array_map('intval', $driverIds), static fn ($id) => $id > 0)));

        if ($driverIds === [] || !$this->validateFilterRange($filters)) {
            return 0;
        }

        $rows = $this->baseBuilder($filters)
            ->whereIn('drivers.id', $driverIds)
            ->orderBy('drivers.full_name', 'ASC')
            ->get()
            ->getResultArray();

        return $this->queueRows($rows, $messageBody, $createdBy, $mediaUrl);
    }

    private function queueRows(array $rows, string $messageBody, int $createdBy, ?string $mediaUrl = null): int
    {
        $queuedAt = Time::now()->toDateTimeString();
        $mediaUrl = trim((string) $mediaUrl);
        $inserted = 0;

        log_message('info', '[WhatsApp Queue] Raw template received: ' . $messageBody);
        log_message('info', '[WhatsApp Queue] Driver rows to process: ' . count($rows));

        foreach ($rows as $row) {
            $personalizedMessage = $this->personalizeMessage($messageBody, $row);

            log_message('debug', '[WhatsApp Queue] Driver #' . $row['id'] . ' (' . ($row['full_name'] ?? 'unknown') . '): "' . $personalizedMessage . '"');

            $this->messageModel->insert([
                'campaign_id' => null,
                'driver_id' => (int) $row['id'],
                'recipient_number' => (string) $row['whatsapp_number'],
                'template_id' => null,
                'template_type' => 'custom_group_message',
                'message_body' => $personalizedMessage,
                'media_type' => $mediaUrl !== '' ? 'image' : null,
                'media_url' => $mediaUrl !== '' ? $mediaUrl : null,
                'status' => 'queued',
                'queue_status' => 'queued',
                'delivery_status' => 'pending',
                'attempt_count' => 0,
                'max_attempts' => 3,
                'queued_at' => $queuedAt,
                'provider_status' => $createdBy > 0 ? 'queued_by_user_' . $createdBy : 'queued_manually',
            ]);
            $inserted++;
        }

        return $inserted;
    }

    public function validateFilterRange(array $filters): bool
    {
        foreach ([['min_visits', 'max_visits'], ['min_guests', 'max_guests']] as [$minKey, $maxKey]) {
            $min = trim((string) ($filters[$minKey] ?? ''));
            $max = trim((string) ($filters[$maxKey] ?? ''));

            if ($min !== '' && $max !== '' && (int) $min > (int) $max) {
                return false;
            }
        }

        return true;
    }

    private function personalizeMessage(string $messageBody, array $row): string
    {
        $tokens = [
            'driver_name' => (string) ($row['full_name'] ?? ''),
            'visit_count' => (string) ((int) ($row['visit_count'] ?? 0)),
            'guest_count' => (string) ((int) ($row['total_guests'] ?? 0)),
            'city' => (string) ($row['city'] ?? ''),
            'vehicle_type' => (string) ($row['vehicle_type'] ?? ''),
        ];

        return preg_replace_callback('/{{\s*([a-zA-Z0-9_\s]+)\s*}}/i', static function (array $matches) use ($tokens): string {
            $key = strtolower(str_replace(' ', '_', trim($matches[1])));
            return $tokens[$key] ?? $matches[0];
        }, trim($messageBody)) ?? trim($messageBody);
    }

    private function baseBuilder(array $filters): BaseBuilder
    {
        $db = Database::connect();
        $builder = $db->table('drivers');

        $builder
            ->select(
                'drivers.id, drivers.full_name, drivers.mobile_number, drivers.whatsapp_number, drivers.city, drivers.status, vehicles.vehicle_type, vehicles.vehicle_number, '
                . 'COUNT(visits.id) AS visit_count, COALESCE(SUM(visits.guest_count), 0) AS total_guests'
            )
            ->join('vehicles', 'vehicles.driver_id = drivers.id AND vehicles.is_primary = 1 AND vehicles.deleted_at IS NULL', 'left')
            ->join('visits', 'visits.driver_id = drivers.id AND visits.deleted_at IS NULL', 'left')
            ->where('drivers.deleted_at', null)
            ->where('drivers.status', 'active')
            ->where('drivers.whatsapp_opt_in', 1)
            ->where('drivers.whatsapp_number IS NOT NULL')
            ->where('drivers.whatsapp_number <>', '');

        $branchId = $this->branchContext->getScopeBranchId();
        if ($branchId !== null) {
            $builder->where('visits.branch_id', $branchId);
        }

        if (($filters['vehicle_type'] ?? '') !== '') {
            $builder->where('vehicles.vehicle_type', (string) $filters['vehicle_type']);
        }

        $builder->groupBy([
            'drivers.id',
            'drivers.full_name',
            'drivers.mobile_number',
            'drivers.whatsapp_number',
            'drivers.city',
            'drivers.status',
            'vehicles.vehicle_type',
            'vehicles.vehicle_number',
        ]);

        if (($filters['min_visits'] ?? '') !== '') {
            $builder->having('COUNT(visits.id) >=', (int) $filters['min_visits']);
        }

        if (($filters['max_visits'] ?? '') !== '') {
            $builder->having('COUNT(visits.id) <=', (int) $filters['max_visits']);
        }

        if (($filters['min_guests'] ?? '') !== '') {
            $builder->having('COALESCE(SUM(visits.guest_count), 0) >=', (int) $filters['min_guests']);
        }

        if (($filters['max_guests'] ?? '') !== '') {
            $builder->having('COALESCE(SUM(visits.guest_count), 0) <=', (int) $filters['max_guests']);
        }

        return $builder;
    }
}
