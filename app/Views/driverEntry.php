<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = 'Driver Directory';
$pageSubtitle = 'Manage registered highway drivers, vehicles, incentives, and account status.';
$pageEyebrow = 'Fleet Management';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Drivers', 'active' => true],
];
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <div class="card ops-card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap ops-toolbar">
                    <div>
                        <h3 class="card-title mb-0">All Drivers</h3>
                        <span class="text-muted small d-block"><?= count($drivers ?? []) ?> registered</span>
                    </div>
                    <a href="<?= base_url('drivers/create') ?>" class="btn btn-primary-enterprise">
                        <i class="fas fa-plus mr-1"></i> Add Driver
                    </a>
                </div>
                <div class="card-body ops-table-wrap driver-directory-wrap p-0">
                    <div class="table-responsive">
                        <table class="table table-modern data_table1 mb-0">
                            <thead>
                                <tr>
                                    <th>Driver Name</th>
                                    <th>Contact</th>
                                    <th>Vehicle Details</th>
                                    <th>Incentive</th>
                                    <th>Status</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($drivers as $driver): ?>
                                    <?php
                                    $status = (string) ($driver['status'] ?? 'active');
                                    $statusClass = match ($status) {
                                        'active' => 'badge-status-active',
                                        'blocked', 'blacklisted' => 'badge-status-blocked',
                                        'duplicate' => 'badge-status-duplicate',
                                        default => 'badge-status-secondary',
                                    };
                                    ?>
                                    <tr>
                                        <!-- DRIVER NAME -->
                                        <td data-label="Driver Name">
                                            <div class="driver-name" style="font-weight: 600; color: #1A1C1C;">
                                                <?= esc($driver['driver_name']) ?>
                                            </div>
                                        </td>
                                        
                                        <!-- CONTACT -->
                                        <td data-label="Contact">
                                            <div class="driver-phone" style="font-family: 'Inter', sans-serif; font-size: 13px; color: #4F4255;">
                                                <i class="fas fa-phone-alt mr-1" style="color: #A600FF; font-size: 11px;"></i>
                                                <?= esc($driver['mobile_number']) ?>
                                            </div>
                                        </td>
                                        
                                        <!-- VEHICLE DETAILS -->
                                        <td data-label="Vehicle Details">
                                            <div style="font-family: 'Inter', sans-serif; font-size: 13px; color: #1A1C1C; font-weight: 500;">
                                                <?= esc(strtoupper($driver['vehicle_number'] ?? 'N/A')) ?>
                                            </div>
                                            <div class="mt-1">
                                                <span class="badge-enterprise-role">
                                                    <?= esc(ucwords($driver['vehicle_type'] ?? 'Unknown')) ?>
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <!-- INCENTIVE -->
                                        <td data-label="Incentive">
                                            <div style="font-family: 'Inter', sans-serif; font-size: 13px; color: #1A1C1C;">
                                                ₹<?= number_format((float)($driver['default_cash_incentive_amount'] ?? 200), 2) ?>
                                            </div>
                                        </td>

                                        <!-- STATUS -->
                                        <td data-label="Status">
                                            <span class="badge-enterprise-role <?= $statusClass ?>">
                                                <?= esc(ucwords($status)) ?>
                                            </span>
                                        </td>
                                        
                                        <!-- ACTION BUTTONS -->
                                        <td data-label="Actions" class="btn-group-ops">
                                            <div class="action-buttons">
                                                <a href="<?= base_url('drivers/' . $driver['id']) ?>"
                                                   class="btn" title="View Profile">
                                                    <i class="fas fa-user"></i>
                                                </a>
                                                <a href="<?= base_url('drivers/' . $driver['id'] . '/edit') ?>"
                                                   class="btn" title="Edit Driver">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <?php if (in_array((int) $user_role, [1, 3], true)): ?>
                                                    <a href="<?= base_url('drivers/' . $driver['id'] . '/delete') ?>"
                                                       class="btn btn-danger-enterprise"
                                                       onclick="return confirm('Delete this driver?');" title="Delete Driver">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
   DRIVER INFO
========================================= */
.driver-card {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.driver-name {
    font-family: 'Inter', sans-serif;
    font-size: 14px;
    font-weight: 600;
    color: #1A1C1C;
    line-height: 1.2;
}
.driver-phone {
    font-family: 'JetBrains Mono', monospace;
    font-size: 12px;
    color: #4F4255;
}
.driver-phone i {
    color: #807287;
}

/* Enterprise Badges */
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
.badge-status-active { border-left: 2px solid #10B981; padding-left: 8px; }
.badge-status-blocked { border-left: 2px solid #F43F5E; padding-left: 8px; }

/* =========================================
   ACTION BUTTONS
========================================= */
.btn-group-ops {
    width: 140px;
    min-width: 140px;
}
.action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}
.action-buttons .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #E0E0E0;
    background: #FFFFFF;
    color: #1A1C1C;
    transition: all 0.2s;
    font-size: 12px;
}
.action-buttons .btn:hover {
    background: #F5F5F5;
    border-color: #1A1C1C;
}
.action-buttons .btn-danger-enterprise {
    color: #F43F5E;
}
.action-buttons .btn-danger-enterprise:hover {
    background: #FFF1F2;
    border-color: #F43F5E;
    color: #F43F5E;
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

/* Inputs */
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

/* Pagination Buttons */
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
    .action-buttons {
        justify-content: flex-end;
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
