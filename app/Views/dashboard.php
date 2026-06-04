<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = 'Dashboard';
$breadcrumbs = [
    ['label' => 'Home', 'active' => true],
];
$isAdminLike = in_array((int) $role, [1, 3], true);
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <div class="row dashboard-stat-grid mb-4">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info stat-card">
                        <div class="inner">
                            <h3><?= $driversCount ?></h3>
                            <p>Total Drivers</p>
                        </div>
                        <div class="icon"><i class="fas fa-id-card"></i></div>
                        <?php if ($isAdminLike): ?>
                            <a href="<?= base_url('driverEntry') ?>" class="small-box-footer">View drivers <i class="fas fa-arrow-circle-right"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success stat-card">
                        <div class="inner">
                            <h3><?= $visitsCount ?></h3>
                            <p>Total Visits</p>
                        </div>
                        <div class="icon"><i class="fas fa-road"></i></div>
                        <?php if ($isAdminLike): ?>
                            <a href="<?= base_url('visitEntry') ?>" class="small-box-footer">Visit Entry <i class="fas fa-arrow-circle-right"></i></a>
                        <?php else: ?>
                            <a href="<?= base_url('reports') ?>" class="small-box-footer">View reports <i class="fas fa-arrow-circle-right"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card ops-card action-card">
                        <div class="card-body d-flex flex-column flex-sm-row align-items-sm-center justify-content-between p-3 p-md-4">
                            <h3 class="mb-2 mb-sm-0">Quick actions</h3>
                            <div class="d-flex flex-column flex-sm-row flex-sm-wrap w-100 w-sm-auto" style="gap:0.5rem;">
                                <?php if ($isAdminLike): ?>
                                    <a href="<?= base_url('visitEntry') ?>" class="btn btn-primary"><i class="fa-solid fa-door-open mr-1"></i> Visit Entry</a>
                                    <a href="<?= base_url('drivers/create') ?>" class="btn btn-outline-primary"><i class="fa-solid fa-id-card mr-1"></i> Add Driver</a>
                                <?php else: ?>
                                    <a href="<?= base_url('reports') ?>" class="btn btn-primary"><i class="fa-solid fa-chart-line mr-1"></i> Reports</a>
                                    <a href="<?= base_url('reports?type=drivers-registered') ?>" class="btn btn-outline-primary"><i class="fa-solid fa-percent mr-1"></i> Incentive Report</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'app/Views/templates/footer.php'; ?>
