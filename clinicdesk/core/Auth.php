<?php
require_once __DIR__ . '/../config/config.php';

class Auth {

    public static function login(array $user): void {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'          => $user['id'],
            'name'        => $user['name'],
            'email'       => $user['email'],
            'role'        => $user['role'],
            'avatar'      => $user['avatar'] ?? null,
            'first_login' => $user['first_login'] ?? 0,
        ];
    }

    public static function logout(): void {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/index.php?page=auth&action=login');
        exit();
    }

    public static function check(): bool {
        return isset($_SESSION['user']);
    }

    public static function currentUser(): ?array {
        return $_SESSION['user'] ?? null;
    }

    public static function role(): string {
        return $_SESSION['user']['role'] ?? '';
    }

    public static function id(): int {
        return (int)($_SESSION['user']['id'] ?? 0);
    }

    public static function requireRole(string ...$roles): void {
        if (!self::check()) {
            header('Location: ' . BASE_URL . '/index.php?page=auth&action=login');
            exit();
        }

        if (!in_array(self::role(), $roles)) {
            http_response_code(403);
            require_once __DIR__ . '/../views/errors/403.php';
            exit();
        }

        // Force password change on first login
        $user = self::currentUser();
        if (
            isset($user['first_login']) &&
            $user['first_login'] == 1 &&
            !(isset($_GET['page']) && $_GET['page'] === 'auth' && isset($_GET['action']) && $_GET['action'] === 'change_password')
        ) {
            header('Location: ' . BASE_URL . '/index.php?page=auth&action=change_password');
            exit();
        }
    }
}