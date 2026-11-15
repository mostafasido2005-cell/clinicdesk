<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('admin', 'doctor', 'patient');
$pageTitle = 'Appointments — ' . APP_NAME;
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
$role = Auth::role();
?>
<div class="wrapper">
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold">
                        <i class="fas fa-calendar-alt mr-2" style="color:#3a7bd5;"></i> Appointments
                    </h1>
                </div>
                <?php if ($role === 'patient'): ?>
                <div class="col-sm-6">
                    <a href="<?= BASE_URL ?>/index.php?page=appointments&action=book"
                       class="btn btn-primary float-right">
                        <i class="fas fa-plus mr-1"></i> Book New
                    </a>
                </div>
                <?php endif; ?>
                <?php if ($role === 'doctor'): ?>
                <div class="col-sm-6">
                    <a href="<?= BASE_URL ?>/index.php?page=appointments&action=calendar"
                       class="btn btn-info float-right">
                        <i class="fas fa-calendar-week mr-1"></i> Calendar View
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <!-- Filter Bar -->
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="<?= BASE_URL ?>/index.php" class="form-inline flex-wrap">
                        <input type="hidden" name="page" value="appointments">
                        <select name="status" class="form-control mr-2 mb-2">
                            <option value="">All Status</option>
                            <?php foreach (['pending','confirmed','completed','cancelled'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($_GET['status'] ?? '') === $s ? 'selected' : '' ?>>
                                <?= ucfirst($s) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="date" name="start_date" class="form-control mr-2 mb-2"
                               value="<?= htmlspecialchars($_GET['start_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               placeholder="Start date">
                        <input type="date" name="end_date" class="form-control mr-2 mb-2"
                               value="<?= htmlspecialchars($_GET['end_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               placeholder="End date">
                        <?php if ($role === 'admin'): ?>
                        <select name="doctor_id" class="form-control mr-2 mb-2">
                            <option value="">All Doctors</option>
                            <?php foreach ($doctors as $doc): ?>
                            <option value="<?= $doc['id'] ?>" <?= ($_GET['doctor_id'] ?? '') == $doc['id'] ? 'selected' : '' ?>>
                                Dr. <?= htmlspecialchars($doc['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="patient_name" class="form-control mr-2 mb-2"
                               placeholder="Patient name..."
                               value="<?= htmlspecialchars($_GET['patient_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary mb-2 mr-2">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments" class="btn btn-secondary mb-2">
                            <i class="fas fa-times mr-1"></i> Clear
                        </a>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <?php if ($role !== 'patient'): ?><th>Patient</th><?php endif; ?>
                                    <?php if ($role !== 'doctor'): ?><th>Doctor</th><?php endif; ?>
                                    <th>Specialization</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($appointments)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fas fa-calendar-times fa-3x mb-3 d-block" style="opacity:0.3;"></i>
                                        No appointments found
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($appointments as $appt): ?>
                                <tr>
                                    <td><?= $appt['id'] ?></td>
                                    <?php if ($role !== 'patient'): ?>
                                    <td><?= htmlspecialchars($appt['patient_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <?php endif; ?>
                                    <?php if ($role !== 'doctor'): ?>
                                    <td><?= htmlspecialchars($appt['doctor_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <?php endif; ?>
                                    <td><?= htmlspecialchars($appt['specialization_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= formatDate($appt['appt_date']) ?></td>
                                    <td><?= formatTime($appt['appt_time']) ?></td>
                                    <td><?= statusBadge($appt['status']) ?></td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/index.php?page=appointments&action=detail&id=<?= $appt['id'] ?>"
                                           class="btn btn-xs btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if ($paginator->totalPages() > 1): ?>
                <div class="card-footer">
                    <ul class="pagination pagination-sm m-0 float-right">
                        <?php if ($paginator->hasPrev()): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=appointments&p=<?= $paginator->currentPage()-1 ?>&status=<?= $_GET['status'] ?? '' ?>">«</a>
                        </li>
                        <?php endif; ?>
                        <?php foreach ($paginator->pages() as $p): ?>
                        <li class="page-item <?= $p === $paginator->currentPage() ? 'active' : '' ?>">
                            <a class="page-link" href="?page=appointments&p=<?= $p ?>&status=<?= $_GET['status'] ?? '' ?>"><?= $p ?></a>
                        </li>
                        <?php endforeach; ?>
                        <?php if ($paginator->hasNext()): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=appointments&p=<?= $paginator->currentPage()+1 ?>&status=<?= $_GET['status'] ?? '' ?>">»</a>
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