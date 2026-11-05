<?php
/**
 * Club Yatri Admin - entry point.
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

header('Location: ' . admin_url(admin_user() ? 'dashboard.php' : 'login.php'));
exit;
