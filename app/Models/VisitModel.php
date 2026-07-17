<?php

namespace App\Models;

class VisitModel extends BranchScopedModel
{
    protected $table            = 'visits';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'branch_id',
        'driver_id',
        'vehicle_id',
        'incentive_rule_id',
        'verified_by_user_id',
        'handled_by_user_id',
        'incentive_given_by_user_id',
        'visited_at',
        'guest_count',
        'food_offered',
        'food_quantity',
        'food_type',
        'cash_incentive_amount',
        'amount_paid',
        'verification_method',
        'verification_reference',
        'remarks',
        'latitude',
        'longitude',
        'location_accuracy',
        'location_captured_at',
        'location_address',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
