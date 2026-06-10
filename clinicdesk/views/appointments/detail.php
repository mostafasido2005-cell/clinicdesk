<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
require_once __DIR__ . '/../../models/PrescriptionModel.php';
Auth::requireRole('admin', 'doctor', 'patient');
$pageTitle = 'Appointment Detail — ' . APP_NAME;
$role = Auth::role();
$prescription = (new PrescriptionModel())->findByAppointmentId($appointment['id']);
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
                        <i class="fas fa-calendar-check mr-2" style="color:#3a7bd5;"></i>
                        Appointment #<?= $appointment['id'] ?>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>/index.php?page=appointments">Appointments</a>
                        </li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>
            <div class="row">
                <div class="col-md-8">
                    <!-- Appointment Info -->
                    <div class="card">
                        <div class="card-header" style="background:linear-gradient(135deg,#3a7bd5,#00d2ff);">
                            <h3 class="card-title text-white">
                                <i class="fas fa-info-circle mr-2"></i> Appointment Details
                            </h3>
                            <div class="card-tools">
                                <?= statusBadge($appointment['status']) ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-user mr-2 text-primary"></i>Patient:</strong><br>
                                        <?= htmlspecialchars($appointment['patient_name'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <p><strong><i class="fas fa-user-md mr-2 text-success"></i>Doctor:</strong><br>
                                        Dr. <?= htmlspecialchars($appointment['doctor_name'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <p><strong><i class="fas fa-stethoscope mr-2 text-info"></i>Specialization:</strong><br>
                                        <?= htmlspecialchars($appointment['specialization_name'], ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-calendar mr-2 text-warning"></i>Date:</strong><br>
                                        <?= formatDate($appointment['appt_date']) ?></p>
                                    <p><strong><i class="fas fa-clock mr-2 text-danger"></i>Time:</strong><br>
                                        <?= formatTime($appointment['appt_time']) ?></p>
                                    <p><strong><i class="fas fa-dollar-sign mr-2 text-success"></i>Fee:</strong><br>
                                        $<?= number_format($appointment['consultation_fee'] ?? 0, 2) ?></p>
                                </div>
                                <div class="col-12">
                                    <p><strong><i class="fas fa-comment mr-2 text-secondary"></i>Reason:</strong><br>
                                        <?= htmlspecialchars($appointment['reason'] ?? 'Not specified', ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                                <?php if ($appointment['doctor_notes']): ?>
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <strong><i class="fas fa-notes-medical mr-2"></i>Doctor Notes:</strong><br>
                                        <?= htmlspecialchars($appointment['doctor_notes'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if ($appointment['cancellation_reason']): ?>
                                <div class="col-12">
                                    <div class="alert alert-danger">
                                        <strong><i class="fas fa-times-circle mr-2"></i>Cancellation Reason:</strong><br>
                                        <?= htmlspecialchars($appointment['cancellation_reason'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Prescription -->
                    <?php if ($prescription): ?>
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-file-medical mr-2" style="color:#11998e;"></i> Prescription
                            </h3>
                            <div class="card-tools">
                                <?php if ($prescription['file_path']): ?>
                                <a href="<?= BASE_URL ?>/index.php?page=prescriptions&action=download&id=<?= $prescription['id'] ?>"
                                   class="btn btn-sm btn-success mr-1">
                                    <i class="fas fa-download mr-1"></i> Download PDF
                                </a>
                                <?php endif; ?>
                                <a href="<?= BASE_URL ?>/index.php?page=prescriptions&action=print&id=<?= $prescription['id'] ?>"
                                   class="btn btn-sm btn-secondary" target="_blank">
                                    <i class="fas fa-print mr-1"></i> Print
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <p><strong>Diagnosis:</strong><br>
                                <?= htmlspecialchars($prescription['diagnosis'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p><strong>Medications:</strong><br>
                                <?= nl2br(htmlspecialchars($prescription['medications'], ENT_QUOTES, 'UTF-8')) ?></p>
                            <?php if ($prescription['notes']): ?>
                            <p><strong>Notes:</strong><br>
                                <?= htmlspecialchars($prescription['notes'], ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Rating -->
                    <?php if ($role === 'patient' && $appointment['status'] === 'completed'): ?>
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-star mr-2" style="color:#ffd200;"></i>
                                <?= $hasRated ? 'Your Rating' : 'Rate This Appointment' ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if ($hasRated): ?>
                            <p class="text-muted">You have already rated this appointment. Thank you!</p>
                            <?php else: ?>
                            <form method="POST" action="<?= BASE_URL ?>/index.php?page=ratings&action=store">
                                <?= CSRF::field() ?>
                                <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">
                                <div class="form-group">
                                    <label>Rating</label>
                                    <div class="star-rating">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>">
                                        <label for="star<?= $i ?>" style="font-size:24px;color:#ffd200;cursor:pointer;">★</label>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Comment (optional)</label>
                                    <textarea name="comment" class="form-control" rows="3"
                                              placeholder="Share your experience..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-star mr-1"></i> Submit Rating
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Actions -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-cogs mr-2"></i> Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if ($role === 'doctor' || $role === 'admin'): ?>

                            <?php if ($appointment['status'] === 'pending'): ?>
                            <form method="POST" action="<?= BASE_URL ?>/index.php?page=appointments&action=update_status" class="mb-2">
                                <?= CSRF::field() ?>
                                <input type="hidden" name="id" value="<?= $appointment['id'] ?>">
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="btn btn-info btn-block">
                                    <i class="fas fa-check mr-1"></i> Confirm Appointment
                                </button>
                            </form>
                            <?php endif; ?>

                            <?php if ($appointment['status'] === 'confirmed'): ?>
                            <form method="POST" action="<?= BASE_URL ?>/index.php?page=appointments&action=update_status" class="mb-2">
                                <?= CSRF::field() ?>
                                <input type="hidden" name="id" value="<?= $appointment['id'] ?>">
                                <input type="hidden" name="status" value="completed">
                                <div class="form-group">
                                    <textarea name="doctor_notes" class="form-control mb-2"
                                              rows="3" placeholder="Add notes (optional)..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-check-double mr-1"></i> Mark as Completed
                                </button>
                            </form>
                            <?php endif; ?>

                            <?php if ($appointment['status'] === 'completed' && !$prescription && $role === 'doctor'): ?>
                            <a href="<?= BASE_URL ?>/index.php?page=prescriptions&action=add&appointment_id=<?= $appointment['id'] ?>"
                               class="btn btn-primary btn-block mb-2">
                                <i class="fas fa-file-medical mr-1"></i> Add Prescription
                            </a>
                            <?php endif; ?>

                            <?php endif; ?>

                            <?php if (in_array($appointment['status'], ['pending', 'confirmed'])): ?>
                            <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#cancelModal">
                                <i class="fas fa-times mr-1"></i> Cancel Appointment
                            </button>
                            <?php endif; ?>

                            <a href="<?= BASE_URL ?>/index.php?page=appointments"
                               class="btn btn-secondary btn-block mt-2">
                                <i class="fas fa-arrow-left mr-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;">
            <div class="modal-header" style="background:linear-gradient(135deg,#eb3349,#f45c43);">
                <h5 class="modal-title text-white">
                    <i class="fas fa-times-circle mr-2"></i> Cancel Appointment
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">×</button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>/index.php?page=appointments&action=update_status">
                <?= CSRF::field() ?>
                <input type="hidden" name="id" value="<?= $appointment['id'] ?>">
                <input type="hidden" name="status" value="cancelled">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Reason for Cancellation <span class="text-danger">*</span></label>
                        <textarea name="cancellation_reason" class="form-control" rows="4"
                                  placeholder="Please provide a reason (min 10 characters)..."
                                  required minlength="10"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times mr-1"></i> Confirm Cancellation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>