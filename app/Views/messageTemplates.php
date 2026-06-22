<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>

<?php
$pageTitle = 'Message Templates';
$pageSubtitle = 'Manage WhatsApp message templates.';
$pageEyebrow = 'Marketing';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Message Templates', 'active' => true],
];
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <div class="card ops-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title m-0">All Templates</h3>
                    <a href="<?= base_url('message-templates/create') ?>" class="btn btn-sm btn-primary ml-auto">
                        <i class="fas fa-plus mr-1"></i> Create New Template
                    </a>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Message Content</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (($templates ?? []) as $template): ?>
                                <tr>
                                    <td><?= esc($template['name']) ?></td>
                                    <td>
                                        <small class="text-muted d-block" style="white-space: pre-wrap; max-width: 400px;"><?= esc($template['content']) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($template['is_active']): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group">
                                            <a href="<?= base_url('message-templates/' . esc($template['id']) . '/edit') ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('message-templates/' . esc($template['id']) . '/toggle') ?>" class="btn btn-sm btn-outline-info" title="Toggle Status">
                                                <i class="fas <?= $template['is_active'] ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                                            </a>
                                            <a href="<?= base_url('message-templates/' . esc($template['id']) . '/delete') ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this template?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($templates)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No templates found. Create one to get started.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'app/Views/templates/footer.php'; ?>
