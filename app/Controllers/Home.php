<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\BranchModel;
use App\Models\DriverModel;
use App\Models\RoleModel;
use App\Models\UserModel;
use App\Models\VisitModel;

class Home extends BaseController
{
    public function signupForm()
    {
        return view('/auth/signup');
    }

    public function signup()
    {
        $user = new UserModel();
        $email = trim((string) $this->request->getVar('email'));
        $password = (string) $this->request->getVar('password');
        $confirmPassword = (string) $this->request->getVar('confirm_password');

        if ($email === '' || $password === '' || $confirmPassword === '') {
            session()->setFlashdata('Required', 'Please fill your fields');
            return redirect()->to(base_url('/signup'));
        }

        if ($password !== $confirmPassword) {
            session()->setFlashdata('Sorry', "Password doesn't match");
            return redirect()->to(base_url('/signup'));
        }

        if ($user->where('email', $email)->first()) {
            session()->setFlashdata('Sorry', 'Email already exists');
            return redirect()->to(base_url('/signup'));
        }

        $user->save([
            'name'     => 'New User',
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role_id'  => self::ROLE_FRONTDESK,
            'status'   => self::USER_STATUS_INACTIVE,
        ]);

        session()->setFlashdata('success', 'Register Successfully');
        return redirect()->to(base_url('/'));
    }

    public function loginForm()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('/auth/login');
    }

    public function login()
    {
        $userModel = new UserModel();
        $email = trim((string) $this->request->getVar('email'));
        $password = (string) $this->request->getVar('password');
        $result = $userModel->where('email', $email)->first();

        if ($result && password_verify($password, $result['password'])) {
            $status = strtolower((string) ($result['status'] ?? ''));
            if ($status === self::USER_STATUS_DISABLED) {
                session()->setFlashdata('failed', 'This account has been disabled. Please contact an administrator.');
                return redirect()->to(base_url());
            }

            if ($status === self::USER_STATUS_INACTIVE) {
                session()->setFlashdata('failed', 'This employee account is inactive and cannot sign in.');
                return redirect()->to(base_url());
            }

            $userModel->update($result['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
            $result['role'] = (int) $result['role_id'];

            $activeBranchId = (int) $result['role_id'] === self::ROLE_SUPER_ADMIN
                ? 'all'
                : ($result['branch_id'] ?? null);

            session()->set([
                'logged_in' => true,
                'user'      => $result,
                'role'      => (int) $result['role_id'],
                'active_branch_id' => $activeBranchId,
            ]);

            $this->logAudit('auth.login', 'user', (int) $result['id'], null, ['email' => $result['email']]);
            return redirect()->to('/dashboard');
        }

        session()->setFlashdata('failed', 'Invalid email or password');
        return redirect()->to(base_url());
    }

    public function dashboard()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $role = (int) session()->get('role');
        $driverModel = new DriverModel();
        $visitModel = new VisitModel();

        $driversCount = $driverModel->countAllResults();
        $visitQuery = $visitModel;
        $this->applyBranchScope($visitQuery, 'branch_id');
        $visitsCount = $visitQuery->countAllResults();

        if (in_array($role, $this->adminLikeRoles(), true)) {
            return view('dashboard', [
                'driversCount' => $driversCount,
                'visitsCount'  => $visitsCount,
                'role'         => $role,
                'branchLabel'  => $this->branchContext->getActiveBranchLabel(),
            ]);
        }

        if ($role === self::ROLE_FRONTDESK) {
            return redirect()->to('/visitEntry');
        }

        if ($role === self::ROLE_ACCOUNTANT) {
            return redirect()->to('/report');
        }

        return redirect()->to(base_url());
    }

    public function logout()
    {
        $userId = (int) (session()->get('user')['id'] ?? 0);
        if ($userId > 0) {
            $this->logAudit('auth.logout', 'user', $userId, null, null);
        }
        session()->destroy();
        return redirect()->to(base_url());
    }

    public function driverEntry()
    {
        $data['drivers'] = $this->getDriverListing();
        $data['user_role'] = (int) session()->get('role');

        return view('driverEntry', $data);
    }

    public function addDriver()
    {
        return view('addDriver');
    }

    public function saveDriverEntry()
    {
        return redirect()->to('/drivers/create');
    }

    public function saveDriverAjax()
    {
        return redirect()->to('/visitEntry');
    }

    public function deleteDriver($id)
    {
        $driverModel = new DriverModel();

        if ($driverModel->delete($id)) {
            return redirect()->to('/driverEntry')->with('success', 'Driver deleted successfully');
        }

        return redirect()->to('/driverEntry')->with('error', 'Failed to delete driver');
    }

    public function editDriver($id)
    {
        $driver = $this->getDriverDetails((int) $id);

        if (!$driver) {
            return redirect()->to('/driverEntry')->with('error', 'Driver not found.');
        }

        return view('editDriver', ['driver' => $driver]);
    }

    public function updateDriver($id)
    {
        $driver = $this->getDriverDetails((int) $id);
        if (!$driver) {
            return redirect()->to('/driverEntry')->with('error', 'Driver not found.');
        }

        $validationRules = [
            'mobile_number' => 'required|numeric|exact_length[10]|is_unique[drivers.mobile_number,id,' . $id . ']',
            'vehicle_number' => 'required|is_unique[vehicles.vehicle_number,id,' . ($driver['vehicle_id'] ?? 0) . ']',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $driverModel = new DriverModel();
        $vehicleModel = new VehicleModel();
        $db = \Config\Database::connect();
        $db->transStart();

        $driverModel->update($id, [
            'full_name'       => $this->request->getPost('driver_name'),
            'mobile_number'   => $this->request->getPost('mobile_number'),
            'whatsapp_number' => $this->request->getPost('mobile_number'),
            'notes'           => 'Incentive offered: ' . $this->request->getPost('incentive_offered'),
        ]);

        if (!empty($driver['vehicle_id'])) {
            $vehicleModel->update($driver['vehicle_id'], [
                'vehicle_number' => $this->request->getPost('vehicle_number'),
                'vehicle_type'   => $this->request->getPost('vehicle_type'),
            ]);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->with('error', 'Failed to update driver.');
        }

        return redirect()->to('/driverEntry')->with('success', 'Driver updated successfully.');
    }

    public function profilePage()
    {
        if ($redirect = $this->authorize($this->adminLikeRoles())) {
            return $redirect;
        }

        return view('profile');
    }

    public function updateAdmin()
    {
        if ($redirect = $this->authorize($this->adminLikeRoles())) {
            return $redirect;
        }

        $userModel = new UserModel();
        $adminId = session()->get('user')['id'];
        $email = trim((string) $this->request->getPost('email'));
        $currentPassword = (string) $this->request->getPost('current_password');
        $newPassword = (string) $this->request->getPost('new_password');
        $confirmNewPassword = (string) $this->request->getPost('confirm_new_password');

        if ($email === '' || $currentPassword === '') {
            session()->setFlashdata('error', 'Email and current password are required.');
            return redirect()->back();
        }

        $existingUser = $userModel->where('email', $email)->first();
        if ($existingUser && (int) $existingUser['id'] !== (int) $adminId) {
            session()->setFlashdata('error', 'Email is already taken.');
            return redirect()->back();
        }

        $userData = $userModel->find($adminId);
        if (!$userData || !password_verify($currentPassword, $userData['password'])) {
            session()->setFlashdata('error', 'Current password is incorrect.');
            return redirect()->back();
        }

        $payload = ['email' => $email];
        if ($newPassword !== '') {
            if ($newPassword !== $confirmNewPassword) {
                session()->setFlashdata('error', 'New passwords do not match.');
                return redirect()->back();
            }
            $payload['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        $before = $userModel->find($adminId);
        $userModel->update($adminId, $payload);

        $updatedUser = $userModel->find($adminId);
        $updatedUser['role'] = (int) $updatedUser['role_id'];
        session()->set('user', $updatedUser);
        $this->logAudit('user.profile.updated', 'user', (int) $adminId, $before, $updatedUser);

        session()->setFlashdata('success', 'Profile updated successfully.');
        return redirect()->to(base_url('/profile'));
    }

    public function createUser()
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        return view('create_user', [
            'roles' => $this->getAssignableRoles(),
            'statusOptions' => $this->getUserStatusOptions(),
            'branches' => $this->getAssignableBranches(),
        ]);
    }

    public function createUserHandler()
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        $name = trim((string) $this->request->getPost('name'));
        $email = trim((string) $this->request->getPost('email'));
        $passwordInput = (string) $this->request->getPost('password');
        $roleId = (int) $this->request->getPost('role');
        $branchId = $this->request->getPost('branch_id');
        $status = strtolower(trim((string) $this->request->getPost('status')));

        if (
            $name === ''
            || $email === ''
            || $passwordInput === ''
            || !in_array($roleId, $this->getAssignableRoleIds(), true)
            || !in_array($status, $this->getUserStatusOptions(), true)
        ) {
            return redirect()->back()->with('error', 'All fields are required.');
        }

        $resolvedBranchId = $this->resolveUserBranchId($roleId, $branchId);
        if ($resolvedBranchId === false) {
            return redirect()->back()->withInput()->with('error', 'Please select a branch for this employee.');
        }

        $userModel = new UserModel();
        if ($userModel->where('email', $email)->first()) {
            return redirect()->back()->with('error', 'Email is already taken.');
        }

        $userData = [
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($passwordInput, PASSWORD_BCRYPT),
            'role_id'  => $roleId,
            'branch_id' => $resolvedBranchId,
            'status'   => $status,
        ];

        if ($userModel->insert($userData)) {
            $userId = (int) $userModel->getInsertID();
            $this->logAudit('user.created', 'user', $userId, null, $userModel->find($userId));
            return redirect()->to(base_url('user_list'))->with('success', 'User created successfully.');
        }

        return redirect()->back()->with('error', 'Failed to create user.');
    }

    public function listUsers()
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        $query = (new UserModel())
            ->select('users.*, roles.name as role_name, branches.name AS branch_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->join('branches', 'branches.id = users.branch_id', 'left');

        if (!$this->isSuperAdmin()) {
            $query->where('users.branch_id', $this->requireBranchId());
        } elseif ($this->branchContext->getScopeBranchId() !== null) {
            $query->where('users.branch_id', $this->branchContext->getScopeBranchId());
        }

        return view('user_list', ['users' => $query->findAll()]);
    }

    public function editUser($id)
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        $user = (new UserModel())->find($id);
        if (!$user || !$this->canManageUser($user)) {
            return redirect()->to(base_url('user_list'))->with('error', 'User not found.');
        }

        return view('edit_user', [
            'user'  => $user,
            'roles' => $this->getAssignableRoles(),
            'statusOptions' => $this->getUserStatusOptions(),
            'branches' => $this->getAssignableBranches(),
        ]);
    }

    public function updateUser($id)
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        $name = trim((string) $this->request->getPost('name'));
        $email = trim((string) $this->request->getPost('email'));
        $roleId = (int) $this->request->getPost('role');
        $branchId = $this->request->getPost('branch_id');
        $status = strtolower(trim((string) $this->request->getPost('status')));

        if (
            $name === ''
            || $email === ''
            || !in_array($roleId, $this->getAssignableRoleIds(), true)
            || !in_array($status, $this->getUserStatusOptions(), true)
        ) {
            return redirect()->back()->with('error', 'All fields are required.');
        }

        $userModel = new UserModel();
        $existingRecord = $userModel->find((int) $id);
        if (!$existingRecord || !$this->canManageUser($existingRecord)) {
            return redirect()->to(base_url('user_list'))->with('error', 'User not found.');
        }

        $resolvedBranchId = $this->resolveUserBranchId($roleId, $branchId);
        if ($resolvedBranchId === false) {
            return redirect()->back()->withInput()->with('error', 'Please select a branch for this employee.');
        }
        $existingUser = $userModel->where('email', $email)->first();
        if ($existingUser && (int) $existingUser['id'] !== (int) $id) {
            return redirect()->back()->with('error', 'Email is already taken.');
        }

        if ($userModel->update($id, [
            'name'      => $name,
            'email'     => $email,
            'role_id'   => $roleId,
            'branch_id' => $resolvedBranchId,
            'status'    => $status,
        ])) {
            $updated = $userModel->find((int) $id);
            $this->logAudit('user.updated', 'user', (int) $id, $existingRecord, $updated);
            return redirect()->to(base_url('user_list'))->with('success', 'User updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update user.');
    }

    public function deleteUser($id)
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        $userModel = new UserModel();
        $user = $userModel->find((int) $id);
        if (!$user || !$this->canManageUser($user)) {
            return redirect()->to(base_url('user_list'))->with('error', 'User not found.');
        }

        if ($userModel->delete($id)) {
            if ($user) {
                $this->logAudit('user.deleted', 'user', (int) $id, $user, null);
            }
            return redirect()->to(base_url('user_list'))->with('success', 'User deleted successfully.');
        }

        return redirect()->back()->with('error', 'Failed to delete user.');
    }

    public function resetUserPassword($id)
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        $newPassword = (string) $this->request->getPost('new_password');
        if (strlen($newPassword) < 6) {
            return redirect()->back()->with('error', 'Reset password must be at least 6 characters.');
        }

        $userModel = new UserModel();
        $user = $userModel->find((int) $id);
        if (!$user || !$this->canManageUser($user)) {
            return redirect()->to(base_url('user_list'))->with('error', 'User not found.');
        }

        $userModel->update((int) $id, ['password' => password_hash($newPassword, PASSWORD_BCRYPT)]);
        $this->logAudit('user.password.reset', 'user', (int) $id, ['email' => $user['email']], ['reset_by_admin' => true]);

        return redirect()->to(base_url('edit_user/' . (int) $id))->with('success', 'Password reset successfully.');
    }

    public function auditTrail()
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        $query = (new AuditLogModel())
            ->select('audit_logs.*, users.name AS user_name, users.email AS user_email, branches.name AS branch_name')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->join('branches', 'branches.id = audit_logs.branch_id', 'left');

        $this->applyBranchScope($query, 'audit_logs.branch_id');

        $logs = $query->orderBy('audit_logs.created_at', 'DESC')->findAll(200);

        return view('audit_trail', ['logs' => $logs]);
    }

    private function getDriverListing(): array
    {
        return (new DriverModel())
            ->select('drivers.id, drivers.full_name AS driver_name, drivers.mobile_number, vehicles.id AS vehicle_id, vehicles.vehicle_number, vehicles.vehicle_type, drivers.notes')
            ->join('vehicles', 'vehicles.driver_id = drivers.id AND vehicles.is_primary = 1', 'left')
            ->orderBy('drivers.id', 'DESC')
            ->findAll();
    }

    private function getUserStatusOptions(): array
    {
        return [
            self::USER_STATUS_ACTIVE,
            self::USER_STATUS_INACTIVE,
            self::USER_STATUS_DISABLED,
        ];
    }

    private function getAssignableRoles(): array
    {
        return (new RoleModel())
            ->whereIn('id', $this->getAssignableRoleIds())
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    private function getAssignableRoleIds(): array
    {
        $roles = [
            self::ROLE_BRANCH_ADMIN,
            self::ROLE_ACCOUNTANT,
            self::ROLE_SECURITY,
            self::ROLE_STAFF,
        ];

        if ($this->isSuperAdmin()) {
            $roles[] = self::ROLE_SUPER_ADMIN;
        }

        return $roles;
    }

    private function getAssignableBranches(): array
    {
        if ($this->isSuperAdmin()) {
            return (new BranchModel())->getActiveBranches();
        }

        $branchId = $this->requireBranchId();
        $branch = (new BranchModel())->find($branchId);

        return $branch ? [$branch] : [];
    }

    private function resolveUserBranchId(int $roleId, $branchIdInput): int|null|false
    {
        if ($roleId === self::ROLE_SUPER_ADMIN) {
            return null;
        }

        if (!$this->isSuperAdmin()) {
            return $this->requireBranchId();
        }

        $branchId = (int) $branchIdInput;
        if ($branchId <= 0) {
            return false;
        }

        return $branchId;
    }

    private function canManageUser(array $user): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return (int) ($user['branch_id'] ?? 0) === $this->requireBranchId();
    }

}
