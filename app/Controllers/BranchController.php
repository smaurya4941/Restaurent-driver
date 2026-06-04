<?php

namespace App\Controllers;

use App\Models\BranchModel;
use App\Models\UserModel;

class BranchController extends BaseController
{
    public function index()
    {
        if ($redirect = $this->authorizeSuperAdmin()) {
            return $redirect;
        }

        return view('branches/index', [
            'branches' => (new BranchModel())->orderBy('name', 'ASC')->findAll(),
            'branchAdmins' => $this->getBranchAdmins(),
        ]);
    }

    public function store()
    {
        if ($redirect = $this->authorizeSuperAdmin()) {
            return $redirect;
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[255]',
            'branch_code' => 'required|alpha_numeric_punct|min_length[2]|max_length[50]|is_unique[branches.branch_code]',
            'city' => 'required|max_length[100]',
            'state' => 'required|max_length[100]',
            'phone' => 'permit_empty|max_length[20]',
            'status' => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $model = new BranchModel();
        $model->insert($this->branchPayload());

        return redirect()->to('/branches')->with('success', 'Branch created successfully.');
    }

    public function update(int $id)
    {
        if ($redirect = $this->authorizeSuperAdmin()) {
            return $redirect;
        }

        $model = new BranchModel();
        $branch = $model->find($id);
        if (!$branch) {
            return redirect()->to('/branches')->with('error', 'Branch not found.');
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[255]',
            'branch_code' => 'required|alpha_numeric_punct|min_length[2]|max_length[50]|is_unique[branches.branch_code,id,' . $id . ']',
            'city' => 'required|max_length[100]',
            'state' => 'required|max_length[100]',
            'phone' => 'permit_empty|max_length[20]',
            'status' => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $payload = $this->branchPayload();
        $model->update($id, $payload);
        $this->logAudit('branch.updated', 'branch', $id, $branch, $model->find($id));

        return redirect()->to('/branches')->with('success', 'Branch updated successfully.');
    }

    public function toggleStatus(int $id)
    {
        if ($redirect = $this->authorizeSuperAdmin()) {
            return $redirect;
        }

        $model = new BranchModel();
        $branch = $model->find($id);
        if (!$branch) {
            return redirect()->to('/branches')->with('error', 'Branch not found.');
        }

        $nextStatus = ($branch['status'] ?? 'inactive') === 'active' ? 'inactive' : 'active';
        $model->update($id, ['status' => $nextStatus]);
        $this->logAudit('branch.status.updated', 'branch', $id, $branch, $model->find($id));

        return redirect()->to('/branches')->with('success', 'Branch marked ' . $nextStatus . '.');
    }

    public function assignAdmins(int $id)
    {
        if ($redirect = $this->authorizeSuperAdmin()) {
            return $redirect;
        }

        $branch = (new BranchModel())->find($id);
        if (!$branch) {
            return redirect()->to('/branches')->with('error', 'Branch not found.');
        }

        $adminIds = array_values(array_unique(array_map(
            'intval',
            (array) $this->request->getPost('admin_ids')
        )));
        $adminIds = array_filter($adminIds, static fn (int $adminId): bool => $adminId > 0);

        $userModel = new UserModel();
        $validAdminIds = [];
        if ($adminIds !== []) {
            $validAdminIds = array_map(
                'intval',
                array_column(
                    $userModel->select('id')
                        ->where('role_id', self::ROLE_BRANCH_ADMIN)
                        ->whereIn('id', $adminIds)
                        ->findAll(),
                    'id'
                )
            );
        }

        if (count($validAdminIds) !== count($adminIds)) {
            return redirect()->to('/branches')->with('error', 'One or more selected users are not branch admins.');
        }

        $db = \Config\Database::connect();
        $beforeAdmins = $this->getAssignedAdminIds($id);

        $db->transStart();
        $db->table('users')
            ->where('role_id', self::ROLE_BRANCH_ADMIN)
            ->where('branch_id', $id)
            ->update(['branch_id' => null]);

        if ($validAdminIds !== []) {
            $db->table('users')
                ->where('role_id', self::ROLE_BRANCH_ADMIN)
                ->whereIn('id', $validAdminIds)
                ->update(['branch_id' => $id]);
        }
        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->to('/branches')->with('error', 'Failed to assign branch admins.');
        }

        $this->logAudit(
            'branch.admins.assigned',
            'branch',
            $id,
            ['admin_ids' => $beforeAdmins],
            ['admin_ids' => $validAdminIds]
        );

        return redirect()->to('/branches')->with('success', 'Branch admins updated successfully.');
    }

    public function switchBranch()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        if (!$this->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Only super admins can switch branches.');
        }

        $branchId = $this->request->getPost('branch_id');
        if ($branchId === 'all' || $branchId === '' || $branchId === null) {
            $this->branchContext->setActiveBranch(null);
            return redirect()->back()->with('success', 'Now viewing all branches.');
        }

        $branch = (new BranchModel())->find((int) $branchId);
        if (!$branch) {
            return redirect()->back()->with('error', 'Branch not found.');
        }

        $this->branchContext->setActiveBranch((int) $branch['id']);

        return redirect()->back()->with('success', 'Switched to ' . $branch['name'] . '.');
    }

    private function branchPayload(): array
    {
        return [
            'name' => trim((string) $this->request->getPost('name')),
            'branch_code' => strtoupper(trim((string) $this->request->getPost('branch_code'))),
            'city' => trim((string) $this->request->getPost('city')),
            'state' => trim((string) $this->request->getPost('state')),
            'address' => trim((string) $this->request->getPost('address')),
            'phone' => trim((string) $this->request->getPost('phone')) ?: null,
            'status' => (string) $this->request->getPost('status'),
        ];
    }

    private function getBranchAdmins(): array
    {
        $admins = (new UserModel())
            ->select('id, branch_id, name, email, status')
            ->where('role_id', self::ROLE_BRANCH_ADMIN)
            ->orderBy('name', 'ASC')
            ->findAll();

        $grouped = ['all' => $admins, 'byBranch' => []];
        foreach ($admins as $admin) {
            $branchId = (int) ($admin['branch_id'] ?? 0);
            if ($branchId <= 0) {
                continue;
            }

            $grouped['byBranch'][$branchId][] = $admin;
        }

        return $grouped;
    }

    private function getAssignedAdminIds(int $branchId): array
    {
        return array_map(
            'intval',
            array_column(
                (new UserModel())
                    ->select('id')
                    ->where('role_id', self::ROLE_BRANCH_ADMIN)
                    ->where('branch_id', $branchId)
                    ->findAll(),
                'id'
            )
        );
    }
}
