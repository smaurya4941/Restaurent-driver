<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = lang('App.add_driver');
// $pageSubtitle = 'Register a new highway driver with vehicle details and default cash incentive.';
// $pageEyebrow = 'Fleet Management';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => lang('App.drivers'), 'url' => base_url('drivers')],
    ['label' => lang('App.add_driver'), 'active' => true],
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
                    <strong><?= lang('App.possible_duplicate') ?></strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach ($duplicateWarnings as $warning): ?>
                            <li><?= esc($warning) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-12">
                        <form action="<?= site_url('drivers') ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field(); ?>
                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-lg-8">
                                    <!-- Personal Details Card -->
                                    <div class="card ops-card">
                                        <div class="card-header ops-toolbar">
                                            <h3 class="card-title">
                                                <i class="fas fa-user mr-2"></i>
                                                <?= lang('App.personal_details') ?>
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="full_name"><?= lang('App.full_name') ?> <span class="text-danger">*</span></label>
                                                        <input type="text" name="full_name" id="full_name" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" value="<?= old('full_name') ?>" required>
                                                        <?php if (isset($errors['full_name'])): ?>
                                                            <div class="invalid-feedback"><?= esc($errors['full_name']) ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="mobile_number"><?= lang('App.mobile_number') ?> <span class="text-danger">*</span></label>
                                                        <input type="text" name="mobile_number" id="mobile_number" class="form-control <?= isset($errors['mobile_number']) ? 'is-invalid' : '' ?>" maxlength="20" value="<?= old('mobile_number') ?>" required>
                                                        <?php if (isset($errors['mobile_number'])): ?>
                                                            <div class="invalid-feedback"><?= esc($errors['mobile_number']) ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="whatsapp_number"><?= lang('App.whatsapp_number') ?></label>
                                                        <input type="text" name="whatsapp_number" id="whatsapp_number" class="form-control <?= isset($errors['whatsapp_number']) ? 'is-invalid' : '' ?>" maxlength="20" value="<?= old('whatsapp_number') ?>">
                                                        <?php if (isset($errors['whatsapp_number'])): ?>
                                                            <div class="invalid-feedback"><?= esc($errors['whatsapp_number']) ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="address"><?= lang('App.address') ?></label>
                                                        <textarea name="address" id="address" rows="2" class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>"><?= old('address') ?></textarea>
                                                        <?php if (isset($errors['address'])): ?>
                                                            <div class="invalid-feedback"><?= esc($errors['address']) ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="city"><?= lang('App.city') ?></label>
                                                        <input type="text" name="city" id="city" class="form-control <?= isset($errors['city']) ? 'is-invalid' : '' ?>" value="<?= old('city') ?>">
                                                        <?php if (isset($errors['city'])): ?>
                                                            <div class="invalid-feedback"><?= esc($errors['city']) ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="state"><?= lang('App.state') ?></label>
                                                        <input type="text" name="state" id="state" class="form-control <?= isset($errors['state']) ? 'is-invalid' : '' ?>" value="<?= old('state') ?>">
                                                        <?php if (isset($errors['state'])): ?>
                                                            <div class="invalid-feedback"><?= esc($errors['state']) ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Vehicle Details Card -->
                                    <div class="card ops-card">
                                        <div class="card-header ops-toolbar">
                                            <h3 class="card-title">
                                                <i class="fas fa-truck mr-2"></i>
                                                <?= lang('App.vehicle_details') ?>
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="vehicle_number"><?= lang('App.vehicle_number') ?> <span class="text-danger">*</span></label>
                                                        <input type="text" name="vehicle_number" id="vehicle_number" class="form-control <?= isset($errors['vehicle_number']) ? 'is-invalid' : '' ?>" value="<?= old('vehicle_number') ?>" required>
                                                        <?php if (isset($errors['vehicle_number'])): ?>
                                                            <div class="invalid-feedback"><?= esc($errors['vehicle_number']) ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="vehicle_type"><?= lang('App.vehicle_category') ?> <span class="text-danger">*</span></label>
                                                        <select name="vehicle_type" id="vehicle_type" class="form-control <?= isset($errors['vehicle_type']) ? 'is-invalid' : '' ?>" required>
                                                            <option value=""><?= lang('App.select_vehicle_category') ?></option>
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
                                                        <label for="license_number"><?= lang('App.license_number') ?></label>
                                                        <input type="text" name="license_number" id="license_number" class="form-control <?= isset($errors['license_number']) ? 'is-invalid' : '' ?>" value="<?= old('license_number') ?>">
                                                        <?php if (isset($errors['license_number'])): ?>
                                                            <div class="invalid-feedback"><?= esc($errors['license_number']) ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-lg-4">
                                    <!-- Account Settings Card -->
                                    <div class="card ops-card">
                                        <div class="card-header ops-toolbar">
                                            <h3 class="card-title">
                                                <i class="fas fa-cog mr-2"></i>
                                                <?= lang('App.account_settings') ?>
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="status"><?= lang('App.driver_status') ?> <span class="text-danger">*</span></label>
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
                                            <div class="form-group">
                                                <label for="default_cash_incentive_amount"><?= lang('App.default_incentive') ?> <span class="text-danger">*</span></label>
                                                <input type="number" step="0.01" min="0" name="default_cash_incentive_amount" id="default_cash_incentive_amount" class="form-control <?= isset($errors['default_cash_incentive_amount']) ? 'is-invalid' : '' ?>" value="<?= old('default_cash_incentive_amount', '200.00') ?>" required>
                                                <?php if (isset($errors['default_cash_incentive_amount'])): ?>
                                                    <div class="invalid-feedback"><?= esc($errors['default_cash_incentive_amount']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="form-group">
                                                <label for="whatsapp_opt_in"><?= lang('App.whatsapp_consent') ?></label>
                                                <select name="whatsapp_opt_in" id="whatsapp_opt_in" class="form-control <?= isset($errors['whatsapp_opt_in']) ? 'is-invalid' : '' ?>">
                                                    <option value="1" <?= old('whatsapp_opt_in', '1') === '1' ? 'selected' : '' ?>><?= lang('App.opted_in') ?></option>
                                                    <option value="0" <?= old('whatsapp_opt_in') === '0' ? 'selected' : '' ?>><?= lang('App.opted_out') ?></option>
                                                </select>
                                                <?php if (isset($errors['whatsapp_opt_in'])): ?>
                                                    <div class="invalid-feedback"><?= esc($errors['whatsapp_opt_in']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="form-group">
                                                <label for="whatsapp_opt_out_reason"><?= lang('App.opt_out_reason') ?></label>
                                                <input type="text" name="whatsapp_opt_out_reason" id="whatsapp_opt_out_reason" class="form-control <?= isset($errors['whatsapp_opt_out_reason']) ? 'is-invalid' : '' ?>" value="<?= old('whatsapp_opt_out_reason') ?>" placeholder="<?= lang('App.optional_reason') ?>">
                                                <?php if (isset($errors['whatsapp_opt_out_reason'])): ?>
                                                    <div class="invalid-feedback"><?= esc($errors['whatsapp_opt_out_reason']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="form-group mb-0">
                                                <label for="notes"><?= lang('App.notes') ?></label>
                                                <textarea name="notes" id="notes" rows="2" class="form-control"><?= old('notes') ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Documents Card -->
                                    <div class="card ops-card">
                                        <div class="card-header ops-toolbar">
                                            <h3 class="card-title">
                                                <i class="fas fa-id-card mr-2"></i>
                                                <?= lang('App.documents') ?>
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="photo_path"><?= lang('App.driver_photo') ?></label>
                                                <div class="custom-file">
                                                    <input type="file" name="photo_path" id="photo_path" class="custom-file-input <?= isset($errors['photo_path']) ? 'is-invalid' : '' ?>" accept="image/*" capture="environment">
                                                    <label class="custom-file-label" for="photo_path"><?= lang('App.choose_photo') ?></label>
                                                </div>
                                                <div class="camera-capture mt-2" data-camera-capture data-input="photo_path">
                                                    <div class="camera-capture__actions">
                                                        <button type="button" class="btn btn-outline-enterprise btn-sm" data-camera-start><?= lang('App.capture_photo') ?></button>
                                                        <button type="button" class="btn btn-primary-enterprise btn-sm" data-camera-snap style="display:none;"><?= lang('App.use_this') ?></button>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-camera-stop style="display:none;"><?= lang('App.cancel') ?></button>
                                                    </div>
                                                    <video class="camera-capture__media" data-camera-video playsinline></video>
                                                    <canvas data-camera-canvas hidden></canvas>
                                                    <img class="camera-capture__preview" data-camera-preview alt="Preview">
                                                    <small class="form-text text-muted" data-camera-status></small>
                                                </div>
                                                <?php if (isset($errors['photo_path'])): ?>
                                                    <div class="text-danger small mt-1"><?= esc($errors['photo_path']) ?></div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group mb-0">
                                                <label for="license_photo_path"><?= lang('App.license_document') ?></label>
                                                <div class="custom-file">
                                                    <input type="file" name="license_photo_path" id="license_photo_path" class="custom-file-input <?= isset($errors['license_photo_path']) ? 'is-invalid' : '' ?>" accept="image/*" capture="environment">
                                                    <label class="custom-file-label" for="license_photo_path"><?= lang('App.choose_document') ?></label>
                                                </div>
                                                <div class="camera-capture mt-2" data-camera-capture data-input="license_photo_path">
                                                    <div class="camera-capture__actions">
                                                        <button type="button" class="btn btn-outline-enterprise btn-sm" data-camera-start><?= lang('App.capture_license') ?></button>
                                                        <button type="button" class="btn btn-primary-enterprise btn-sm" data-camera-snap style="display:none;"><?= lang('App.use_this') ?></button>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-camera-stop style="display:none;"><?= lang('App.cancel') ?></button>
                                                    </div>
                                                    <video class="camera-capture__media" data-camera-video playsinline></video>
                                                    <canvas data-camera-canvas hidden></canvas>
                                                    <img class="camera-capture__preview" data-camera-preview alt="Preview">
                                                    <small class="form-text text-muted" data-camera-status></small>
                                                </div>
                                                <?php if (isset($errors['license_photo_path'])): ?>
                                                    <div class="text-danger small mt-1"><?= esc($errors['license_photo_path']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mb-4" style="gap:0.5rem;">
                                <a href="<?= base_url('drivers') ?>" class="btn btn-outline-enterprise"><i class="fas fa-arrow-left mr-1"></i> <?= lang('App.back') ?></a>
                                <button type="reset" class="btn btn-outline-enterprise"><i class="fas fa-undo mr-1"></i> <?= lang('App.reset') ?></button>
                                <button type="submit" class="btn btn-primary-enterprise"><i class="fas fa-save mr-1"></i> <?= lang('App.save_driver') ?></button>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* =========================================
   ENTERPRISE FORM STYLING
========================================= */
.ops-card {
    background: #FFFFFF;
    border-radius: 4px;
    border: 1px solid #E0E0E0;
    box-shadow: none;
    margin-bottom: 24px;
}
.ops-card .card-header {
    background: #F5F5F5;
    padding: 16px 20px;
    border-bottom: 1px solid #E0E0E0;
    border-radius: 4px 4px 0 0;
}
.ops-card .card-title {
    font-family: 'Hanken Grotesk', sans-serif;
    font-weight: 600;
    color: #1A1C1C;
    font-size: 18px;
    margin: 0;
}
.ops-section-title {
    font-family: 'Hanken Grotesk', sans-serif;
    font-size: 14px;
    font-weight: 600;
    color: #4F4255;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-top: 24px;
    margin-bottom: 16px;
    border-bottom: 1px solid #EEEEEE;
    padding-bottom: 8px;
}

/* Form Inputs */
.form-group label {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    font-weight: 500;
    color: #4F4255;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 6px;
}
.form-control, .custom-file-label {
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
.form-control:focus, .custom-file-input:focus ~ .custom-file-label {
    border-color: #A600FF;
    outline: 0;
}
textarea.form-control {
    min-height: 80px;
}
.custom-file-label::after {
    height: auto;
    padding: 10px 12px;
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    background: #F5F5F5;
    color: #4F4255;
    border-left: 1px solid #E0E0E0;
    border-radius: 0 4px 4px 0;
    line-height: 1.5;
}

/* Buttons */
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

<?php include 'app/Views/partials/camera_capture.php'; ?>
<?php include 'app/Views/templates/footer.php'; ?>
