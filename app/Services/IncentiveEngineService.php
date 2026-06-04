<?php

namespace App\Services;

use App\Models\DriverMonthlySummaryModel;
use App\Models\DriverBonusAwardModel;
use App\Models\IncentiveRuleModel;
use App\Models\VisitModel;

class IncentiveEngineService
{
    public function __construct(
        private readonly BranchContext $branchContext = new BranchContext(),
    ) {
    }

    public function recomputeForVisitDate(int $driverId, string $visitedAt, ?int $branchId = null): void
    {
        if ($branchId === null) {
            $visit = (new VisitModel())
                ->select('branch_id')
                ->where('driver_id', $driverId)
                ->where('visited_at', $visitedAt)
                ->orderBy('id', 'DESC')
                ->first();
            $branchId = isset($visit['branch_id']) ? (int) $visit['branch_id'] : null;
        }

        if ($branchId === null || $branchId <= 0) {
            $branchId = $this->branchContext->requireBranchId();
        }

        $this->recomputeDriverMonth($driverId, (int) date('Y', strtotime($visitedAt)), (int) date('n', strtotime($visitedAt)), $branchId);
    }

    public function recomputeDriverMonth(int $driverId, int $year, int $month, ?int $branchId = null): array
    {
        if ($branchId === null || $branchId <= 0) {
            $branchId = $this->branchContext->requireBranchId();
        }

        $period = $this->getPeriodBounds($year, $month);
        $totals = $this->aggregateDriverMonth($driverId, $period['start'], $period['end'], $branchId);
        $summaryModel = new DriverMonthlySummaryModel();
        $existing = $summaryModel
            ->where('driver_id', $driverId)
            ->where('branch_id', $branchId)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if ($totals['total_visits'] === 0) {
            if ($existing) {
                $summaryModel->delete($existing['id']);
            }
            (new DriverBonusAwardModel())
                ->where('driver_id', $driverId)
                ->where('branch_id', $branchId)
                ->where('year', $year)
                ->where('month', $month)
                ->delete();

            return [
                'driver_id' => $driverId,
                'year' => $year,
                'month' => $month,
                'deleted' => true,
            ];
        }

        $matchedRule = $this->resolveApplicableRule($totals['total_visits'], $period['start'], $period['end'], $branchId);
        $this->syncEligibleBonusAwards($driverId, $year, $month, $period, $totals, $branchId);

        $bonusPercentage = $matchedRule ? (float) $matchedRule['bonus_value'] : 0.0;
        $bonusBasisAmount = $matchedRule
            ? $this->sumLastVisitIncentives($driverId, $period['start'], $period['end'], (int) $matchedRule['visit_threshold'], $branchId)
            : 0.0;
        $bonusAmount = round($bonusBasisAmount * ($bonusPercentage / 100), 2);
        $payoutState = $this->resolvePayoutState($existing, $bonusAmount);

        $payload = [
            'branch_id' => $branchId,
            'driver_id' => $driverId,
            'incentive_rule_id' => $matchedRule['id'] ?? null,
            'year' => $year,
            'month' => $month,
            'total_visits' => $totals['total_visits'],
            'total_guests' => $totals['total_guests'],
            'total_cash_incentive' => number_format((float) $totals['total_cash_incentive'], 2, '.', ''),
            'total_amount_paid' => '0.00',
            'bonus_percentage' => number_format($bonusPercentage, 2, '.', ''),
            'bonus_amount' => number_format($bonusAmount, 2, '.', ''),
            'computed_at' => date('Y-m-d H:i:s'),
            'payout_status' => $payoutState['payout_status'],
            'approved_by_user_id' => $payoutState['approved_by_user_id'],
            'approved_at' => $payoutState['approved_at'],
            'paid_by_user_id' => $payoutState['paid_by_user_id'],
            'paid_at' => $payoutState['paid_at'],
            'payout_reference' => $payoutState['payout_reference'],
            'payout_notes' => $payoutState['payout_notes'],
        ];

        if ($existing) {
            $summaryModel->update($existing['id'], $payload);
            return $payload + ['id' => $existing['id']];
        }

        $summaryModel->insert($payload);

        return $payload + ['id' => $summaryModel->getInsertID()];
    }

    public function recomputeRange(?int $driverId, int $fromYear, int $fromMonth, int $toYear, int $toMonth, ?int $branchId = null): int
    {
        $periods = $this->buildMonthSequence($fromYear, $fromMonth, $toYear, $toMonth);
        $pairs = $this->getDriverBranchPairsForPeriods($periods, $driverId, $branchId);
        $count = 0;

        foreach ($pairs as $pair) {
            foreach ($periods as $period) {
                $this->recomputeDriverMonth((int) $pair['driver_id'], $period['year'], $period['month'], (int) $pair['branch_id']);
                $count++;
            }
        }

        return $count;
    }

    public function getMonthlyBonusReport(?int $year, ?int $month, ?string $search = null): array
    {
        $query = (new DriverBonusAwardModel())
            ->select('driver_bonus_awards.*, drivers.full_name AS driver_name, drivers.mobile_number, incentive_rules.name AS rule_name, branches.name AS branch_name, approver.name AS approved_by_name, payer.name AS paid_by_name')
            ->join('drivers', 'drivers.id = driver_bonus_awards.driver_id')
            ->join('incentive_rules', 'incentive_rules.id = driver_bonus_awards.incentive_rule_id')
            ->join('branches', 'branches.id = driver_bonus_awards.branch_id', 'left')
            ->join('users approver', 'approver.id = driver_bonus_awards.approved_by_user_id', 'left')
            ->join('users payer', 'payer.id = driver_bonus_awards.paid_by_user_id', 'left')
            ->where('driver_bonus_awards.bonus_amount >', 0);

        $this->branchContext->applyScope($query, 'driver_bonus_awards.branch_id');

        if ($year !== null) {
            $query->where('driver_bonus_awards.year', $year);
        }

        if ($month !== null) {
            $query->where('driver_bonus_awards.month', $month);
        }

        if ($search !== null && trim($search) !== '') {
            $query->groupStart()
                ->like('drivers.full_name', trim($search))
                ->orLike('drivers.mobile_number', trim($search))
                ->groupEnd();
        }

        return $query
            ->orderBy('driver_bonus_awards.year', 'DESC')
            ->orderBy('driver_bonus_awards.month', 'DESC')
            ->orderBy('driver_bonus_awards.visit_threshold', 'ASC')
            ->orderBy('driver_bonus_awards.bonus_amount', 'DESC')
            ->findAll();
    }

    public function recomputeForRule(array $rule): int
    {
        $from = strtotime((string) $rule['effective_from']);
        if ($from === false) {
            return 0;
        }

        $to = !empty($rule['effective_to']) ? strtotime((string) $rule['effective_to']) : time();
        if ($to === false) {
            $to = time();
        }

        $branchId = isset($rule['branch_id']) ? (int) $rule['branch_id'] : null;

        return $this->recomputeRange(
            null,
            (int) date('Y', $from),
            (int) date('n', $from),
            (int) date('Y', $to),
            (int) date('n', $to),
            $branchId > 0 ? $branchId : null
        );
    }

    public function getCurrentRuleListing(): array
    {
        $query = new IncentiveRuleModel();
        $this->branchContext->applyScope($query);

        return $query
            ->orderBy('effective_from', 'DESC')
            ->orderBy('visit_threshold', 'ASC')
            ->findAll();
    }

    public function getDriverMonthlySummaries(int $driverId): array
    {
        return (new DriverMonthlySummaryModel())
            ->select('driver_monthly_summaries.*, incentive_rules.name AS rule_name, incentive_rules.visit_threshold, approver.name AS approved_by_name, payer.name AS paid_by_name')
            ->join('incentive_rules', 'incentive_rules.id = driver_monthly_summaries.incentive_rule_id', 'left')
            ->join('users approver', 'approver.id = driver_monthly_summaries.approved_by_user_id', 'left')
            ->join('users payer', 'payer.id = driver_monthly_summaries.paid_by_user_id', 'left')
            ->where('driver_monthly_summaries.driver_id', $driverId)
            ->orderBy('driver_monthly_summaries.year', 'DESC')
            ->orderBy('driver_monthly_summaries.month', 'DESC')
            ->findAll();
    }

    private function aggregateDriverMonth(int $driverId, string $periodStart, string $periodEnd, int $branchId): array
    {
        $row = (new VisitModel())
            ->select('COUNT(id) AS total_visits, COALESCE(SUM(guest_count), 0) AS total_guests, COALESCE(SUM(cash_incentive_amount), 0) AS total_cash_incentive')
            ->where('driver_id', $driverId)
            ->where('branch_id', $branchId)
            ->where('visited_at >=', $periodStart)
            ->where('visited_at <=', $periodEnd)
            ->first();

        return [
            'total_visits' => (int) ($row['total_visits'] ?? 0),
            'total_guests' => (int) ($row['total_guests'] ?? 0),
            'total_cash_incentive' => (float) ($row['total_cash_incentive'] ?? 0),
            'total_amount_paid' => 0.0,
        ];
    }

    private function resolveApplicableRule(int $totalVisits, string $periodStart, string $periodEnd, int $branchId): ?array
    {
        $exactCoverageRules = (new IncentiveRuleModel())
            ->where('branch_id', $branchId)
            ->where('is_active', 1)
            ->where('effective_from <=', date('Y-m-d', strtotime($periodStart)))
            ->groupStart()
                ->where('effective_to', null)
                ->orWhere('effective_to >=', date('Y-m-d', strtotime($periodEnd)))
            ->groupEnd()
            ->orderBy('visit_threshold', 'DESC')
            ->findAll();

        $rules = $exactCoverageRules !== []
            ? $exactCoverageRules
            : (new IncentiveRuleModel())
                ->where('branch_id', $branchId)
                ->where('is_active', 1)
                ->where('effective_from <=', date('Y-m-d', strtotime($periodEnd)))
                ->groupStart()
                    ->where('effective_to', null)
                    ->orWhere('effective_to >=', date('Y-m-d', strtotime($periodStart)))
                ->groupEnd()
                ->orderBy('effective_from', 'DESC')
                ->orderBy('visit_threshold', 'DESC')
                ->findAll();

        foreach ($rules as $rule) {
            if ((int) $rule['visit_threshold'] <= $totalVisits) {
                return $rule;
            }
        }

        return null;
    }

    private function syncEligibleBonusAwards(int $driverId, int $year, int $month, array $period, array $totals, int $branchId): void
    {
        $awardModel = new DriverBonusAwardModel();
        $eligibleRuleIds = [];
        $rules = (new IncentiveRuleModel())
            ->where('branch_id', $branchId)
            ->where('is_active', 1)
            ->where('effective_from <=', date('Y-m-d', strtotime($period['end'])))
            ->groupStart()
                ->where('effective_to', null)
                ->orWhere('effective_to >=', date('Y-m-d', strtotime($period['start'])))
            ->groupEnd()
            ->orderBy('visit_threshold', 'ASC')
            ->findAll();

        foreach ($rules as $rule) {
            $threshold = (int) ($rule['visit_threshold'] ?? 0);
            if ($threshold <= 0 || $threshold > (int) $totals['total_visits']) {
                continue;
            }

            $eligibleRuleIds[] = (int) $rule['id'];
            $existing = $awardModel
                ->where('driver_id', $driverId)
                ->where('branch_id', $branchId)
                ->where('incentive_rule_id', (int) $rule['id'])
                ->where('year', $year)
                ->where('month', $month)
                ->first();

            $bonusBasisAmount = $this->sumLastVisitIncentives($driverId, $period['start'], $period['end'], $threshold, $branchId);
            $bonusPercentage = (float) ($rule['bonus_value'] ?? 0);
            $bonusAmount = round($bonusBasisAmount * ($bonusPercentage / 100), 2);
            $payoutState = $this->resolvePayoutState($existing, $bonusAmount);

            $payload = [
                'branch_id' => $branchId,
                'driver_id' => $driverId,
                'incentive_rule_id' => (int) $rule['id'],
                'year' => $year,
                'month' => $month,
                'visit_threshold' => $threshold,
                'total_visits' => (int) $totals['total_visits'],
                'total_guests' => (int) $totals['total_guests'],
                'total_cash_incentive' => number_format((float) $totals['total_cash_incentive'], 2, '.', ''),
                'bonus_basis_amount' => number_format($bonusBasisAmount, 2, '.', ''),
                'bonus_percentage' => number_format($bonusPercentage, 2, '.', ''),
                'bonus_amount' => number_format($bonusAmount, 2, '.', ''),
                'computed_at' => date('Y-m-d H:i:s'),
                'payout_status' => $payoutState['payout_status'],
                'approved_by_user_id' => $payoutState['approved_by_user_id'],
                'approved_at' => $payoutState['approved_at'],
                'paid_by_user_id' => $payoutState['paid_by_user_id'],
                'paid_at' => $payoutState['paid_at'],
                'payout_reference' => $payoutState['payout_reference'],
                'payout_notes' => $payoutState['payout_notes'],
            ];

            if ($existing) {
                $awardModel->update((int) $existing['id'], $payload);
                continue;
            }

            $awardModel->insert($payload);
        }

        $staleQuery = $awardModel
            ->where('driver_id', $driverId)
            ->where('branch_id', $branchId)
            ->where('year', $year)
            ->where('month', $month);

        if ($eligibleRuleIds === []) {
            $staleQuery->delete();
            return;
        }

        $staleQuery->whereNotIn('incentive_rule_id', $eligibleRuleIds)->delete();
    }

    private function sumLastVisitIncentives(int $driverId, string $periodStart, string $periodEnd, int $visitThreshold, int $branchId): float
    {
        if ($visitThreshold <= 0) {
            return 0.0;
        }

        $visits = (new VisitModel())
            ->select('cash_incentive_amount')
            ->where('driver_id', $driverId)
            ->where('branch_id', $branchId)
            ->where('visited_at >=', $periodStart)
            ->where('visited_at <=', $periodEnd)
            ->orderBy('visited_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit($visitThreshold)
            ->findAll();

        return array_sum(array_map(static fn (array $visit): float => (float) ($visit['cash_incentive_amount'] ?? 0), $visits));
    }

    private function getPeriodBounds(int $year, int $month): array
    {
        $monthStart = sprintf('%04d-%02d-01 00:00:00', $year, $month);

        return [
            'start' => $monthStart,
            'end' => date('Y-m-t 23:59:59', strtotime($monthStart)),
        ];
    }

    private function buildMonthSequence(int $fromYear, int $fromMonth, int $toYear, int $toMonth): array
    {
        $cursor = strtotime(sprintf('%04d-%02d-01', $fromYear, $fromMonth));
        $last = strtotime(sprintf('%04d-%02d-01', $toYear, $toMonth));
        $periods = [];

        while ($cursor <= $last) {
            $periods[] = [
                'year' => (int) date('Y', $cursor),
                'month' => (int) date('n', $cursor),
            ];
            $cursor = strtotime('+1 month', $cursor);
        }

        return $periods;
    }

    private function getDriverBranchPairsForPeriods(array $periods, ?int $driverId, ?int $branchId): array
    {
        $query = (new VisitModel())->select('driver_id, branch_id');
        $query->groupStart();
        foreach ($periods as $index => $period) {
            $bounds = $this->getPeriodBounds($period['year'], $period['month']);
            if ($index === 0) {
                $query->groupStart()
                    ->where('visited_at >=', $bounds['start'])
                    ->where('visited_at <=', $bounds['end'])
                    ->groupEnd();
                continue;
            }

            $query->orGroupStart()
                ->where('visited_at >=', $bounds['start'])
                ->where('visited_at <=', $bounds['end'])
                ->groupEnd();
        }
        $query->groupEnd();

        if ($driverId !== null) {
            $query->where('driver_id', $driverId);
        }

        $scopedBranchId = $branchId ?? $this->branchContext->getScopeBranchId();
        if ($scopedBranchId !== null) {
            $query->where('branch_id', $scopedBranchId);
        }

        $rows = $query->groupBy('driver_id, branch_id')->findAll();

        return array_map(static fn (array $row): array => [
            'driver_id' => (int) $row['driver_id'],
            'branch_id' => (int) $row['branch_id'],
        ], $rows);
    }

    private function resolvePayoutState(?array $existing, float $bonusAmount): array
    {
        if ($bonusAmount <= 0) {
            return [
                'payout_status' => 'not_eligible',
                'approved_by_user_id' => null,
                'approved_at' => null,
                'paid_by_user_id' => null,
                'paid_at' => null,
                'payout_reference' => null,
                'payout_notes' => null,
            ];
        }

        $status = (string) ($existing['payout_status'] ?? 'eligible');
        if (!in_array($status, ['eligible', 'approved', 'paid'], true)) {
            $status = 'eligible';
        }

        return [
            'payout_status' => $status,
            'approved_by_user_id' => $existing['approved_by_user_id'] ?? null,
            'approved_at' => $existing['approved_at'] ?? null,
            'paid_by_user_id' => $existing['paid_by_user_id'] ?? null,
            'paid_at' => $existing['paid_at'] ?? null,
            'payout_reference' => $existing['payout_reference'] ?? null,
            'payout_notes' => $existing['payout_notes'] ?? null,
        ];
    }
}
