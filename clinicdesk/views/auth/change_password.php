<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
require_once __DIR__ . '/../../core/Auth.php';

$pageTitle = 'Change Password — ' . APP_NAME;
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>

<div class="wrapper">
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Change Password</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>/index.php?page=dashboard">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Change Password</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <?php $user = Auth::currentUser(); if ($user['first_login'] ?? 0): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Security Notice:</strong> You must change your password before continuing.
            </div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header"
                             style="background: linear-gradient(135deg, #3a7bd5, #00d2ff);">
                            <h3 class="card-title text-white">
                                <i class="fas fa-key mr-2"></i> Change Password
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST"
                                  action="<?= BASE_URL ?>/index.php?page=auth&action=change_password">
                                <?= CSRF::field() ?>

                                <div class="form-group">
                                    <label>Current Password</label>
                                    <input type="password" name="current_password"
                                           class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" name="new_password"
                                           class="form-control" required
                                           minlength="8" id="newPass">
                                    <small class="text-muted">
                                        Min 8 characters, must include uppercase and number.
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label>Confirm New Password</label>
                                    <input type="password" name="confirm_password"
                                           class="form-control" required id="confirmPass">
                                </div>

                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar" id="strengthBar"
                                         style="width:0%;transition:all 0.3s;"></div>
                                </div>
                                <small id="strengthText" class="text-muted"></small>

                                <button type="submit" class="btn btn-primary btn-block mt-3">
                                    <i class="fas fa-save mr-2"></i> Update Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

<script>
document.getElementById('newPass').addEventListener('input', function() {
    const val = this.value;
    const bar = document.getElementById('strengthBar');
    const txt = document.getElementById('strengthText');
    let strength = 0;
    if (val.length >= 8) strength++;
    if (/[A-Z]/.test(val)) strength++;
    if (/[0-9]/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;
    const colors = ['#dc3545','#fd7e14','#ffc107','#28a745'];
    const labels = ['Weak','Fair','Good','Strong'];
    bar.style.width = (strength * 25) + '%';
    bar.style.background = colors[strength-1] || '#dc3545';
    txt.textContent = strength > 0 ? labels[strength-1] : '';
    txt.style.color = colors[strength-1] || '#dc3545';
});
</script>