<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('admin');
$pageTitle = 'Create User — ' . APP_NAME;
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="wrapper">
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 font-weight-bold">
                <i class="fas fa-user-plus mr-2" style="color:#3a7bd5;"></i> Create User
            </h1>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header" style="background:linear-gradient(135deg,#3a7bd5,#00d2ff);">
                            <h3 class="card-title text-white">
                                <i class="fas fa-user-plus mr-2"></i> New User
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?= BASE_URL ?>/index.php?page=users&action=store">
                                <?= CSRF::field() ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Full Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Password <span class="text-danger">*</span></label>
                                            <input type="password" name="password" class="form-control" required minlength="8">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input type="text" name="phone" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Role <span class="text-danger">*</span></label>
                                            <select name="role" class="form-control" id="roleSelect" required>
                                                <option value="patient">Patient</option>
                                                <option value="doctor">Doctor</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Doctor Fields -->
                                <div id="doctorFields" style="display:none;">
                                    <hr>
                                    <h5 class="font-weight-bold text-primary">
                                        <i class="fas fa-user-md mr-2"></i> Doctor Details
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Specialization</label>
                                                <select name="specialization_id" class="form-control">
                                                    <?php foreach ($specializations as $spec): ?>
                                                    <option value="<?= $spec['id'] ?>">
                                                        <?= htmlspecialchars($spec['name'], ENT_QUOTES, 'UTF-8') ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Consultation Fee ($)</label>
                                                <input type="number" name="consultation_fee"
                                                       class="form-control" step="0.01" min="0" value="0">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>Bio</label>
                                                <textarea name="bio" class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>Available Days</label>
                                                <div class="row">
                                                    <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day): ?>
                                                    <div class="col-auto">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input"
                                                                   id="day_<?= $day ?>"
                                                                   name="available_days[]"
                                                                   value="<?= $day ?>"
                                                                   <?= in_array($day, ['Sun','Mon','Tue','Wed','Thu']) ? 'checked' : '' ?>>
                                                            <label class="custom-control-label" for="day_<?= $day ?>">
                                                                <?= $day ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i> Create User
                                    </button>
                                    <a href="<?= BASE_URL ?>/index.php?page=users"
                                       class="btn btn-secondary ml-2">Cancel</a>
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
document.getElementById('roleSelect').addEventListener('change', function() {
    document.getElementById('doctorFields').style.display =
        this.value === 'doctor' ? 'block' : 'none';
});
</script>