<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('admin');
$pageTitle = 'Edit User — ' . APP_NAME;
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="wrapper">
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 font-weight-bold">
                <i class="fas fa-user-edit mr-2" style="color:#3a7bd5;"></i> Edit User
            </h1>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header" style="background:linear-gradient(135deg,#3a7bd5,#00d2ff);">
                            <h3 class="card-title text-white">
                                <i class="fas fa-user-edit mr-2"></i>
                                <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?= BASE_URL ?>/index.php?page=users&action=update"
                                  enctype="multipart/form-data">
                                <?= CSRF::field() ?>
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Full Name</label>
                                            <input type="text" name="name" class="form-control"
                                                   value="<?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input type="text" name="phone" class="form-control"
                                                   value="<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Avatar</label>
                                            <?php if (!empty($user['avatar'])): ?>
                                            <div class="mb-2">
                                                <img src="<?= BASE_URL ?>/public/uploads/avatars/<?= htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8') ?>"
                                                     class="img-circle" style="width:50px;height:50px;object-fit:cover;">
                                            </div>
                                            <?php endif; ?>
                                            <input type="file" name="avatar" class="form-control-file"
                                                   accept="image/jpeg,image/png">
                                            <small class="text-muted">JPEG/PNG, max 1MB</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Role</label>
                                            <input type="text" class="form-control"
                                                   value="<?= ucfirst($user['role']) ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Save Changes
                                </button>
                                <a href="<?= BASE_URL ?>/index.php?page=users" class="btn btn-secondary ml-2">Cancel</a>
                            </form>

                            <hr>
                            <!-- Reset Password -->
                            <h5 class="font-weight-bold">Reset Password</h5>
                            <form method="POST" action="<?= BASE_URL ?>/index.php?page=users&action=reset_password">
                                <?= CSRF::field() ?>
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <div class="input-group">
                                    <input type="password" name="new_password" class="form-control"
                                           placeholder="New password (min 8 chars)" required minlength="8">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-key mr-1"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>