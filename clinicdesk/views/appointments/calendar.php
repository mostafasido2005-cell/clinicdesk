<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('doctor');
$pageTitle = 'My Calendar — ' . APP_NAME;
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
                        <i class="fas fa-calendar-week mr-2" style="color:#3a7bd5;"></i> Weekly Calendar
                    </h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="?page=appointments&action=calendar&week=<?= ($weekOffset ?? 0) - 1 ?>"
                       class="btn btn-secondary">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                    <a href="?page=appointments&action=calendar&week=0"
                       class="btn btn-primary mx-1">Today</a>
                    <a href="?page=appointments&action=calendar&week=<?= ($weekOffset ?? 0) + 1 ?>"
                       class="btn btn-secondary">
                        Next <i class="fas fa-chevron-right"></i>
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
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <?php foreach ($weekDays as $day): ?>
                                    <th class="text-center <?= $day === date('Y-m-d') ? 'bg-primary text-white' : '' ?>"
                                        style="width:14.28%;">
                                        <div><?= date('D', strtotime($day)) ?></div>
                                        <small><?= date('M d', strtotime($day)) ?></small>
                                    </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="vertical-align:top;">
                                    <?php foreach ($weekDays as $day): ?>
                                    <td style="min-height:150px;padding:8px;">
                                        <?php if (!empty($calendarData[$day])): ?>
                                            <?php foreach ($calendarData[$day] as $appt): ?>
                                            <a href="<?= BASE_URL ?>/index.php?page=appointments&action=detail&id=<?= $appt['id'] ?>"
                                               class="d-block mb-1 p-1 rounded text-white text-decoration-none"
                                               style="background:<?= $appt['status'] === 'completed' ? '#28a745' : ($appt['status'] === 'confirmed' ? '#17a2b8' : ($appt['status'] === 'cancelled' ? '#dc3545' : '#ffc107')) ?>;font-size:11px;">
                                                <strong><?= formatTime($appt['appt_time']) ?></strong><br>
                                                <?= htmlspecialchars($appt['patient_name'], ENT_QUOTES, 'UTF-8') ?>
                                            </a>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                        <div class="text-center text-muted mt-3" style="font-size:11px;">
                                            No appointments
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                    <?php endforeach; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>