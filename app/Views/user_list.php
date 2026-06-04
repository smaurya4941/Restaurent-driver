<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = 'Employee Directory';
$pageSubtitle = 'Manage employee roles, login status, and password recovery.';
$pageEyebrow = 'Administration';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Users', 'active' => true],
];
$canCreateUsers = in_array((int) session()->get('role'), [1, 5], true);
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <div class="ops-toolbar">
                <p class="toolbar-copy">Restaurant staff accounts for admin, accountant, and front desk roles.</p>
                <?php if ($canCreateUsers): ?>
                    <a href="<?= base_url('create_user') ?>" class="btn btn-primary"><i class="fas fa-user-plus mr-1"></i> Create User</a>
                <?php endif; ?>
            </div>

            <div class="card ops-card">
                <div class="card-header">
                    <h3 class="card-title mb-0">All Employees</h3>
                </div>
                <div class="card-body ops-table-wrap table-responsive p-0">
                    <table class="table table-hover data_table1 mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Branch</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <?php $status = strtolower((string) ($user['status'] ?? 'inactive')); ?>
                                    <tr>
                                        <td>#<?= esc((string) $user['id']) ?></td>
                                        <td><strong><?= esc($user['name']) ?></strong></td>
                                        <td><?= esc($user['email']) ?></td>
                                        <td><?= esc(ucwords(str_replace('_', ' ', $user['role_name'] ?? 'Unknown'))) ?></td>
                                        <td><?= esc($user['branch_name'] ?? 'All branches') ?></td>
                                        <td>
                                            <span class="status-pill <?= $status === 'active' ? 'status-found' : ($status === 'disabled' ? 'status-missing' : 'status-blocked') ?>">
                                                <?= esc(ucfirst($status)) ?>
                                            </span>
                                        </td>
                                        <td class="btn-group-ops text-nowrap">
                                            <a href="<?= base_url('edit_user/' . $user['id']) ?>" class="btn btn-warning btn-sm"><i class="fas fa-pen"></i></a>
                                            <a href="<?= base_url('delete_user/' . $user['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?');"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="ops-empty">No users found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'app/Views/templates/footer.php'; ?>
