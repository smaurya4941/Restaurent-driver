<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>

<?php
$pageTitle = (string) ($driver['driver_name'] ?? lang('App.profile'));
$pageSubtitle = lang('App.driverProfile_subtitle');
$pageEyebrow = lang('App.profile');
$breadcrumbs = [
    ['label' => lang('App.home'), 'url' => base_url('dashboard')],
    ['label' => lang('App.drivers'), 'url' => base_url('drivers')],
    ['label' => $driver['driver_name'] ?? lang('App.profile'), 'active' => true],
];

$driverPhotoUrl = !empty($driver['photo_path'])
    ? base_url('drivers/uploads/photos/' . basename((string) $driver['photo_path']))
    : null;
$licensePhotoUrl = !empty($driver['license_photo_path'])
    ? base_url('drivers/uploads/licenses/' . basename((string) $driver['license_photo_path']))
    : null;
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card ops-card">
                        <div class="card-header ops-toolbar">
                            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;"><?= lang('App.driver_details') ?></h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php
                            $driverStatus = (string) ($driver['status'] ?? 'active');
                            $statusClass = match ($driverStatus) {
                                'active' => 'status-found',
                                'blocked', 'blacklisted' => 'status-blocked',
                                default => 'badge-status-secondary',
                            };
                            ?>
                            <div class="snapshot-grid mb-3" style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                                <div class="snapshot-item">
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase;"><?= lang('App.name') ?></div>
                                    <div style="font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 600; color: #1A1C1C;"><?= esc($driver['driver_name']) ?></div>
                                </div>
                                <div class="snapshot-item">
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase;"><?= lang('App.mobile') ?></div>
                                    <div style="font-family: 'Inter', sans-serif; font-size: 14px; color: #1A1C1C;"><?= esc($driver['mobile_number']) ?></div>
                                </div>
                                <div class="snapshot-item">
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase;"><?= lang('App.whatsapp') ?></div>
                                    <div style="font-family: 'Inter', sans-serif; font-size: 14px; color: #1A1C1C;"><?= esc($driver['whatsapp_number'] ?? '—') ?></div>
                                </div>
                                <div class="snapshot-item">
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase;"><?= lang('App.license') ?></div>
                                    <div style="font-family: 'Inter', sans-serif; font-size: 14px; color: #1A1C1C;"><?= esc($driver['license_number'] ?? '—') ?></div>
                                </div>
                                <div class="snapshot-item">
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase;"><?= lang('App.vehicle') ?></div>
                                    <div style="font-family: 'Inter', sans-serif; font-size: 14px; color: #1A1C1C;"><?= esc($driver['vehicle_number'] ?? '—') ?></div>
                                </div>
                                <div class="snapshot-item">
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase;"><?= lang('App.type') ?></div>
                                    <div style="font-family: 'Inter', sans-serif; font-size: 14px; color: #1A1C1C;"><?= esc($driver['vehicle_type'] ? ucwords($driver['vehicle_type']) : '—') ?></div>
                                </div>
                                <div class="snapshot-item">
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase;"><?= lang('App.cash_incentive') ?></div>
                                    <div style="font-family: 'Inter', sans-serif; font-size: 14px; color: #1A1C1C;">₹<?= esc(number_format((float) ($driver['default_cash_incentive_amount'] ?? 200), 2)) ?></div>
                                </div>
                                <div class="snapshot-item">
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase;"><?= lang('App.status') ?></div>
                                    <div>
                                        <span class="badge-enterprise-role" style="background: <?= in_array($driverStatus, ['blocked', 'blacklisted'], true) ? '#F43F5E' : '#10B981' ?>; color: #FFFFFF;">
                                            <?= esc(ucfirst($driverStatus)) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php if ($driverPhotoUrl || $licensePhotoUrl): ?>
                                <div class="row mb-3 mt-4">
                                    <?php if ($driverPhotoUrl): ?>
                                        <div class="col-sm-6 mb-3">
                                            <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase; margin-bottom: 8px;"><?= lang('App.driver_photo') ?></div>
                                            <a href="<?= esc($driverPhotoUrl) ?>" target="_blank" rel="noopener">
                                                <img src="<?= esc($driverPhotoUrl) ?>" alt="Driver photo" class="img-fluid rounded border" style="border-color: #E0E0E0 !important;">
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($licensePhotoUrl): ?>
                                        <div class="col-sm-6 mb-3">
                                            <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase; margin-bottom: 8px;"><?= lang('App.license_document') ?></div>
                                            <a href="<?= esc($licensePhotoUrl) ?>" target="_blank" rel="noopener">
                                                <img src="<?= esc($licensePhotoUrl) ?>" alt="License photo" class="img-fluid rounded border" style="border-color: #E0E0E0 !important;">
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <div class="mt-4">
                                <a href="<?= base_url('drivers/' . $driver['id'] . '/edit') ?>" class="btn btn-outline-enterprise w-100 text-center"><i class="fas fa-pen mr-1"></i> <?= lang('App.edit_driver') ?></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card ops-card">
                        <div class="card-header ops-toolbar">
                            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;"><?= lang('App.monthly_bonus_workflow') ?></h3>
                            </div>
                        </div>
                        <div class="card-body ops-table-wrap p-0">
                            <div class="table-responsive">
                                <table class="table table-modern mb-0 <?= !empty($monthlySummaries) ? 'data_table1' : '' ?>">
                                    <thead>
                                        <tr>
                                            <th><?= lang('App.month') ?></th>
                                            <th><?= lang('App.visits') ?></th>
                                            <th><?= lang('App.cash_incentive') ?></th>
                                            <th><?= lang('App.bonus_percentage') ?></th>
                                            <th><?= lang('App.bonus_amount') ?></th>
                                            <th><?= lang('App.status') ?></th>
                                            <th><?= lang('App.actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($monthlySummaries as $summary): ?>
                                            <tr>
                                                <td data-label="Month">
                                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #1A1C1C;">
                                                        <?= esc(date('F Y', strtotime(sprintf('%04d-%02d-01', (int) $summary['year'], (int) $summary['month'])))) ?>
                                                    </div>
                                                </td>
                                                <td data-label="Visits">
                                                    <div style="font-weight: 500; color: #1A1C1C; font-size: 13px;">
                                                        <?= esc((string) $summary['total_visits']) ?>
                                                    </div>
                                                </td>
                                                <td data-label="Cash Incentive">
                                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #1A1C1C;">
                                                        ₹<?= esc(number_format((float) $summary['total_cash_incentive'], 2)) ?>
                                                    </div>
                                                </td>
                                                <td data-label="Bonus %">
                                                    <div class="badge-enterprise-role" style="background: #F3E8FF; color: #A600FF;">
                                                        <?= esc(number_format((float) $summary['bonus_percentage'], 2)) ?>%
                                                    </div>
                                                </td>
                                                <td data-label="Bonus Amount">
                                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 13px; font-weight: 600; color: #10B981;">
                                                        ₹<?= esc(number_format((float) $summary['bonus_amount'], 2)) ?>
                                                    </div>
                                                </td>
                                                <td data-label="Status">
                                                    <div style="font-family: 'Inter', sans-serif; font-size: 13px; color: #1A1C1C;">
                                                        <?= esc(ucwords(str_replace('_', ' ', (string) ($summary['payout_status'] ?? 'not_eligible')))) ?>
                                                    </div>
                                                    <?php if (!empty($summary['approved_by_name'])): ?>
                                                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #4F4255; margin-top: 2px;">
                                                            <?= lang('App.approved_by') ?> <?= esc($summary['approved_by_name']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($summary['paid_by_name'])): ?>
                                                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #4F4255; margin-top: 2px;">
                                                            <?= lang('App.paid_by') ?> <?= esc($summary['paid_by_name']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td data-label="Actions" style="min-width: 180px;">
                                                    <?php if ($canManagePayouts && (string) ($summary['payout_status'] ?? '') === 'eligible'): ?>
                                                        <form action="<?= base_url('driver-bonus/' . $summary['id'] . '/approve') ?>" method="post" class="mb-2">
                                                            <?= csrf_field() ?>
                                                            <button type="submit" class="btn btn-sm btn-outline-enterprise py-1 px-2" style="font-size: 12px;"><?= lang('App.approve') ?></button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <?php if ($canManagePayouts && in_array((string) ($summary['payout_status'] ?? ''), ['eligible', 'approved'], true)): ?>
                                                        <form action="<?= base_url('driver-bonus/' . $summary['id'] . '/pay') ?>" method="post">
                                                            <?= csrf_field() ?>
                                                            <input type="text" name="payout_reference" class="form-control form-control-sm mb-2" placeholder="<?= lang('App.payout_ref') ?>">
                                                            <textarea name="payout_notes" class="form-control mb-2" style="font-size: 12px; padding: 6px; min-height: 40px;" rows="1" placeholder="<?= lang('App.payout_notes') ?>"></textarea>
                                                            <button type="submit" class="btn btn-sm btn-primary-enterprise py-1 px-2" style="font-size: 12px;"><?= lang('App.mark_paid') ?></button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($monthlySummaries)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4" style="font-family: 'Inter', sans-serif; font-size: 14px;">
                                                    <?= lang('App.no_monthly_bonus') ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card ops-card">
                        <div class="card-header ops-toolbar">
                            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;"><?= lang('App.visit_incentive_history') ?></h3>
                            </div>
                        </div>
                        <div class="card-body ops-table-wrap p-0">
                            <div class="table-responsive">
                                <table class="table table-modern mb-0 <?= !empty($visitHistory) ? 'data_table1' : '' ?>">
                                    <thead>
                                        <tr>
                                            <th><?= lang('App.visit_time') ?></th>
                                            <th><?= lang('App.vehicle') ?></th>
                                            <th><?= lang('App.guests') ?></th>
                                            <th><?= lang('App.food_issued') ?></th>
                                            <th><?= lang('App.cash_incentive') ?></th>
                                            <th><?= lang('App.given_by') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($visitHistory as $visit): ?>
                                            <tr>
                                                <td data-label="Visit Time">
                                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #1A1C1C;">
                                                        <?= esc(date('d M Y, H:i', strtotime((string) $visit['visited_at']))) ?>
                                                    </div>
                                                </td>
                                                <td data-label="Vehicle">
                                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #1A1C1C;">
                                                        <?= esc($visit['vehicle_number'] ?? '-') ?>
                                                    </div>
                                                </td>
                                                <td data-label="Guests">
                                                    <div class="badge-enterprise-role" style="background: #10B981; color: #FFFFFF;">
                                                        <i class="fas fa-users mr-1"></i> <?= esc((string) $visit['guest_count']) ?>
                                                    </div>
                                                </td>
                                                <td data-label="Food Issued">
                                                    <?php if ((int) $visit['food_offered'] === 1): ?>
                                                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #10B981;">
                                                            <i class="fas fa-utensils mr-1"></i> <?= lang('App.yes') ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #F43F5E;">
                                                            <i class="fas fa-times mr-1"></i> <?= lang('App.no') ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td data-label="Cash Incentive">
                                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #1A1C1C;">
                                                        ₹<?= esc(number_format((float) $visit['cash_incentive_amount'], 2)) ?>
                                                    </div>
                                                </td>
                                                <td data-label="Given By">
                                                    <div style="font-family: 'Inter', sans-serif; font-size: 13px; color: #4F4255;">
                                                        <?= esc($visit['incentive_given_by_name'] ?? '-') ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($visitHistory)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4" style="font-family: 'Inter', sans-serif; font-size: 14px;">
                                                    <?= lang('App.no_visits_recorded_driver') ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
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
textarea.form-control {
    min-height: 80px;
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

/* DataTables Global Overrides for Enterprise View */
div.dataTables_wrapper div.dataTables_length label {
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    color: #4F4255;
    margin-left: 20px;
    margin-top: 15px;
}
div.dataTables_wrapper div.dataTables_length select {
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    padding: 4px 8px;
    margin: 0 4px;
}
div.dataTables_wrapper div.dataTables_filter label {
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    color: #4F4255;
    margin-right: 20px;
    margin-top: 15px;
}
div.dataTables_wrapper div.dataTables_filter input {
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    padding: 6px 10px;
    margin-left: 8px;
}
div.dataTables_wrapper div.dataTables_filter input:focus {
    border-color: #A600FF;
    outline: none;
}
div.dataTables_wrapper div.dataTables_info {
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    color: #4F4255;
    padding: 20px;
}
div.dataTables_wrapper div.dataTables_paginate {
    padding: 20px;
}
.page-item.active .page-link {
    background-color: #007BFF;
    border-color: #007BFF;
}

/* Mobile Responsive Data Tables */
@media (max-width: 768px) {
    .table-modern thead {
        display: none;
    }
    .table-modern tbody td {
        display: block;
        text-align: right;
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
    div.dataTables_wrapper div.dataTables_length label,
    div.dataTables_wrapper div.dataTables_filter label {
        margin: 10px;
        display: block;
        text-align: left;
    }
    div.dataTables_wrapper div.dataTables_info,
    div.dataTables_wrapper div.dataTables_paginate {
        padding: 10px;
        text-align: center;
    }
}
</style>

<?php include 'app/Views/templates/footer.php'; ?>
