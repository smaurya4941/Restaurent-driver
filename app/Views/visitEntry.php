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

            <div class="card ops-card mb-4 visit-search-card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Driver lookup</h3>
                    <span class="text-muted small">Handled by <?= esc($currentUserName) ?></span>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('visitEntry') ?>" method="post">
                        <?= csrf_field(); ?>
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control" name="search_term" id="search_term" value="<?= esc($searchTerm) ?>" placeholder="Mobile, vehicle, license, or ID" required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary px-4">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-5 mb-4">
                    <div class="card ops-card h-100">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:0.75rem;">
                                <div>
                                    <h3 class="card-title mb-0">Verification Result</h3>
                                </div>
                                <?php if ($isLookupPerformed && $isDriverFound): ?>
                                    <?php $driverStatus = (string) ($driver['status'] ?? 'active'); ?>
                                    <span class="status-pill <?= in_array($driverStatus, ['blocked', 'blacklisted'], true) ? 'status-blocked' : 'status-found' ?>">
                                        <i class="fas <?= in_array($driverStatus, ['blocked', 'blacklisted'], true) ? 'fa-exclamation-triangle' : 'fa-check-circle' ?>"></i>
                                        <?= esc(ucfirst($driverStatus)) ?>
                                    </span>
                                <?php elseif ($isLookupPerformed): ?>
                                    <span class="status-pill status-missing">
                                        <i class="fas fa-user-plus"></i>
                                        Not Registered
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($isDriverFound): ?>
                                <div class="snapshot-grid">
                                    <div class="snapshot-item">
                                        <span class="label">Driver Name</span>
                                        <span class="value"><?= esc($driver['driver_name']) ?></span>
                                    </div>
                                    <!-- <div class="snapshot-item">
                                        <span class="label">Driver ID</span>
                                        <span class="value">#<?= esc((string) $driver['id']) ?></span>
                                    </div> -->
                                    <div class="snapshot-item">
                                        <span class="label">Mobile</span>
                                        <span class="value"><?= esc($driver['mobile_number']) ?></span>
                                    </div>
                                    <!-- <div class="snapshot-item">
                                        <span class="label">WhatsApp</span>
                                        <span class="value"><?= esc($driver['whatsapp_number'] ?? 'Not captured') ?></span>
                                    </div> -->
                                    <div class="snapshot-item">
                                        <span class="label">License</span>
                                        <span class="value"><?= esc($driver['license_number'] ?? 'Not captured') ?></span>
                                    </div>
                                    <!-- <div class="snapshot-item">
                                        <span class="label">Vehicle</span>
                                        <span class="value"><?= esc($driver['vehicle_number'] ?? 'No primary vehicle') ?></span>
                                    </div>
                                    <div class="snapshot-item">
                                        <span class="label">Vehicle Type</span>
                                        <span class="value"><?= esc($driver['vehicle_type'] ? ucwords($driver['vehicle_type']) : 'Not assigned') ?></span>
                                    </div> -->
                                    <div class="snapshot-item">
                                        <span class="label">Latest Visit</span>
                                        <span class="value"><?= !empty($driver['recent_visit_summary']['latest_visit_at']) ? esc($driver['recent_visit_summary']['latest_visit_at']) : 'No previous visit' ?></span>
                                    </div>
                                    <div class="snapshot-item">
                                        <span class="label">Today Visits</span>
                                        <span class="value"><?= esc((string) ($driver['recent_visit_summary']['today_count'] ?? 0)) ?></span>
                                    </div>
                                </div>
                            <?php elseif ($isLookupPerformed): ?>
                                <div class="desk-note">
                                    <strong>No driver found for "<?= esc($searchTerm) ?>".</strong><br>
                                    Register this driver first, then return here to log the visit.
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">Run a lookup to see the registration status and driver snapshot here.</p>
                            <?php endif; ?>

                            <!-- <div class="field-group">
                                <div class="ops-section-title">Operational Guidance</div>
                                <ul class="mb-0 pl-3 text-muted">
                                    <li>Existing driver found: record today’s visit from the visit form.</li>
                                    <li>Not found: use the registration button to open the full driver form.</li>
                                    <li>Blocked or blacklisted drivers will be shown clearly before you save a visit.</li>
                                </ul>
                            </div> -->
                        </div>
                    </div>
                </div>

                <div class="col-lg-7 mb-4">
                    <?php if ($isDriverFound): ?>
                        <div class="card ops-card">
                            <div class="card-header bg-white">
                                <h3 class="card-title mb-0">Log Visit For Registered Driver</h3>
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
                                                <small class="text-muted"> edit if today's vehicle is different.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="vehicle_type">Vehicle Type</label>
                                                <select name="vehicle_type" id="vehicle_type" class="form-control" required>
                                                    <option value="">Select vehicle type</option>
                                                    <?php foreach (($vehicleTypeOptions ?? []) as $vehicleTypeOption): ?>
                                                        <?php $selectedVehicleType = old('vehicle_type', $driver['vehicle_type'] ?? ''); ?>
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
                                        <!-- <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="verification_method">Verification Method</label>
                                                <select name="verification_method" id="verification_method" class="form-control" required>
                                                    <?php foreach (($verificationMethods ?? []) as $method): ?>
                                                        <option value="<?= esc($method) ?>" <?= old('verification_method') === $method ? 'selected' : ($method === 'phone' ? 'selected' : '') ?>><?= esc(strtoupper($method)) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div> -->
                                        <!-- <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="verification_reference">Reference</label>
                                                <input type="text" name="verification_reference" id="verification_reference" class="form-control" value="<?= esc(old('verification_reference')) ?>" placeholder="OTP / QR / manual note">
                                            </div>
                                         </div> -->

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
                                                <option value="" disabled selected>
                                                                            ---Food Issued---
                                                </option>
                                                <option value="1"
                <?= old('food_offered') == '1' ? 'selected' : '' ?>>
                Yes
            </option>

            <option value="0"
                <?= old('food_offered') == '0' ? 'selected' : '' ?>>
                No
            </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        
                                        <div class="col-md-4 ">
                                            <div class="form-group">
                                                <!-- <label>Handled By</label> -->
                                                <input hidden type="text" class="form-control" value="<?= esc($currentUserName) ?>" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="remarks">Remarks</label>
                                        <textarea name="remarks" id="remarks" class="form-control" rows="3" placeholder="Payment/Remarks"><?= esc(old('remarks')) ?></textarea>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-0">
                                    <div class="floating-actions d-flex justify-content-between align-items-center flex-wrap" style="gap:0.75rem;">
                                        <div class="text-muted small">Visit will be saved against registered driver #<?= esc((string) $driver['id']) ?>.</div>
                                        <div>
                                            <a href="<?= site_url('visitEntryList') ?>" class="btn btn-outline-secondary">Visit List</a>
                                            <button type="submit" class="btn btn-success ml-2">Save Visit Log</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php elseif ($isLookupPerformed): ?>
                        <div class="card ops-card">
                            <div class="card-header bg-white">
                                <h3 class="card-title mb-0">Driver Not Registered</h3>
                            </div>
                            <div class="card-body">
                                <div class="desk-note mb-3">
                                    No registered driver matched <strong><?= esc($searchTerm) ?></strong>. Use the driver registration form to create the driver record before logging visits.
                                </div>
                                <a href="<?= base_url('drivers/create') ?>" class="btn btn-primary">Register Driver</a>
                                <a href="<?= base_url('visitEntry') ?>" class="btn btn-outline-secondary ml-2">Search Again</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card ops-card">
                            <div class="card-body text-muted">
                                Search for a driver to start a visit entry.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'app/Views/templates/footer.php'; ?>
