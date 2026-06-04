<?php

namespace App\Controllers;

use App\Models\BranchModel;

class BranchController extends BaseController
{
    public function index()
    {
        if ($redirect = $this->authorizeSuperAdmin()) {
            return $redirect;
        }

        return view('branches/index', [
            'branches' => (new BranchModel())->orderBy('name', 'ASC')->findAll(),
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
        $model->insert([
            'name' => trim((string) $this->request->getPost('name')),
            'branch_code' => strtoupper(trim((string) $this->request->getPost('branch_code'))),
            'city' => trim((string) $this->request->getPost('city')),
            'state' => trim((string) $this->request->getPost('state')),
            'address' => trim((string) $this->request->getPost('address')),
            'phone' => trim((string) $this->request->getPost('phone')) ?: null,
            'status' => (string) $this->request->getPost('status'),
        ]);

        return redirect()->to('/branches')->with('success', 'Branch created successfully.');
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
}
