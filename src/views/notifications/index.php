<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('admin', 'doctor', 'patient');
$pageTitle = 'Notifications — ' . APP_NAME;
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="wrapper">
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 font-weight-bold">
                <i class="fas fa-bell mr-2" style="color:#3a7bd5;"></i> Notifications
            </h1>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>
            <div class="card">
                <div class="card-body p-0">
                    <?php if (empty($notifications)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-bell-slash fa-3x mb-3 d-block" style="opacity:0.3;"></i>
                        <p>No notifications yet</p>
                    </div>
                    <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($notifications as $n): ?>
                        <li class="list-group-item <?= !$n['is_read'] ? 'list-group-item-light' : '' ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <?php if (!$n['is_read']): ?>
                                    <span class="badge badge-primary mr-2">New</span>
                                    <?php endif; ?>
                                    <strong><?= htmlspecialchars($n['title'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <p class="mb-1 text-muted">
                                        <?= htmlspecialchars($n['message'], ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock mr-1"></i>
                                        <?= timeAgo($n['created_at']) ?>
                                    </small>
                                </div>
                                <form method="POST"
                                      action="<?= BASE_URL ?>/index.php?page=notifications&action=delete">
                                    <?= CSRF::field() ?>
                                    <input type="hidden" name="id" value="<?= $n['id'] ?>">
                                    <button type="submit" class="btn btn-xs btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>