<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('admin', 'doctor');
$pageTitle = 'Edit Appointment — ' . APP_NAME;
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
                        <i class="fas fa-calendar-edit mr-2" style="color:#3a7bd5;"></i>
                        Edit Appointment #<?= $appointment['id'] ?>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>/index.php?page=appointments">Appointments</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header" style="background:linear-gradient(135deg,#3a7bd5,#00d2ff);">
                            <h3 class="card-title text-white">
                                <i class="fas fa-edit mr-2"></i> Update Appointment Status
                            </h3>
                            <div class="card-tools">
                                <?= statusBadge($appointment['status']) ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Appointment Info -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <strong><i class="fas fa-user mr-2 text-primary"></i>Patient:</strong>
                                        <?= htmlspecialchars($appointment['patient_name'], ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                    <p class="mb-1">
                                        <strong><i class="fas fa-user-md mr-2 text-success"></i>Doctor:</strong>
                                        Dr. <?= htmlspecialchars($appointment['doctor_name'], ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                    <p class="mb-1">
                                        <strong><i class="fas fa-stethoscope mr-2 text-info"></i>Specialization:</strong>
                                        <?= htmlspecialchars($appointment['specialization_name'], ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <strong><i class="fas fa-calendar mr-2 text-warning"></i>Date:</strong>
                                        <?= formatDate($appointment['appt_date']) ?>
                                    </p>
                                    <p class="mb-1">
                                        <strong><i class="fas fa-clock mr-2 text-danger"></i>Time:</strong>
                                        <?= formatTime($appointment['appt_time']) ?>
                                    </p>
                                    <p class="mb-1">
                                        <strong><i class="fas fa-info-circle mr-2"></i>Current Status:</strong>
                                        <?= statusBadge($appointment['status']) ?>
                                    </p>
                                </div>
                            </div>

                            <hr>

                            <!-- Update Status Form -->
                            <form method="POST"
                                  action="<?= BASE_URL ?>/index.php?page=appointments&action=update_status">
                                <?= CSRF::field() ?>
                                <input type="hidden" name="id" value="<?= $appointment['id'] ?>">

                                <div class="form-group">
                                    <label>Update Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control" id="statusSelect" required>
                                        <?php
                                        $role = Auth::role();
                                        $statuses = $role === 'admin'
                                            ? ['pending','confirmed','completed','cancelled']
                                            : ['confirmed','completed','cancelled'];
                                        foreach ($statuses as $s):
                                        ?>
                                        <option value="<?= $s ?>"
                                            <?= $appointment['status'] === $s ? 'selected' : '' ?>>
                                            <?= ucfirst($s) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Doctor Notes</label>
                                    <textarea name="doctor_notes" class="form-control" rows="4"
                                              placeholder="Add notes about this appointment..."><?= htmlspecialchars($appointment['doctor_notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                </div>

                                <!-- Cancellation Reason -->
                                <div class="form-group" id="cancelReasonGroup" style="display:none;">
                                    <label>Cancellation Reason <span class="text-danger">*</span></label>
                                    <textarea name="cancellation_reason" class="form-control" rows="3"
                                              placeholder="Please provide a reason (min 10 characters)..."
                                              id="cancelReason" minlength="10"></textarea>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i> Update Appointment
                                    </button>
                                    <a href="<?= BASE_URL ?>/index.php?page=appointments&action=detail&id=<?= $appointment['id'] ?>"
                                       class="btn btn-secondary ml-2">
                                        <i class="fas fa-arrow-left mr-1"></i> Back
                                    </a>
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

<script>
document.getElementById('statusSelect').addEventListener('change', function() {
    const cancelGroup = document.getElementById('cancelReasonGroup');
    const cancelReason = document.getElementById('cancelReason');
    if (this.value === 'cancelled') {
        cancelGroup.style.display = 'block';
        cancelReason.required = true;
    } else {
        cancelGroup.style.display = 'none';
        cancelReason.required = false;
    }
});
</script>