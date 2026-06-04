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
                <div class="alert alert-warning ops-alert">
                    <strong>Possible duplicate found:</strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach ($duplicateWarnings as $warning): ?>
                            <li><?= esc($warning) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card ops-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Edit Driver</h4>
                        </div>

                        <div class="card-body">
                            <form action="<?= base_url('drivers/' . $driver['id']) ?>" method="POST" enctype="multipart/form-data">
                                <?= csrf_field(); ?>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="full_name">Full Name:</label>
                                            <input type="text" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" id="full_name" name="full_name" value="<?= old('full_name', $driver['driver_name']) ?>" required>
                                            <?php if (isset($errors['full_name'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['full_name']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mobile_number">Mobile Number:</label>
                                            <input type="text" class="form-control <?= isset($errors['mobile_number']) ? 'is-invalid' : '' ?>" id="mobile_number" name="mobile_number" value="<?= old('mobile_number', $driver['mobile_number']) ?>" required>
                                            <?php if (isset($errors['mobile_number'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['mobile_number']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="whatsapp_number">WhatsApp Number:</label>
                                            <input type="text" class="form-control <?= isset($errors['whatsapp_number']) ? 'is-invalid' : '' ?>" id="whatsapp_number" name="whatsapp_number" value="<?= old('whatsapp_number', $driver['whatsapp_number']) ?>">
                                            <?php if (isset($errors['whatsapp_number'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['whatsapp_number']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="license_number">License Number:</label>
                                            <input type="text" class="form-control <?= isset($errors['license_number']) ? 'is-invalid' : '' ?>" id="license_number" name="license_number" value="<?= old('license_number', $driver['license_number']) ?>">
                                            <?php if (isset($errors['license_number'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['license_number']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="whatsapp_opt_in">WhatsApp Consent:</label>
                                            <select class="form-control <?= isset($errors['whatsapp_opt_in']) ? 'is-invalid' : '' ?>" id="whatsapp_opt_in" name="whatsapp_opt_in">
                                                <option value="1" <?= old('whatsapp_opt_in', (string) ($driver['whatsapp_opt_in'] ?? '1')) === '1' ? 'selected' : '' ?>>Opted In</option>
                                                <option value="0" <?= old('whatsapp_opt_in', (string) ($driver['whatsapp_opt_in'] ?? '1')) === '0' ? 'selected' : '' ?>>Opted Out</option>
                                            </select>
                                            <?php if (isset($errors['whatsapp_opt_in'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['whatsapp_opt_in']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="whatsapp_opt_out_reason">Opt-out Reason:</label>
                                            <input type="text" class="form-control <?= isset($errors['whatsapp_opt_out_reason']) ? 'is-invalid' : '' ?>" id="whatsapp_opt_out_reason" name="whatsapp_opt_out_reason" value="<?= old('whatsapp_opt_out_reason', $driver['whatsapp_opt_out_reason'] ?? '') ?>">
                                            <?php if (isset($errors['whatsapp_opt_out_reason'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['whatsapp_opt_out_reason']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="city">City:</label>
                                            <input type="text" class="form-control <?= isset($errors['city']) ? 'is-invalid' : '' ?>" id="city" name="city" value="<?= old('city', $driver['city']) ?>">
                                            <?php if (isset($errors['city'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['city']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="state">State:</label>
                                            <input type="text" class="form-control <?= isset($errors['state']) ? 'is-invalid' : '' ?>" id="state" name="state" value="<?= old('state', $driver['state']) ?>">
                                            <?php if (isset($errors['state'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['state']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="default_cash_incentive_amount">Default Cash Incentive:</label>
                                            <input type="number" step="0.01" min="0" class="form-control <?= isset($errors['default_cash_incentive_amount']) ? 'is-invalid' : '' ?>" id="default_cash_incentive_amount" name="default_cash_incentive_amount" value="<?= old('default_cash_incentive_amount', $driver['default_cash_incentive_amount'] ?? '200.00') ?>" required>
                                            <?php if (isset($errors['default_cash_incentive_amount'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['default_cash_incentive_amount']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Driver Status:</label>
                                            <select class="form-control <?= isset($errors['status']) ? 'is-invalid' : '' ?>" id="status" name="status" required>
                                                <?php foreach (($statusOptions ?? []) as $statusOption): ?>
                                                    <option value="<?= esc($statusOption) ?>" <?= old('status', $driver['status']) === $statusOption ? 'selected' : '' ?>>
                                                        <?= esc(ucfirst($statusOption)) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (isset($errors['status'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['status']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="verified_at">Verification:</label>
                                            <select class="form-control" id="verified_at" name="verified_at">
                                                <option value="">Not Verified</option>
                                                <option value="1" <?= old('verified_at', $driver['verified_at']) ? 'selected' : '' ?>>Verified</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="address">Address:</label>
                                            <textarea class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>" id="address" name="address" rows="3"><?= old('address', $driver['address']) ?></textarea>
                                            <?php if (isset($errors['address'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['address']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="notes">Notes:</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="3"><?= old('notes', $driver['notes']) ?></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="photo_path">Driver Photo:</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="photo_path" name="photo_path" accept="image/*" capture="environment">
                                                <label class="custom-file-label" for="photo_path">Choose driver photo</label>
                                            </div>
                                            <div class="camera-capture" data-camera-capture data-input="photo_path">
                                                <div class="camera-capture__actions">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" data-camera-start>Capture Photo</button>
                                                    <button type="button" class="btn btn-primary btn-sm" data-camera-snap style="display:none;">Use This Photo</button>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-camera-stop style="display:none;">Cancel</button>
                                                </div>
                                                <video class="camera-capture__media" data-camera-video playsinline></video>
                                                <canvas data-camera-canvas hidden></canvas>
                                                <img class="camera-capture__preview" data-camera-preview alt="Captured driver photo preview">
                                                <small class="form-text text-muted" data-camera-status></small>
                                            </div>
                                            <?php if (!empty($driver['photo_path'])): ?>
                                                <small class="form-text text-muted">Current: <?= esc($driver['photo_path']) ?></small>
                                                <?php if ($driverPhotoUrl): ?>
                                                    <a href="<?= esc($driverPhotoUrl) ?>" target="_blank" rel="noopener" class="d-inline-block mt-2">
                                                        <img src="<?= esc($driverPhotoUrl) ?>" alt="Current driver photo" class="img-fluid rounded border" style="max-height: 180px;">
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="license_photo_path">License Document / Photo:</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="license_photo_path" name="license_photo_path" accept="image/*" capture="environment">
                                                <label class="custom-file-label" for="license_photo_path">Choose license document</label>
                                            </div>
                                            <div class="camera-capture" data-camera-capture data-input="license_photo_path">
                                                <div class="camera-capture__actions">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" data-camera-start>Capture License</button>
                                                    <button type="button" class="btn btn-primary btn-sm" data-camera-snap style="display:none;">Use This Photo</button>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-camera-stop style="display:none;">Cancel</button>
                                                </div>
                                                <video class="camera-capture__media" data-camera-video playsinline></video>
                                                <canvas data-camera-canvas hidden></canvas>
                                                <img class="camera-capture__preview" data-camera-preview alt="Captured license preview">
                                                <small class="form-text text-muted" data-camera-status></small>
                                            </div>
                                            <?php if (!empty($driver['license_photo_path'])): ?>
                                                <small class="form-text text-muted">Current: <?= esc($driver['license_photo_path']) ?></small>
                                                <?php if ($licensePhotoUrl): ?>
                                                    <a href="<?= esc($licensePhotoUrl) ?>" target="_blank" rel="noopener" class="d-inline-block mt-2">
                                                        <img src="<?= esc($licensePhotoUrl) ?>" alt="Current license photo" class="img-fluid rounded border" style="max-height: 180px;">
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Update Driver</button>
                                <a href="<?= base_url('drivers') ?>" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title">Assign / Change Primary Vehicle</h4>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('drivers/' . $driver['id'] . '/vehicles') ?>" method="POST">
                                <?= csrf_field(); ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="vehicle_number">Vehicle Number</label>
                                            <input type="text" class="form-control <?= isset($errors['vehicle_number']) ? 'is-invalid' : '' ?>" id="vehicle_number" name="vehicle_number" value="<?= old('vehicle_number') ?>" required>
                                            <?php if (isset($errors['vehicle_number'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['vehicle_number']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="vehicle_type">Vehicle Category</label>
                                            <select class="form-control <?= isset($errors['vehicle_type']) ? 'is-invalid' : '' ?>" id="vehicle_type" name="vehicle_type" required>
                                                <option value="">-- Select Vehicle Category --</option>
                                                <?php foreach (($vehicleTypeOptions ?? []) as $vehicleTypeOption): ?>
                                                    <option value="<?= esc($vehicleTypeOption) ?>" <?= old('vehicle_type') === $vehicleTypeOption ? 'selected' : '' ?>>
                                                        <?= esc(ucwords($vehicleTypeOption)) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (isset($errors['vehicle_type'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['vehicle_type']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                </div>

                                <button type="submit" class="btn btn-info">Save Vehicle</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h4 class="card-title">Vehicle History</h4>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Vehicle Number</th>
                                        <th>Category</th>
                                        <th>Assigned From</th>
                                        <th>Assigned Until</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (($vehicleHistory ?? []) as $vehicle): ?>
                                        <tr>
                                            <td><?= esc($vehicle['vehicle_number']) ?></td>
                                            <td><?= esc(ucwords($vehicle['vehicle_type'])) ?></td>
                                            <td><?= esc($vehicle['assigned_from'] ?? '') ?></td>
                                            <td><?= esc($vehicle['assigned_until'] ?? 'Current') ?></td>
                                            <td><?= esc(ucfirst($vehicle['status'] ?? '')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'app/Views/partials/camera_capture.php'; ?>
<?php include 'app/Views/templates/footer.php'; ?>
