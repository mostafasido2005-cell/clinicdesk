<?php
require_once __DIR__ . '/BaseModel.php';

class DoctorModel extends BaseModel {

    public function findByUserId(int $userId): ?array {
        return $this->fetchOne(
            'SELECT d.*, u.name, u.email, u.phone, u.avatar, u.is_active,
                    s.name as specialization_name
             FROM doctors d
             JOIN users u ON d.user_id = u.id
             JOIN specializations s ON d.specialization_id = s.id
             WHERE d.user_id = ?',
            'i', [$userId]
        );
    }

    public function findById(int $id): ?array {
        return $this->fetchOne(
            'SELECT d.*, u.name, u.email, u.phone, u.avatar, u.is_active,
                    s.name as specialization_name
             FROM doctors d
             JOIN users u ON d.user_id = u.id
             JOIN specializations s ON d.specialization_id = s.id
             WHERE d.id = ?',
            'i', [$id]
        );
    }

    public function getAll(): array {
        return $this->fetchAll(
            'SELECT d.*, u.name, u.email, u.is_active,
                    s.name as specialization_name
             FROM doctors d
             JOIN users u ON d.user_id = u.id
             JOIN specializations s ON d.specialization_id = s.id
             WHERE u.is_active = 1
             ORDER BY u.name ASC'
        );
    }

    public function getAllPaginated(int $offset, int $perPage): array {
        return $this->fetchAll(
            'SELECT d.*, u.name, u.email, u.is_active,
                    s.name as specialization_name
             FROM doctors d
             JOIN users u ON d.user_id = u.id
             JOIN specializations s ON d.specialization_id = s.id
             ORDER BY u.name ASC
             LIMIT ? OFFSET ?',
            'ii', [$perPage, $offset]
        );
    }

    public function countAll(): int {
        $row = $this->fetchOne('SELECT COUNT(*) as total FROM doctors');
        return (int)($row['total'] ?? 0);
    }

    public function create(array $data): int {
        $this->execute(
            'INSERT INTO doctors (user_id, specialization_id, bio, consultation_fee, available_days, photo)
             VALUES (?, ?, ?, ?, ?, ?)',
            'iisdss',
            [
                $data['user_id'],
                $data['specialization_id'],
                $data['bio'] ?? null,
                $data['consultation_fee'] ?? 0.00,
                $data['available_days'] ?? 'Sun,Mon,Tue,Wed,Thu',
                $data['photo'] ?? null,
            ]
        );
        return $this->lastInsertId();
    }

    public function update(int $doctorId, array $data): bool {
        $result = $this->execute(
            'UPDATE doctors SET specialization_id = ?, bio = ?, consultation_fee = ?,
             available_days = ?, photo = ? WHERE id = ?',
            'isdssi',
            [
                $data['specialization_id'],
                $data['bio'] ?? null,
                $data['consultation_fee'] ?? 0.00,
                $data['available_days'] ?? 'Sun,Mon,Tue,Wed,Thu',
                $data['photo'] ?? null,
                $doctorId,
            ]
        );
        return (bool) $result;
    }

    public function getAvailableDays(int $doctorId): array {
        $row = $this->fetchOne(
            'SELECT available_days FROM doctors WHERE id = ?',
            'i', [$doctorId]
        );
        if (!$row) return [];
        return explode(',', $row['available_days']);
    }

    public function getAverageRating(int $doctorId): float {
        $row = $this->fetchOne(
            'SELECT AVG(rating) as avg_rating, COUNT(*) as total
             FROM ratings WHERE doctor_id = ?',
            'i', [$doctorId]
        );
        return round((float)($row['avg_rating'] ?? 0), 1);
    }
}