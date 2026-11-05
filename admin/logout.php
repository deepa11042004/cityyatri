<?php
/**
 * Club Yatri Admin - sign out (POST with CSRF token).
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && verify_csrf_token($_POST['csrf_token'] ?? '')) {
    admin_logout_session();
}
header('Location: ' . admin_url('login.php'));
exit;
