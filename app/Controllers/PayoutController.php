<?php

namespace App\Controllers;

use App\Models\PayoutModel;

class PayoutController extends BaseController
{
    private const TYPES = ['driver_incentive', 'driver_bonus', 'expense_reimbursement', 'vendor_payment', 'other'];
    private const STATUSES = ['pending', 'approved', 'paid', 'cancelled'];

    public function index()
    {
        if ($redirect = $this->authorize($this->reportingRoles())) {
            return $redirect;
        }

        $query = (new PayoutModel())
            ->select('payouts.*, branches.name AS branch_name, drivers.full_name AS driver_name, creator.name AS created_by_name')
            ->join('branches', 'branches.id = payouts.branch_id', 'left')
            ->join('drivers', 'drivers.id = payouts.driver_id', 'left')
            ->join('users creator', 'creator.id = payouts.created_by_user_id', 'left');

        return view('payouts/index', [
            'payouts' => $query->orderBy('payouts.payout_date', 'DESC')->findAll(200),
            'types' => self::TYPES,
            'statuses' => self::STATUSES,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        $rules = [
            'payout_type' => 'required|in_list[' . implode(',', self::TYPES) . ']',
            'recipient_name' => 'required|max_length[150]',
            'amount' => 'required|decimal|greater_than[0]',
            'payout_date' => 'required|valid_date',
            'payment_mode' => 'permit_empty|max_length[50]',
            'reference_number' => 'permit_empty|max_length[120]',
            'notes' => 'permit_empty|max_length[1000]',
            'status' => 'required|in_list[pending,approved,paid,cancelled]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $status = (string) $this->request->getPost('status');
        $userId = $this->currentUser()['id'] ?? null;
        $payload = [
            'branch_id' => $this->requireBranchId(),
            'driver_id' => $this->optionalInt($this->request->getPost('driver_id')),
            'visit_id' => $this->optionalInt($this->request->getPost('visit_id')),
            'expense_id' => $this->optionalInt($this->request->getPost('expense_id')),
            'payout_type' => (string) $this->request->getPost('payout_type'),
            'recipient_name' => trim((string) $this->request->getPost('recipient_name')),
            'amount' => number_format((float) $this->request->getPost('amount'), 2, '.', ''),
            'payout_date' => (string) $this->request->getPost('payout_date'),
            'payment_mode' => $this->emptyToNull($this->request->getPost('payment_mode')),
            'reference_number' => $this->emptyToNull($this->request->getPost('reference_number')),
            'notes' => $this->emptyToNull($this->request->getPost('notes')),
            'status' => $status,
            'created_by_user_id' => $userId,
            'approved_by_user_id' => in_array($status, ['approved', 'paid'], true) ? $userId : null,
            'paid_by_user_id' => $status === 'paid' ? $userId : null,
            'approved_at' => in_array($status, ['approved', 'paid'], true) ? date('Y-m-d H:i:s') : null,
            'paid_at' => $status === 'paid' ? date('Y-m-d H:i:s') : null,
        ];

        $model = new PayoutModel();
        $model->insert($payload);
        $payoutId = (int) $model->getInsertID();
        $this->logAudit('payout.created', 'payout', $payoutId, null, $model->find($payoutId));

        return redirect()->to('/payouts')->with('success', 'Payout saved successfully.');
    }

    public function updateStatus(int $id)
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        $status = (string) $this->request->getPost('status');
        if (!in_array($status, self::STATUSES, true)) {
            return redirect()->back()->with('error', 'Invalid payout status.');
        }

        $model = new PayoutModel();
        $payout = $model->find($id);
        if (!$payout) {
            return redirect()->to('/payouts')->with('error', 'Payout not found.');
        }

        $payload = ['status' => $status];
        $userId = $this->currentUser()['id'] ?? null;
        if (in_array($status, ['approved', 'paid'], true)) {
            $payload['approved_by_user_id'] = $userId;
            $payload['approved_at'] = date('Y-m-d H:i:s');
        }
        if ($status === 'paid') {
            $payload['paid_by_user_id'] = $userId;
            $payload['paid_at'] = date('Y-m-d H:i:s');
        }

        $model->update($id, $payload);
        $this->logAudit('payout.status.updated', 'payout', $id, $payout, $model->find($id));

        return redirect()->to('/payouts')->with('success', 'Payout status updated.');
    }

    private function optionalInt($value): ?int
    {
        $id = (int) $value;
        return $id > 0 ? $id : null;
    }

    private function emptyToNull($value): ?string
    {
        $trimmed = trim((string) $value);
        return $trimmed === '' ? null : $trimmed;
    }
}
