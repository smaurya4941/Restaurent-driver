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

            <div class="card ops-card mb-3">
                <div class="card-header">
                    <h3 class="card-title mb-0">Filters</h3>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('driver-bonuses') ?>" method="get" class="row align-items-end">
                        <div class="col-md-2 col-sm-6 mb-3 mb-md-0">
                            <label for="year">Year</label>
                            <select name="year" id="year" class="form-control">
                                <?php for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
                                    <option value="<?= $y ?>" <?= (int) $year === $y ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-3 mb-md-0">
                            <label for="month">Month</label>
                            <select name="month" id="month" class="form-control">
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?= $m ?>" <?= (int) $month === $m ? 'selected' : '' ?>>
                                        <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4 col-sm-12 mb-3 mb-md-0">
                            <label for="search_input">Search Driver</label>
                            <input type="text" name="search_input" id="search_input" class="form-control" value="<?= esc($searchInput) ?>" placeholder="Driver name or mobile">
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-play mr-1"></i> Run</button>
                            <a href="<?= base_url('driver-bonuses') ?>" class="btn btn-outline-secondary"><i class="fas fa-rotate-left mr-1"></i> Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card ops-card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                    <h3 class="card-title mb-0">Eligible Bonuses</h3>
                    <span class="badge badge-light"><?= count($bonuses) ?> rows</span>
                </div>
                <div class="card-body ops-table-wrap table-responsive p-0">
                    <table class="table table-hover mb-0 <?= !empty($bonuses) ? 'data_table1' : '' ?>">
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
                                    <td>
                                        <a href="<?= base_url('drivers/' . $bonus['driver_id']) ?>"><?= esc($bonus['driver_name']) ?></a>
                                        <br><small class="text-muted"><?= esc($bonus['mobile_number'] ?? '-') ?></small>
                                    </td>
                                    <td><?= esc(date('F Y', strtotime(sprintf('%04d-%02d-01', (int) $bonus['year'], (int) $bonus['month'])))) ?></td>
                                    <td>
                                        <?= esc($bonus['rule_name'] ?? 'Bonus Rule') ?>
                                        <!-- <br><small class="text-muted"><?= esc((string) $bonus['visit_threshold']) ?> visits</small> -->
                                    </td>
                                    <!-- <td><?= esc((string) $bonus['total_visits']) ?></td> -->
                                    <td><?= esc(number_format((float) $bonus['bonus_basis_amount'], 2)) ?></td>
                                    <td><?= esc(number_format((float) $bonus['bonus_percentage'], 2)) ?>%</td>
                                    <td><?= esc(number_format((float) $bonus['bonus_amount'], 2)) ?></td>
                                    <td>
                                        <?= esc(ucwords(str_replace('_', ' ', (string) ($bonus['payout_status'] ?? 'eligible')))) ?>
                                        <?php if (!empty($bonus['approved_by_name'])): ?>
                                            <br><small>Approved by <?= esc($bonus['approved_by_name']) ?></small>
                                        <?php endif; ?>
                                        <?php if (!empty($bonus['paid_by_name'])): ?>
                                            <br><small>Paid by <?= esc($bonus['paid_by_name']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td style="min-width: 210px;">
                                        <?php if ($canManagePayouts && (string) ($bonus['payout_status'] ?? '') === 'eligible'): ?>
                                            <form action="<?= base_url('driver-bonuses/' . $bonus['id'] . '/approve') ?>" method="post" class="mb-2">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-warning">Approve</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($canManagePayouts && in_array((string) ($bonus['payout_status'] ?? ''), ['eligible', 'approved'], true)): ?>
                                            <form action="<?= base_url('driver-bonuses/' . $bonus['id'] . '/pay') ?>" method="post">
                                                <?= csrf_field() ?>
                                                <input hidden type="text" name="payout_reference" class="form-control form-control-sm mb-2" placeholder="Payout ref">
                                                <textarea name="payout_notes" class="form-control form-control-sm mb-2" rows="2" placeholder="Payout notes"></textarea>
                                                <button type="submit" class="btn btn-sm btn-success">Mark Paid</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($bonuses)): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted">No eligible bonuses found for these filters.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'app/Views/templates/footer.php'; ?>
