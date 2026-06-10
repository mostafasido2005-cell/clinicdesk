<?php
require_once __DIR__ . '/../config/database.php';

class Database {
    private static ?Database $instance = null;
    private mysqli $conn;

    private function __construct() {
        mysqli_report(MYSQLI_REPORT_OFF);
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->conn->connect_error) {
            error_log('Database connection failed: ' . $this->conn->connect_error);
            throw new RuntimeException('Database connection failed. Please try again later.');
        }
        
        $this->conn->set_charset(DB_CHARSET);
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function query(string $sql, string $types = '', array $params = []): mixed {
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            error_log('Query prepare failed: ' . $this->conn->error . ' SQL: ' . $sql);
            throw new RuntimeException('Query failed. Please try again.');
        }

        if ($types && $params) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();

        $result = $stmt->get_result();
        
        if ($result instanceof mysqli_result) {
            return $result;
        }

        return $stmt->affected_rows >= 0;
    }

    public function lastInsertId(): int {
        return $this->conn->insert_id;
    }

    public function getConn(): mysqli {
        return $this->conn;
    }

    // Prevent cloning and unserialization
    private function __clone() {}
    public function __wakeup() {
        throw new RuntimeException('Cannot unserialize singleton.');
    }
}