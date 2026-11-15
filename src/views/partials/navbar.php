<?php
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../models/NotificationModel.php';

$currentUser      = Auth::currentUser();
$notificationModel = new NotificationModel();
$unreadCount      = Auth::check() ? $notificationModel->countUnread(Auth::id()) : 0;
?>

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= BASE_URL ?>/index.php?page=dashboard" class="nav-link font-weight-bold">
                <i class="fas fa-clinic-medical mr-1" style="color: #3a7bd5;"></i>
                <?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?>
            </a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <!-- Dark Mode Toggle -->
        <li class="nav-item">
            <a class="nav-link" href="#" id="darkModeToggle" title="Toggle Dark Mode">
                <i class="fas fa-moon" id="darkModeIcon"></i>
            </a>
        </li>

        <!-- Notifications -->
        <li class="nav-item dropdown">
            <a class="nav-link" href="<?= BASE_URL ?>/index.php?page=notifications" style="position: relative;">
                <i class="fas fa-bell"></i>
                <?php if ($unreadCount > 0): ?>
                    <span class="notification-badge"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>
        </li>

        <!-- User Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" 
               href="#" id="userDropdown" 
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php if (!empty($currentUser['avatar'])): ?>
                    <img src="<?= BASE_URL ?>/public/uploads/avatars/<?= htmlspecialchars($currentUser['avatar'], ENT_QUOTES, 'UTF-8') ?>" 
                         class="user-avatar mr-2" alt="Avatar">
                <?php else: ?>
                    <div class="user-avatar mr-2 d-flex align-items-center justify-content-center"
                         style="background: linear-gradient(135deg, #3a7bd5, #00d2ff); color: white; font-weight: bold; font-size: 14px;">
                        <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <span class="d-none d-md-inline font-weight-bold">
                    <?= htmlspecialchars($currentUser['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown"
                 style="border-radius: 12px; border: none; box-shadow: 0 8px 30px rgba(0,0,0,0.15); min-width: 200px;">
                
                <div class="dropdown-header text-center py-3"
                     style="background: linear-gradient(135deg, #3a7bd5, #00d2ff); border-radius: 12px 12px 0 0;">
                    <strong class="text-white"><?= htmlspecialchars($currentUser['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                    <br>
                    <small class="text-white opacity-75">
                        <span class="badge badge-light"><?= ucfirst($currentUser['role'] ?? '') ?></span>
                    </small>
                </div>

                <?php if (Auth::role() === 'doctor'): ?>
                <a class="dropdown-item py-2" href="<?= BASE_URL ?>/index.php?page=doctors&action=edit&id=<?= htmlspecialchars($currentUser['id'], ENT_QUOTES, 'UTF-8') ?>">
                    <i class="fas fa-user-md mr-2 text-primary"></i> My Profile
                </a>
                <?php endif; ?>

                <a class="dropdown-item py-2" href="<?= BASE_URL ?>/index.php?page=auth&action=change_password">
                    <i class="fas fa-key mr-2 text-warning"></i> Change Password
                </a>

                <a class="dropdown-item py-2" href="<?= BASE_URL ?>/index.php?page=notifications">
                    <i class="fas fa-bell mr-2 text-info"></i> Notifications
                    <?php if ($unreadCount > 0): ?>
                        <span class="badge badge-danger float-right"><?= $unreadCount ?></span>
                    <?php endif; ?>
                </a>

                <div class="dropdown-divider"></div>

                <form method="POST" action="<?= BASE_URL ?>/index.php?page=auth&action=logout">
                    <?= CSRF::field() ?>
                    <button type="submit" class="dropdown-item py-2 text-danger">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>