<?php

namespace App\Models;

class DriverBonusAwardModel extends BranchScopedModel
{
    protected $table            = 'driver_bonus_awards';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'branch_id',
        'driver_id',
        'incentive_rule_id',
        'month',
        'year',
        'visit_threshold',
        'total_visits',
        'total_guests',
        'total_cash_incentive',
        'bonus_basis_amount',
        'bonus_percentage',
        'bonus_amount',
        'computed_at',
        'payout_status',
        'approved_by_user_id',
        'approved_at',
        'paid_by_user_id',
        'paid_at',
        'payout_reference',
        'payout_notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
