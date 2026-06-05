<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = 'Driver Loyalty';
$breadcrumbs = [['label' => 'Drivers'], ['label' => 'Loyalty', 'active' => true]];
$isSuperAdmin = (int) session()->get('role') === 5;
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>
    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>
            <div class="card ops-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title">National Loyalty Leaderboard</h3>
                    <?php if ($isSuperAdmin): ?>
                        <form action="<?= base_url('loyalty/recompute') ?>" method="post">
                            <button class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-rotate mr-1"></i> Recompute</button>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Driver</th>
                                <th>Mobile</th>
                                <th>Tier</th>
                                <th class="text-right">Points</th>
                                <th class="text-right">Visits</th>
                                <th class="text-right">Branches</th>
                                <th class="text-right">Bonus Paid</th>
                                <th>Last Visit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leaderboard as $index => $row): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= esc($row['full_name'] ?? '') ?></td>
                                    <td><?= esc($row['mobile_number'] ?? '') ?></td>
                                    <td><span class="badge badge-info"><?= esc(ucwords($row['tier'] ?? 'bronze')) ?></span></td>
                                    <td class="text-right"><?= number_format((int) ($row['loyalty_points'] ?? 0)) ?></td>
                                    <td class="text-right"><?= number_format((int) ($row['total_visits'] ?? 0)) ?></td>
                                    <td class="text-right"><?= number_format((int) ($row['total_branches_visited'] ?? 0)) ?></td>
                                    <td class="text-right">₹<?= number_format((float) ($row['total_bonus_paid'] ?? 0), 2) ?></td>
                                    <td><?= esc($row['last_visit_at'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ($leaderboard === []): ?>
                                <tr><td colspan="9" class="text-center text-muted py-4">No loyalty data yet. Recompute after visits are available.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'app/Views/templates/footer.php'; ?>
