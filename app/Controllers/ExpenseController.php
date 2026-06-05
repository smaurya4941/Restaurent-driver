<?php

namespace App\Controllers;

use App\Models\ExpenseModel;

class ExpenseController extends BaseController
{
    private const CATEGORIES = ['fuel', 'food', 'maintenance', 'rent', 'salary', 'utilities', 'marketing', 'other'];
    private const STATUSES = ['draft', 'submitted', 'approved', 'rejected', 'paid'];

    public function index()
    {
        if ($redirect = $this->authorize($this->reportingRoles())) {
            return $redirect;
        }

        $query = (new ExpenseModel())
            ->select('expenses.*, branches.name AS branch_name, creator.name AS created_by_name, approver.name AS approved_by_name')
            ->join('branches', 'branches.id = expenses.branch_id', 'left')
            ->join('users creator', 'creator.id = expenses.created_by_user_id', 'left')
            ->join('users approver', 'approver.id = expenses.approved_by_user_id', 'left');

        $this->applyDateFilters($query, 'expenses.expense_date');

        return view('expenses/index', [
            'expenses' => $query->orderBy('expenses.expense_date', 'DESC')->findAll(200),
            'categories' => self::CATEGORIES,
            'statuses' => self::STATUSES,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        $rules = [
            'category' => 'required|in_list[' . implode(',', self::CATEGORIES) . ']',
            'vendor_name' => 'permit_empty|max_length[150]',
            'amount' => 'required|decimal|greater_than[0]',
            'expense_date' => 'required|valid_date',
            'payment_mode' => 'permit_empty|max_length[50]',
            'reference_number' => 'permit_empty|max_length[120]',
            'notes' => 'permit_empty|max_length[1000]',
            'status' => 'required|in_list[draft,submitted,approved,rejected,paid]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $payload = $this->payload();
        $model = new ExpenseModel();
        $model->insert($payload);
        $expenseId = (int) $model->getInsertID();
        $this->logAudit('expense.created', 'expense', $expenseId, null, $model->find($expenseId));

        return redirect()->to('/expenses')->with('success', 'Expense saved successfully.');
    }

    public function updateStatus(int $id)
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        $status = (string) $this->request->getPost('status');
        if (!in_array($status, self::STATUSES, true)) {
            return redirect()->back()->with('error', 'Invalid expense status.');
        }

        $model = new ExpenseModel();
        $expense = $model->find($id);
        if (!$expense) {
            return redirect()->to('/expenses')->with('error', 'Expense not found.');
        }

        $payload = ['status' => $status];
        if (in_array($status, ['approved', 'paid'], true)) {
            $payload['approved_by_user_id'] = $this->currentUser()['id'] ?? null;
            $payload['approved_at'] = date('Y-m-d H:i:s');
        }

        $model->update($id, $payload);
        $this->logAudit('expense.status.updated', 'expense', $id, $expense, $model->find($id));

        return redirect()->to('/expenses')->with('success', 'Expense status updated.');
    }

    private function payload(): array
    {
        $status = (string) $this->request->getPost('status');

        return [
            'branch_id' => $this->requireBranchId(),
            'category' => (string) $this->request->getPost('category'),
            'vendor_name' => $this->emptyToNull($this->request->getPost('vendor_name')),
            'amount' => number_format((float) $this->request->getPost('amount'), 2, '.', ''),
            'expense_date' => (string) $this->request->getPost('expense_date'),
            'payment_mode' => $this->emptyToNull($this->request->getPost('payment_mode')),
            'reference_number' => $this->emptyToNull($this->request->getPost('reference_number')),
            'notes' => $this->emptyToNull($this->request->getPost('notes')),
            'status' => $status,
            'created_by_user_id' => $this->currentUser()['id'] ?? null,
            'approved_by_user_id' => in_array($status, ['approved', 'paid'], true) ? ($this->currentUser()['id'] ?? null) : null,
            'approved_at' => in_array($status, ['approved', 'paid'], true) ? date('Y-m-d H:i:s') : null,
        ];
    }

    private function applyDateFilters($query, string $column): void
    {
        $start = trim((string) $this->request->getGet('start_date'));
        $end = trim((string) $this->request->getGet('end_date'));
        if ($start !== '') {
            $query->where($column . ' >=', $start);
        }
        if ($end !== '') {
            $query->where($column . ' <=', $end);
        }
    }

    private function emptyToNull($value): ?string
    {
        $trimmed = trim((string) $value);
        return $trimmed === '' ? null : $trimmed;
    }
}
