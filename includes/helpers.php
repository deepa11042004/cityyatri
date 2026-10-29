<?php
/**
 * Club Yatri - Helper Functions & Utilities
 */

declare(strict_types=1);

/**
 * Escape HTML output safely
 */
function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate a consistent URL respecting the base path
 */
function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    if ($path === '' || $path === '/') {
        return (defined('BASE_PATH') && BASE_PATH !== '') ? BASE_PATH . '/' : '/';
    }
    return (defined('BASE_PATH') && BASE_PATH !== '') ? BASE_PATH . '/' . $path : '/' . $path;
}

/**
 * Check if the given page corresponds to current script
 */
function isActive(string $page): string
{
    $currentPage = basename($_SERVER['PHP_SELF'] ?? '');
    return ($currentPage === $page || ($page === 'index.php' && $currentPage === '')) ? 'active' : '';
}

/**
 * Generate or retrieve CSRF token
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify submitted CSRF token
 */
function verify_csrf_token(?string $token): bool
{
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Set a flash message
 */
function set_flash_message(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

/**
 * Get and clear flash message
 */
function get_flash_message(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Format currency in Indian Rupees format (e.g. ₹25,000)
 */
function format_inr(int|float $amount): string
{
    return '₹' . number_format($amount, 0, '.', ',');
}

/**
 * Render star ratings in HTML
 */
function render_stars(int $rating = 5): string
{
    $stars = '';
    for ($i = 0; $i < 5; $i++) {
        $stars .= ($i < $rating) ? '★' : '☆';
    }
    return $stars;
}
