
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register — Hawa Hawai Driver Management</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="<?php echo base_url()?>asset/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="<?php echo base_url()?>asset/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
<link rel="stylesheet" href="<?php echo base_url()?>asset/dist/css/adminlte.min.css?v=3.2.0">
<link rel="stylesheet" href="<?php echo base_url()?>asset/dist/css/style.css">
</head>
<body class="hold-transition register-page">
<div class="register-box">
    <div class="register-logo text-center">
        <img src="<?php echo base_url('uploads/hawahhawai_logo1.png');?>" alt="Hawa Hawai">
        <div><b>Driver</b> Management</div>
    </div>

    <?php foreach (['success', 'Required', 'Sorry'] as $flashKey): ?>
        <?php if (session()->getFlashdata($flashKey)): ?>
            <div class="alert alert-<?= in_array($flashKey, ['Required', 'Sorry'], true) ? 'danger' : 'success' ?> ops-alert">
                <?= esc(session()->getFlashdata($flashKey)) ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <div class="card border-0 shadow-none bg-transparent">
        <div class="card-body register-card-body bg-white">
            <form action="" method="post">
                <?= csrf_field() ?>
                <div class="input-group mb-3">
                    <input type="tel" name="phone" class="form-control" placeholder="Mobile Number" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-phone"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                <div class="icheck-primary mb-3">
                    <input type="checkbox" id="agreeTerms" name="terms" value="agree" required>
                    <label for="agreeTerms">I agree to the <a href="#">terms</a></label>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>
            <a href="<?php echo base_url()?>" class="d-block text-center mt-3 font-weight-bold">I already have an account</a>
        </div>
    </div>
</div>

<script src="<?php echo base_url()?>asset/plugins/jquery/jquery.min.js"></script>
<script src="<?php echo base_url()?>asset/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url()?>asset/dist/js/adminlte.min.js?v=3.2.0"></script>
</body>
</html>
