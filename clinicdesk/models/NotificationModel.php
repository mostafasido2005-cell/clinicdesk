<?php
require_once __DIR__ . '/BaseModel.php';

class NotificationModel extends BaseModel {

    public function create(int $userId, string $title, string $message): int {
        $this->execute(
            'INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)',
            'iss', [$userId, $title, $message]
        );
        return $this->lastInsertId();
    }

    public function getByUser(int $userId, int $limit = 10): array {
        return $this->fetchAll(
            'SELECT * FROM notifications WHERE user_id = ? 
             ORDER BY created_at DESC LIMIT ?',
            'ii', [$userId, $limit]
        );
    }

    public function countUnread(int $userId): int {
        $row = $this->fetchOne(
            'SELECT COUNT(*) as total FROM notifications 
             WHERE user_id = ? AND is_read = 0',
            'i', [$userId]
        );
        return (int)($row['total'] ?? 0);
    }

    public function markAsRead(int $id, int $userId): bool {
        $result = $this->execute(
            'UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?',
            'ii', [$id, $userId]
        );
        return (bool) $result;
    }

    public function markAllAsRead(int $userId): bool {
        $result = $this->execute(
            'UPDATE notifications SET is_read = 1 WHERE user_id = ?',
            'i', [$userId]
        );
        return (bool) $result;
    }

    public function delete(int $id, int $userId): bool {
        $result = $this->execute(
            'DELETE FROM notifications WHERE id = ? AND user_id = ?',
            'ii', [$id, $userId]
        );
        return (bool) $result;
    }
}