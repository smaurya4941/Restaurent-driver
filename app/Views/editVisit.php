<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = 'Edit Visit';
$pageSubtitle = 'Update guest count, food issued, and verification details for this check-in.';
$pageEyebrow = 'Operations';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Visit Log', 'url' => base_url('visitEntryList')],
    ['label' => 'Edit Visit', 'active' => true],
];
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <div class="card ops-card">
                <div class="card-header">
                    <h3 class="card-title">Update Visit Record</h3>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('saveEditedVisit/' . $visit['id']) ?>" method="post">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Driver Name</label>
                                    <input type="text" class="form-control" value="<?= esc($driver['driver_name']) ?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Mobile Number</label>
                                    <input type="text" class="form-control" value="<?= esc($driver['mobile_number']) ?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Vehicle Number</label>
                                    <input type="text" class="form-control" value="<?= esc($driver['vehicle_number'] ?? '-') ?>" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="verification_method">Verification Method</label>
                                    <select name="verification_method" id="verification_method" class="form-control" required>
                                        <?php foreach (['phone', 'qr', 'manual'] as $method): ?>
                                            <option value="<?= esc($method) ?>" <?= ($visit['verification_method'] ?? 'phone') === $method ? 'selected' : '' ?>>
                                                <?= esc(strtoupper($method)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="verification_reference">Verification Reference</label>
                                    <input type="text" class="form-control" name="verification_reference" id="verification_reference" value="<?= esc($visit['verification_reference'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="visited_at">Visit Time</label>
                                    <input type="datetime-local" class="form-control" name="visited_at" id="visited_at" value="<?= esc(date('Y-m-d\TH:i', strtotime($visit['visited_at']))) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="guest_count">Guest Count</label>
                                    <input type="number" min="0" class="form-control" name="guest_count" id="guest_count" value="<?= esc((string) $visit['guest_count']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="food_offered">Food Issued</label>
                                    <select name="food_offered" id="food_offered" class="form-control" required>
                                        <option value="1" <?= (int) $visit['food_offered'] === 1 ? 'selected' : '' ?>>Yes</option>
                                        <option value="0" <?= (int) $visit['food_offered'] === 0 ? 'selected' : '' ?>>No</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Cash Incentive</label>
                                    <input type="text" class="form-control" value="<?= esc((string) $visit['cash_incentive_amount']) ?>" disabled>
                                    <small class="text-muted">This visit uses the auto-assigned incentive amount.</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea class="form-control" name="remarks" id="remarks" rows="3"><?= esc($visit['remarks'] ?? '') ?></textarea>
                        </div>

                        <div class="floating-actions d-flex justify-content-between align-items-center flex-wrap mt-3">
                            <span class="text-muted small">Editing visit #<?= esc((string) ($visit['id'] ?? '')) ?></span>
                            <div>
                                <a href="<?= base_url('visitEntryList') ?>" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary ml-2"><i class="fas fa-save mr-1"></i> Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'app/Views/templates/footer.php'; ?>
