<?php

namespace App\Models;

use CodeIgniter\Model;

class DriverLoyaltyAccountModel extends Model
{
    protected $table = 'driver_loyalty_accounts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'driver_id',
        'total_visits',
        'total_branches_visited',
        'total_guests',
        'total_cash_incentive',
        'total_bonus_paid',
        'loyalty_points',
        'tier',
        'last_visit_at',
        'computed_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
