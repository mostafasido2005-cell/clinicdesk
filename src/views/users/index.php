<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('admin');
$pageTitle = 'Manage Users — ' . APP_NAME;
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
                        <i class="fas fa-users mr-2" style="color:#3a7bd5;"></i> Manage Users
                    </h1>
                </div>
                <div class="col-sm-6">
                    <a href="<?= BASE_URL ?>/index.php?page=users&action=create"
                       class="btn btn-primary float-right">
                        <i class="fas fa-user-plus mr-1"></i> Add User
                    </a>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <!-- Filter Bar -->
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="<?= BASE_URL ?>/index.php" class="form-inline">
                        <input type="hidden" name="page" value="users">
                        <input type="text" name="search" class="form-control mr-2 mb-2"
                               placeholder="Search name or email..."
                               value="<?= htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <select name="role" class="form-control mr-2 mb-2">
                            <option value="">All Roles</option>
                            <option value="admin" <?= ($_GET['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="doctor" <?= ($_GET['role'] ?? '') === 'doctor' ? 'selected' : '' ?>>Doctor</option>
                            <option value="patient" <?= ($_GET['role'] ?? '') === 'patient' ? 'selected' : '' ?>>Patient</option>
                        </select>
                        <button type="submit" class="btn btn-primary mb-2 mr-2">
                            <i class="fas fa-search mr-1"></i> Search
                        </button>
                        <a href="<?= BASE_URL ?>/index.php?page=users" class="btn btn-secondary mb-2">
                            <i class="fas fa-times mr-1"></i> Clear
                        </a>
                    </form>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No users found</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($user['avatar'])): ?>
                                            <img src="<?= BASE_URL ?>/public/uploads/avatars/<?= htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8') ?>"
                                                 class="img-circle mr-2" style="width:32px;height:32px;object-fit:cover;">
                                            <?php else: ?>
                                            <div class="img-circle mr-2 d-flex align-items-center justify-content-center"
                                                 style="width:32px;height:32px;background:linear-gradient(135deg,#3a7bd5,#00d2ff);color:white;font-size:12px;font-weight:bold;">
                                                <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                            </div>
                                            <?php endif; ?>
                                            <strong><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <?php
                                        $roleColors = ['admin'=>'danger','doctor'=>'primary','patient'=>'success'];
                                        $roleColor = $roleColors[$user['role']] ?? 'secondary';
                                        ?>
                                        <span class="badge badge-<?= $roleColor ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($user['phone'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <?php if ($user['is_active']): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= formatDate($user['created_at']) ?></td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/index.php?page=users&action=edit&id=<?= $user['id'] ?>"
                                           class="btn btn-xs btn-info mr-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($user['id'] !== Auth::id()): ?>
                                        <form method="POST" action="<?= BASE_URL ?>/index.php?page=users&action=toggle"
                                              class="d-inline">
                                            <?= CSRF::field() ?>
                                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                            <button type="submit"
                                                    class="btn btn-xs <?= $user['is_active'] ? 'btn-warning' : 'btn-success' ?>"
                                                    onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-<?= $user['is_active'] ? 'ban' : 'check' ?>"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Pagination -->
                <?php if ($paginator->totalPages() > 1): ?>
                <div class="card-footer">
                    <ul class="pagination pagination-sm m-0 float-right">
                        <?php if ($paginator->hasPrev()): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=users&p=<?= $paginator->currentPage()-1 ?>&role=<?= $_GET['role'] ?? '' ?>&search=<?= $_GET['search'] ?? '' ?>">«</a>
                        </li>
                        <?php endif; ?>
                        <?php foreach ($paginator->pages() as $p): ?>
                        <li class="page-item <?= $p === $paginator->currentPage() ? 'active' : '' ?>">
                            <a class="page-link" href="?page=users&p=<?= $p ?>&role=<?= $_GET['role'] ?? '' ?>&search=<?= $_GET['search'] ?? '' ?>"><?= $p ?></a>
                        </li>
                        <?php endforeach; ?>
                        <?php if ($paginator->hasNext()): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=users&p=<?= $paginator->currentPage()+1 ?>&role=<?= $_GET['role'] ?? '' ?>&search=<?= $_GET['search'] ?? '' ?>">»</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>