<?php
/**
 * Club Yatri - Action: Subscriber Sign Out
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/helpers.php';

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
session_destroy();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

set_flash_message('success', 'You have been securely signed out of your subscriber session.');
header('Location: ' . url('login.php'));
exit;
