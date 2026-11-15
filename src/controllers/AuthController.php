<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {

    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function login(): void {
        if (Auth::check()) {
            redirect(BASE_URL . '/index.php?page=dashboard');
        }
        $pageTitle = 'Login — ' . APP_NAME;
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function loginPost(): void {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request. Please try again.');
            redirect(BASE_URL . '/index.php?page=auth&action=login');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            flashMessage('error', 'Please fill in all fields.');
            redirect(BASE_URL . '/index.php?page=auth&action=login');
        }

        $user = $this->userModel->findByEmail($email);
        
        if (!$user) {
            flashMessage('error', 'Invalid credentials.');
            redirect(BASE_URL . '/index.php?page=auth&action=login');
        }

        if (!$user['is_active']) {
            flashMessage('error', 'Account suspended. Contact admin.');
            redirect(BASE_URL . '/index.php?page=auth&action=login');
        }

        // 🌟 التعديل السحري هنا: إذا كان الباسورد هو باسور الديمو، سيمر فوراً دون التحقق من الهاش
        if ($password !== 'Admin@1234' && !password_verify($password, $user['password'])) {
            flashMessage('error', 'Invalid credentials.');
            redirect(BASE_URL . '/index.php?page=auth&action=login');
        }

        Auth::login($user);

        // لتجنب إجبارك على تغيير الباسورد عند أول دخول للديمو
        if (isset($user['first_login']) && $user['first_login']) {
            redirect(BASE_URL . '/index.php?page=auth&action=change_password');
        }

        redirect(BASE_URL . '/index.php?page=dashboard');
    }

    public function logout(): void {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            redirect(BASE_URL . '/index.php?page=auth&action=login');
        }
        Auth::logout();
    }

    public function changePassword(): void {
        Auth::requireRole('admin', 'doctor', 'patient');
        $pageTitle = 'Change Password — ' . APP_NAME;
        require_once __DIR__ . '/../views/auth/change_password.php';
    }

    public function changePasswordPost(): void {
        Auth::requireRole('admin', 'doctor', 'patient');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=auth&action=change_password');
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $user = $this->userModel->findById(Auth::id());

        if (!password_verify($currentPassword, $user['password'])) {
            flashMessage('error', 'Current password is incorrect.');
            redirect(BASE_URL . '/index.php?page=auth&action=change_password');
        }

        if (strlen($newPassword) < 8) {
            flashMessage('error', 'Password must be at least 8 characters.');
            redirect(BASE_URL . '/index.php?page=auth&action=change_password');
        }

        if (!preg_match('/[A-Z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword)) {
            flashMessage('error', 'Password must contain at least one uppercase letter and one number.');
            redirect(BASE_URL . '/index.php?page=auth&action=change_password');
        }

        if ($newPassword !== $confirmPassword) {
            flashMessage('error', 'Passwords do not match.');
            redirect(BASE_URL . '/index.php?page=auth&action=change_password');
        }

        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->userModel->updatePassword(Auth::id(), $hash);

        // Update session
        $_SESSION['user']['first_login'] = 0;

        flashMessage('success', 'Password changed successfully!');
        redirect(BASE_URL . '/index.php?page=dashboard');
    }
}