<?php

namespace App\Services;

use Config\Database;

class ReportingService
{
    public function __construct(
        private readonly BranchContext $branchContext = new BranchContext(),
    ) {
    }
    private const REPORT_DEFINITIONS = [
        'driver-report' => [
            'label' => 'Driver Report',
            'description' => 'Driver list with current primary vehicle details.',
        ],
        'visit-ledger' => [
            'label' => 'Visits Report',
            'description' => 'Basic visit details with driver, guests, food, and cash incentive.',
        ],
        'drivers-registered' => [
            'label' => 'Incentive Report',
            'description' => 'Registered drivers and their default driver incentive amounts.',
        ],
        'monthly-bonuses' => [
            'label' => 'Monthly Bonuses',
            'description' => 'Drivers who qualified for visit milestone bonuses in the selected month.',
        ],
    ];

    public function getReportDefinitions(): array
    {
        return self::REPORT_DEFINITIONS;
    }

    public function getDefaultType(): string
    {
        return 'driver-report';
    }

    public function normalizeType(?string $type): string
    {
        $type = trim((string) $type);

        return array_key_exists($type, self::REPORT_DEFINITIONS) ? $type : $this->getDefaultType();
    }

    public function collectFilters(array $input): array
    {
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('m');

        $year = trim((string) ($input['year'] ?? ''));
        $month = trim((string) ($input['month'] ?? ''));
        $limit = (int) ($input['limit'] ?? 25);

        return [
            'start_date' => trim((string) ($input['start_date'] ?? '')),
            'end_date' => trim((string) ($input['end_date'] ?? '')),
            'search_input' => trim((string) ($input['search_input'] ?? '')),
            'year' => $year !== '' ? max(2000, min(2100, (int) $year)) : $currentYear,
            'month' => $month !== '' ? max(1, min(12, (int) $month)) : $currentMonth,
            'limit' => max(10, min(200, $limit)),
        ];
    }

    public function buildReport(string $type, array $filters): array
    {
        $type = $this->normalizeType($type);
        $definition = self::REPORT_DEFINITIONS[$type];

        return match ($type) {
            'driver-report' => $this->buildDriverReport($type, $definition, $filters),
            'visit-ledger' => $this->buildVisitLedgerReport($type, $definition, $filters),
            'drivers-registered' => $this->buildDriversRegisteredReport($type, $definition, $filters),
            'monthly-bonuses' => $this->buildIncentivePayoutReport($type, $definition, $filters),
            default => $this->buildDriverReport($type, $definition, $filters),
        };
    }

    private function buildDriverReport(string $type, array $definition, array $filters): array
    {
        $builder = Database::connect()
            ->table('drivers')
            ->select('drivers.full_name AS driver_name, drivers.mobile_number, drivers.status, drivers.default_cash_incentive_amount, vehicles.vehicle_number, vehicles.vehicle_type, vehicles.status AS vehicle_status, vehicles.assigned_from')
            ->join('vehicles', 'vehicles.driver_id = drivers.id AND vehicles.is_primary = 1 AND vehicles.deleted_at IS NULL', 'left')
            ->where('drivers.deleted_at', null);

        $this->applyCreatedAtDateRange($builder, 'drivers.created_at', $filters);
        $this->applyDriverSearch($builder, $filters['search_input']);

        $rows = $builder
            ->orderBy('drivers.full_name', 'ASC')
            ->limit($filters['limit'])
            ->get()
            ->getResultArray();

        return $this->buildResponse(
            $type,
            $definition,
            [
                ['key' => 'driver_name', 'label' => 'Driver'],
                ['key' => 'mobile_number', 'label' => 'Mobile'],
                ['key' => 'status', 'label' => 'Driver Status'],
                ['key' => 'vehicle_number', 'label' => 'Current Vehicle'],
                ['key' => 'vehicle_type', 'label' => 'Vehicle Type'],
                ['key' => 'vehicle_status', 'label' => 'Vehicle Status'],
                ['key' => 'assigned_from', 'label' => 'Assigned From'],
                ['key' => 'default_cash_incentive_amount', 'label' => 'Default Incentive', 'format' => 'currency'],
            ],
            $rows,
            $filters,
            [
                'Drivers' => count($rows),
                'With Vehicles' => count(array_filter($rows, static fn(array $row): bool => trim((string) ($row['vehicle_number'] ?? '')) !== '')),
            ]
        );
    }

    private function buildVisitLedgerReport(string $type, array $definition, array $filters): array
    {
        $builder = Database::connect()
            ->table('visits')
            ->select('drivers.full_name AS driver_name, drivers.mobile_number, visits.guest_count, visits.cash_incentive_amount, visits.food_offered, visits.visited_at AS visit_date')
            ->join('drivers', 'drivers.id = visits.driver_id')
            ->where('visits.deleted_at', null)
            ->where('drivers.deleted_at', null);

        $this->applyBranchFilter($builder, 'visits.branch_id');
        $this->applyVisitDateRange($builder, $filters);
        $this->applyDriverSearch($builder, $filters['search_input']);

        $rows = $builder
            ->orderBy('visits.visited_at', 'DESC')
            ->limit($filters['limit'])
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $row['food_offered_label'] = ((int) ($row['food_offered'] ?? 0)) === 1 ? 'Yes' : 'No';
        }
        unset($row);

        return $this->buildResponse(
            $type,
            $definition,
            [
                ['key' => 'visit_date', 'label' => 'Visit Date'],
                ['key' => 'driver_name', 'label' => 'Driver'],
                ['key' => 'mobile_number', 'label' => 'Mobile'],
                ['key' => 'guest_count', 'label' => 'Guests'],
                ['key' => 'cash_incentive_amount', 'label' => 'Cash Incentive', 'format' => 'currency'],
                ['key' => 'food_offered_label', 'label' => 'Food Issued'],
            ],
            $rows,
            $filters,
            [
                'Visits' => count($rows),
                'Guests' => array_sum(array_map(static fn(array $row): int => (int) ($row['guest_count'] ?? 0), $rows)),
                'Cash Incentive' => array_sum(array_map(static fn(array $row): float => (float) ($row['cash_incentive_amount'] ?? 0), $rows)),
            ]
        );
    }

    private function buildDriverRankingReport(string $type, array $definition, array $filters): array
    {
        $builder = Database::connect()
            ->table('visits')
            ->select('drivers.id AS driver_id, drivers.full_name AS driver_name, drivers.mobile_number, COUNT(visits.id) AS visit_count, COALESCE(SUM(visits.guest_count), 0) AS total_guests, COALESCE(SUM(visits.cash_incentive_amount), 0) AS total_cash_incentive')
            ->join('drivers', 'drivers.id = visits.driver_id')
            ->where('visits.deleted_at', null)
            ->where('drivers.deleted_at', null);

        $this->applyBranchFilter($builder, 'visits.branch_id');
        $this->applyVisitDateRange($builder, $filters);
        $this->applyDriverSearch($builder, $filters['search_input']);

        $rows = $builder
            ->groupBy('drivers.id, drivers.full_name, drivers.mobile_number')
            ->orderBy('visit_count', 'DESC')
            ->orderBy('total_guests', 'DESC')
            ->orderBy('total_cash_incentive', 'DESC')
            ->limit($filters['limit'])
            ->get()
            ->getResultArray();

        foreach ($rows as $index => &$row) {
            $row['rank'] = $index + 1;
        }
        unset($row);

        return $this->buildResponse(
            $type,
            $definition,
            [
                ['key' => 'rank', 'label' => 'Rank'],
                ['key' => 'driver_name', 'label' => 'Driver'],
                ['key' => 'mobile_number', 'label' => 'Mobile'],
                ['key' => 'visit_count', 'label' => 'Visits'],
                ['key' => 'total_guests', 'label' => 'Guests'],
                ['key' => 'total_cash_incentive', 'label' => 'Cash Incentive', 'format' => 'currency'],
            ],
            $rows,
            $filters,
            [
                'Ranked Drivers' => count($rows),
                'Visits' => array_sum(array_map(static fn(array $row): int => (int) $row['visit_count'], $rows)),
                'Guests' => array_sum(array_map(static fn(array $row): int => (int) $row['total_guests'], $rows)),
            ]
        );
    }

    private function buildIncentivePayoutReport(string $type, array $definition, array $filters): array
    {
        $rows = (new IncentiveEngineService())->getMonthlyBonusReport(
            $filters['year'],
            $filters['month'],
            $filters['search_input']
        );

        return $this->buildResponse(
            $type,
            $definition,
            [
                ['key' => 'driver_name', 'label' => 'Driver'],
                ['key' => 'mobile_number', 'label' => 'Mobile'],
                ['key' => 'year_month', 'label' => 'Month'],
                ['key' => 'rule_name', 'label' => 'Bonus Rule'],
                ['key' => 'total_visits', 'label' => 'Visits'],
                ['key' => 'total_guests', 'label' => 'Guests'],
                ['key' => 'total_cash_incentive', 'label' => 'Cash Incentive', 'format' => 'currency'],
                ['key' => 'qualified_slab', 'label' => 'Qualified Slab'],
                ['key' => 'bonus_basis_amount', 'label' => 'Basis Incentive', 'format' => 'currency'],
                ['key' => 'bonus_percentage', 'label' => 'Bonus %'],
                ['key' => 'bonus_amount', 'label' => 'Bonus Amount', 'format' => 'currency'],
                ['key' => 'payout_status_label', 'label' => 'Payout Status'],
                ['key' => 'computed_at', 'label' => 'Computed At'],
            ],
            array_map(function (array $row): array {
                $row['year_month'] = sprintf('%04d-%02d', (int) $row['year'], (int) $row['month']);
                $row['qualified_slab'] = ((int) ($row['visit_threshold'] ?? 0)) . ' visits';
                $row['payout_status_label'] = ucwords(str_replace('_', ' ', (string) ($row['payout_status'] ?? 'not_eligible')));

                return $row;
            }, $rows),
            $filters,
            [
                'Qualified Drivers' => count($rows),
                'Bonus Total' => array_sum(array_map(static fn(array $row): float => (float) $row['bonus_amount'], $rows)),
                'Cash Incentive' => array_sum(array_map(static fn(array $row): float => (float) $row['total_cash_incentive'], $rows)),
            ]
        );
    }

    private function buildDriversRegisteredReport(string $type, array $definition, array $filters): array
    {
        $builder = Database::connect()
            ->table('drivers')
            ->select('drivers.full_name AS driver_name, drivers.mobile_number, drivers.status, drivers.default_cash_incentive_amount, drivers.created_at')
            ->where('drivers.deleted_at', null);

        $this->applyCreatedAtDateRange($builder, 'drivers.created_at', $filters);
        $this->applyDriverSearch($builder, $filters['search_input']);

        $rows = $builder
            ->orderBy('drivers.created_at', 'DESC')
            ->limit($filters['limit'])
            ->get()
            ->getResultArray();

        return $this->buildResponse(
            $type,
            $definition,
            [
                ['key' => 'driver_name', 'label' => 'Driver'],
                ['key' => 'mobile_number', 'label' => 'Mobile'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'default_cash_incentive_amount', 'label' => 'Default Incentive', 'format' => 'currency'],
                ['key' => 'created_at', 'label' => 'Registered At'],
            ],
            $rows,
            $filters,
            [
                'Drivers' => count($rows),
                'Default Incentive Total' => array_sum(array_map(static fn(array $row): float => (float) ($row['default_cash_incentive_amount'] ?? 0), $rows)),
            ]
        );
    }

    private function buildComplianceFlagsReport(string $type, array $definition, array $filters): array
    {
        $db = Database::connect();
        $rows = [];

        $mobileDuplicates = $db->table('drivers')
            ->select("'driver_mobile_duplicate' AS issue_type, MAX(drivers.full_name) AS subject_name, drivers.mobile_number AS reference_value, COUNT(drivers.id) AS evidence_count, MAX(drivers.updated_at) AS last_seen_at", false)
            ->where('drivers.deleted_at', null)
            ->where('drivers.mobile_number IS NOT NULL', null, false)
            ->where("TRIM(drivers.mobile_number) <>", '', false)
            ->groupBy('drivers.mobile_number')
            ->having('COUNT(drivers.id) >', 1, false)
            ->get()
            ->getResultArray();

        $vehicleDuplicates = $db->table('vehicles')
            ->select("'vehicle_duplicate' AS issue_type, MAX(drivers.full_name) AS subject_name, vehicles.vehicle_number AS reference_value, COUNT(vehicles.id) AS evidence_count, MAX(vehicles.updated_at) AS last_seen_at", false)
            ->join('drivers', 'drivers.id = vehicles.driver_id', 'left')
            ->where('vehicles.deleted_at', null)
            ->where('vehicles.vehicle_number IS NOT NULL', null, false)
            ->where("TRIM(vehicles.vehicle_number) <>", '', false)
            ->groupBy('vehicles.vehicle_number')
            ->having('COUNT(vehicles.id) >', 1, false)
            ->get()
            ->getResultArray();

        $statusFlags = $db->table('drivers')
            ->select("'status_flag' AS issue_type, drivers.full_name AS subject_name, CONCAT('status:', drivers.status) AS reference_value, 1 AS evidence_count, COALESCE(drivers.updated_at, drivers.created_at) AS last_seen_at", false)
            ->whereIn('drivers.status', ['duplicate', 'blacklisted'])
            ->where('drivers.deleted_at', null)
            ->get()
            ->getResultArray();

        $suspiciousVisits = $db->table('visits')
            ->select("'high_frequency_visit' AS issue_type, drivers.full_name AS subject_name, DATE(visits.visited_at) AS reference_value, COUNT(visits.id) AS evidence_count, MAX(visits.visited_at) AS last_seen_at", false)
            ->join('drivers', 'drivers.id = visits.driver_id')
            ->where('visits.deleted_at', null);

        $this->applyBranchFilter($suspiciousVisits, 'visits.branch_id');

        $suspiciousVisits = $suspiciousVisits
            ->groupBy('visits.driver_id, DATE(visits.visited_at)')
            ->having('COUNT(visits.id) >', 3, false)
            ->get()
            ->getResultArray();

        $rows = array_merge($mobileDuplicates, $vehicleDuplicates, $statusFlags, $suspiciousVisits);

        if ($filters['search_input'] !== '') {
            $needle = mb_strtolower($filters['search_input']);
            $rows = array_values(array_filter($rows, static function (array $row) use ($needle): bool {
                $haystack = mb_strtolower(
                    implode(' ', [
                        (string) ($row['issue_type'] ?? ''),
                        (string) ($row['subject_name'] ?? ''),
                        (string) ($row['reference_value'] ?? ''),
                    ])
                );

                return str_contains($haystack, $needle);
            }));
        }

        usort($rows, static function (array $left, array $right): int {
            return strcmp((string) ($right['last_seen_at'] ?? ''), (string) ($left['last_seen_at'] ?? ''));
        });

        $rows = array_slice($rows, 0, $filters['limit']);

        foreach ($rows as &$row) {
            $row['issue_type'] = str_replace('_', ' ', (string) ($row['issue_type'] ?? ''));
        }
        unset($row);

        return $this->buildResponse(
            $type,
            $definition,
            [
                ['key' => 'issue_type', 'label' => 'Issue Type'],
                ['key' => 'subject_name', 'label' => 'Subject'],
                ['key' => 'reference_value', 'label' => 'Reference'],
                ['key' => 'evidence_count', 'label' => 'Evidence Count'],
                ['key' => 'last_seen_at', 'label' => 'Last Seen'],
            ],
            $rows,
            $filters,
            [
                'Flagged Rows' => count($rows),
            ]
        );
    }

    private function buildCampaignPerformanceReport(string $type, array $definition, array $filters): array
    {
        $builder = Database::connect()
            ->table('whatsapp_campaigns')
            ->select("name, campaign_type, audience_type, status, total_recipients, sent_count, delivered_count, failed_count, scheduled_for, last_dispatched_at, created_at, ROUND((delivered_count / NULLIF(sent_count, 0)) * 100, 2) AS delivery_rate", false);

        $this->applyBranchFilter($builder, 'whatsapp_campaigns.branch_id');
        $this->applyCreatedAtDateRange($builder, 'whatsapp_campaigns.created_at', $filters);

        if ($filters['search_input'] !== '') {
            $builder->groupStart()
                ->like('name', $filters['search_input'])
                ->orLike('campaign_type', $filters['search_input'])
                ->orLike('audience_type', $filters['search_input'])
                ->groupEnd();
        }

        $rows = $builder
            ->orderBy('created_at', 'DESC')
            ->limit($filters['limit'])
            ->get()
            ->getResultArray();

        return $this->buildResponse(
            $type,
            $definition,
            [
                ['key' => 'name', 'label' => 'Campaign'],
                ['key' => 'campaign_type', 'label' => 'Type'],
                ['key' => 'audience_type', 'label' => 'Audience'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'total_recipients', 'label' => 'Recipients'],
                ['key' => 'sent_count', 'label' => 'Sent'],
                ['key' => 'delivered_count', 'label' => 'Delivered'],
                ['key' => 'failed_count', 'label' => 'Failed'],
                ['key' => 'delivery_rate', 'label' => 'Delivery %'],
                ['key' => 'created_at', 'label' => 'Created At'],
            ],
            $rows,
            $filters,
            [
                'Campaigns' => count($rows),
                'Recipients' => array_sum(array_map(static fn(array $row): int => (int) ($row['total_recipients'] ?? 0), $rows)),
                'Delivered' => array_sum(array_map(static fn(array $row): int => (int) ($row['delivered_count'] ?? 0), $rows)),
                'Failed' => array_sum(array_map(static fn(array $row): int => (int) ($row['failed_count'] ?? 0), $rows)),
            ]
        );
    }

    private function buildResponse(string $type, array $definition, array $columns, array $rows, array $filters, array $summary): array
    {
        return [
            'type' => $type,
            'title' => $definition['label'],
            'description' => $definition['description'],
            'columns' => $columns,
            'rows' => $rows,
            'filters' => $filters,
            'summary' => $summary,
        ];
    }

    private function applyVisitDateRange(object $builder, array $filters): void
    {
        $this->applyCreatedAtDateRange($builder, 'visits.visited_at', $filters);
    }

    private function applyCreatedAtDateRange(object $builder, string $column, array $filters): void
    {
        if ($filters['start_date'] !== '') {
            $builder->where($column . ' >=', $filters['start_date'] . ' 00:00:00');
        }

        if ($filters['end_date'] !== '') {
            $builder->where($column . ' <=', $filters['end_date'] . ' 23:59:59');
        }
    }

    private function applyDriverSearch(object $builder, string $searchInput): void
    {
        if ($searchInput === '') {
            return;
        }

        $builder->groupStart()
            ->like('drivers.full_name', $searchInput)
            ->orLike('drivers.mobile_number', $searchInput)
            ->groupEnd();
    }

    private function applyBranchFilter(object $builder, string $column): void
    {
        $branchId = $this->branchContext->getScopeBranchId();
        if ($branchId !== null) {
            $builder->where($column, $branchId);
        }
    }
}
