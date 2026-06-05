<?php

namespace App\Models;

class PayoutModel extends BranchScopedModel
{
    protected $table = 'payouts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'branch_id',
        'driver_id',
        'visit_id',
        'expense_id',
        'payout_type',
        'recipient_name',
        'amount',
        'payout_date',
        'payment_mode',
        'reference_number',
        'notes',
        'status',
        'created_by_user_id',
        'approved_by_user_id',
        'paid_by_user_id',
        'approved_at',
        'paid_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
}
