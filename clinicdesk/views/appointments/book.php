<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('patient');
$pageTitle = 'Book Appointment — ' . APP_NAME;
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="wrapper">
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 font-weight-bold">
                <i class="fas fa-calendar-plus mr-2" style="color:#3a7bd5;"></i> Book Appointment
            </h1>
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
                                <i class="fas fa-calendar-plus mr-2"></i> New Appointment
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?= BASE_URL ?>/index.php?page=appointments&action=store">
                                <?= CSRF::field() ?>
                                <div class="form-group">
                                    <label>Select Doctor <span class="text-danger">*</span></label>
                                    <select name="doctor_id" class="form-control" id="doctorSelect" required>
                                        <option value="">— Choose a doctor —</option>
                                        <?php foreach ($doctors as $doc): ?>
                                        <option value="<?= $doc['id'] ?>"
                                                data-days="<?= htmlspecialchars($doc['available_days'], ENT_QUOTES, 'UTF-8') ?>"
                                                data-fee="<?= $doc['consultation_fee'] ?>"
                                                data-spec="<?= htmlspecialchars($doc['specialization_name'], ENT_QUOTES, 'UTF-8') ?>">
                                            Dr. <?= htmlspecialchars($doc['name'], ENT_QUOTES, 'UTF-8') ?>
                                            — <?= htmlspecialchars($doc['specialization_name'], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Doctor Info -->
                                <div id="doctorInfo" class="alert alert-info" style="display:none;">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong id="infoSpec"></strong> |
                                    Available: <strong id="infoDays"></strong> |
                                    Fee: <strong>$<span id="infoFee"></span></strong>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Date <span class="text-danger">*</span></label>
                                            <input type="date" name="appt_date" class="form-control"
                                                   id="apptDate" required
                                                   min="<?= date('Y-m-d') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Time Slot <span class="text-danger">*</span></label>
                                            <select name="appt_time" class="form-control" required>
                                                <option value="">— Select time —</option>
                                                <?php foreach ($timeSlots as $slot): ?>
                                                <option value="<?= $slot ?>">
                                                    <?= date('h:i A', strtotime($slot)) ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Reason for Visit</label>
                                    <textarea name="reason" class="form-control" rows="3"
                                              placeholder="Briefly describe your symptoms or reason..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-calendar-check mr-2"></i> Book Appointment
                                </button>
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
document.getElementById('doctorSelect').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const info = document.getElementById('doctorInfo');
    if (this.value) {
        document.getElementById('infoSpec').textContent = opt.dataset.spec;
        document.getElementById('infoDays').textContent = opt.dataset.days;
        document.getElementById('infoFee').textContent = parseFloat(opt.dataset.fee).toFixed(2);
        info.style.display = 'block';
    } else {
        info.style.display = 'none';
    }
});
</script>