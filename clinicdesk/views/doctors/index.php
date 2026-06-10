<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('admin');
$pageTitle = 'Manage Doctors — ' . APP_NAME;
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
                    <h1 class="m-0 font-weight-bold">
                        <i class="fas fa-user-md mr-2" style="color:#3a7bd5;"></i> Manage Doctors
                    </h1>
                </div>
                <div class="col-sm-6">
                    <a href="<?= BASE_URL ?>/index.php?page=users&action=create"
                       class="btn btn-primary float-right">
                        <i class="fas fa-plus mr-1"></i> Add Doctor
                    </a>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover data-table">
                            <thead>
                                <tr>
                                    <th>Doctor</th>
                                    <th>Specialization</th>
                                    <th>Fee</th>
                                    <th>Available Days</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($doctors as $doc): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($doc['photo'])): ?>
                                            <img src="<?= BASE_URL ?>/public/uploads/doctor_photos/<?= htmlspecialchars($doc['photo'], ENT_QUOTES, 'UTF-8') ?>"
                                                 class="img-circle mr-2" style="width:40px;height:40px;object-fit:cover;">
                                            <?php else: ?>
                                            <div class="img-circle mr-2 d-flex align-items-center justify-content-center"
                                                 style="width:40px;height:40px;background:linear-gradient(135deg,#3a7bd5,#00d2ff);color:white;font-weight:bold;">
                                                <?= strtoupper(substr($doc['name'], 0, 1)) ?>
                                            </div>
                                            <?php endif; ?>
                                            <div>
                                                <strong><?= htmlspecialchars($doc['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                                                <br>
                                                <small class="text-muted"><?= htmlspecialchars($doc['email'], ENT_QUOTES, 'UTF-8') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($doc['specialization_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>$<?= number_format($doc['consultation_fee'], 2) ?></td>
                                    <td>
                                        <?php foreach (explode(',', $doc['available_days']) as $day): ?>
                                        <span class="badge badge-primary mr-1"><?= $day ?></span>
                                        <?php endforeach; ?>
                                    </td>
                                    <td>
                                        <?php if ($doc['is_active']): ?>
                                        <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                        <span class="badge badge-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/index.php?page=doctors&action=edit&id=<?= $doc['id'] ?>"
                                           class="btn btn-xs btn-info">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if ($paginator->totalPages() > 1): ?>
                <div class="card-footer">
                    <ul class="pagination pagination-sm m-0 float-right">
                        <?php if ($paginator->hasPrev()): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=doctors&p=<?= $paginator->currentPage()-1 ?>">«</a>
                        </li>
                        <?php endif; ?>
                        <?php foreach ($paginator->pages() as $p): ?>
                        <li class="page-item <?= $p === $paginator->currentPage() ? 'active' : '' ?>">
                            <a class="page-link" href="?page=doctors&p=<?= $p ?>"><?= $p ?></a>
                        </li>
                        <?php endforeach; ?>
                        <?php if ($paginator->hasNext()): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=doctors&p=<?= $paginator->currentPage()+1 ?>">»</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>