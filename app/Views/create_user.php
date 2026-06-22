<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = 'Create Employee';
$pageSubtitle = 'Add a new admin, accountant, or front desk account.';
$pageEyebrow = 'Administration';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Users', 'url' => base_url('user_list')],
    ['label' => 'Create User', 'active' => true],
];
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <div class="card ops-card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Employee Access Setup</h3>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('create_user_handler') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="desk-note mb-4">
                            <strong>Role guide:</strong> Admin manages everything, Accountant sees reports, and Security handles visits and driver verification.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Mobile Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role">Role</label>
                                    <select class="form-control" id="role" name="role" required>
                                        <?php foreach (($roles ?? []) as $role): ?>
                                            <option value="<?= $role['id'] ?>"><?= esc(ucwords(str_replace('_', ' ', $role['name']))) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <?php if (count($branches ?? []) > 1): ?>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="branch_id">Branch</label>
                                        <select class="form-control" id="branch_id" name="branch_id">
                                            <option value="">Select branch</option>
                                            <?php foreach (($branches ?? []) as $branch): ?>
                                                <option value="<?= (int) $branch['id'] ?>"><?= esc($branch['name']) ?></option>
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
                                    <select class="form-control" id="status" name="status" required>
                                        <?php foreach (($statusOptions ?? []) as $status): ?>
                                            <option value="<?= esc($status) ?>" <?= $status === 'active' ? 'selected' : '' ?>><?= esc(ucfirst($status)) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="floating-actions d-flex justify-content-between align-items-center flex-wrap mt-3">
                            <span class="text-muted small">Inactive and disabled users cannot sign in.</span>
                            <div>
                                <a href="<?= base_url('user_list') ?>" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary ml-2">Create User</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'app/Views/templates/footer.php'; ?>
