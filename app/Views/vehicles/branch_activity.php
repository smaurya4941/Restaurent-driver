<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = 'Vehicle Branch Activity';
$breadcrumbs = [['label' => 'Vehicles'], ['label' => 'Branch Activity', 'active' => true]];
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>
    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>
            <div class="card ops-card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Branch</th>
                                <th>Vehicle</th>
                                <th>Type</th>
                                <th>Driver</th>
                                <th>Activity</th>
                                <th>Visit ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activities as $activity): ?>
                                <tr>
                                    <td><?= esc($activity['activity_at']) ?></td>
                                    <td><?= esc($activity['branch_name'] ?? '') ?></td>
                                    <td><?= esc($activity['vehicle_number'] ?? '') ?></td>
                                    <td><?= esc($activity['vehicle_type'] ?? '') ?></td>
                                    <td><?= esc($activity['driver_name'] ?? '') ?></td>
                                    <td><?= esc(ucwords(str_replace('_', ' ', $activity['activity_type'] ?? ''))) ?></td>
                                    <td><?= esc($activity['visit_id'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ($activities === []): ?>
                                <tr><td colspan="7" class="text-center text-muted py-4">No vehicle activity found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'app/Views/templates/footer.php'; ?>
