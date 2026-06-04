
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign In — Hawa Hawai Driver Management</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<link rel="stylesheet" href="<?php echo base_url()?>/asset/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="<?php echo base_url()?>/asset/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
<link rel="stylesheet" href="<?php echo base_url()?>/asset/dist/css/adminlte.min.css?v=3.2.0">
<link rel="stylesheet" href="<?php echo base_url()?>/asset/dist/css/style.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo text-center">
        <img src="<?php echo base_url('uploads/hawahhawai_logo1.png');?>" alt="Hawa Hawai">
        <div><b>Driver</b> Management</div>
    </div>

    <?php if (session()->getFlashdata('failed')): ?>
        <div class="alert alert-danger ops-alert"><?= esc(session()->getFlashdata('failed')) ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-none bg-transparent">
        <div class="card-body login-card-body bg-white">
            <form action="" method="post">
                <?= csrf_field() ?>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" name="email" placeholder="Email" required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo base_url()?>/asset/plugins/jquery/jquery.min.js"></script>
<script src="<?php echo base_url()?>/asset/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url()?>/asset/dist/js/adminlte.min.js?v=3.2.0"></script>
</body>
</html>
