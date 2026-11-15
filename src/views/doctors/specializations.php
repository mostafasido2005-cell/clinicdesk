<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('admin');
$pageTitle = 'Specializations — ' . APP_NAME;
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="wrapper">
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 font-weight-bold">
                <i class="fas fa-stethoscope mr-2" style="color:#3a7bd5;"></i> Specializations
            </h1>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>
            <div class="row">
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header" style="background:linear-gradient(135deg,#3a7bd5,#00d2ff);">
                            <h3 class="card-title text-white">
                                <i class="fas fa-plus mr-2"></i> Add Specialization
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?= BASE_URL ?>/index.php?page=doctors&action=add_specialization">
                                <?= CSRF::field() ?>
                                <div class="input-group">
                                    <input type="text" name="name" class="form-control"
                                           placeholder="Specialization name" required>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold">All Specializations</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover">
                                <thead>
                                    <tr><th>#</th><th>Name</th><th>Action</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($specializations as $spec): ?>
                                    <tr>
                                        <td><?= $spec['id'] ?></td>
                                        <td><?= htmlspecialchars($spec['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            <form method="POST"
                                                  action="<?= BASE_URL ?>/index.php?page=doctors&action=delete_specialization"
                                                  class="d-inline">
                                                <?= CSRF::field() ?>
                                                <input type="hidden" name="id" value="<?= $spec['id'] ?>">
                                                <button type="submit" class="btn btn-xs btn-danger"
                                                        onclick="return confirm('Delete this specialization?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>