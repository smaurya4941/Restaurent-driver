<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>

<?php
$pageTitle = (string) ($driver['driver_name'] ?? 'Driver Profile');
$pageSubtitle = 'Visit history, monthly bonus summary, and payout workflow.';
$pageEyebrow = 'Driver Profile';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Drivers', 'url' => base_url('drivers')],
    ['label' => $driver['driver_name'] ?? 'Profile', 'active' => true],
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
                        <div class="card-header">
                            <h3 class="card-title">Driver Details</h3>
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
                            <div class="snapshot-grid mb-3">
                                <div class="snapshot-item">
                                    <span class="label">Name</span>
                                    <span class="value"><?= esc($driver['driver_name']) ?></span>
                                </div>
                                <div class="snapshot-item">
                                    <span class="label">Mobile</span>
                                    <span class="value"><?= esc($driver['mobile_number']) ?></span>
                                </div>
                                <div class="snapshot-item">
                                    <span class="label">WhatsApp</span>
                                    <span class="value"><?= esc($driver['whatsapp_number'] ?? '—') ?></span>
                                </div>
                                <div class="snapshot-item">
                                    <span class="label">License</span>
                                    <span class="value"><?= esc($driver['license_number'] ?? '—') ?></span>
                                </div>
                                <div class="snapshot-item">
                                    <span class="label">Vehicle</span>
                                    <span class="value"><?= esc($driver['vehicle_number'] ?? '—') ?></span>
                                </div>
                                <div class="snapshot-item">
                                    <span class="label">Type</span>
                                    <span class="value"><?= esc($driver['vehicle_type'] ? ucwords($driver['vehicle_type']) : '—') ?></span>
                                </div>
                                <div class="snapshot-item">
                                    <span class="label">Cash Incentive</span>
                                    <span class="value">₹<?= esc(number_format((float) ($driver['default_cash_incentive_amount'] ?? 200), 2)) ?></span>
                                </div>
                                <div class="snapshot-item">
                                    <span class="label">Status</span>
                                    <span class="value"><span class="status-pill <?= esc($statusClass) ?>"><?= esc(ucfirst($driverStatus)) ?></span></span>
                                </div>
                            </div>
                            <?php if ($driverPhotoUrl || $licensePhotoUrl): ?>
                                <div class="row mb-3">
                                    <?php if ($driverPhotoUrl): ?>
                                        <div class="col-sm-6 mb-3">
                                            <span class="label d-block mb-2">Driver Photo</span>
                                            <a href="<?= esc($driverPhotoUrl) ?>" target="_blank" rel="noopener">
                                                <img src="<?= esc($driverPhotoUrl) ?>" alt="Driver photo" class="img-fluid rounded border">
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($licensePhotoUrl): ?>
                                        <div class="col-sm-6 mb-3">
                                            <span class="label d-block mb-2">License Photo</span>
                                            <a href="<?= esc($licensePhotoUrl) ?>" target="_blank" rel="noopener">
                                                <img src="<?= esc($licensePhotoUrl) ?>" alt="License photo" class="img-fluid rounded border">
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <a href="<?= base_url('drivers/' . $driver['id'] . '/edit') ?>" class="btn btn-outline-primary btn-sm"><i class="fas fa-pen mr-1"></i> Edit Driver</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card ops-card">
                        <div class="card-header">
                            <h3 class="card-title">Monthly Bonus Workflow</h3>
                        </div>
                        <div class="card-body ops-table-wrap table-responsive p-0">
                            <table class="table table-hover mb-0 <?= !empty($monthlySummaries) ? 'data_table1' : '' ?>">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Visits</th>
                                        <th>Cash Incentive</th>
                                        <th>Bonus %</th>
                                        <th>Bonus Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($monthlySummaries as $summary): ?>
                                        <tr>
                                            <td><?= esc(date('F Y', strtotime(sprintf('%04d-%02d-01', (int) $summary['year'], (int) $summary['month'])))) ?></td>
                                            <td><?= esc((string) $summary['total_visits']) ?></td>
                                            <td><?= esc(number_format((float) $summary['total_cash_incentive'], 2)) ?></td>
                                            <td><?= esc(number_format((float) $summary['bonus_percentage'], 2)) ?>%</td>
                                            <td><?= esc(number_format((float) $summary['bonus_amount'], 2)) ?></td>
                                            <td>
                                                <?= esc(ucwords(str_replace('_', ' ', (string) ($summary['payout_status'] ?? 'not_eligible')))) ?>
                                                <?php if (!empty($summary['approved_by_name'])): ?>
                                                    <br><small>Approved by <?= esc($summary['approved_by_name']) ?></small>
                                                <?php endif; ?>
                                                <?php if (!empty($summary['paid_by_name'])): ?>
                                                    <br><small>Paid by <?= esc($summary['paid_by_name']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($canManagePayouts && (string) ($summary['payout_status'] ?? '') === 'eligible'): ?>
                                                    <form action="<?= base_url('driver-bonus/' . $summary['id'] . '/approve') ?>" method="post" class="mb-2">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-sm btn-warning">Approve</button>
                                                    </form>
                                                <?php endif; ?>
                                                <?php if ($canManagePayouts && in_array((string) ($summary['payout_status'] ?? ''), ['eligible', 'approved'], true)): ?>
                                                    <form action="<?= base_url('driver-bonus/' . $summary['id'] . '/pay') ?>" method="post">
                                                        <?= csrf_field() ?>
                                                        <input type="text" name="payout_reference" class="form-control form-control-sm mb-2" placeholder="Payout ref">
                                                        <textarea name="payout_notes" class="form-control form-control-sm mb-2" rows="2" placeholder="Payout notes"></textarea>
                                                        <button type="submit" class="btn btn-sm btn-success">Mark Paid</button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($monthlySummaries)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No monthly bonus records found for this driver yet.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card ops-card">
                        <div class="card-header">
                            <h3 class="card-title">Visit Incentive History</h3>
                        </div>
                        <div class="card-body ops-table-wrap table-responsive p-0">
                            <table class="table table-hover mb-0 <?= !empty($visitHistory) ? 'data_table1' : '' ?>">
                                <thead>
                                    <tr>
                                        <th>Visit Time</th>
                                        <th>Vehicle</th>
                                        <th>Guests</th>
                                        <th>Food Issued</th>
                                        <th>Cash Incentive</th>
                                        <th>Given By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($visitHistory as $visit): ?>
                                        <tr>
                                            <td><?= esc(date('Y-m-d H:i:s', strtotime((string) $visit['visited_at']))) ?></td>
                                            <td><?= esc($visit['vehicle_number'] ?? '-') ?></td>
                                            <td><?= esc((string) $visit['guest_count']) ?></td>
                                            <td><?= (int) $visit['food_offered'] === 1 ? 'Yes' : 'No' ?></td>
                                            <td><?= esc(number_format((float) $visit['cash_incentive_amount'], 2)) ?></td>
                                            <td><?= esc($visit['incentive_given_by_name'] ?? '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($visitHistory)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No visits recorded for this driver yet.</td>
                                        </tr>
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
