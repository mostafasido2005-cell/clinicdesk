<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('patient');
$pageTitle = 'My Prescriptions — ' . APP_NAME;
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="wrapper">
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 font-weight-bold">
                <i class="fas fa-file-medical mr-2" style="color:#3a7bd5;"></i> My Prescriptions
            </h1>
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
                                    <th>Date</th>
                                    <th>Diagnosis</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($prescriptions)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-file-medical fa-3x mb-3 d-block" style="opacity:0.3;"></i>
                                        No prescriptions yet
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($prescriptions as $pr): ?>
                                <tr>
                                    <td>Dr. <?= htmlspecialchars($pr['doctor_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($pr['specialization_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= formatDate($pr['appt_date']) ?></td>
                                    <td><?= htmlspecialchars(truncate($pr['diagnosis'], 50), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/index.php?page=appointments&action=detail&id=<?= $pr['appointment_id'] ?>"
                                           class="btn btn-xs btn-info mr-1">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <?php if ($pr['file_path']): ?>
                                        <a href="<?= BASE_URL ?>/index.php?page=prescriptions&action=download&id=<?= $pr['id'] ?>"
                                           class="btn btn-xs btn-success mr-1">
                                            <i class="fas fa-download"></i> PDF
                                        </a>
                                        <?php endif; ?>
                                        <a href="<?= BASE_URL ?>/index.php?page=prescriptions&action=print&id=<?= $pr['id'] ?>"
                                           class="btn btn-xs btn-secondary" target="_blank">
                                            <i class="fas fa-print"></i> Print
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>