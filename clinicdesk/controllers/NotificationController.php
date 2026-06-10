<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class NotificationController {

    private NotificationModel $notificationModel;

    public function __construct() {
        $this->notificationModel = new NotificationModel();
    }

    public function index(): void {
        Auth::requireRole('admin', 'doctor', 'patient');

        $notifications = $this->notificationModel->getByUser(Auth::id(), 20);
        $this->notificationModel->markAllAsRead(Auth::id());

        $pageTitle = 'Notifications — ' . APP_NAME;
        require_once __DIR__ . '/../views/notifications/index.php';
    }

    public function markRead(): void {
        Auth::requireRole('admin', 'doctor', 'patient');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            redirect(BASE_URL . '/index.php?page=notifications');
        }

        $id = (int)($_POST['id'] ?? 0);
        $this->notificationModel->markAsRead($id, Auth::id());
        redirect(BASE_URL . '/index.php?page=notifications');
    }

    public function delete(): void {
        Auth::requireRole('admin', 'doctor', 'patient');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            redirect(BASE_URL . '/index.php?page=notifications');
        }

        $id = (int)($_POST['id'] ?? 0);
        $this->notificationModel->delete($id, Auth::id());
        redirect(BASE_URL . '/index.php?page=notifications');
    }

    public function getUnreadCount(): void {
        Auth::requireRole('admin', 'doctor', 'patient');

        header('Content-Type: application/json');
        $count = $this->notificationModel->countUnread(Auth::id());
        echo json_encode(['count' => $count]);
        exit();
    }
}