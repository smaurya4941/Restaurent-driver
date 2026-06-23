<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = lang('App.my_profile');
$pageSubtitle = lang('App.my_profile_desc');
$pageEyebrow = lang('App.account');
$breadcrumbs = [
    ['label' => lang('App.home'), 'url' => base_url('dashboard')],
    ['label' => lang('App.profile'), 'active' => true],
];
$userSession = session()->get('user') ?? [];
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card ops-card mb-4">
                        <div class="card-header ops-toolbar">
                            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;"><?= lang('App.account_settings') ?></h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('updateAdmin') ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="form-group">
                                <div class="form-group">
                                    <label for="phone"><?= lang('App.mobile_number') ?></label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?= esc($userSession['phone'] ?? '') ?>" required>
                                </div>
                                <div class="form-group" style="margin-bottom: 32px;">
                                    <label for="current_password"><?= lang('App.current_password') ?> <span style="color: #F43F5E;">*</span></label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required placeholder="<?= lang('App.current_password_req') ?>">
                                </div>
                                
                                <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #E0E0E0; padding-bottom: 8px; margin-bottom: 16px;">
                                    <?= lang('App.change_password_optional') ?>
                                </div>

                                <div class="form-group">
                                    <label for="new_password"><?= lang('App.new_password') ?></label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="<?= lang('App.leave_blank_keep_current') ?>">
                                </div>
                                <div class="form-group mb-4">
                                    <label for="confirm_new_password"><?= lang('App.confirm_new_password') ?></label>
                                    <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password">
                                </div>
                            </div>
                            <div class="card-footer" style="background: #F8F9FA; border-top: 1px solid #E0E0E0;">
                                <button type="submit" class="btn btn-primary-enterprise w-100"><i class="fas fa-save mr-1"></i> <?= lang('App.update_profile') ?></button>
                            </div>
                        </form>
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
</style>

<?php include 'app/Views/templates/footer.php'; ?>
