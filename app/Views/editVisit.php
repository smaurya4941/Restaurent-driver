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
                <div class="card-header ops-toolbar">
                    <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                        <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Update Visit Record</h3>
                    </div>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('saveEditedVisit/' . $visit['id']) ?>" method="post">
                        <?= csrf_field() ?>
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
                                    <small class="text-muted" style="font-family: 'Inter', sans-serif; font-size: 11px;">This visit uses the auto-assigned incentive amount.</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea class="form-control" name="remarks" id="remarks" rows="3"><?= esc($visit['remarks'] ?? '') ?></textarea>
                        </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
                        <span class="text-muted small" style="font-family: 'JetBrains Mono', monospace;">Editing visit #<?= esc((string) ($visit['id'] ?? '')) ?></span>
                        <div>
                            <a href="<?= base_url('visitEntryList') ?>" class="btn btn-outline-enterprise">Cancel</a>
                            <button type="submit" class="btn btn-primary-enterprise ml-2"><i class="fas fa-save mr-1"></i> Save Changes</button>
                        </div>
                    </div>
                </div>
                    </form>
            </div>
        </div>
    </section>
</div>

<style>
/* =========================================
   ENTERPRISE LAYOUT & CARD
========================================= */
.ops-card {
    background: #FFFFFF;
    border-radius: 4px;
    border: 1px solid #E0E0E0;
    box-shadow: none;
    margin-bottom: 24px;
}
.ops-toolbar {
    background: #F5F5F5;
    padding: 16px 20px;
    border-bottom: 1px solid #E0E0E0;
    border-radius: 4px 4px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}
.ops-toolbar .card-title {
    font-family: 'Hanken Grotesk', sans-serif;
    font-weight: 600;
    color: #1A1C1C;
    font-size: 18px;
}
.ops-toolbar .text-muted {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    color: #4F4255;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-top: 4px;
}

/* =========================================
   FORM INPUTS
========================================= */
.form-group label {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    font-weight: 500;
    color: #4F4255;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 6px;
}
.form-control {
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    padding: 10px 12px;
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    color: #1A1C1C;
    background: #FFFFFF;
    box-shadow: none !important;
    transition: all 0.2s ease;
    height: auto;
}
.form-control:focus {
    border-color: #A600FF;
    outline: 0;
}
textarea.form-control {
    min-height: 80px;
}

/* =========================================
   BUTTONS
========================================= */
.btn-primary-enterprise {
    background: #A600FF;
    color: #FFFFFF;
    border: none;
    border-radius: 4px;
    font-family: 'Hanken Grotesk', sans-serif;
    font-size: 13px;
    font-weight: 600;
    padding: 10px 20px;
    transition: background 0.2s;
    text-decoration: none;
}
.btn-primary-enterprise:hover {
    background: #8300CA;
    color: #FFFFFF;
}
.btn-outline-enterprise {
    background: transparent;
    color: #1A1C1C;
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    font-family: 'Hanken Grotesk', sans-serif;
    font-size: 13px;
    font-weight: 600;
    padding: 10px 20px;
    transition: all 0.2s;
    text-decoration: none;
}
.btn-outline-enterprise:hover {
    background: #F5F5F5;
    border-color: #1A1C1C;
}

.card-footer {
    background: #FFFFFF;
    padding: 20px;
    border-top: 1px solid #E0E0E0 !important;
    border-radius: 0 0 4px 4px;
}
</style>

<?php include 'app/Views/templates/footer.php'; ?>
