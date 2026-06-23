<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$isAdminLike = in_array((int) $role, [5, 1, 3, 4], true);
$metrics = $dashboardMetrics ?? [];
$todayVisits = $metrics['todayVisits'] ?? ['total' => 0, 'guests' => 0, 'cash_total' => 0];
$monthVisits = $metrics['monthVisits'] ?? ['total' => 0, 'guests' => 0, 'cash_total' => 0];
$monthExpenses = $metrics['monthExpenses'] ?? ['total' => 0, 'amount' => 0];
$monthPayouts = $metrics['monthPayouts'] ?? ['total' => 0, 'amount' => 0];
?>

<style>
    /* 
       Color Template: "High-Contrast Enterprise"
       - Primary/Accent: #A600FF (Purple)
       - Surface: #FFFFFF
       - Background: #F9F9F9
       - On-Surface: #1A1C1C
       - Border Subtle: #E0E0E0
    */
    .content-wrapper {
        background-color: #F9F9F9 !important;
    }
    
    /* Enterprise Dashboard Cards */
    .dash-card {
        background: #FFFFFF;
        border-radius: 4px;
        border: 1px solid #E0E0E0;
        box-shadow: none;
        transition: all 0.2s ease;
        position: relative;
        padding: 16px;
    }
    .dash-card:hover {
        border: 1px solid #A600FF;
    }
    
    /* Small, sharp icon wrapper */
    .dash-icon-wrapper {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        margin-bottom: 0.75rem;
        background: #F5F5F5;
        color: #1A1C1C;
        border: 1px solid #E0E0E0;
    }
    
    .dash-value {
        font-family: 'Hanken Grotesk', sans-serif;
        font-size: 24px;
        font-weight: 700;
        color: #1A1C1C;
        line-height: 1.2;
        margin-bottom: 0.25rem;
        letter-spacing: -0.01em;
    }
    .dash-label {
        font-family: 'JetBrains Mono', monospace;
        font-size: 11px;
        color: #4F4255;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .dash-link {
        display: inline-block;
        margin-top: 0.75rem;
        font-family: 'JetBrains Mono', monospace;
        font-size: 11px;
        font-weight: 500;
        color: #A600FF;
        text-decoration: none;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }
    .dash-link:hover {
        color: #8300CA;
        text-decoration: underline;
    }

    /* Enterprise Table Styling */
    .premium-table-card {
        background: #FFFFFF;
        border-radius: 4px;
        border: 1px solid #E0E0E0;
        box-shadow: none;
    }
    .premium-table-card .card-header-custom {
        background: #F5F5F5;
        padding: 12px 16px;
        border-bottom: 1px solid #E0E0E0;
        font-family: 'Hanken Grotesk', sans-serif;
        font-weight: 600;
        color: #1A1C1C;
        font-size: 16px;
    }
    .table-modern {
        width: 100%;
        margin-bottom: 0;
    }
    .table-modern th {
        border-top: none;
        border-bottom: 1px solid #E0E0E0;
        color: #4F4255;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.05em;
        padding: 12px 16px;
        background: #FFFFFF;
    }
    .table-modern td {
        padding: 12px 16px;
        vertical-align: middle;
        border-top: 1px solid #EEEEEE;
        color: #1A1C1C;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 400;
    }
    .table-modern tbody tr {
        transition: background-color 0.15s ease;
        border-left: 2px solid transparent;
    }
    .table-modern tbody tr:hover {
        background-color: #F9F9F9;
        border-left: 2px solid #A600FF;
    }
    
    /* Quick Actions */
    .action-btn {
        border-radius: 4px;
        padding: 8px 16px;
        font-family: 'Hanken Grotesk', sans-serif;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s;
        border: 1px solid transparent;
        text-transform: none;
    }
    .action-btn-primary {
        background: #A600FF;
        color: #FFFFFF;
    }
    .action-btn-primary:hover {
        background: #8300CA;
        color: #FFFFFF;
    }
    .action-btn-outline {
        background: #FFFFFF;
        border: 1px solid #1A1C1C;
        color: #1A1C1C;
    }
    .action-btn-outline:hover {
        background: #F5F5F5;
        color: #1A1C1C;
    }
    .badge-enterprise {
        background: #1A1C1C;
        color: #FFF;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 500;
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 2px;
    }
</style>

<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <!-- STATS ROW 1 -->
            <div class="row mb-3">
                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                    <div class="dash-card">
                        <div class="dash-icon-wrapper">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="dash-value"><?= $driversCount ?></div>
                        <div class="dash-label">Total Drivers</div>
                        <?php if ($isAdminLike): ?>
                            <a href="<?= base_url('driverEntry') ?>" class="dash-link">Manage Drivers <i class="fas fa-arrow-right ml-1"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                    <div class="dash-card">
                        <div class="dash-icon-wrapper">
                            <i class="fas fa-road"></i>
                        </div>
                        <div class="dash-value"><?= $visitsCount ?></div>
                        <div class="dash-label">Total Visits</div>
                        <?php if ($isAdminLike): ?>
                            <a href="<?= base_url('visitEntry') ?>" class="dash-link">Log Visit <i class="fas fa-arrow-right ml-1"></i></a>
                        <?php else: ?>
                            <a href="<?= base_url('reports') ?>" class="dash-link">View Reports <i class="fas fa-arrow-right ml-1"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                    <div class="dash-card">
                        <div class="dash-icon-wrapper">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="dash-value"><?= (int) ($todayVisits['total'] ?? 0) ?></div>
                        <div class="dash-label">Today's Visits</div>
                        <a href="<?= base_url('visitEntryList') ?>" class="dash-link">View Today <i class="fas fa-arrow-right ml-1"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                    <div class="dash-card">
                        <div class="dash-icon-wrapper">
                            <i class="fas fa-indian-rupee-sign"></i>
                        </div>
                        <div class="dash-value">₹<?= number_format((float) ($monthVisits['cash_total'] ?? 0), 0) ?></div>
                        <div class="dash-label">Monthly Incentives</div>
                        <a href="<?= base_url('reports?type=visit-ledger') ?>" class="dash-link">View Ledger <i class="fas fa-arrow-right ml-1"></i></a>
                    </div>
                </div>
            </div>

            <!-- STATS ROW 2 -->
            <div class="row mb-3">
                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                    <div class="dash-card">
                        <div class="dash-icon-wrapper">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="dash-value"><?= (int) ($monthVisits['total'] ?? 0) ?></div>
                        <div class="dash-label">Monthly Visits</div>
                        <a href="<?= base_url('reports?type=branch-monthly-summary') ?>" class="dash-link">Monthly Report <i class="fas fa-arrow-right ml-1"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                    <div class="dash-card">
                        <div class="dash-icon-wrapper">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="dash-value"><?= (int) ($monthVisits['guests'] ?? 0) ?></div>
                        <div class="dash-label">Monthly Guests</div>
                        <a href="<?= base_url('reports?type=top-drivers') ?>" class="dash-link">Top Drivers <i class="fas fa-arrow-right ml-1"></i></a>
                    </div>
                </div>
                
                <!-- Quick Actions (Spanning remaining space) -->
                <div class="col-lg-6 col-md-12 mb-3 mb-lg-0">
                    <div class="dash-card h-100 d-flex flex-column justify-content-center">
                        <h4 class="mb-3" style="font-family: 'Hanken Grotesk', sans-serif; font-weight: 600; color: #1A1C1C; font-size: 18px;">Quick Actions</h4>
                        <div class="d-flex flex-wrap" style="gap: 8px;">
                            <?php if ($isAdminLike): ?>
                                <a href="<?= base_url('visitEntry') ?>" class="btn action-btn action-btn-primary">
                                    <i class="fa-solid fa-door-open mr-1"></i>Add New Visit
                                </a>
                                <a href="<?= base_url('drivers/create') ?>" class="btn action-btn action-btn-outline">
                                    <i class="fa-solid fa-id-card mr-1"></i> Add Driver
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url('reports') ?>" class="btn action-btn action-btn-primary">
                                    <i class="fa-solid fa-chart-line mr-1"></i> View Reports
                                </a>
                                <a href="<?= base_url('reports?type=drivers-registered') ?>" class="btn action-btn action-btn-outline">
                                    <i class="fa-solid fa-percent mr-1"></i> Incentive Report
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLES ROW -->
            <div class="row">
                <div class="col-lg-7 mb-4">
                    <div class="premium-table-card">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <span>Top Drivers This Month</span>
                            <i class="fas fa-trophy" style="color: #A600FF;"></i>
                        </div>
                        <div class="table-responsive">
                            <table class="table-modern">
                                <thead>
                                    <tr>
                                        <th>Driver</th>
                                        <th>Mobile</th>
                                        <th class="text-center">Visits</th>
                                        <th class="text-center">Guests</th>
                                        <th class="text-right">Incentive</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (($metrics['topDrivers'] ?? []) as $driver): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span style="font-weight: 600; color: #1A1C1C;"><?= esc($driver['driver_name'] ?? '') ?></span>
                                                </div>
                                            </td>
                                            <td style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #4F4255;"><?= esc($driver['mobile_number'] ?? '') ?></td>
                                            <td class="text-center"><span class="badge-enterprise"><?= (int) ($driver['visit_count'] ?? 0) ?></span></td>
                                            <td class="text-center" style="color: #4F4255;"><?= (int) ($driver['guest_count'] ?? 0) ?></td>
                                            <td class="text-right" style="font-weight: 600; color: #A600FF;">₹<?= number_format((float) ($driver['cash_total'] ?? 0), 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (($metrics['topDrivers'] ?? []) === []): ?>
                                        <tr><td colspan="5" class="text-center text-muted py-4">No visits this month.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 mb-4">
                    <div class="premium-table-card">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <span>Recent Activity</span>
                            <i class="fas fa-clock" style="color: #1A1C1C;"></i>
                        </div>
                        <div class="table-responsive">
                            <table class="table-modern">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Driver</th>
                                        <th>Vehicle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (($metrics['recentVisits'] ?? []) as $visit): ?>
                                        <tr>
                                            <td style="color: #4F4255; font-family: 'JetBrains Mono', monospace; font-size: 11px;">
                                                <?php 
                                                    $time = strtotime($visit['visited_at'] ?? '');
                                                    echo $time ? date('d M, h:i A', $time) : ''; 
                                                ?>
                                            </td>
                                            <td style="font-weight: 600; color: #1A1C1C;"><?= esc($visit['driver_name'] ?? '') ?></td>
                                            <td><span class="badge-enterprise" style="background:#F5F5F5; color:#1A1C1C; border:1px solid #E0E0E0;"><?= esc($visit['vehicle_number'] ?? '') ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (($metrics['recentVisits'] ?? []) === []): ?>
                                        <tr><td colspan="3" class="text-center text-muted py-4">No recent visits.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'app/Views/templates/footer.php'; ?>
