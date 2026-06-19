<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = 'Add Driver';
// $pageSubtitle = 'Register a new highway driver with vehicle details and default cash incentive.';
// $pageEyebrow = 'Fleet Management';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Drivers', 'url' => base_url('drivers')],
    ['label' => 'Add Driver', 'active' => true],
];
$errors = session('errors') ?? [];
$duplicateWarnings = session('duplicate_warnings') ?? ($duplicateWarnings ?? []);
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
                <div class="col-lg-12">
                    <div class="card ops-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-plus mr-2"></i>
                                Driver Registration Form
                            </h3>
                        </div>

                        <form action="<?= site_url('drivers') ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field(); ?>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="full_name">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" name="full_name" id="full_name" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" value="<?= old('full_name') ?>" required>
                                            <?php if (isset($errors['full_name'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['full_name']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mobile_number">Mobile Number <span class="text-danger">*</span></label>
                                            <input type="text" name="mobile_number" id="mobile_number" class="form-control <?= isset($errors['mobile_number']) ? 'is-invalid' : '' ?>" maxlength="20" value="<?= old('mobile_number') ?>" required>
                                            <?php if (isset($errors['mobile_number'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['mobile_number']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="whatsapp_number">WhatsApp Number</label>
                                            <input type="text" name="whatsapp_number" id="whatsapp_number" class="form-control <?= isset($errors['whatsapp_number']) ? 'is-invalid' : '' ?>" maxlength="20" value="<?= old('whatsapp_number') ?>">
                                            <?php if (isset($errors['whatsapp_number'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['whatsapp_number']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="license_number">License Number</label>
                                            <input type="text" name="license_number" id="license_number" class="form-control <?= isset($errors['license_number']) ? 'is-invalid' : '' ?>" value="<?= old('license_number') ?>">
                                            <?php if (isset($errors['license_number'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['license_number']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="whatsapp_opt_in">WhatsApp Consent</label>
                                            <select name="whatsapp_opt_in" id="whatsapp_opt_in" class="form-control <?= isset($errors['whatsapp_opt_in']) ? 'is-invalid' : '' ?>">
                                                <option value="1" <?= old('whatsapp_opt_in', '1') === '1' ? 'selected' : '' ?>>Opted In</option>
                                                <option value="0" <?= old('whatsapp_opt_in') === '0' ? 'selected' : '' ?>>Opted Out</option>
                                            </select>
                                            <?php if (isset($errors['whatsapp_opt_in'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['whatsapp_opt_in']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="whatsapp_opt_out_reason">Opt-out Reason</label>
                                            <input type="text" name="whatsapp_opt_out_reason" id="whatsapp_opt_out_reason" class="form-control <?= isset($errors['whatsapp_opt_out_reason']) ? 'is-invalid' : '' ?>" value="<?= old('whatsapp_opt_out_reason') ?>" placeholder="Optional reason when consent is removed">
                                            <?php if (isset($errors['whatsapp_opt_out_reason'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['whatsapp_opt_out_reason']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="ops-section-title field-group">Primary Vehicle</div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="vehicle_number">Vehicle Number <span class="text-danger">*</span></label>
                                            <input type="text" name="vehicle_number" id="vehicle_number" class="form-control <?= isset($errors['vehicle_number']) ? 'is-invalid' : '' ?>" value="<?= old('vehicle_number') ?>" required>
                                            <?php if (isset($errors['vehicle_number'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['vehicle_number']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="vehicle_type">Vehicle Category <span class="text-danger">*</span></label>
                                            <select name="vehicle_type" id="vehicle_type" class="form-control <?= isset($errors['vehicle_type']) ? 'is-invalid' : '' ?>" required>
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


                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="city">City</label>
                                            <input type="text" name="city" id="city" class="form-control <?= isset($errors['city']) ? 'is-invalid' : '' ?>" value="<?= old('city') ?>">
                                            <?php if (isset($errors['city'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['city']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="state">State</label>
                                            <input type="text" name="state" id="state" class="form-control <?= isset($errors['state']) ? 'is-invalid' : '' ?>" value="<?= old('state') ?>">
                                            <?php if (isset($errors['state'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['state']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="default_cash_incentive_amount">Default Cash Incentive <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0" name="default_cash_incentive_amount" id="default_cash_incentive_amount" class="form-control <?= isset($errors['default_cash_incentive_amount']) ? 'is-invalid' : '' ?>" value="<?= old('default_cash_incentive_amount', '200.00') ?>" required>
                                            <?php if (isset($errors['default_cash_incentive_amount'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['default_cash_incentive_amount']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Driver Status <span class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-control <?= isset($errors['status']) ? 'is-invalid' : '' ?>" required>
                                                <?php foreach (($statusOptions ?? []) as $statusOption): ?>
                                                    <option value="<?= esc($statusOption) ?>" <?= old('status', 'active') === $statusOption ? 'selected' : '' ?>>
                                                        <?= esc(ucfirst($statusOption)) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (isset($errors['status'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['status']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="verified_at">Verification</label>
                                            <select name="verified_at" id="verified_at" class="form-control">
                                                <option value="">Not Verified</option>
                                                <option value="1" <?= old('verified_at') ? 'selected' : '' ?>>Verified</option>
                                            </select>
                                        </div>
                                    </div> -->

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea name="address" id="address" rows="3" class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>"><?= old('address') ?></textarea>
                                            <?php if (isset($errors['address'])): ?>
                                                <div class="invalid-feedback"><?= esc($errors['address']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="notes">Notes</label>
                                            <textarea name="notes" id="notes" rows="3" class="form-control"><?= old('notes') ?></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="photo_path">Driver Photo</label>
                                            <div class="custom-file">
                                                <input type="file" name="photo_path" id="photo_path" class="custom-file-input <?= isset($errors['photo_path']) ? 'is-invalid' : '' ?>" accept="image/*" capture="environment">
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
                                            <?php if (isset($errors['photo_path'])): ?>
                                                <div class="text-danger small mt-1"><?= esc($errors['photo_path']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="license_photo_path">License Document / Photo</label>
                                            <div class="custom-file">
                                                <input type="file" name="license_photo_path" id="license_photo_path" class="custom-file-input <?= isset($errors['license_photo_path']) ? 'is-invalid' : '' ?>" accept="image/*" capture="environment">
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
                                            <?php if (isset($errors['license_photo_path'])): ?>
                                                <div class="text-danger small mt-1"><?= esc($errors['license_photo_path']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer border-0">
                                <div class="floating-actions d-flex justify-content-end flex-wrap" style="gap:0.5rem;">
                                    <a href="<?= base_url('drivers') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Back</a>
                                    <button type="reset" class="btn btn-warning"><i class="fas fa-undo mr-1"></i> Reset</button>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save Driver</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'app/Views/partials/camera_capture.php'; ?>
<?php include 'app/Views/templates/footer.php'; ?>
