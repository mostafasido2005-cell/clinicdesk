<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('admin', 'doctor');
$pageTitle = 'Edit Doctor — ' . APP_NAME;
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
$availableDaysArr = explode(',', $doctor['available_days'] ?? '');
?>
<div class="wrapper">
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 font-weight-bold">
                <i class="fas fa-user-edit mr-2" style="color:#3a7bd5;"></i> Edit Doctor Profile
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
                                <i class="fas fa-user-md mr-2"></i>
                                <?= htmlspecialchars($doctor['name'], ENT_QUOTES, 'UTF-8') ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?= BASE_URL ?>/index.php?page=doctors&action=update"
                                  enctype="multipart/form-data">
                                <?= CSRF::field() ?>
                                <input type="hidden" name="doctor_id" value="<?= $doctor['id'] ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Specialization</label>
                                            <select name="specialization_id" class="form-control">
                                                <?php foreach ($specializations as $spec): ?>
                                                <option value="<?= $spec['id'] ?>"
                                                    <?= $spec['id'] == $doctor['specialization_id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($spec['name'], ENT_QUOTES, 'UTF-8') ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Consultation Fee ($)</label>
                                            <input type="number" name="consultation_fee" class="form-control"
                                                   step="0.01" min="0"
                                                   value="<?= $doctor['consultation_fee'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Bio</label>
                                            <textarea name="bio" class="form-control" rows="4"><?= htmlspecialchars($doctor['bio'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
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
                                                               <?= in_array($day, $availableDaysArr) ? 'checked' : '' ?>>
                                                        <label class="custom-control-label" for="day_<?= $day ?>">
                                                            <?= $day ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Profile Photo</label>
                                            <?php if (!empty($doctor['photo'])): ?>
                                            <div class="mb-2">
                                                <img src="<?= BASE_URL ?>/public/uploads/doctor_photos/<?= htmlspecialchars($doctor['photo'], ENT_QUOTES, 'UTF-8') ?>"
                                                     class="img-circle" style="width:60px;height:60px;object-fit:cover;">
                                            </div>
                                            <?php endif; ?>
                                            <input type="file" name="photo" class="form-control-file"
                                                   accept="image/jpeg,image/png">
                                            <small class="text-muted">JPEG/PNG, max 1MB</small>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Save Changes
                                </button>
                                <a href="<?= BASE_URL ?>/index.php?page=doctors" class="btn btn-secondary ml-2">Cancel</a>
                            </form>
                        </div>
                    </div>

                    <!-- Ratings -->
                    <?php if (!empty($ratings)): ?>
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-star mr-2" style="color:#ffd200;"></i>
                                Patient Ratings
                                <span class="badge badge-warning ml-2">
                                    <?= $ratingStats['avg'] ?> / 5 (<?= $ratingStats['total'] ?> reviews)
                                </span>
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($ratings as $r): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= htmlspecialchars($r['patient_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                                        <span>
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star" style="color:<?= $i <= $r['rating'] ? '#ffd200' : '#ddd' ?>;font-size:12px;"></i>
                                            <?php endfor; ?>
                                        </span>
                                    </div>
                                    <?php if ($r['comment']): ?>
                                    <p class="text-muted mb-0 mt-1"><?= htmlspecialchars($r['comment'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <?php endif; ?>
                                    <small class="text-muted"><?= formatDate($r['created_at']) ?></small>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>