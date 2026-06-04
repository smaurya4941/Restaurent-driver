<?php

namespace App\Models;

class IncentiveRuleModel extends BranchScopedModel
{
    protected $table            = 'incentive_rules';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'branch_id',
        'name',
        'visit_threshold',
        'bonus_type',
        'bonus_value',
        'effective_from',
        'effective_to',
        'is_active',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getActiveRulesForDateRange(string $periodStart, string $periodEnd): array
    {
        return $this->where('is_active', 1)
            ->where('effective_from <=', $periodEnd)
            ->groupStart()
                ->where('effective_to', null)
                ->orWhere('effective_to >=', $periodStart)
            ->groupEnd()
            ->orderBy('effective_from', 'DESC')
            ->orderBy('visit_threshold', 'ASC')
            ->findAll();
    }
}
