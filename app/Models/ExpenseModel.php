<?php

namespace App\Models;

class ExpenseModel extends BranchScopedModel
{
    protected $table = 'expenses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'branch_id',
        'category',
        'vendor_name',
        'amount',
        'expense_date',
        'payment_mode',
        'reference_number',
        'notes',
        'status',
        'created_by_user_id',
        'approved_by_user_id',
        'approved_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
}
