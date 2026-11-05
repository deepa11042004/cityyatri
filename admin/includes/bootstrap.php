<?php
/**
 * Club Yatri Admin - shared bootstrap: its own session, security headers and helper functions.
 * Every admin page requires this file first.
 */

declare(strict_types=1);

// A separate session name keeps the admin login completely apart from the member (subscriber) login.
if (session_status() === PHP_SESSION_NONE) {
    session_name('cy_admin');
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443;
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/db.php';

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('X-Robots-Tag: noindex, nofollow');

const ADMIN_IDLE_SECONDS = 7200;   // sign out after 2 hours without activity

function admin_url(string $path = ''): string
{
    return url('admin/' . ltrim($path, '/'));
}

function admin_pdo(): PDO
{
    $pdo = db();
    if (!$pdo) {
        http_response_code(503);
        exit('Database unavailable.');
    }
    return $pdo;
}

function admin_user(): ?array
{
    if (empty($_SESSION['admin_id'])) {
        return null;
    }
    if (time() - (int)($_SESSION['admin_seen'] ?? 0) > ADMIN_IDLE_SECONDS) {
        admin_logout_session();
        return null;
    }
    $_SESSION['admin_seen'] = time();
    return ['id' => (int)$_SESSION['admin_id'], 'name' => (string)$_SESSION['admin_name'], 'email' => (string)$_SESSION['admin_email']];
}

function admin_logout_session(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], (bool)$p['secure'], (bool)$p['httponly']);
    }
    session_destroy();
}

/** Redirect to the login page unless an admin is signed in (JSON 401 for AJAX calls). */
function require_admin(): array
{
    $u = admin_user();
    if ($u) {
        return $u;
    }
    if (strtolower((string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'fetch') {
        http_response_code(401);
        header('Content-Type: application/json');
        exit(json_encode(['ok' => false, 'message' => 'Session expired. Please sign in again.']));
    }
    header('Location: ' . admin_url('login.php'));
    exit;
}

function admin_csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/** Stop the request unless it is a POST with a valid CSRF token. */
function admin_require_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? '')) {
        http_response_code(400);
        exit('Invalid or expired request. Go back, refresh the page and try again.');
    }
}

function admin_flash(string $type, string $msg): void
{
    $_SESSION['admin_flash'][] = ['type' => $type, 'message' => $msg];
}

function admin_take_flash(): array
{
    $f = $_SESSION['admin_flash'] ?? [];
    unset($_SESSION['admin_flash']);
    return $f;
}

function admin_redirect(string $path): never
{
    header('Location: ' . (str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : admin_url($path)));
    exit;
}

function admin_ago(?string $datetime): string
{
    if (!$datetime) {
        return '';
    }
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return intdiv($diff, 60) . ' min ago';
    if ($diff < 86400) return intdiv($diff, 3600) . ' h ago';
    if ($diff < 86400 * 30) return intdiv($diff, 86400) . ' d ago';
    return date('j M Y', strtotime($datetime));
}

/** Count query helper used by the dashboard and sidebar. */
function admin_count(PDO $pdo, string $sql, array $params = []): int
{
    try {
        $st = $pdo->prepare($sql);
        $st->execute($params);
        return (int)$st->fetchColumn();
    } catch (PDOException $e) {
        error_log('admin_count: ' . $e->getMessage());
        return 0;
    }
}
