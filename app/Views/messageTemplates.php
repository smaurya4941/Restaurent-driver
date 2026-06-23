<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>

<?php
$pageTitle = lang('App.message_templates');
$pageSubtitle = lang('App.message_templates_desc');
$pageEyebrow = lang('App.marketing');
$breadcrumbs = [
    ['label' => lang('App.home'), 'url' => base_url('dashboard')],
    ['label' => lang('App.message_templates'), 'active' => true],
];
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <div class="card ops-card mb-4">
                <div class="card-header ops-toolbar">
                    <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                        <h3 class="card-title mb-0" style="float: none; line-height: 1.2;"><?= lang('App.all_templates') ?></h3>
                    </div>
                    <a href="<?= base_url('message-templates/create') ?>" class="btn btn-primary-enterprise py-2 px-3" style="font-size: 13px;">
                        <i class="fas fa-plus mr-1"></i> <?= lang('App.create_new_template') ?>
                    </a>
                </div>
                <div class="card-body ops-table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th><?= lang('App.name') ?></th>
                                    <th><?= lang('App.message_content') ?></th>
                                    <th><?= lang('App.status') ?></th>
                                    <th class="text-right"><?= lang('App.actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (($templates ?? []) as $template): ?>
                                    <tr>
                                        <td data-label="Name">
                                            <div style="font-weight: 600; color: #1A1C1C; font-size: 14px;">
                                                <?= esc($template['name']) ?>
                                            </div>
                                        </td>
                                        <td data-label="Message Content">
                                            <div style="font-family: 'Inter', sans-serif; font-size: 13px; color: #4F4255; white-space: pre-wrap; max-width: 500px;">
                                                <?= esc($template['content']) ?>
                                            </div>
                                        </td>
                                        <td data-label="Status">
                                            <?php if ($template['is_active']): ?>
                                                <span class="badge-enterprise-role" style="background: #10B981; color: #FFFFFF;"><?= lang('App.active') ?></span>
                                            <?php else: ?>
                                                <span class="badge-enterprise-role" style="background: #E0E0E0; color: #1A1C1C;"><?= lang('App.inactive') ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="Actions" class="text-right">
                                            <div class="btn-group" style="gap: 4px;">
                                                <a href="<?= base_url('message-templates/' . esc($template['id']) . '/edit') ?>" class="btn btn-sm btn-outline-enterprise py-1 px-2" title="<?= lang('App.edit') ?>">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('message-templates/' . esc($template['id']) . '/toggle') ?>" class="btn btn-sm btn-outline-enterprise py-1 px-2" title="<?= lang('App.toggle_status') ?>">
                                                    <i class="fas <?= $template['is_active'] ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                                                </a>
                                                <a href="<?= base_url('message-templates/' . esc($template['id']) . '/delete') ?>" class="btn btn-sm btn-outline-enterprise py-1 px-2 text-danger" title="<?= lang('App.delete') ?>" onclick="return confirm('<?= lang('App.delete_template_confirm') ?>');" style="border-color: #F43F5E;">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($templates)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4" style="font-family: 'Inter', sans-serif; font-size: 14px;">
                                            <?= lang('App.no_templates_found') ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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
.ops-toolbar .text-muted {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    color: #4F4255;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-top: 4px;
}
.badge-enterprise-role {
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    font-weight: 500;
    padding: 4px 8px;
    background: #1A1C1C;
    color: #FFFFFF;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    display: inline-block;
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

/* =========================================
   TABLE MODERN
========================================= */
.table-modern {
    width: 100%;
    border-collapse: collapse;
}
.table-modern thead th {
    background: #F8F9FA;
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    font-weight: 600;
    color: #4F4255;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 16px 20px;
    border-bottom: 2px solid #E0E0E0;
    border-top: none;
    white-space: nowrap;
}
.table-modern tbody td {
    padding: 16px 20px;
    vertical-align: middle;
    border-bottom: 1px solid #EEEEEE;
}
.table-modern tbody tr:last-child td {
    border-bottom: none;
}
.table-modern tbody tr:hover {
    background-color: #F8F9FA;
}

/* Mobile Responsive Data Tables */
@media (max-width: 768px) {
    .table-modern thead {
        display: none;
    }
    .table-modern tbody td {
        display: block;
        text-align: right !important;
        padding: 10px 15px;
        border-bottom: 1px solid #EEEEEE;
        position: relative;
    }
    .table-modern tbody td::before {
        content: attr(data-label);
        float: left;
        font-family: 'JetBrains Mono', monospace;
        font-size: 11px;
        font-weight: 600;
        color: #4F4255;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .table-modern tbody tr {
        border-bottom: 2px solid #E0E0E0;
        display: block;
        margin-bottom: 10px;
    }
    .table-modern tbody td:last-child {
        border-bottom: none;
    }
    .btn-group {
        display: flex;
        justify-content: flex-end;
    }
}
</style>

<?php include 'app/Views/templates/footer.php'; ?>
