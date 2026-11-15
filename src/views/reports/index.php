<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('admin');
$pageTitle = 'Reports — ' . APP_NAME;
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="wrapper">
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 font-weight-bold">
                <i class="fas fa-chart-bar mr-2" style="color:#3a7bd5;"></i> Reports
            </h1>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <!-- Filter Form -->
            <div class="card">
                <div class="card-header" style="background:linear-gradient(135deg,#3a7bd5,#00d2ff);">
                    <h3 class="card-title text-white">
                        <i class="fas fa-filter mr-2"></i> Filter Report
                    </h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= BASE_URL ?>/index.php" class="row">
                        <input type="hidden" name="page" value="reports">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" required
                                       value="<?= htmlspecialchars($_GET['start_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control" required
                                       value="<?= htmlspecialchars($_GET['end_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Doctor</label>
                                <select name="doctor_id" class="form-control">
                                    <option value="">All Doctors</option>
                                    <?php foreach ($doctors as $doc): ?>
                                    <option value="<?= $doc['id'] ?>"
                                        <?= ($_GET['doctor_id'] ?? '') == $doc['id'] ? 'selected' : '' ?>>
                                        Dr. <?= htmlspecialchars($doc['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <?php foreach (['pending','confirmed','completed','cancelled'] as $s): ?>
                                    <option value="<?= $s ?>" <?= ($_GET['status'] ?? '') === $s ? 'selected' : '' ?>>
                                        <?= ucfirst($s) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-search mr-1"></i> Generate Report
                            </button>
                            <?php if ($hasSearch && !empty($results)): ?>
                            <a href="<?= BASE_URL ?>/index.php?page=reports&start_date=<?= urlencode($_GET['start_date'] ?? '') ?>&end_date=<?= urlencode($_GET['end_date'] ?? '') ?>&doctor_id=<?= urlencode($_GET['doctor_id'] ?? '') ?>&status=<?= urlencode($_GET['status'] ?? '') ?>&export=csv"
                               class="btn btn-success">
                                <i class="fas fa-file-csv mr-1"></i> Export CSV
                            </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($hasSearch): ?>
            <!-- Summary -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary"><i class="fas fa-list"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total</span>
                            <span class="info-box-number"><?= count($results) ?></span>
                        </div>
                    </div>
                </div>
                <?php foreach ($summary as $status => $count): ?>
                <?php
                $colors = ['pending'=>'warning','confirmed'=>'info','completed'=>'success','cancelled'=>'danger'];
                $color = $colors[$status] ?? 'secondary';
                ?>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-<?= $color ?>">
                            <i class="fas fa-calendar"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text"><?= ucfirst($status) ?></span>
                            <span class="info-box-number"><?= $count ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Results Table -->
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title font-weight-bold">
                        Report Results
                        <span class="badge badge-primary ml-2"><?= count($results) ?> records</span>
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover data-table">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Specialization</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($results)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        No records found for the selected criteria
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($results as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['patient_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>Dr. <?= htmlspecialchars($row['doctor_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['specialization_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= formatDate($row['appt_date']) ?></td>
                                    <td><?= formatTime($row['appt_time']) ?></td>
                                    <td><?= statusBadge($row['status']) ?></td>
                                    <td><?= htmlspecialchars(truncate($row['reason'] ?? '—', 40), ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>