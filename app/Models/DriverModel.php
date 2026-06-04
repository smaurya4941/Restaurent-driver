<?php

namespace App\Models;

use CodeIgniter\Model;

class DriverModel extends Model
{
    protected $table            = 'drivers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'full_name',
        'mobile_number',
        'whatsapp_number',
        'whatsapp_opt_in',
        'whatsapp_opt_in_at',
        'whatsapp_opt_out_at',
        'whatsapp_opt_out_reason',
        'photo_path',
        'license_photo_path',
        'license_number',
        'address',
        'city',
        'state',
        'notes',
        'default_cash_incentive_amount',
        'status',
        'registered_by',
        'verified_at',
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

    public function withPrimaryVehicle()
    {
        return $this->select('drivers.*, drivers.full_name AS driver_name, vehicles.id AS vehicle_id, vehicles.vehicle_number, vehicles.vehicle_type')
            ->join('vehicles', 'vehicles.driver_id = drivers.id AND vehicles.is_primary = 1 AND vehicles.deleted_at IS NULL', 'left');
    }

    public function optedInForWhatsApp()
    {
        return $this->where('drivers.status', 'active')
            ->where('drivers.whatsapp_opt_in', 1)
            ->where('drivers.whatsapp_number IS NOT NULL')
            ->where('drivers.whatsapp_number <>', '');
    }
}
