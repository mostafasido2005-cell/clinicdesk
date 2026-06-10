<?php
require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel {

    public function findById(int $id): ?array {
        return $this->fetchOne(
            'SELECT * FROM users WHERE id = ?',
            'i', [$id]
        );
    }

    public function findByEmail(string $email): ?array {
        return $this->fetchOne(
            'SELECT * FROM users WHERE email = ?',
            's', [$email]
        );
    }

    public function create(array $data): int {
        $this->execute(
            'INSERT INTO users (name, email, password, role, phone, is_active, first_login) 
             VALUES (?, ?, ?, ?, ?, 1, 1)',
            'sssss',
            [
                $data['name'],
                $data['email'],
                $data['password'],
                $data['role'],
                $data['phone'] ?? null,
            ]
        );
        return $this->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $result = $this->execute(
            'UPDATE users SET name = ?, phone = ?, avatar = ? WHERE id = ?',
            'sssi',
            [
                $data['name'],
                $data['phone'] ?? null,
                $data['avatar'] ?? null,
                $id,
            ]
        );
        return (bool) $result;
    }

    public function updatePassword(int $id, string $newHash): bool {
        $result = $this->execute(
            'UPDATE users SET password = ?, first_login = 0 WHERE id = ?',
            'si', [$newHash, $id]
        );
        return (bool) $result;
    }

    public function getAllPaginated(int $offset, int $perPage, string $role = ''): array {
        if ($role) {
            return $this->fetchAll(
                'SELECT * FROM users WHERE role = ? ORDER BY created_at DESC LIMIT ? OFFSET ?',
                'sii', [$role, $perPage, $offset]
            );
        }
        return $this->fetchAll(
            'SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?',
            'ii', [$perPage, $offset]
        );
    }

    public function countAll(string $role = ''): int {
        if ($role) {
            $row = $this->fetchOne(
                'SELECT COUNT(*) as total FROM users WHERE role = ?',
                's', [$role]
            );
        } else {
            $row = $this->fetchOne('SELECT COUNT(*) as total FROM users');
        }
        return (int)($row['total'] ?? 0);
    }

    public function toggleActive(int $id): bool {
        $result = $this->execute(
            'UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?',
            'i', [$id]
        );
        return (bool) $result;
    }

    public function countByRole(): array {
        return $this->fetchAll(
            'SELECT role, COUNT(*) as total FROM users GROUP BY role'
        );
    }

    public function search(string $keyword, string $role = '', int $offset = 0, int $perPage = 10): array {
        $keyword = '%' . $keyword . '%';
        if ($role) {
            return $this->fetchAll(
                'SELECT * FROM users WHERE role = ? AND (name LIKE ? OR email LIKE ?) 
                 ORDER BY created_at DESC LIMIT ? OFFSET ?',
                'sssii', [$role, $keyword, $keyword, $perPage, $offset]
            );
        }
        return $this->fetchAll(
            'SELECT * FROM users WHERE name LIKE ? OR email LIKE ? 
             ORDER BY created_at DESC LIMIT ? OFFSET ?',
            'ssii', [$keyword, $keyword, $perPage, $offset]
        );
    }

    public function countSearch(string $keyword, string $role = ''): int {
        $keyword = '%' . $keyword . '%';
        if ($role) {
            $row = $this->fetchOne(
                'SELECT COUNT(*) as total FROM users WHERE role = ? AND (name LIKE ? OR email LIKE ?)',
                'sss', [$role, $keyword, $keyword]
            );
        } else {
            $row = $this->fetchOne(
                'SELECT COUNT(*) as total FROM users WHERE name LIKE ? OR email LIKE ?',
                'ss', [$keyword, $keyword]
            );
        }
        return (int)($row['total'] ?? 0);
    }
}