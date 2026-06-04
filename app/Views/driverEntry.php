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
                    <a href="<?= base_url('drivers/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Add Driver
                    </a>
                </div>
                <div class="card-body ops-table-wrap driver-directory-wrap table-responsive p-0">
                    <table class="table table-hover data_table1 driver-directory-table mb-0">
                    <thead>
    <tr>
        <th>Driver Info</th>
        <th width="120">Actions</th>
    </tr>
</thead>
                        <tbody>
    <?php $i = 1; ?>
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

<!-- DRIVER INFO -->
<td>
    <div class="driver-card">

        <div class="driver-name">
            <strong><?= esc($driver['driver_name']) ?></strong>
        </div>

        <div class="driver-phone">
            <i class="fas fa-phone-alt mr-1"></i>
            <?= esc($driver['mobile_number']) ?>
        </div>

        <div class="driver-category">
            <span class="badge badge-primary">
                <?= esc(ucwords($driver['vehicle_type'] ?? '')) ?>
            </span>
        </div>

    </div>
</td>

<!-- ACTION BUTTONS -->
<td class="btn-group-ops">
    <div class="action-buttons">

        <a href="<?= base_url('drivers/' . $driver['id']) ?>"
           class="btn btn-info btn-sm">
            <i class="fas fa-user"></i>
        </a>

        <a href="<?= base_url('drivers/' . $driver['id'] . '/edit') ?>"
           class="btn btn-warning btn-sm">
            <i class="fas fa-pen"></i>
        </a>

        <?php if (in_array((int) $user_role, [1, 3], true)): ?>
            <a href="<?= base_url('drivers/' . $driver['id'] . '/delete') ?>"
               class="btn btn-danger btn-sm"
               onclick="return confirm('Delete this driver?');">
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
   TABLE LAYOUT
========================================= */

.table {
    width: 100%;
    margin-bottom: 0;
}

.table td,
.table th {
    vertical-align: middle !important;
    border-top: 1px solid #eee;
    padding: 14px 12px;
}

.ops-table-wrap {
    overflow-x: hidden !important;
}

.driver-directory-table {
    table-layout: fixed;
}

.driver-directory-table th:first-child,
.driver-directory-table td:first-child {
    width: auto;
}

/* =========================================
   DRIVER INFO
========================================= */

.driver-card {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.driver-name {
    font-size: 16px;
    font-weight: 700;
    color: #222;
    line-height: 1.2;
}

.driver-phone {
    font-size: 14px;
    color: #666;
}

.driver-category .badge {
    font-size: 11px;
    padding: 5px 10px;
    border-radius: 20px;
}

/* =========================================
   ACTION BUTTONS
========================================= */

.btn-group-ops {
    width: 120px;
    min-width: 120px;
}

.action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    justify-content: center;
    align-items: center;
}

.action-buttons .btn {
    width: 34px;
    height: 34px;
    padding: 0;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* =========================================
   MOBILE VIEW
========================================= */

@media (max-width: 767px) {

    .card-body {
        padding: 0 !important;
    }

    .driver-directory-wrap,
    .driver-directory-wrap .dt-scroll,
    .driver-directory-wrap .dt-scroll-head,
    .driver-directory-wrap .dt-scroll-headInner,
    .driver-directory-wrap .dt-scroll-body,
    .driver-directory-wrap .dataTables_scroll,
    .driver-directory-wrap .dataTables_scrollHead,
    .driver-directory-wrap .dataTables_scrollHeadInner,
    .driver-directory-wrap .dataTables_scrollBody {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
    }

    .driver-directory-table,
    .driver-directory-wrap .dt-scroll table,
    .driver-directory-wrap .dataTables_scroll table {
        display: block !important;
        width: 100% !important;
        min-width: 0 !important;
    }

    .driver-directory-table thead {
        display: none;
    }

    .driver-directory-table tbody {
        display: block;
        width: 100%;
    }

    .driver-directory-table tbody tr {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 82px;
        column-gap: 8px;
        align-items: center;
        padding: 12px 10px;
        border-bottom: 1px solid #eee;
        width: 100%;
    }

    .driver-directory-table tbody td {
        display: block;
        border: none !important;
        padding: 0 !important;
    }

    /* LEFT SIDE INFO */
    .driver-directory-table tbody td:first-child {
        width: auto !important;
        min-width: 0;
    }

    /* RIGHT SIDE BUTTONS */c
    .driver-directory-table tbody td:last-child {
        width: 82px !important;
        min-width: 82px;
    }

    .driver-name {
        font-size: 15px;
        overflow-wrap: anywhere;
    }

    .driver-phone {
        font-size: 13px;
    }

    .driver-category .badge {
        font-size: 10px;
    }

    .action-buttons {
        flex-direction: row;
        gap: 5px;
        justify-content: flex-end;
    }

    .action-buttons .btn {
        width: 24px;
        height: 28px;
        border-radius: 6px;
        font-size: 12px;
    }

    /* SEARCH + SHOW DROPDOWN */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        width: 100%;
        text-align: left !important;
        margin-bottom: 10px;
        padding: 0 10px;
    }

    .dataTables_wrapper .dataTables_filter input {
        width: 100% !important;
        margin-left: 0 !important;
    }
}

/* =========================================
   DESKTOP VIEW
========================================= */

@media (min-width: 768px) {

    .table tbody tr {
        height: 90px;
    }

    .action-buttons {
        flex-direction: row;
    }
}

</style>

<?php include 'app/Views/templates/footer.php'; ?>
