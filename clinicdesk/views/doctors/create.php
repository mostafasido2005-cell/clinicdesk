<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('admin');
$pageTitle = 'Create Doctor — ' . APP_NAME;
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="wrapper">
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 font-weight-bold">
                <i class="fas fa-user-md mr-2" style="color:#3a7bd5;"></i> Create Doctor
            </h1>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                To add a doctor, please use the
                <a href="<?= BASE_URL ?>/index.php?page=users&action=create">
                    <strong>Add User</strong>
                </a>
                page and select the role <strong>Doctor</strong>.
            </div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>