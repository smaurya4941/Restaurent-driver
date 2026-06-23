<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>

<?php
$pageTitle = 'Driver Bonuses';
$pageSubtitle = 'Eligible driver bonus awards by monthly visit rule.';
$pageEyebrow = 'Rewards Engine';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Driver Bonuses', 'active' => true],
];
$currentYear = (int) date('Y');
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <div class="card ops-card mb-4">
                <div class="card-header ops-toolbar">
                    <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                        <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Filters</h3>
                    </div>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('driver-bonuses') ?>" method="get" class="row align-items-end">
                        <div class="col-md-2 col-sm-6 mb-3 mb-md-0">
                            <div class="form-group mb-0">
                                <label for="year">Year</label>
                                <select name="year" id="year" class="form-control">
                                    <?php for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
                                        <option value="<?= $y ?>" <?= (int) $year === $y ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-3 mb-md-0">
                            <div class="form-group mb-0">
                                <label for="month">Month</label>
                                <select name="month" id="month" class="form-control">
                                    <?php for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?= $m ?>" <?= (int) $month === $m ? 'selected' : '' ?>>
                                            <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12 mb-3 mb-md-0">
                            <div class="form-group mb-0">
                                <label for="search_input">Search Driver</label>
                                <input type="text" name="search_input" id="search_input" class="form-control" value="<?= esc($searchInput) ?>" placeholder="Driver name or mobile">
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <button type="submit" class="btn btn-primary-enterprise mr-2" style="font-size: 13px; padding: 10px 20px;"><i class="fas fa-play mr-1"></i> Run</button>
                            <a href="<?= base_url('driver-bonuses') ?>" class="btn btn-outline-enterprise" style="font-size: 13px; padding: 10px 20px;"><i class="fas fa-rotate-left mr-1"></i> Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card ops-card">
                <div class="card-header ops-toolbar">
                    <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                        <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Eligible Bonuses</h3>
                    </div>
                    <span class="badge-enterprise-role" style="background: #E0E0E0; color: #1A1C1C;"><?= count($bonuses) ?> rows</span>
                </div>
                <div class="card-body ops-table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table table-modern <?= !empty($bonuses) ? 'data_table1' : '' ?> mb-0">
                            <thead>
                                <tr>
                                    <th>Driver</th>
                                    <th>Month</th>
                                    <th>Rule</th>
                                    <!-- <th>Visits</th> -->
                                    <th>Basis Incentive</th>
                                    <th>Bonus %</th>
                                    <th>Bonus Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bonuses as $bonus): ?>
                                    <tr>
                                        <td data-label="Driver">
                                            <div style="font-weight: 600; color: #1A1C1C;">
                                                <a href="<?= base_url('drivers/' . $bonus['driver_id']) ?>" style="color: #A600FF; text-decoration: none;"><?= esc($bonus['driver_name']) ?></a>
                                            </div>
                                            <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; margin-top: 4px;">
                                                <i class="fas fa-phone-alt text-muted mr-1" style="font-size: 10px;"></i>
                                                <?= esc($bonus['mobile_number'] ?? '-') ?>
                                            </div>
                                        </td>
                                        <td data-label="Month">
                                            <div style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #1A1C1C;">
                                                <?= esc(date('F Y', strtotime(sprintf('%04d-%02d-01', (int) $bonus['year'], (int) $bonus['month'])))) ?>
                                            </div>
                                        </td>
                                        <td data-label="Rule">
                                            <div style="font-weight: 500; color: #1A1C1C; font-size: 13px;">
                                                <?= esc($bonus['rule_name'] ?? 'Bonus Rule') ?>
                                            </div>
                                            <!-- <br><small class="text-muted"><?= esc((string) $bonus['visit_threshold']) ?> visits</small> -->
                                        </td>
                                        <!-- <td><?= esc((string) $bonus['total_visits']) ?></td> -->
                                        <td data-label="Basis Incentive">
                                            <div style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #1A1C1C;">
                                                ₹<?= esc(number_format((float) $bonus['bonus_basis_amount'], 2)) ?>
                                            </div>
                                        </td>
                                        <td data-label="Bonus %">
                                            <div class="badge-enterprise-role" style="background: #F3E8FF; color: #A600FF;">
                                                <?= esc(number_format((float) $bonus['bonus_percentage'], 2)) ?>%
                                            </div>
                                        </td>
                                        <td data-label="Bonus Amount">
                                            <div style="font-family: 'JetBrains Mono', monospace; font-size: 13px; font-weight: 600; color: #10B981;">
                                                ₹<?= esc(number_format((float) $bonus['bonus_amount'], 2)) ?>
                                            </div>
                                        </td>
                                        <td data-label="Status">
                                            <div style="font-family: 'Inter', sans-serif; font-size: 13px; color: #1A1C1C;">
                                                <?= esc(ucwords(str_replace('_', ' ', (string) ($bonus['payout_status'] ?? 'eligible')))) ?>
                                            </div>
                                            <?php if (!empty($bonus['approved_by_name'])): ?>
                                                <div style="font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #4F4255; margin-top: 2px;">
                                                    Approved by <?= esc($bonus['approved_by_name']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($bonus['paid_by_name'])): ?>
                                                <div style="font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #4F4255; margin-top: 2px;">
                                                    Paid by <?= esc($bonus['paid_by_name']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="Action" style="min-width: 210px;">
                                            <?php if ($canManagePayouts && (string) ($bonus['payout_status'] ?? '') === 'eligible'): ?>
                                                <form action="<?= base_url('driver-bonuses/' . $bonus['id'] . '/approve') ?>" method="post" class="mb-2">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-enterprise py-1 px-2" style="font-size: 12px;">Approve</button>
                                                </form>
                                            <?php endif; ?>
                                            <?php if ($canManagePayouts && in_array((string) ($bonus['payout_status'] ?? ''), ['eligible', 'approved'], true)): ?>
                                                <form action="<?= base_url('driver-bonuses/' . $bonus['id'] . '/pay') ?>" method="post">
                                                    <?= csrf_field() ?>
                                                    <input hidden type="text" name="payout_reference" class="form-control form-control-sm mb-2" placeholder="Payout ref">
                                                    <textarea name="payout_notes" class="form-control mb-2" style="font-size: 12px; padding: 6px; min-height: 40px;" rows="1" placeholder="Payout notes"></textarea>
                                                    <button type="submit" class="btn btn-sm btn-primary-enterprise py-1 px-2" style="font-size: 12px;">Mark Paid</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($bonuses)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4" style="font-family: 'Inter', sans-serif; font-size: 14px;">
                                            No eligible bonuses found for these filters.
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
