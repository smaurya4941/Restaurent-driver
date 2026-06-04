<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>

<?php
$pageTitle = 'Bonus Rules';
$pageSubtitle = 'Configure visit milestone bonuses. Incentive is paid on every visit; bonus is paid once when the monthly rule is satisfied.';
$pageEyebrow = 'Rewards Engine';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Bonus Rules', 'active' => true],
];
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
                            <h3 class="card-title">Add Bonus Rule</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('bonus-rules') ?>" method="post">
                                <div class="form-group">
                                    <label for="name">Rule Label</label>
                                    <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name')) ?>" placeholder="Example: June 2026 Bonus Plan" required>
                                </div>
                                <div class="form-group">
                                    <label for="visit_threshold">Minimum Monthly Visits</label>
                                    <input type="number" min="0" name="visit_threshold" id="visit_threshold" class="form-control" value="<?= esc(old('visit_threshold')) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="bonus_value">Bonus Percentage</label>
                                    <input type="number" step="0.01" min="0" name="bonus_value" id="bonus_value" class="form-control" value="<?= esc(old('bonus_value')) ?>" placeholder="10.00" required>
                                </div>
                                <div class="form-group">
                                    <label for="effective_from">Effective From</label>
                                    <input type="date" name="effective_from" id="effective_from" class="form-control" value="<?= esc(old('effective_from')) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="effective_to">Effective To</label>
                                    <input type="date" name="effective_to" id="effective_to" class="form-control" value="<?= esc(old('effective_to')) ?>">
                                    <small class="form-text text-muted">Leave empty to keep this bonus rule active until replaced.</small>
                                </div>
                                <div class="form-group">
                                    <label for="is_active">Status</label>
                                    <select name="is_active" id="is_active" class="form-control">
                                        <option value="1" <?= old('is_active', '1') === '1' ? 'selected' : '' ?>>Active</option>
                                        <option value="0" <?= old('is_active') === '0' ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">Save Bonus Rule</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card ops-card">
                        <div class="card-header">
                            <h3 class="card-title">Configured Bonus Rules</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Version</th>
                                        <th>Threshold</th>
                                        <th>Bonus %</th>
                                        <th>Effective From</th>
                                        <th>Effective To</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rules as $rule): ?>
                                        <tr>
                                            <td><?= esc($rule['name']) ?></td>
                                            <td><?= esc((string) $rule['visit_threshold']) ?> visits</td>
                                            <td><?= esc(number_format((float) $rule['bonus_value'], 2)) ?>%</td>
                                            <td><?= esc($rule['effective_from'] ?: '-') ?></td>
                                            <td><?= esc($rule['effective_to'] ?: 'Open') ?></td>
                                            <td>
                                                <span class="badge badge-<?= (int) $rule['is_active'] === 1 ? 'success' : 'secondary' ?>">
                                                    <?= (int) $rule['is_active'] === 1 ? 'Active' : 'Inactive' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('bonus-rules/' . $rule['id'] . '/toggle') ?>" class="btn btn-sm btn-outline-primary">
                                                    <?= (int) $rule['is_active'] === 1 ? 'Deactivate' : 'Activate' ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($rules)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No bonus rules configured yet.</td>
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
