<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= lang('App.login_title') ?></title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <!-- Google Fonts: Hanken Grotesk, Inter, JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?php echo base_url()?>/asset/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?php echo base_url()?>/asset/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url()?>/asset/dist/css/adminlte.min.css?v=3.2.0">
    <link rel="stylesheet" href="<?php echo base_url()?>/asset/dist/css/style.css">
    
    <style>
        /* Global Enterprise Font Settings */
        body, .form-control {
            font-family: 'Inter', sans-serif !important;
            font-size: 13px !important; 
        }
        h1, h2, h3, h4, h5, h6, .brand-text {
            font-family: 'Hanken Grotesk', sans-serif !important;
            font-weight: 600;
        }

        /* Login Page Specific Styles */
        body.login-page {
            min-height: 100vh;
            background: radial-gradient(circle at top left, #F3E8FF, transparent 28%),
                        radial-gradient(circle at bottom right, #E0E7FF, transparent 30%),
                        linear-gradient(135deg, #F8F9FA 0%, #FFFFFF 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-box {
            width: min(420px, calc(100% - 32px));
            margin: auto;
        }

        .login-logo img {
            max-width: 180px;
            margin-bottom: 16px;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.05));
        }

        .login-logo {
            font-family: 'Hanken Grotesk', sans-serif;
            color: #1A1C1C;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 30px;
        }
        
        .login-logo b {
            color: #A600FF;
            font-weight: 800;
        }

        /* Modern Card styling */
        .login-card-body {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px !important;
            border: 1px solid #E0E0E0 !important;
            box-shadow: 0 18px 45px rgba(26, 28, 28, 0.08) !important;
            padding: 32px !important;
        }

        /* Inputs */
        .form-group label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 600;
            color: #4F4255;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
            display: block;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .form-control {
            border: 1px solid #E0E0E0;
            border-right: none;
            border-radius: 6px 0 0 6px !important;
            padding: 12px 14px;
            font-size: 14px !important;
            color: #1A1C1C;
            background: #FFFFFF;
            box-shadow: none !important;
            height: 48px;
            transition: border-color 0.2s;
        }

        .input-group-append .input-group-text {
            background: #FFFFFF;
            border: 1px solid #E0E0E0;
            border-left: none;
            border-radius: 0 6px 6px 0 !important;
            color: #A600FF;
            height: 48px;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: #A600FF;
            outline: 0;
        }

        .form-control:focus + .input-group-append .input-group-text {
            border-color: #A600FF;
        }

        .form-control::placeholder {
            color: #9CA3AF;
        }

        /* Primary Button */
        .btn-primary-enterprise {
            background: #A600FF;
            color: #FFFFFF;
            border: none;
            border-radius: 6px;
            font-family: 'Hanken Grotesk', sans-serif;
            font-size: 15px;
            font-weight: 600;
            padding: 12px 20px;
            width: 100%;
            height: 48px;
            transition: all 0.2s;
            text-decoration: none;
            margin-top: 10px;
            box-shadow: 0 4px 12px rgba(166, 0, 255, 0.2);
        }

        .btn-primary-enterprise:hover {
            background: #8300CA;
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(166, 0, 255, 0.3);
        }

        /* Alerts */
        .ops-alert {
            background-color: #FFF0F2;
            color: #E11D48;
            border: 1px solid #FECDD3;
            border-radius: 6px;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 500;
            padding: 12px 16px;
            margin-bottom: 24px;
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo text-center">
        <img src="<?php echo base_url('uploads/hawahhawai_logo1.png');?>" alt="Hawa Hawai Logo">
        <div><b>Driver</b> Management</div>
    </div>

    <?php if (session()->getFlashdata('failed')): ?>
        <div class="alert ops-alert">
            <i class="fas fa-exclamation-circle mr-2"></i><?= esc(session()->getFlashdata('failed')) ?>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-none bg-transparent mb-0">
        <div class="card-body login-card-body">
            <form action="" method="post">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label for="phone"><?= lang('App.mobile_number') ?></label>
                    <div class="input-group">
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="<?= lang('App.enter_mobile') ?>" required autofocus autocomplete="tel">
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-phone-alt"></span></div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password"><?= lang('App.password') ?></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="<?= lang('App.enter_password') ?>" required autocomplete="current-password">
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-lock"></span></div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary-enterprise mt-2">
                    <?= lang('App.sign_in') ?> <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </form>
        </div>
    </div>
    
    <div class="text-center mt-4 text-muted" style="font-family: 'Inter', sans-serif; font-size: 12px;">
        &copy; <?= date('Y') ?> Hawa Hawai Aeroplane Restaurant.<br>All rights reserved.
    </div>
</div>

<script src="<?php echo base_url()?>/asset/plugins/jquery/jquery.min.js"></script>
<script src="<?php echo base_url()?>/asset/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url()?>/asset/dist/js/adminlte.min.js?v=3.2.0"></script>
</body>
</html>
