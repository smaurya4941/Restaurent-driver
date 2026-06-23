<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = 'Edit Employee';
$pageSubtitle = 'Update role, status, or reset password for ' . ($user['name'] ?? 'this user') . '.';
$pageEyebrow = 'Administration';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Users', 'url' => base_url('user_list')],
    ['label' => 'Edit User', 'active' => true],
];
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card ops-card mb-4">
                        <div class="card-header ops-toolbar">
                            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Employee Access Controls</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('update_user/' . $user['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" id="name" name="name" class="form-control" value="<?= esc(old('name', $user['name'])) ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Mobile Number</label>
                                            <input type="tel" id="phone" name="phone" class="form-control" value="<?= esc(old('phone', $user['phone'])) ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="role">Role</label>
                                            <select id="role" name="role" class="form-control" required>
                                                <?php foreach (($roles ?? []) as $role): ?>
                                                    <option value="<?= $role['id'] ?>" <?= (int) $user['role_id'] === (int) $role['id'] ? 'selected' : '' ?>>
                                                        <?= esc(ucwords(str_replace('_', ' ', $role['name']))) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php if (count($branches ?? []) > 1): ?>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="branch_id">Branch</label>
                                                <select id="branch_id" name="branch_id" class="form-control">
                                                    <option value="">No branch (super admin only)</option>
                                                    <?php foreach (($branches ?? []) as $branch): ?>
                                                        <option value="<?= (int) $branch['id'] ?>" <?= (int) ($user['branch_id'] ?? 0) === (int) $branch['id'] ? 'selected' : '' ?>>
                                                            <?= esc($branch['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php elseif (count($branches ?? []) === 1): ?>
                                        <input type="hidden" name="branch_id" value="<?= (int) $branches[0]['id'] ?>">
                                    <?php endif; ?>
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label for="status">Employee Status</label>
                                            <select id="status" name="status" class="form-control" required>
                                                <?php foreach (($statusOptions ?? []) as $status): ?>
                                                    <option value="<?= esc($status) ?>" <?= (string) ($user['status'] ?? '') === $status ? 'selected' : '' ?>>
                                                        <?= esc(ucfirst($status)) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer" style="background: #F8F9FA; border-top: 1px solid #E0E0E0; display: flex; gap: 10px;">
                                <button type="submit" class="btn btn-primary-enterprise"><i class="fas fa-save mr-1"></i> Update User</button>
                                <a href="<?= base_url('user_list') ?>" class="btn btn-outline-enterprise">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card ops-card">
                        <div class="card-header ops-toolbar">
                            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Password Reset</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; margin-bottom: 16px;">
                                Set a new password directly for this employee.
                            </div>
                            <form action="<?= base_url('reset_user_password/' . $user['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <input type="password" id="new_password" name="new_password" class="form-control" minlength="6" required>
                                </div>
                                <button type="submit" class="btn btn-outline-enterprise w-100 text-danger" style="border-color: #F43F5E;"><i class="fas fa-key mr-1"></i> Reset Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* =========================================
   ENTERPRISE LAYOUT & CARD
========================================= */
.ops-card {
    background: #FFFFFF;
    border-radius: 4px;
    border: 1px solid #E0E0E0;
    box-shadow: none;
    margin-bottom: 24px;
}
.ops-toolbar {
    background: #F5F5F5;
    padding: 16px 20px;
    border-bottom: 1px solid #E0E0E0;
    border-radius: 4px 4px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}
.ops-toolbar .card-title {
    font-family: 'Hanken Grotesk', sans-serif;
    font-weight: 600;
    color: #1A1C1C;
    font-size: 18px;
}

/* =========================================
   FORM INPUTS
========================================= */
.form-group label {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    font-weight: 500;
    color: #4F4255;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 6px;
}
.form-control {
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    padding: 10px 12px;
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    color: #1A1C1C;
    background: #FFFFFF;
    box-shadow: none !important;
    transition: all 0.2s ease;
    height: auto;
}
.form-control:focus {
    border-color: #A600FF;
    outline: 0;
}

/* =========================================
   BUTTONS
========================================= */
.btn-primary-enterprise {
    background: #A600FF;
    color: #FFFFFF;
    border: none;
    border-radius: 4px;
    font-family: 'Hanken Grotesk', sans-serif;
    font-size: 13px;
    font-weight: 600;
    padding: 10px 20px;
    transition: background 0.2s;
    text-decoration: none;
}
.btn-primary-enterprise:hover {
    background: #8300CA;
    color: #FFFFFF;
}
.btn-outline-enterprise {
    background: transparent;
    color: #1A1C1C;
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    font-family: 'Hanken Grotesk', sans-serif;
    font-size: 13px;
    font-weight: 600;
    padding: 10px 20px;
    transition: all 0.2s;
    text-decoration: none;
}
.btn-outline-enterprise:hover {
    background: #F5F5F5;
    border-color: #1A1C1C;
}
.btn-outline-enterprise.text-danger:hover {
    background: #FFF0F2;
    color: #E11D48 !important;
    border-color: #F43F5E;
}
</style>

<?php include 'app/Views/templates/footer.php'; ?>
