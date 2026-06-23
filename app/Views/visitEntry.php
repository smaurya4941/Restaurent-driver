<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$currentUserName = session()->get('user')['name'] ?? 'Current user';
$searchTerm = $search_term ?? '';
$isLookupPerformed = $searchTerm !== '';
$isDriverFound = isset($driver) && $driver;
$pageTitle = 'Driver Check-In Desk';
$pageSubtitle = 'Verify registration and log driver visits at the highway counter.';
$pageEyebrow = 'Front Desk';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Driver Check-In', 'active' => true],
];
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <!-- DRIVER LOOKUP -->
            <div class="card ops-card mb-4 visit-search-card">
                <div class="card-header ops-toolbar">
                    <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                        <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Driver Lookup</h3>
                        <span class="text-muted small" style="margin-top: 4px;">Handled by <?= esc($currentUserName) ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('visitEntry') ?>" method="post">
                        <?= csrf_field(); ?>
                        <div class="d-flex flex-column flex-sm-row">
                            <input type="text" class="form-control form-control-lg mb-2 mb-sm-0 mr-sm-2" name="search_term" id="search_term" value="<?= esc($searchTerm) ?>" placeholder="Mobile, vehicle, license, or ID" required style="font-size: 16px;">
                            <button type="submit" class="btn btn-primary-enterprise px-4" style="font-size: 15px; padding: 12px 24px;">Search</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <!-- VERIFICATION RESULT -->
                <div class="col-lg-5 mb-4">
                    <div class="card ops-card h-100">
                        <div class="card-header ops-toolbar">
                            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Verification Result</h3>
                            </div>
                            <?php if ($isLookupPerformed && $isDriverFound): ?>
                                <?php $driverStatus = (string) ($driver['status'] ?? 'active'); ?>
                                <span class="badge-enterprise-role" style="background: <?= in_array($driverStatus, ['blocked', 'blacklisted'], true) ? '#F43F5E' : '#10B981' ?>;">
                                    <i class="fas <?= in_array($driverStatus, ['blocked', 'blacklisted'], true) ? 'fa-exclamation-triangle' : 'fa-check-circle' ?> mr-1"></i>
                                    <?= esc(ucfirst($driverStatus)) ?>
                                </span>
                            <?php elseif ($isLookupPerformed): ?>
                                <span class="badge-enterprise-role" style="background: #4F4255;">
                                    <i class="fas fa-user-plus mr-1"></i> Not Registered
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if ($isDriverFound): ?>
                                <div class="snapshot-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                    <div class="snapshot-item">
                                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase;">Driver Name</div>
                                        <div style="font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 600; color: #1A1C1C;"><?= esc($driver['driver_name']) ?></div>
                                    </div>
                                    <div class="snapshot-item">
                                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase;">Mobile</div>
                                        <div style="font-family: 'Inter', sans-serif; font-size: 14px; color: #1A1C1C;"><?= esc($driver['mobile_number']) ?></div>
                                    </div>
                                    <div class="snapshot-item">
                                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase;">License</div>
                                        <div style="font-family: 'Inter', sans-serif; font-size: 14px; color: #1A1C1C;"><?= esc($driver['license_number'] ?? 'Not captured') ?></div>
                                    </div>
                                    <div class="snapshot-item">
                                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase;">Latest Visit</div>
                                        <div style="font-family: 'Inter', sans-serif; font-size: 14px; color: #1A1C1C;"><?= !empty($driver['recent_visit_summary']['latest_visit_at']) ? esc($driver['recent_visit_summary']['latest_visit_at']) : 'No previous visit' ?></div>
                                    </div>
                                    <div class="snapshot-item">
                                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase;">Today Visits</div>
                                        <div style="font-family: 'Inter', sans-serif; font-size: 14px; color: #1A1C1C;"><?= esc((string) ($driver['recent_visit_summary']['today_count'] ?? 0)) ?></div>
                                    </div>
                                </div>
                            <?php elseif ($isLookupPerformed): ?>
                                <div class="alert" style="background: #FFF1F2; border: 1px solid #FECDD3; color: #BE123C; border-radius: 4px; padding: 12px; font-family: 'Inter', sans-serif; font-size: 13px;">
                                    <strong>No driver found for "<?= esc($searchTerm) ?>".</strong><br>
                                    Register this driver first, then return here to log the visit.
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0" style="font-family: 'Inter', sans-serif; font-size: 13px;">Run a lookup to see the registration status and driver snapshot here.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- LOG VISIT FORM -->
                <div class="col-lg-7 mb-4">
                    <?php if ($isDriverFound): ?>
                        <div class="card ops-card">
                            <div class="card-header ops-toolbar">
                                <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                    <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Log Visit</h3>
                                    <span class="text-muted small" style="margin-top: 4px;">For Registered Driver #<?= esc((string) $driver['id']) ?></span>
                                </div>
                            </div>
                            <form action="<?= base_url('saveVisit') ?>" method="post">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="driver_id" value="<?= esc((string) $driver['id']) ?>">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="guest_count">Guest Count</label>
                                                <input type="number" min="0" name="guest_count" id="guest_count" class="form-control" value="<?= esc(old('guest_count')) ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="vehicle_number">Vehicle Number</label>
                                                <input type="text" name="vehicle_number" id="vehicle_number" class="form-control" value="<?= esc(old('vehicle_number', $driver['vehicle_number'] ?? '')) ?>" maxlength="30" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="vehicle_type">Vehicle Type</label>
                                                <?php $selectedVehicleType = old('vehicle_type', $driver['vehicle_type'] ?? ''); ?>
                                                <select name="vehicle_type" id="vehicle_type" class="form-control" required>
                                                    <option value="">Select vehicle type</option>
                                                    <?php foreach (($vehicleTypeOptions ?? []) as $vehicleTypeOption): ?>
                                                        <option value="<?= esc($vehicleTypeOption) ?>" <?= $selectedVehicleType === $vehicleTypeOption ? 'selected' : '' ?>><?= esc(ucwords($vehicleTypeOption)) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="cash_incentive_amount">Cash Incentive</label>
                                                <input type="number" step="0.01" min="0" name="cash_incentive_amount" id="cash_incentive_amount" class="form-control" value="<?= esc(old('cash_incentive_amount', number_format((float) ($driver['default_cash_incentive_amount'] ?? 200), 2, '.', ''))) ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="visited_at">Visit Time</label>
                                                <input type="datetime-local" name="visited_at" id="visited_at" class="form-control" value="<?= esc(old('visited_at', $visitDefaults['visited_at'] ?? '')) ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="food_offered">Food Issued</label>
                                                <select name="food_offered" id="food_offered" class="form-control" required>
                                                    <option value="" disabled selected>---Food Issued---</option>
                                                    <option value="1" <?= old('food_offered') == '1' ? 'selected' : '' ?>>Yes</option>
                                                    <option value="0" <?= old('food_offered') == '0' ? 'selected' : '' ?>>No</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mt-2">
                                        <label for="remarks">Remarks</label>
                                        <textarea name="remarks" id="remarks" class="form-control" rows="3" placeholder="Payment/Remarks"><?= esc(old('remarks')) ?></textarea>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-end align-items-center flex-wrap" style="gap:12px;">
                                        <a href="<?= site_url('visitEntryList') ?>" class="btn btn-outline-enterprise">Visit List</a>
                                        <button type="submit" class="btn btn-primary-enterprise">Save Visit Log</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php elseif ($isLookupPerformed): ?>
                        <div class="card ops-card">
                            <div class="card-header ops-toolbar">
                                <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                    <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Driver Not Registered</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <p style="font-family: 'Inter', sans-serif; font-size: 13px; color: #4F4255;">
                                    No registered driver matched <strong><?= esc($searchTerm) ?></strong>. Use the driver registration form to create the driver record before logging visits.
                                </p>
                                <div style="display: flex; gap: 12px; margin-top: 16px;">
                                    <a href="<?= base_url('drivers/create') ?>" class="btn btn-primary-enterprise">Register Driver</a>
                                    <a href="<?= base_url('visitEntry') ?>" class="btn btn-outline-enterprise">Search Again</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card ops-card">
                            <div class="card-body text-center" style="padding: 40px 20px;">
                                <i class="fas fa-search mb-3" style="font-size: 24px; color: #E0E0E0;"></i>
                                <p class="text-muted mb-0" style="font-family: 'Inter', sans-serif; font-size: 14px;">Search for a driver to start a visit entry.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
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
.badge-enterprise-role {
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    font-weight: 500;
    padding: 4px 8px;
    background: #1A1C1C;
    color: #FFFFFF;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    display: inline-block;
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
