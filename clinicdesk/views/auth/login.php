<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/dist/css/adminlte.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Source Sans Pro', sans-serif;
        }
        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }
        .login-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #3a7bd5, #00d2ff);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 10px 30px rgba(58,123,213,0.4);
        }
        .login-logo h1 {
            color: white;
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(90deg, #3a7bd5, #00d2ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .login-logo p {
            color: rgba(255,255,255,0.5);
            font-size: 13px;
            margin-top: 5px;
        }
        .form-group label {
            color: rgba(255,255,255,0.7);
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .input-group .form-control {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            color: white;
            border-radius: 10px 0 0 10px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .input-group .form-control:focus {
            background: rgba(255,255,255,0.12);
            border-color: #3a7bd5;
            box-shadow: 0 0 0 3px rgba(58,123,213,0.2);
            color: white;
        }
        .input-group .form-control::placeholder { color: rgba(255,255,255,0.3); }
        .input-group-text {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-left: none;
            color: #3a7bd5;
            border-radius: 0 10px 10px 0;
        }
        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #3a7bd5, #00d2ff);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            letter-spacing: 0.5px;
        }
        .btn-login:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(58,123,213,0.4);
        }
        .alert {
            border-radius: 10px;
            border: none;
            font-size: 13px;
            margin-bottom: 20px;
        }
        .alert-danger {
            background: rgba(220,53,69,0.2);
            color: #ff6b7a;
            border: 1px solid rgba(220,53,69,0.3);
        }
        .alert-success {
            background: rgba(40,167,69,0.2);
            color: #6bcf8a;
            border: 1px solid rgba(40,167,69,0.3);
        }
        .demo-accounts {
            margin-top: 25px;
            padding: 15px;
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.08);
        }
        .demo-accounts p {
            color: rgba(255,255,255,0.5);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .demo-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            margin: 2px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .demo-badge:hover { transform: scale(1.05); }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <!-- Logo -->
        <div class="login-logo">
            <div class="logo-icon">
                <i class="fas fa-clinic-medical text-white" style="font-size: 30px;"></i>
            </div>
            <h1>ClinicDesk</h1>
            <p>Clinic Management Dashboard</p>
        </div>

        <!-- Alert -->
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?>">
                <i class="fas fa-<?= $flash['type'] === 'error' ? 'exclamation-circle' : 'check-circle' ?> mr-2"></i>
                <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" action="<?= BASE_URL ?>/index.php?page=auth&action=login">
            <?= CSRF::field() ?>

            <div class="form-group">
                <label>Email Address</label>
                <div class="input-group">
                    <input type="email" name="email" class="form-control"
                           placeholder="Enter your email" required autocomplete="email">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="passwordField"
                           class="form-control" placeholder="Enter your password"
                           required autocomplete="current-password">
                    <div class="input-group-append">
                        <span class="input-group-text" id="togglePassword"
                              style="cursor:pointer;" onclick="togglePass()">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt mr-2"></i> Sign In
            </button>
        </form>

        <!-- Demo Accounts -->
        <div class="demo-accounts">
            <p>Demo Accounts</p>
            <span class="demo-badge" style="background:rgba(220,53,69,0.2);color:#ff6b7a;"
                  onclick="fillLogin('admin@clinic.local','Admin@1234')">
                <i class="fas fa-shield-alt mr-1"></i> Admin
            </span>
        </div>
    </div>
</div>

<script>
function togglePass() {
    const field = document.getElementById('passwordField');
    const icon  = document.getElementById('eyeIcon');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function fillLogin(email, pass) {
    document.querySelector('input[name="email"]').value = email;
    document.querySelector('input[name="password"]').value = pass;
}
</script>
</body>
</html>