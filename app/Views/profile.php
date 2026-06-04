<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = 'My Profile';
$pageSubtitle = 'Update your email and password for this account.';
$pageEyebrow = 'Account';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Profile', 'active' => true],
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
                    <div class="card ops-card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Account Settings</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('updateAdmin') ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= esc($userSession['email'] ?? '') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="current_password">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                </div>
                                <div class="form-group">
                                    <label for="confirm_new_password">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password">
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Update Profile</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'app/Views/templates/footer.php'; ?>
