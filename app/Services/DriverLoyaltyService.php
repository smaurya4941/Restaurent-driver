<?php

namespace App\Services;

use App\Models\DriverLoyaltyAccountModel;
use Config\Database;

class DriverLoyaltyService
{
    public function recomputeDriver(int $driverId): ?array
    {
        if ($driverId <= 0) {
            return null;
        }

        $db = Database::connect();
        $visitStats = $db->table('visits')
            ->select('COUNT(id) AS total_visits, COUNT(DISTINCT branch_id) AS total_branches_visited, COALESCE(SUM(guest_count), 0) AS total_guests, COALESCE(SUM(cash_incentive_amount), 0) AS total_cash_incentive, MAX(visited_at) AS last_visit_at')
            ->where('driver_id', $driverId)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray() ?? [];

        $bonusStats = $db->table('driver_bonus_awards')
            ->select('COALESCE(SUM(bonus_amount), 0) AS total_bonus_paid')
            ->where('driver_id', $driverId)
            ->where('payout_status', 'paid')
            ->get()
            ->getRowArray() ?? [];

        $totalVisits = (int) ($visitStats['total_visits'] ?? 0);
        $totalBranches = (int) ($visitStats['total_branches_visited'] ?? 0);
        $totalGuests = (int) ($visitStats['total_guests'] ?? 0);
        $points = ($totalVisits * 10) + ($totalBranches * 50) + $totalGuests;

        $payload = [
            'driver_id' => $driverId,
            'total_visits' => $totalVisits,
            'total_branches_visited' => $totalBranches,
            'total_guests' => $totalGuests,
            'total_cash_incentive' => number_format((float) ($visitStats['total_cash_incentive'] ?? 0), 2, '.', ''),
            'total_bonus_paid' => number_format((float) ($bonusStats['total_bonus_paid'] ?? 0), 2, '.', ''),
            'loyalty_points' => $points,
            'tier' => $this->tierForPoints($points),
            'last_visit_at' => $visitStats['last_visit_at'] ?? null,
            'computed_at' => date('Y-m-d H:i:s'),
        ];

        $model = new DriverLoyaltyAccountModel();
        $existing = $model->where('driver_id', $driverId)->first();
        if ($existing) {
            $model->update((int) $existing['id'], $payload);
            return $model->find((int) $existing['id']);
        }

        $model->insert($payload);
        return $model->find((int) $model->getInsertID());
    }

    public function recomputeAll(): int
    {
        $driverIds = array_map(
            'intval',
            array_column(
                Database::connect()->table('drivers')->select('id')->where('deleted_at', null)->get()->getResultArray(),
                'id'
            )
        );

        foreach ($driverIds as $driverId) {
            $this->recomputeDriver($driverId);
        }

        return count($driverIds);
    }

    public function getNationalLeaderboard(int $limit = 50): array
    {
        return (new DriverLoyaltyAccountModel())
            ->select('driver_loyalty_accounts.*, drivers.full_name, drivers.mobile_number, drivers.city, drivers.state')
            ->join('drivers', 'drivers.id = driver_loyalty_accounts.driver_id')
            ->where('drivers.deleted_at', null)
            ->orderBy('driver_loyalty_accounts.loyalty_points', 'DESC')
            ->orderBy('driver_loyalty_accounts.total_visits', 'DESC')
            ->findAll($limit);
    }

    private function tierForPoints(int $points): string
    {
        if ($points >= 2000) {
            return 'platinum';
        }

        if ($points >= 1000) {
            return 'gold';
        }

        if ($points >= 400) {
            return 'silver';
        }

        return 'bronze';
    }
}
