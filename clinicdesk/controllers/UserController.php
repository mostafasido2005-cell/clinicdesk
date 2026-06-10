<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../core/Paginator.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/SpecializationModel.php';

class UserController {

    private UserModel $userModel;
    private DoctorModel $doctorModel;
    private SpecializationModel $specializationModel;

    public function __construct() {
        $this->userModel           = new UserModel();
        $this->doctorModel         = new DoctorModel();
        $this->specializationModel = new SpecializationModel();
    }

    public function index(): void {
        Auth::requireRole('admin');

        $page     = max(1, (int)($_GET['p'] ?? 1));
        $role     = $_GET['role'] ?? '';
        $search   = trim($_GET['search'] ?? '');

        if ($search) {
            $total = $this->userModel->countSearch($search, $role);
            $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);
            $users = $this->userModel->search($search, $role, $paginator->offset(), ITEMS_PER_PAGE);
        } else {
            $total = $this->userModel->countAll($role);
            $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);
            $users = $this->userModel->getAllPaginated($paginator->offset(), ITEMS_PER_PAGE, $role);
        }

        $pageTitle = 'Manage Users — ' . APP_NAME;
        require_once __DIR__ . '/../views/users/index.php';
    }

    public function create(): void {
        Auth::requireRole('admin');
        $specializations = $this->specializationModel->getAll();
        $pageTitle = 'Create User — ' . APP_NAME;
        require_once __DIR__ . '/../views/users/create.php';
    }

    public function store(): void {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=users&action=create');
        }

        $name     = trim($_POST['name'] ?? '');
        $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $role     = $_POST['role'] ?? 'patient';
        $phone    = trim($_POST['phone'] ?? '');

        if (!$name || !$email || !$password) {
            flashMessage('error', 'Please fill in all required fields.');
            redirect(BASE_URL . '/index.php?page=users&action=create');
        }

        if (!in_array($role, ['admin', 'doctor', 'patient'])) {
            flashMessage('error', 'Invalid role.');
            redirect(BASE_URL . '/index.php?page=users&action=create');
        }

        if ($this->userModel->findByEmail($email)) {
            flashMessage('error', 'Email already exists.');
            redirect(BASE_URL . '/index.php?page=users&action=create');
        }

        $hash   = password_hash($password, PASSWORD_BCRYPT);
        $userId = $this->userModel->create([
            'name'     => $name,
            'email'    => $email,
            'password' => $hash,
            'role'     => $role,
            'phone'    => $phone,
        ]);

        // If doctor, create doctor record too
        if ($role === 'doctor') {
            $availableDays = isset($_POST['available_days']) 
                ? implode(',', $_POST['available_days']) 
                : 'Sun,Mon,Tue,Wed,Thu';

            $this->doctorModel->create([
                'user_id'           => $userId,
                'specialization_id' => (int)($_POST['specialization_id'] ?? 1),
                'bio'               => trim($_POST['bio'] ?? ''),
                'consultation_fee'  => (float)($_POST['consultation_fee'] ?? 0),
                'available_days'    => $availableDays,
            ]);
        }

        flashMessage('success', 'User created successfully!');
        redirect(BASE_URL . '/index.php?page=users');
    }

    public function edit(): void {
        Auth::requireRole('admin');

        $id   = (int)($_GET['id'] ?? 0);
        $user = $this->userModel->findById($id);

        if (!$user) {
            flashMessage('error', 'User not found.');
            redirect(BASE_URL . '/index.php?page=users');
        }

        $pageTitle = 'Edit User — ' . APP_NAME;
        require_once __DIR__ . '/../views/users/edit.php';
    }

    public function update(): void {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=users');
        }

        $id   = (int)($_POST['id'] ?? 0);
        $user = $this->userModel->findById($id);

        if (!$user) {
            flashMessage('error', 'User not found.');
            redirect(BASE_URL . '/index.php?page=users');
        }

        $name  = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        // Handle avatar upload
        $avatar = $user['avatar'];
        if (!empty($_FILES['avatar']['name'])) {
            $uploaded = $this->handleAvatarUpload($_FILES['avatar']);
            if ($uploaded === false) {
                flashMessage('error', 'Invalid image file.');
                redirect(BASE_URL . '/index.php?page=users&action=edit&id=' . $id);
            }
            // Delete old avatar
            if ($avatar && file_exists(UPLOAD_AVATARS . $avatar)) {
                unlink(UPLOAD_AVATARS . $avatar);
            }
            $avatar = $uploaded;
        }

        $this->userModel->update($id, [
            'name'   => $name,
            'phone'  => $phone,
            'avatar' => $avatar,
        ]);

        flashMessage('success', 'User updated successfully!');
        redirect(BASE_URL . '/index.php?page=users');
    }

    public function toggleActive(): void {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=users');
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id === Auth::id()) {
            flashMessage('error', 'You cannot deactivate your own account.');
            redirect(BASE_URL . '/index.php?page=users');
        }

        $this->userModel->toggleActive($id);
        flashMessage('success', 'User status updated.');
        redirect(BASE_URL . '/index.php?page=users');
    }

    public function resetPassword(): void {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=users');
        }

        $id       = (int)($_POST['id'] ?? 0);
        $password = $_POST['new_password'] ?? '';

        if (strlen($password) < 8) {
            flashMessage('error', 'Password must be at least 8 characters.');
            redirect(BASE_URL . '/index.php?page=users&action=edit&id=' . $id);
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $this->userModel->updatePassword($id, $hash);

        flashMessage('success', 'Password reset successfully!');
        redirect(BASE_URL . '/index.php?page=users&action=edit&id=' . $id);
    }

    private function handleAvatarUpload(array $file): string|false {
        if ($file['error'] !== UPLOAD_ERR_OK) return false;
        if ($file['size'] > MAX_AVATAR_SIZE) return false;

        $imageInfo = getimagesize($file['tmp_name']);
        if (!$imageInfo) return false;

        $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
        if (!in_array($imageInfo[2], $allowedTypes)) return false;

        $ext      = $imageInfo[2] === IMAGETYPE_JPEG ? 'jpg' : 'png';
        $filename = 'avatar_' . uniqid() . '.' . $ext;

        if (!move_uploaded_file($file['tmp_name'], UPLOAD_AVATARS . $filename)) return false;

        return $filename;
    }
}