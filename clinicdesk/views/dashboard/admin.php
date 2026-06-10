<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('admin');
$pageTitle = 'Admin Dashboard — ' . APP_NAME;
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
                        <i class="fas fa-tachometer-alt mr-2" style="color:#3a7bd5;"></i>
                        Admin Dashboard
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
                            <h3><?= $userCounts['patient'] ?? 0 ?></h3>
                            <p>Total Patients</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=users&role=patient"
                           class="small-box-footer text-white">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box" style="background:linear-gradient(135deg,#11998e,#38ef7d);">
                        <div class="inner">
                            <h3><?= $totalDoctors ?? 0 ?></h3>
                            <p>Total Doctors</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-md"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=doctors"
                           class="small-box-footer text-white">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box" style="background:linear-gradient(135deg,#f7971e,#ffd200);">
                        <div class="inner">
                            <h3><?= $appointmentsToday ?? 0 ?></h3>
                            <p>Appointments Today</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-day"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments"
                           class="small-box-footer text-white">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box" style="background:linear-gradient(135deg,#eb3349,#f45c43);">
                        <div class="inner">
                            <h3><?= $weeklyStats['pending'] ?? 0 ?></h3>
                            <p>Pending This Week</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&status=pending"
                           class="small-box-footer text-white">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Status Cards Row -->
            <div class="row">
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning elevation-1">
                            <i class="fas fa-hourglass-half"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pending</span>
                            <span class="info-box-number"><?= $statusCounts['pending'] ?? 0 ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info elevation-1">
                            <i class="fas fa-check"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Confirmed</span>
                            <span class="info-box-number"><?= $statusCounts['confirmed'] ?? 0 ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-success elevation-1">
                            <i class="fas fa-check-double"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Completed</span>
                            <span class="info-box-number"><?= $statusCounts['completed'] ?? 0 ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger elevation-1">
                            <i class="fas fa-times"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Cancelled</span>
                            <span class="info-box-number"><?= $statusCounts['cancelled'] ?? 0 ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Chart -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-chart-bar mr-2" style="color:#3a7bd5;"></i>
                                Appointments — Last 14 Days
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="appointmentsChart" height="120"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-bolt mr-2" style="color:#f7971e;"></i>
                                Quick Actions
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                <a href="<?= BASE_URL ?>/index.php?page=users&action=create"
                                   class="list-group-item list-group-item-action">
                                    <i class="fas fa-user-plus mr-3 text-primary"></i> Add New User
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?page=doctors"
                                   class="list-group-item list-group-item-action">
                                    <i class="fas fa-user-md mr-3 text-success"></i> Manage Doctors
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?page=appointments"
                                   class="list-group-item list-group-item-action">
                                    <i class="fas fa-calendar-alt mr-3 text-warning"></i> View Appointments
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?page=reports"
                                   class="list-group-item list-group-item-action">
                                    <i class="fas fa-chart-bar mr-3 text-danger"></i> Generate Report
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?page=doctors&action=specializations"
                                   class="list-group-item list-group-item-action">
                                    <i class="fas fa-stethoscope mr-3 text-info"></i> Specializations
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Appointments -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-history mr-2" style="color:#3a7bd5;"></i>
                                Recent Appointments
                            </h3>
                            <div class="card-tools">
                                <a href="<?= BASE_URL ?>/index.php?page=appointments"
                                   class="btn btn-sm btn-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th>Specialization</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($recentAppointments)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-calendar-times fa-2x mb-2 d-block"></i>
                                                No appointments yet
                                            </td>
                                        </tr>
                                        <?php else: ?>
                                        <?php foreach ($recentAppointments as $appt): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($appt['patient_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                                            </td>
                                            <td><?= htmlspecialchars($appt['doctor_name'], ENT_QUOTES, 'UTF-8') ?></td>
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
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

<script>
const ctx = document.getElementById('appointmentsChart').getContext('2d');
const labels = <?= json_encode($chartLabels ?? []) ?>;
const values = <?= json_encode($chartValues ?? []) ?>;

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Appointments',
            data: values,
            backgroundColor: 'rgba(58,123,213,0.7)',
            borderColor: '#3a7bd5',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});
</script>