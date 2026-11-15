<?php
function redirect(string $url): void {
    header('Location: ' . $url);
    exit();
}

function sanitize(string $value): string {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function formatDate(string $date): string {
    return date('d M Y', strtotime($date));
}

function formatTime(string $time): string {
    return date('h:i A', strtotime($time));
}

function flashMessage(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message,
    ];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function baseUrl(string $path = ''): string {
    return BASE_URL . '/' . ltrim($path, '/');
}

function assetUrl(string $path = ''): string {
    return BASE_URL . '/public/assets/' . ltrim($path, '/');
}

function uploadUrl(string $folder, string $filename): string {
    return BASE_URL . '/public/uploads/' . $folder . '/' . $filename;
}

function isActivePage(string $page, string $action = ''): string {
    $currentPage   = $_GET['page']   ?? '';
    $currentAction = $_GET['action'] ?? '';
    
    if ($action) {
        return ($currentPage === $page && $currentAction === $action) ? 'active' : '';
    }
    return $currentPage === $page ? 'active' : '';
}

function statusBadge(string $status): string {
    $badges = [
        'pending'   => 'warning',
        'confirmed' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger',
    ];
    $color = $badges[$status] ?? 'secondary';
    return '<span class="badge badge-' . $color . '">' . ucfirst($status) . '</span>';
}

function truncate(string $text, int $length = 50): string {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

function timeAgo(string $datetime): string {
    $time = time() - strtotime($datetime);
    
    if ($time < 60)     return 'Just now';
    if ($time < 3600)   return floor($time / 60) . ' minutes ago';
    if ($time < 86400)  return floor($time / 3600) . ' hours ago';
    if ($time < 604800) return floor($time / 86400) . ' days ago';
    
    return formatDate($datetime);
}