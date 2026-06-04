<?php

namespace App\Models;

use CodeIgniter\Model;

class VehicleModel extends Model
{
    protected $table            = 'vehicles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'driver_id',
        'vehicle_number',
        'vehicle_type',
        'rc_number',
        'rc_expiry_date',
        'permit_number',
        'permit_expiry_date',
        'insurance_policy_number',
        'insurance_expiry_date',
        'vehicle_brand',
        'vehicle_model',
        'vehicle_color',
        'is_primary',
        'status',
        'assigned_from',
        'assigned_until',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getPrimaryVehicleForDriver(int $driverId): ?array
    {
        return $this->where('driver_id', $driverId)
            ->where('is_primary', 1)
            ->where('deleted_at', null)
            ->orderBy('assigned_from', 'DESC')
            ->first();
    }

    public function getVehicleHistoryForDriver(int $driverId): array
    {
        return $this->where('driver_id', $driverId)
            ->where('deleted_at', null)
            ->orderBy('is_primary', 'DESC')
            ->orderBy('assigned_from', 'DESC')
            ->findAll();
    }
}
