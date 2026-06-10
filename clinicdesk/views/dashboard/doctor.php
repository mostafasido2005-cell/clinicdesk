<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('doctor');
$pageTitle = 'Doctor Dashboard — ' . APP_NAME;
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
                        <i class="fas fa-user-md mr-2" style="color:#3a7bd5;"></i>
                        Doctor Dashboard
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <!-- Stats Row -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box" style="background:linear-gradient(135deg,#3a7bd5,#00d2ff);">
                        <div class="inner">
                            <h3><?= count($todayAppointments ?? []) ?></h3>
                            <p>Today's Appointments</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-day"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments"
                           class="small-box-footer text-white">
                            View Schedule <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box" style="background:linear-gradient(135deg,#f7971e,#ffd200);">
                        <div class="inner">
                            <h3><?= $statusCounts['pending'] ?? 0 ?></h3>
                            <p>Pending</p>
                        </div>
                        <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&status=pending"
                           class="small-box-footer text-white">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box" style="background:linear-gradient(135deg,#11998e,#38ef7d);">
                        <div class="inner">
                            <h3><?= $statusCounts['completed'] ?? 0 ?></h3>
                            <p>Completed</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-double"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&status=completed"
                           class="small-box-footer text-white">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box" style="background:linear-gradient(135deg,#eb3349,#f45c43);">
                        <div class="inner">
                            <h3><?= number_format($rating ?? 0, 1) ?> ⭐</h3>
                            <p>Average Rating</p>
                        </div>
                        <div class="icon"><i class="fas fa-star"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=doctors&action=edit&id=<?= $doctor['id'] ?? 0 ?>"
                           class="small-box-footer text-white">
                            My Profile <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Today's Appointments -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-calendar-day mr-2" style="color:#3a7bd5;"></i>
                                Today's Appointments
                            </h3>
                            <div class="card-tools">
                                <a href="<?= BASE_URL ?>/index.php?page=appointments&action=calendar"
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-calendar-week mr-1"></i> Calendar View
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($todayAppointments)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-calendar-check fa-3x mb-3 d-block" style="color:#3a7bd5;opacity:0.3;"></i>
                                <p>No appointments today. Enjoy your day!</p>
                            </div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Time</th>
                                            <th>Patient</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($todayAppointments as $appt): ?>
                                        <tr>
                                            <td>
                                                <strong><?= formatTime($appt['appt_time']) ?></strong>
                                            </td>
                                            <td><?= htmlspecialchars($appt['patient_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars(truncate($appt['reason'] ?? 'N/A', 30), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= statusBadge($appt['status']) ?></td>
                                            <td>
                                                <a href="<?= BASE_URL ?>/index.php?page=appointments&action=detail&id=<?= $appt['id'] ?>"
                                                   class="btn btn-xs btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Upcoming -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-clock mr-2" style="color:#f7971e;"></i>
                                Upcoming
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($upcomingAppointments)): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-calendar fa-2x mb-2 d-block"></i>
                                No upcoming appointments
                            </div>
                            <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($upcomingAppointments as $appt): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= htmlspecialchars($appt['patient_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar mr-1"></i>
                                                <?= formatDate($appt['appt_date']) ?>
                                                <?= formatTime($appt['appt_time']) ?>
                                            </small>
                                        </div>
                                        <?= statusBadge($appt['status']) ?>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Doctor Info -->
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-id-card mr-2" style="color:#11998e;"></i>
                                My Profile
                            </h3>
                        </div>
                        <div class="card-body text-center">
                            <?php if (!empty($doctor['photo'])): ?>
                            <img src="<?= BASE_URL ?>/public/uploads/doctor_photos/<?= htmlspecialchars($doctor['photo'], ENT_QUOTES, 'UTF-8') ?>"
                                 class="img-circle elevation-2 mb-3"
                                 style="width:80px;height:80px;object-fit:cover;">
                            <?php else: ?>
                            <div class="img-circle elevation-2 mb-3 mx-auto d-flex align-items-center justify-content-center"
                                 style="width:80px;height:80px;background:linear-gradient(135deg,#3a7bd5,#00d2ff);color:white;font-size:28px;font-weight:bold;">
                                <?= strtoupper(substr($doctor['name'] ?? 'D', 0, 1)) ?>
                            </div>
                            <?php endif; ?>
                            <h5 class="font-weight-bold"><?= htmlspecialchars($doctor['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></h5>
                            <p class="text-muted"><?= htmlspecialchars($doctor['specialization_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="text-success font-weight-bold">
                                $<?= number_format($doctor['consultation_fee'] ?? 0, 2) ?> / session
                            </p>
                            <a href="<?= BASE_URL ?>/index.php?page=doctors&action=edit&id=<?= $doctor['id'] ?? 0 ?>"
                               class="btn btn-primary btn-sm btn-block">
                                <i class="fas fa-edit mr-1"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>