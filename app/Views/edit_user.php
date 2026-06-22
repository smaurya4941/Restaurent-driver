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

            <div class="card ops-card mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">Employee Access Controls</h3>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('update_user/' . $user['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" id="name" name="name" class="form-control" value="<?= old('name', $user['name']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Mobile Number</label>
                                    <input type="tel" id="phone" name="phone" class="form-control" value="<?= old('phone', $user['phone']) ?>" required>
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
                                <div class="form-group">
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
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Update User</button>
                    </form>
                </div>
            </div>

            <div class="card ops-card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Password Reset</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Set a new password directly for this employee.</p>
                    <form action="<?= base_url('reset_user_password/' . $user['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <div class="form-group mb-md-0">
                                    <label for="new_password">New Password</label>
                                    <input type="password" id="new_password" name="new_password" class="form-control" minlength="6" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-outline-danger"><i class="fas fa-key mr-1"></i> Reset Password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'app/Views/templates/footer.php'; ?>
