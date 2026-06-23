<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = lang('App.visit_log');
$pageSubtitle = lang('App.visit_log_subtitle');
$pageEyebrow = lang('App.operations');
$breadcrumbs = [
    ['label' => lang('App.home'), 'url' => base_url('dashboard')],
    ['label' => lang('App.visit_log'), 'active' => true],
];
$isAdmin = in_array((int) session()->get('role'), [1, 3], true);
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <div class="card ops-card">
                <div class="card-header ops-toolbar">
                    <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                        <h3 class="card-title mb-0" style="float: none; line-height: 1.2;"><?= lang('App.operational_visits') ?></h3>
                        <span class="text-muted small" style="margin-top: 4px;"><?= lang('App.visit_history_subtitle') ?></span>
                    </div>
                    <a href="<?= base_url('visitEntry') ?>" class="btn btn-primary-enterprise">
                        <i class="fas fa-plus mr-1"></i> <?= lang('App.new_visit_entry') ?>
                    </a>
                </div>
                <div class="card-body ops-table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table table-modern <?= !empty($visits) ? 'data_table1' : '' ?> mb-0">
                            <thead>
                                <tr>
                                    <th><?= lang('App.id') ?></th>
                                    <th><?= lang('App.driver') ?></th>
                                    <th><?= lang('App.vehicle') ?></th>
                                    <th><?= lang('App.guests') ?></th>
                                    <th><?= lang('App.food') ?></th>
                                    <th><?= lang('App.cash') ?></th>
                                    <th><?= lang('App.handled_by') ?></th>
                                    <th><?= lang('App.visit_time') ?></th>
                                    <?php if ($isAdmin): ?><th width="120"><?= lang('App.actions') ?></th><?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($visits)): ?>
                                    <?php foreach ($visits as $visit): ?>
                                        <tr>
                                            <td data-label="<?= lang('App.id') ?>">
                                                <div style="font-family: 'Inter', sans-serif; font-size: 13px; color: #1A1C1C; font-weight: 500;">
                                                    #<?= esc((string) $visit['id']) ?>
                                                </div>
                                            </td>
                                            <td data-label="<?= lang('App.driver') ?>">
                                                <div style="font-weight: 600; color: #1A1C1C;">
                                                    <?= esc($visit['driver_name']) ?>
                                                </div>
                                                <div style="font-family: 'Inter', sans-serif; font-size: 12px; color: #4F4255; margin-top: 2px;">
                                                    <i class="fas fa-phone-alt mr-1" style="color: #A600FF; font-size: 10px;"></i>
                                                    <?= esc($visit['mobile_number'] ?? '—') ?>
                                                </div>
                                            </td>
                                            <td data-label="<?= lang('App.vehicle') ?>">
                                                <div style="font-family: 'Inter', sans-serif; font-size: 13px; color: #1A1C1C;">
                                                    <?= esc(strtoupper($visit['vehicle_number'] ?? '—')) ?>
                                                </div>
                                            </td>
                                            <td data-label="<?= lang('App.guests') ?>">
                                                <span class="badge-enterprise-role" style="background: #10B981;">
                                                    <i class="fas fa-users mr-1"></i> <?= esc((string) $visit['guest_count']) ?>
                                                </span>
                                            </td>
                                            <td data-label="<?= lang('App.food') ?>">
                                                <div style="font-family: 'Inter', sans-serif; font-size: 13px; color: #1A1C1C;">
                                                    <?= (int) $visit['food_offered'] === 1 ? '<i class="fas fa-utensils mr-1" style="color: #10B981;"></i> ' . lang('App.yes') : '<span style="color: #4F4255;">' . lang('App.no') . '</span>' ?>
                                                </div>
                                            </td>
                                            <td data-label="<?= lang('App.cash') ?>">
                                                <div style="font-family: 'Inter', sans-serif; font-size: 13px; color: #1A1C1C;">
                                                    ₹<?= esc((string) $visit['cash_incentive_amount']) ?>
                                                </div>
                                            </td>
                                            <td data-label="<?= lang('App.handled_by') ?>">
                                                <div style="font-family: 'Inter', sans-serif; font-size: 13px; color: #4F4255;">
                                                    <?= esc($visit['handled_by_name'] ?? $visit['verified_by_name'] ?? '—') ?>
                                                </div>
                                            </td>
                                            <td data-label="<?= lang('App.visit_time') ?>">
                                                <div style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #1A1C1C;">
                                                    <?= esc(date('d M Y, H:i', strtotime($visit['visit_date']))) ?>
                                                </div>
                                            </td>
                                            <?php if ($isAdmin): ?>
                                                <td data-label="<?= lang('App.actions') ?>" class="btn-group-ops">
                                                    <div style="display: flex; gap: 8px;">
                                                        <a href="<?= base_url('editVisit/' . $visit['id']) ?>" class="btn btn-outline-enterprise" style="padding: 4px 8px; font-size: 12px;"><i class="fas fa-pen"></i></a>
                                                        <a href="<?= base_url('deleteVisit/' . $visit['id']) ?>" class="btn btn-outline-enterprise" style="padding: 4px 8px; font-size: 12px; color: #F43F5E; border-color: #F43F5E;" onclick="return confirm('<?= lang('App.delete_visit') ?>');"><i class="fas fa-trash"></i></a>
                                                    </div>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="<?= $isAdmin ? '9' : '8' ?>" class="text-center p-4" style="color: #4F4255;">
                                            <?= lang('App.no_visits_recorded_yet') ?>
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
.btn-primary-enterprise {
    background: #A600FF;
    color: #FFFFFF;
    border: none;
    border-radius: 4px;
    font-family: 'Hanken Grotesk', sans-serif;
    font-size: 13px;
    font-weight: 600;
    padding: 8px 16px;
    transition: background 0.2s;
    display: inline-block;
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
    padding: 8px 16px;
    transition: all 0.2s;
}
.btn-outline-enterprise:hover {
    background: #F5F5F5;
    border-color: #1A1C1C;
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
   ENTERPRISE TABLE
========================================= */
.table-modern {
    width: 100%;
    margin-bottom: 0;
}
.table-modern th {
    border-top: none !important;
    border-bottom: 1px solid #E0E0E0 !important;
    color: #4F4255;
    font-family: 'JetBrains Mono', monospace;
    font-weight: 500;
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.05em;
    padding: 12px 20px !important;
    background: #FAFAFA !important;
}
.table-modern td {
    vertical-align: middle !important;
    border-top: 1px solid #EEEEEE !important;
    padding: 16px 20px !important;
}
.table-modern tbody tr {
    transition: background-color 0.15s ease;
    border-left: 2px solid transparent;
}
.table-modern tbody tr:hover {
    background-color: #F9F9F9;
    border-left: 2px solid #A600FF;
}

/* =========================================
   DATA TABLES OVERRIDES
========================================= */
.dataTables_wrapper {
    padding-top: 16px;
    padding-bottom: 16px;
}
.dataTables_wrapper .row:first-child {
    padding: 0 20px 16px 20px;
    margin: 0;
}
.dataTables_wrapper .row:last-child {
    padding: 16px 20px 0 20px;
    margin: 0;
}
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    color: #4F4255;
}
.dataTables_wrapper .dataTables_info {
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    color: #4F4255;
    padding-top: 8px !important;
}
.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    padding: 6px 10px;
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    outline: none;
    margin-left: 8px;
    transition: border-color 0.2s;
}
.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #A600FF;
}
.dataTables_wrapper .dataTables_length select {
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    padding: 4px 8px;
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    outline: none;
    margin: 0 6px;
}
.dataTables_wrapper .dataTables_length select:focus {
    border-color: #A600FF;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 6px 12px !important;
    margin-left: 4px !important;
    border: 1px solid #E0E0E0 !important;
    background: #FFFFFF !important;
    color: #1A1C1C !important;
    border-radius: 4px !important;
    font-family: 'JetBrains Mono', monospace;
    font-size: 12px;
    transition: all 0.2s;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #F5F5F5 !important;
    border-color: #1A1C1C !important;
    color: #1A1C1C !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #A600FF !important;
    border-color: #A600FF !important;
    color: #FFFFFF !important;
    font-weight: 600;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Fix mobile alignment issues */
@media (max-width: 767px) {
    .table-modern thead { display: none; }
    .table-modern tbody tr {
        display: flex;
        flex-direction: column;
        padding: 16px 20px;
        border-bottom: 1px solid #EEEEEE;
        border-left: none !important;
        gap: 12px;
    }
    .table-modern tbody td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: none !important;
        padding: 0 !important;
        text-align: right;
    }
    .table-modern tbody td::before {
        content: attr(data-label);
        font-family: 'JetBrains Mono', monospace;
        font-size: 11px;
        color: #4F4255;
        text-transform: uppercase;
        font-weight: 500;
        text-align: left;
    }
    .dataTables_wrapper .row:first-child,
    .dataTables_wrapper .row:last-child {
        padding-left: 16px;
        padding-right: 16px;
    }
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        text-align: left !important;
        margin-bottom: 12px;
    }
}
</style>

<?php include 'app/Views/templates/footer.php'; ?>
