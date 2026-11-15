<?php
require_once __DIR__ . '/../../core/Auth.php';
$currentUser = Auth::currentUser();
$role = Auth::role();
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= BASE_URL ?>/index.php?page=dashboard" class="brand-link">
        <div class="brand-image d-flex align-items-center justify-content-center"
             style="width:33px;height:33px;background:linear-gradient(135deg,#3a7bd5,#00d2ff);border-radius:8px;margin-left:12px;">
            <i class="fas fa-clinic-medical text-white" style="font-size:16px;"></i>
        </div>
        <span class="brand-text font-weight-bold ml-2">ClinicDesk</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image">
                <?php if (!empty($currentUser['avatar'])): ?>
                    <img src="<?= BASE_URL ?>/public/uploads/avatars/<?= htmlspecialchars($currentUser['avatar'], ENT_QUOTES, 'UTF-8') ?>"
                         class="img-circle elevation-2" alt="Avatar"
                         style="width:35px;height:35px;object-fit:cover;">
                <?php else: ?>
                    <div class="img-circle elevation-2 d-flex align-items-center justify-content-center"
                         style="width:35px;height:35px;background:linear-gradient(135deg,#3a7bd5,#00d2ff);color:white;font-weight:bold;">
                        <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="info ml-2">
                <a href="#" class="d-block text-white font-weight-bold" style="font-size:13px;">
                    <?= htmlspecialchars($currentUser['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </a>
                <small class="text-muted">
                    <span class="badge badge-primary" style="font-size:10px;">
                        <?= ucfirst($role) ?>
                    </span>
                </small>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/index.php?page=dashboard"
                       class="nav-link <?= isActivePage('dashboard') ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <?php if ($role === 'admin'): ?>
                <!-- ===== ADMIN MENU ===== -->
                <li class="nav-header" style="color:rgba(255,255,255,0.4);font-size:10px;letter-spacing:1px;">
                    USER MANAGEMENT
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/index.php?page=users"
                       class="nav-link <?= isActivePage('users') ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>All Users</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/index.php?page=users&action=create"
                       class="nav-link <?= isActivePage('users', 'create') ?>">
                        <i class="nav-icon fas fa-user-plus"></i>
                        <p>Add User</p>
                    </a>
                </li>

                <li class="nav-header" style="color:rgba(255,255,255,0.4);font-size:10px;letter-spacing:1px;">
                    DOCTORS
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/index.php?page=doctors"
                       class="nav-link <?= isActivePage('doctors') ?>">
                        <i class="nav-icon fas fa-user-md"></i>
                        <p>Manage Doctors</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/index.php?page=doctors&action=specializations"
                       class="nav-link <?= isActivePage('doctors', 'specializations') ?>">
                        <i class="nav-icon fas fa-stethoscope"></i>
                        <p>Specializations</p>
                    </a>
                </li>

                <li class="nav-header" style="color:rgba(255,255,255,0.4);font-size:10px;letter-spacing:1px;">
                    APPOINTMENTS
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/index.php?page=appointments"
                       class="nav-link <?= isActivePage('appointments') ?>">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>All Appointments</p>
                    </a>
                </li>

                <li class="nav-header" style="color:rgba(255,255,255,0.4);font-size:10px;letter-spacing:1px;">
                    REPORTS
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/index.php?page=reports"
                       class="nav-link <?= isActivePage('reports') ?>">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Reports</p>
                    </a>
                </li>

                <?php elseif ($role === 'doctor'): ?>
                <!-- ===== DOCTOR MENU ===== -->
                <li class="nav-header" style="color:rgba(255,255,255,0.4);font-size:10px;letter-spacing:1px;">
                    MY SCHEDULE
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/index.php?page=appointments"
                       class="nav-link <?= isActivePage('appointments') ?>">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>My Appointments</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/index.php?page=appointments&action=calendar"
                       class="nav-link <?= isActivePage('appointments', 'calendar') ?>">
                        <i class="nav-icon fas fa-calendar-week"></i>
                        <p>Weekly Calendar</p>
                    </a>
                </li>

                <li class="nav-header" style="color:rgba(255,255,255,0.4);font-size:10px;letter-spacing:1px;">
                    PROFILE
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/index.php?page=doctors&action=edit&id=<?= htmlspecialchars($currentUser['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="nav-link <?= isActivePage('doctors', 'edit') ?>">
                        <i class="nav-icon fas fa-user-edit"></i>
                        <p>Edit Profile</p>
                    </a>
                </li>

                <?php else: ?>
                <!-- ===== PATIENT MENU ===== -->
                <li class="nav-header" style="color:rgba(255,255,255,0.4);font-size:10px;letter-spacing:1px;">
                    APPOINTMENTS
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/index.php?page=appointments&action=book"
                       class="nav-link <?= isActivePage('appointments', 'book') ?>">
                        <i class="nav-icon fas fa-calendar-plus"></i>
                        <p>Book Appointment</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/index.php?page=appointments"
                       class="nav-link <?= isActivePage('appointments') ?>">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>My Appointments</p>
                    </a>
                </li>

                <li class="nav-header" style="color:rgba(255,255,255,0.4);font-size:10px;letter-spacing:1px;">
                    MEDICAL RECORDS
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/index.php?page=prescriptions"
                       class="nav-link <?= isActivePage('prescriptions') ?>">
                        <i class="nav-icon fas fa-file-medical"></i>
                        <p>My Prescriptions</p>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Notifications (all roles) -->
                <li class="nav-header" style="color:rgba(255,255,255,0.4);font-size:10px;letter-spacing:1px;">
                    ACCOUNT
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/index.php?page=notifications"
                       class="nav-link <?= isActivePage('notifications') ?>">
                        <i class="nav-icon fas fa-bell"></i>
                        <p>Notifications</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/index.php?page=auth&action=change_password"
                       class="nav-link <?= isActivePage('auth', 'change_password') ?>">
                        <i class="nav-icon fas fa-key"></i>
                        <p>Change Password</p>
                    </a>
                </li>

                <!-- Logout -->
                <li class="nav-item mt-3">
                    <form method="POST" action="<?= BASE_URL ?>/index.php?page=auth&action=logout">
                        <?= CSRF::field() ?>
                        <button type="submit" class="nav-link btn btn-link text-left w-100"
                                style="color:rgba(255,100,100,0.8)!important;">
                            <i class="nav-icon fas fa-sign-out-alt" style="color:rgba(255,100,100,0.8)!important;"></i>
                            <p>Logout</p>
                        </button>
                    </form>
                </li>

            </ul>
        </nav>
    </div>
</aside>