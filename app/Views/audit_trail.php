<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = 'Audit Trail';
$pageSubtitle = 'Latest 200 tracked actions across login, drivers, visits, incentives, and WhatsApp.';
$pageEyebrow = 'Compliance';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Audit Trail', 'active' => true],
];
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <div class="card ops-card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Recent User Actions</h3>
                </div>
                <div class="card-body ops-table-wrap table-responsive p-0">
                    <table class="table table-hover data_table1 mb-0">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Entity</th>
                                <th>IP</th>
                                <th>Change Summary</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($logs)): ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td><?= esc((string) ($log['created_at'] ?? '')) ?></td>
                                        <td>
                                            <strong><?= esc((string) ($log['user_name'] ?? 'System')) ?></strong><br>
                                            <small class="text-muted"><?= esc((string) ($log['user_email'] ?? '')) ?></small>
                                        </td>
                                        <td><span class="status-pill badge-status-secondary"><?= esc((string) $log['action']) ?></span></td>
                                        <td><?= esc((string) $log['entity_type']) ?> #<?= esc((string) $log['entity_id']) ?></td>
                                        <td><?= esc((string) ($log['ip_address'] ?? '—')) ?></td>
                                        <td>
                                            <?php if (!empty($log['new_values'])): ?>
                                                <code class="d-block" style="white-space:pre-wrap;font-size:0.8rem;"><?= esc((string) $log['new_values']) ?></code>
                                            <?php elseif (!empty($log['old_values'])): ?>
                                                <code class="d-block" style="white-space:pre-wrap;font-size:0.8rem;"><?= esc((string) $log['old_values']) ?></code>
                                            <?php else: ?>
                                                <span class="text-muted">No payload</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="ops-empty">No audit entries yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'app/Views/templates/footer.php'; ?>
