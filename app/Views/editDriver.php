<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = 'Edit Driver';
$pageSubtitle = 'Update driver profile, vehicle assignment, incentives, and consent.';
$pageEyebrow = 'Fleet Management';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Drivers', 'url' => base_url('drivers')],
    ['label' => 'Edit Driver', 'active' => true],
];
$errors = session('errors') ?? [];
$duplicateWarnings = session('duplicate_warnings') ?? ($duplicateWarnings ?? []);
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

            <?php if ($duplicateWarnings): ?>
                <div class="alert alert-warning ops-alert mb-4 border-warning" style="background-color: #FFFBEB; color: #92400E; border-radius: 4px;">
                    <div style="font-family: 'Hanken Grotesk', sans-serif; font-weight: 600; font-size: 15px;"><i class="fas fa-exclamation-triangle mr-2"></i>Possible duplicate found:</div>
                    <ul class="mb-0 mt-2" style="font-family: 'Inter', sans-serif; font-size: 13px;">
                        <?php foreach ($duplicateWarnings as $warning): ?>
                            <li><?= esc($warning) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card ops-card mb-4">
                        <div class="card-header ops-toolbar">
                            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Driver Details</h3>
                                <div class="text-muted" style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase;">Update Profile & Documents</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('drivers/' . $driver['id']) ?>" method="POST" enctype="multipart/form-data">
                                <?= csrf_field(); ?>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="full_name">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?= isset($errors['full_name']) ? 'is-invalid border-danger' : '' ?>" id="full_name" name="full_name" value="<?= esc(old('full_name', $driver['driver_name'])) ?>" required>
                                            <?php if (isset($errors['full_name'])): ?>
                                                <div class="invalid-feedback text-danger small mt-1"><?= esc($errors['full_name']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="mobile_number">Mobile Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?= isset($errors['mobile_number']) ? 'is-invalid border-danger' : '' ?>" id="mobile_number" name="mobile_number" value="<?= esc(old('mobile_number', $driver['mobile_number'])) ?>" required>
                                            <?php if (isset($errors['mobile_number'])): ?>
                                                <div class="invalid-feedback text-danger small mt-1"><?= esc($errors['mobile_number']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="whatsapp_number">WhatsApp Number</label>
                                            <input type="text" class="form-control <?= isset($errors['whatsapp_number']) ? 'is-invalid border-danger' : '' ?>" id="whatsapp_number" name="whatsapp_number" value="<?= esc(old('whatsapp_number', $driver['whatsapp_number'])) ?>">
                                            <?php if (isset($errors['whatsapp_number'])): ?>
                                                <div class="invalid-feedback text-danger small mt-1"><?= esc($errors['whatsapp_number']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="license_number">License Number</label>
                                            <input type="text" class="form-control <?= isset($errors['license_number']) ? 'is-invalid border-danger' : '' ?>" id="license_number" name="license_number" value="<?= esc(old('license_number', $driver['license_number'])) ?>">
                                            <?php if (isset($errors['license_number'])): ?>
                                                <div class="invalid-feedback text-danger small mt-1"><?= esc($errors['license_number']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="whatsapp_opt_in">WhatsApp Consent</label>
                                            <select class="form-control <?= isset($errors['whatsapp_opt_in']) ? 'is-invalid border-danger' : '' ?>" id="whatsapp_opt_in" name="whatsapp_opt_in">
                                                <option value="1" <?= old('whatsapp_opt_in', (string) ($driver['whatsapp_opt_in'] ?? '1')) === '1' ? 'selected' : '' ?>>Opted In</option>
                                                <option value="0" <?= old('whatsapp_opt_in', (string) ($driver['whatsapp_opt_in'] ?? '1')) === '0' ? 'selected' : '' ?>>Opted Out</option>
                                            </select>
                                            <?php if (isset($errors['whatsapp_opt_in'])): ?>
                                                <div class="invalid-feedback text-danger small mt-1"><?= esc($errors['whatsapp_opt_in']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="whatsapp_opt_out_reason">Opt-out Reason</label>
                                            <input type="text" class="form-control <?= isset($errors['whatsapp_opt_out_reason']) ? 'is-invalid border-danger' : '' ?>" id="whatsapp_opt_out_reason" name="whatsapp_opt_out_reason" value="<?= esc(old('whatsapp_opt_out_reason', $driver['whatsapp_opt_out_reason'] ?? '')) ?>" placeholder="Optional">
                                            <?php if (isset($errors['whatsapp_opt_out_reason'])): ?>
                                                <div class="invalid-feedback text-danger small mt-1"><?= esc($errors['whatsapp_opt_out_reason']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="city">City</label>
                                            <input type="text" class="form-control <?= isset($errors['city']) ? 'is-invalid border-danger' : '' ?>" id="city" name="city" value="<?= esc(old('city', $driver['city'])) ?>">
                                            <?php if (isset($errors['city'])): ?>
                                                <div class="invalid-feedback text-danger small mt-1"><?= esc($errors['city']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="state">State</label>
                                            <input type="text" class="form-control <?= isset($errors['state']) ? 'is-invalid border-danger' : '' ?>" id="state" name="state" value="<?= esc(old('state', $driver['state'])) ?>">
                                            <?php if (isset($errors['state'])): ?>
                                                <div class="invalid-feedback text-danger small mt-1"><?= esc($errors['state']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="default_cash_incentive_amount">Default Cash Incentive (₹) <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0" class="form-control <?= isset($errors['default_cash_incentive_amount']) ? 'is-invalid border-danger' : '' ?>" id="default_cash_incentive_amount" name="default_cash_incentive_amount" value="<?= esc(old('default_cash_incentive_amount', $driver['default_cash_incentive_amount'] ?? '200.00')) ?>" required>
                                            <?php if (isset($errors['default_cash_incentive_amount'])): ?>
                                                <div class="invalid-feedback text-danger small mt-1"><?= esc($errors['default_cash_incentive_amount']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="status">Driver Status <span class="text-danger">*</span></label>
                                            <select class="form-control <?= isset($errors['status']) ? 'is-invalid border-danger' : '' ?>" id="status" name="status" required>
                                                <?php foreach (($statusOptions ?? []) as $statusOption): ?>
                                                    <option value="<?= esc($statusOption) ?>" <?= old('status', $driver['status']) === $statusOption ? 'selected' : '' ?>>
                                                        <?= esc(ucfirst($statusOption)) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (isset($errors['status'])): ?>
                                                <div class="invalid-feedback text-danger small mt-1"><?= esc($errors['status']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="verified_at">Verification</label>
                                            <select class="form-control" id="verified_at" name="verified_at" style="max-width: 50%;">
                                                <option value="">Not Verified</option>
                                                <option value="1" <?= old('verified_at', $driver['verified_at']) ? 'selected' : '' ?>>Verified</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="address">Address</label>
                                            <textarea class="form-control <?= isset($errors['address']) ? 'is-invalid border-danger' : '' ?>" id="address" name="address" rows="2" placeholder="Full residential address..."><?= esc(old('address', $driver['address'])) ?></textarea>
                                            <?php if (isset($errors['address'])): ?>
                                                <div class="invalid-feedback text-danger small mt-1"><?= esc($errors['address']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-4">
                                            <label for="notes">Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Any internal notes or references..."><?= esc(old('notes', $driver['notes'])) ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row border-top pt-4">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="photo_path">Driver Photo</label>
                                            <div class="custom-file mb-2">
                                                <input type="file" class="custom-file-input" id="photo_path" name="photo_path" accept="image/*" capture="environment">
                                                <label class="custom-file-label" for="photo_path" style="border: 1px solid #E0E0E0; border-radius: 4px; font-family: 'Inter', sans-serif; font-size: 13px;">Choose photo</label>
                                            </div>
                                            <div class="camera-capture p-3 border rounded bg-light" data-camera-capture data-input="photo_path">
                                                <div class="camera-capture__actions d-flex gap-2 flex-wrap mb-2">
                                                    <button type="button" class="btn btn-outline-enterprise py-1 px-2 text-sm" data-camera-start><i class="fas fa-camera mr-1"></i> Capture Photo</button>
                                                    <button type="button" class="btn btn-primary-enterprise py-1 px-2 text-sm" data-camera-snap style="display:none;">Use This Photo</button>
                                                    <button type="button" class="btn btn-outline-secondary py-1 px-2 text-sm" data-camera-stop style="display:none;">Cancel</button>
                                                </div>
                                                <video class="camera-capture__media w-100 rounded" data-camera-video playsinline></video>
                                                <canvas data-camera-canvas hidden></canvas>
                                                <img class="camera-capture__preview img-fluid mt-2 rounded border" data-camera-preview alt="Captured driver photo preview">
                                                <small class="form-text text-muted" data-camera-status></small>
                                            </div>
                                            <?php if (!empty($driver['photo_path'])): ?>
                                                <div class="mt-3 p-2 border rounded text-center">
                                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #4F4255; text-transform: uppercase; margin-bottom: 8px;">Current Photo</div>
                                                    <?php if ($driverPhotoUrl): ?>
                                                        <a href="<?= esc($driverPhotoUrl) ?>" target="_blank" rel="noopener">
                                                            <img src="<?= esc($driverPhotoUrl) ?>" alt="Current driver photo" class="img-fluid rounded border" style="max-height: 120px; border-color: #E0E0E0 !important;">
                                                        </a>
                                                    <?php else: ?>
                                                        <div class="small text-muted text-truncate"><?= esc($driver['photo_path']) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="license_photo_path">License Document / Photo</label>
                                            <div class="custom-file mb-2">
                                                <input type="file" class="custom-file-input" id="license_photo_path" name="license_photo_path" accept="image/*" capture="environment">
                                                <label class="custom-file-label" for="license_photo_path" style="border: 1px solid #E0E0E0; border-radius: 4px; font-family: 'Inter', sans-serif; font-size: 13px;">Choose license document</label>
                                            </div>
                                            <div class="camera-capture p-3 border rounded bg-light" data-camera-capture data-input="license_photo_path">
                                                <div class="camera-capture__actions d-flex gap-2 flex-wrap mb-2">
                                                    <button type="button" class="btn btn-outline-enterprise py-1 px-2 text-sm" data-camera-start><i class="fas fa-camera mr-1"></i> Capture License</button>
                                                    <button type="button" class="btn btn-primary-enterprise py-1 px-2 text-sm" data-camera-snap style="display:none;">Use This Photo</button>
                                                    <button type="button" class="btn btn-outline-secondary py-1 px-2 text-sm" data-camera-stop style="display:none;">Cancel</button>
                                                </div>
                                                <video class="camera-capture__media w-100 rounded" data-camera-video playsinline></video>
                                                <canvas data-camera-canvas hidden></canvas>
                                                <img class="camera-capture__preview img-fluid mt-2 rounded border" data-camera-preview alt="Captured license preview">
                                                <small class="form-text text-muted" data-camera-status></small>
                                            </div>
                                            <?php if (!empty($driver['license_photo_path'])): ?>
                                                <div class="mt-3 p-2 border rounded text-center">
                                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #4F4255; text-transform: uppercase; margin-bottom: 8px;">Current License</div>
                                                    <?php if ($licensePhotoUrl): ?>
                                                        <a href="<?= esc($licensePhotoUrl) ?>" target="_blank" rel="noopener">
                                                            <img src="<?= esc($licensePhotoUrl) ?>" alt="Current license photo" class="img-fluid rounded border" style="max-height: 120px; border-color: #E0E0E0 !important;">
                                                        </a>
                                                    <?php else: ?>
                                                        <div class="small text-muted text-truncate"><?= esc($driver['license_photo_path']) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer" style="background: #F8F9FA; border-top: 1px solid #E0E0E0; display: flex; gap: 10px;">
                                <button type="submit" class="btn btn-primary-enterprise"><i class="fas fa-save mr-1"></i> Update Driver</button>
                                <a href="<?= base_url('drivers') ?>" class="btn btn-outline-enterprise">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card ops-card mb-4">
                        <div class="card-header ops-toolbar">
                            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Assign Primary Vehicle</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('drivers/' . $driver['id'] . '/vehicles') ?>" method="POST">
                                <?= csrf_field(); ?>
                                <div class="form-group mb-3">
                                    <label for="vehicle_number">Vehicle Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= isset($errors['vehicle_number']) ? 'is-invalid border-danger' : '' ?>" id="vehicle_number" name="vehicle_number" value="<?= esc(old('vehicle_number')) ?>" required placeholder="e.g. DL 1C AA 1111">
                                    <?php if (isset($errors['vehicle_number'])): ?>
                                        <div class="invalid-feedback text-danger small mt-1"><?= esc($errors['vehicle_number']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="vehicle_type">Vehicle Category <span class="text-danger">*</span></label>
                                    <select class="form-control <?= isset($errors['vehicle_type']) ? 'is-invalid border-danger' : '' ?>" id="vehicle_type" name="vehicle_type" required>
                                        <option value="">-- Select Category --</option>
                                        <?php foreach (($vehicleTypeOptions ?? []) as $vehicleTypeOption): ?>
                                            <option value="<?= esc($vehicleTypeOption) ?>" <?= old('vehicle_type') === $vehicleTypeOption ? 'selected' : '' ?>>
                                                <?= esc(ucwords($vehicleTypeOption)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['vehicle_type'])): ?>
                                        <div class="invalid-feedback text-danger small mt-1"><?= esc($errors['vehicle_type']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <button type="submit" class="btn btn-outline-enterprise w-100"><i class="fas fa-car mr-1"></i> Save Vehicle</button>
                            </form>
                        </div>
                    </div>

                    <div class="card ops-card">
                        <div class="card-header ops-toolbar">
                            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Vehicle History</h3>
                            </div>
                        </div>
                        <div class="card-body ops-table-wrap p-0">
                            <div class="table-responsive">
                                <table class="table table-modern mb-0">
                                    <thead>
                                        <tr>
                                            <th>Vehicle</th>
                                            <th>Timeline</th>
                                            <th class="text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($vehicleHistory)): ?>
                                            <?php foreach ($vehicleHistory as $vehicle): ?>
                                                <tr>
                                                    <td data-label="Vehicle">
                                                        <div style="font-weight: 600; color: #1A1C1C; font-size: 13px;">
                                                            <?= esc($vehicle['vehicle_number']) ?>
                                                        </div>
                                                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #4F4255;">
                                                            <?= esc(ucwords($vehicle['vehicle_type'])) ?>
                                                        </div>
                                                    </td>
                                                    <td data-label="Timeline">
                                                        <div style="font-family: 'Inter', sans-serif; font-size: 12px; color: #4F4255;">
                                                            <?= esc(date('M d, Y', strtotime($vehicle['assigned_from'] ?? ''))) ?>
                                                            <br>
                                                            <small class="text-muted">to <?= esc(isset($vehicle['assigned_until']) ? date('M d, Y', strtotime($vehicle['assigned_until'])) : 'Present') ?></small>
                                                        </div>
                                                    </td>
                                                    <td data-label="Status" class="text-right">
                                                        <?php $vStatus = strtolower($vehicle['status'] ?? ''); ?>
                                                        <?php if ($vStatus === 'active'): ?>
                                                            <span class="badge-enterprise-role" style="background: #10B981; color: #FFFFFF;">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge-enterprise-role" style="background: #E0E0E0; color: #1A1C1C;"><?= esc(ucfirst($vStatus)) ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4" style="font-size: 13px; font-family: 'Inter', sans-serif;">
                                                    No vehicle history available.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
    display: block;
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
.form-control.is-invalid {
    border-color: #F43F5E !important;
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

/* =========================================
   TABLE MODERN
========================================= */
.table-modern {
    width: 100%;
    border-collapse: collapse;
}
.table-modern thead th {
    background: #F8F9FA;
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    font-weight: 600;
    color: #4F4255;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 16px 20px;
    border-bottom: 2px solid #E0E0E0;
    border-top: none;
    white-space: nowrap;
}
.table-modern tbody td {
    padding: 16px 20px;
    vertical-align: middle;
    border-bottom: 1px solid #EEEEEE;
}
.table-modern tbody tr:last-child td {
    border-bottom: none;
}
.table-modern tbody tr:hover {
    background-color: #F8F9FA;
}

@media (max-width: 768px) {
    .table-modern thead { display: none; }
    .table-modern tbody td {
        display: block;
        text-align: right !important;
        padding: 10px 15px;
        position: relative;
    }
    .table-modern tbody td::before {
        content: attr(data-label);
        float: left;
        font-family: 'JetBrains Mono', monospace;
        font-size: 11px;
        font-weight: 600;
        color: #4F4255;
        text-transform: uppercase;
    }
    .table-modern tbody tr {
        border-bottom: 2px solid #E0E0E0;
        display: block;
        margin-bottom: 10px;
    }
}
</style>

<?php include 'app/Views/partials/camera_capture.php'; ?>
<?php include 'app/Views/templates/footer.php'; ?>
