<?php
require_once __DIR__ . '/BaseModel.php';

class SpecializationModel extends BaseModel {

    public function getAll(): array {
        return $this->fetchAll(
            'SELECT * FROM specializations ORDER BY name ASC'
        );
    }

    public function findById(int $id): ?array {
        return $this->fetchOne(
            'SELECT * FROM specializations WHERE id = ?',
            'i', [$id]
        );
    }

    public function create(string $name): int {
        $this->execute(
            'INSERT INTO specializations (name) VALUES (?)',
            's', [$name]
        );
        return $this->lastInsertId();
    }

    public function update(int $id, string $name): bool {
        $result = $this->execute(
            'UPDATE specializations SET name = ? WHERE id = ?',
            'si', [$name, $id]
        );
        return (bool) $result;
    }

    public function delete(int $id): bool {
        $result = $this->execute(
            'DELETE FROM specializations WHERE id = ?',
            'i', [$id]
        );
        return (bool) $result;
    }

    public function isSafeToDelete(int $id): bool {
        $row = $this->fetchOne(
            'SELECT COUNT(*) as total FROM doctors WHERE specialization_id = ?',
            'i', [$id]
        );
        return (int)($row['total'] ?? 0) === 0;
    }
}