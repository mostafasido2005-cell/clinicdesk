<?php
require_once __DIR__ . '/../core/Database.php';

abstract class BaseModel {
    protected Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    protected function execute(string $sql, string $types = '', array $params = []): mixed {
        try {
            return $this->db->query($sql, $types, $params);
        } catch (RuntimeException $e) {
            error_log('Model query error: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function fetchAll(string $sql, string $types = '', array $params = []): array {
        $result = $this->execute($sql, $types, $params);
        if ($result instanceof mysqli_result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    protected function fetchOne(string $sql, string $types = '', array $params = []): ?array {
        $result = $this->execute($sql, $types, $params);
        if ($result instanceof mysqli_result) {
            $row = $result->fetch_assoc();
            return $row ?: null;
        }
        return null;
    }

    protected function lastInsertId(): int {
        return $this->db->lastInsertId();
    }
}