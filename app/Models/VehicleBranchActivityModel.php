<?php

namespace App\Models;

class VehicleBranchActivityModel extends BranchScopedModel
{
    protected $table = 'vehicle_branch_activity';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'branch_id',
        'vehicle_id',
        'driver_id',
        'visit_id',
        'activity_type',
        'activity_at',
        'notes',
        'created_by_user_id',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
