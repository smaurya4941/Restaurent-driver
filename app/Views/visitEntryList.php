<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = 'Visit Log';
$pageSubtitle = 'Review every driver check-in with guest count, food, and incentive details.';
$pageEyebrow = 'Operations';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Visit Log', 'active' => true],
];
$isAdmin = in_array((int) session()->get('role'), [1, 3], true);
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <div class="ops-toolbar">
                <p class="toolbar-copy">Operational history of food and cash benefits issued at Hawa Hawai.</p>
                <a href="<?= base_url('visitEntry') ?>" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> New Visit Entry</a>
            </div>

            <div class="card ops-card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Operational Visits</h3>
                </div>
                <div class="card-body ops-table-wrap table-responsive p-0">
                    <table class="table table-hover <?= !empty($visits) ? 'data_table1' : '' ?> mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Driver</th>
                                <th>Vehicle</th>
                                <th>Guests</th>
                                <th>Food</th>
                                <th>Cash</th>
                                <!-- <th>Verified</th> -->
                                <th>Handled By</th>
                                <th>Visit Time</th>
                                <?php if ($isAdmin): ?><th>Actions</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($visits)): ?>
                                <?php foreach ($visits as $visit): ?>
                                    <tr>
                                        <td>#<?= esc((string) $visit['id']) ?></td>
                                        <td>
                                            <strong><?= esc($visit['driver_name']) ?></strong><br>
                                            <small class="text-muted"><?= esc($visit['mobile_number'] ?? '—') ?></small>
                                        </td>
                                        <td><?= esc($visit['vehicle_number'] ?? '—') ?></td>
                                        <td><span class="status-pill status-found"><?= esc((string) $visit['guest_count']) ?></span></td>
                                        <td><?= (int) $visit['food_offered'] === 1 ? '<i class="fas fa-utensils text-success"></i> Yes' : 'No' ?></td>
                                        <td>₹<?= esc((string) $visit['cash_incentive_amount']) ?></td>
                                        <!-- <td><?= esc(strtoupper($visit['verification_method'] ?? 'manual')) ?></td> -->
                                        <td><?= esc($visit['handled_by_name'] ?? $visit['verified_by_name'] ?? '—') ?></td>
                                        <td><?= esc(date('d M Y, H:i', strtotime($visit['visit_date']))) ?></td>
                                        <?php if ($isAdmin): ?>
                                            <td class="btn-group-ops text-nowrap">
                                                <a href="<?= base_url('editVisit/' . $visit['id']) ?>" class="btn btn-warning btn-sm"><i class="fas fa-pen"></i></a>
                                                <a href="<?= base_url('deleteVisit/' . $visit['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this visit?');"><i class="fas fa-trash"></i></a>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?= $isAdmin ? '9' : '8' ?>" class="ops-empty">No visits recorded yet.</td>
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
