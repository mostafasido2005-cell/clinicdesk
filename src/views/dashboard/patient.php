<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('patient');
$pageTitle = 'Patient Dashboard — ' . APP_NAME;
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
                        <i class="fas fa-home mr-2" style="color:#3a7bd5;"></i> My Dashboard
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <!-- Next Appointment Card -->
            <?php if ($nextAppointment): ?>
            <div class="card mb-4" style="background:linear-gradient(135deg,#3a7bd5,#00d2ff);color:white;border-radius:15px;">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="text-white font-weight-bold mb-1">
                                <i class="fas fa-calendar-check mr-2"></i> Next Appointment
                            </h4>
                            <h5 class="text-white mb-1"><?= htmlspecialchars($nextAppointment['doctor_name'], ENT_QUOTES, 'UTF-8') ?></h5>
                            <p class="mb-0 opacity-75"><?= htmlspecialchars($nextAppointment['specialization_name'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="mb-0">
                                <i class="fas fa-clock mr-1"></i>
                                <?= formatDate($nextAppointment['appt_date']) ?> at <?= formatTime($nextAppointment['appt_time']) ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-right">
                            <?= statusBadge($nextAppointment['status']) ?>
                            <br><br>
                            <a href="<?= BASE_URL ?>/index.php?page=appointments&action=detail&id=<?= $nextAppointment['id'] ?>"
                               class="btn btn-light btn-sm">
                                <i class="fas fa-eye mr-1"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="small-box" style="background:linear-gradient(135deg,#f7971e,#ffd200);">
                        <div class="inner"><h3><?= $totalActive ?></h3><p>Active Appointments</p></div>
                        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments" class="small-box-footer text-white">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box" style="background:linear-gradient(135deg,#11998e,#38ef7d);">
                        <div class="inner"><h3><?= $totalCompleted ?></h3><p>Completed</p></div>
                        <div class="icon"><i class="fas fa-check-double"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&status=completed" class="small-box-footer text-white">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box" style="background:linear-gradient(135deg,#3a7bd5,#00d2ff);">
                        <div class="inner"><h3><?= $totalPrescriptions ?></h3><p>Prescriptions</p></div>
                        <div class="icon"><i class="fas fa-file-medical"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=prescriptions" class="small-box-footer text-white">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body py-4">
                            <i class="fas fa-calendar-plus fa-3x mb-3" style="color:#3a7bd5;"></i>
                            <h5 class="font-weight-bold">Book Appointment</h5>
                            <p class="text-muted">Schedule a visit with a doctor</p>
                            <a href="<?= BASE_URL ?>/index.php?page=appointments&action=book"
                               class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body py-4">
                            <i class="fas fa-calendar-alt fa-3x mb-3" style="color:#f7971e;"></i>
                            <h5 class="font-weight-bold">My Appointments</h5>
                            <p class="text-muted">View all your appointments</p>
                            <a href="<?= BASE_URL ?>/index.php?page=appointments"
                               class="btn btn-warning">View All</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body py-4">
                            <i class="fas fa-file-medical fa-3x mb-3" style="color:#11998e;"></i>
                            <h5 class="font-weight-bold">My Prescriptions</h5>
                            <p class="text-muted">View and download prescriptions</p>
                            <a href="<?= BASE_URL ?>/index.php?page=prescriptions"
                               class="btn btn-success">View All</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>