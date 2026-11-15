<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('doctor');
$pageTitle = 'Add Prescription — ' . APP_NAME;
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="wrapper">
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 font-weight-bold">
                <i class="fas fa-file-medical mr-2" style="color:#3a7bd5;"></i> Add Prescription
            </h1>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <!-- Appointment Info -->
                    <div class="alert alert-info">
                        <strong><i class="fas fa-info-circle mr-2"></i>Appointment:</strong>
                        Patient: <?= htmlspecialchars($appointment['patient_name'], ENT_QUOTES, 'UTF-8') ?> |
                        Date: <?= formatDate($appointment['appt_date']) ?> |
                        Time: <?= formatTime($appointment['appt_time']) ?>
                    </div>

                    <div class="card">
                        <div class="card-header" style="background:linear-gradient(135deg,#11998e,#38ef7d);">
                            <h3 class="card-title text-white">
                                <i class="fas fa-file-medical mr-2"></i> Prescription Details
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?= BASE_URL ?>/index.php?page=prescriptions&action=store"
                                  enctype="multipart/form-data">
                                <?= CSRF::field() ?>
                                <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">

                                <div class="form-group">
                                    <label>Diagnosis <span class="text-danger">*</span></label>
                                    <textarea name="diagnosis" class="form-control" rows="3" required
                                              placeholder="Enter diagnosis..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Medications <span class="text-danger">*</span></label>
                                    <textarea name="medications" class="form-control" rows="5" required
                                              placeholder="Enter medications (one per line)..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Additional Notes</label>
                                    <textarea name="notes" class="form-control" rows="3"
                                              placeholder="Optional notes..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Prescription PDF (optional)</label>
                                    <input type="file" name="prescription_file" class="form-control-file"
                                           accept="application/pdf">
                                    <small class="text-muted">PDF only, max 3MB</small>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Save Prescription
                                </button>
                                <a href="<?= BASE_URL ?>/index.php?page=appointments&action=detail&id=<?= $appointment['id'] ?>"
                                   class="btn btn-secondary ml-2">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>