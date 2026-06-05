<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = 'Dashboard';
$breadcrumbs = [
    ['label' => 'Home', 'active' => true],
];
$isAdminLike = in_array((int) $role, [5, 1, 3, 4], true);
$metrics = $dashboardMetrics ?? [];
$todayVisits = $metrics['todayVisits'] ?? ['total' => 0, 'guests' => 0, 'cash_total' => 0];
$monthVisits = $metrics['monthVisits'] ?? ['total' => 0, 'guests' => 0, 'cash_total' => 0];
$monthExpenses = $metrics['monthExpenses'] ?? ['total' => 0, 'amount' => 0];
$monthPayouts = $metrics['monthPayouts'] ?? ['total' => 0, 'amount' => 0];
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
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning stat-card">
                        <div class="inner">
                            <h3><?= (int) ($todayVisits['total'] ?? 0) ?></h3>
                            <p>Today's Visits</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-day"></i></div>
                        <a href="<?= base_url('visitEntryList') ?>" class="small-box-footer">View visits <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger stat-card">
                        <div class="inner">
                            <h3>₹<?= number_format((float) ($monthVisits['cash_total'] ?? 0), 0) ?></h3>
                            <p>Monthly Incentives</p>
                        </div>
                        <div class="icon"><i class="fas fa-indian-rupee-sign"></i></div>
                        <a href="<?= base_url('reports?type=visit-ledger') ?>" class="small-box-footer">View report <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="row dashboard-stat-grid mb-4">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary stat-card">
                        <div class="inner">
                            <h3><?= (int) ($monthVisits['total'] ?? 0) ?></h3>
                            <p>Monthly Visits</p>
                        </div>
                        <div class="icon"><i class="fas fa-chart-line"></i></div>
                        <a href="<?= base_url('reports?type=branch-monthly-summary') ?>" class="small-box-footer">Monthly report <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary stat-card">
                        <div class="inner">
                            <h3><?= (int) ($monthVisits['guests'] ?? 0) ?></h3>
                            <p>Monthly Guests</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <a href="<?= base_url('reports?type=top-drivers') ?>" class="small-box-footer">Top drivers <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- <div class="col-lg-3 col-6">
                    <div class="small-box bg-light stat-card">
                        <div class="inner">
                            <h3>₹<?= number_format((float) ($monthExpenses['amount'] ?? 0), 0) ?></h3>
                            <p>Monthly Expenses</p>
                        </div>
                        <div class="icon"><i class="fas fa-receipt"></i></div>
                        <a href="<?= base_url('expenses') ?>" class="small-box-footer text-dark">Expenses <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-dark stat-card">
                        <div class="inner">
                            <h3>₹<?= number_format((float) ($monthPayouts['amount'] ?? 0), 0) ?></h3>
                            <p>Monthly Payouts</p>
                        </div>
                        <div class="icon"><i class="fas fa-money-bill-transfer"></i></div>
                        <a href="<?= base_url('payouts') ?>" class="small-box-footer">Payouts <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div> -->
            </div>

            <div class="row">
                <div class="col-lg-7 mb-4">
                    <div class="card ops-card">
                        <div class="card-header"><h3 class="card-title">Top Drivers This Month</h3></div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Driver</th>
                                        <th>Mobile</th>
                                        <th class="text-right">Visits</th>
                                        <th class="text-right">Guests</th>
                                        <th class="text-right">Incentive</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (($metrics['topDrivers'] ?? []) as $driver): ?>
                                        <tr>
                                            <td><?= esc($driver['driver_name'] ?? '') ?></td>
                                            <td><?= esc($driver['mobile_number'] ?? '') ?></td>
                                            <td class="text-right"><?= (int) ($driver['visit_count'] ?? 0) ?></td>
                                            <td class="text-right"><?= (int) ($driver['guest_count'] ?? 0) ?></td>
                                            <td class="text-right">₹<?= number_format((float) ($driver['cash_total'] ?? 0), 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (($metrics['topDrivers'] ?? []) === []): ?>
                                        <tr><td colspan="5" class="text-center text-muted py-3">No visits this month.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 mb-4">
                    <div class="card ops-card">
                        <div class="card-header"><h3 class="card-title">Recent Activity</h3></div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Driver</th>
                                        <th>Vehicle</th>
                                        <th class="text-right">Guests</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (($metrics['recentVisits'] ?? []) as $visit): ?>
                                        <tr>
                                            <td><?= esc($visit['visited_at'] ?? '') ?></td>
                                            <td><?= esc($visit['driver_name'] ?? '') ?></td>
                                            <td><?= esc($visit['vehicle_number'] ?? '') ?></td>
                                            <td class="text-right"><?= (int) ($visit['guest_count'] ?? 0) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (($metrics['recentVisits'] ?? []) === []): ?>
                                        <tr><td colspan="4" class="text-center text-muted py-3">No recent visits.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
