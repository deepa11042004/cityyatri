<?php
/**
 * Club Yatri Admin - Leads: single action endpoint (POST + CSRF).
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/leads_repo.php';

$admin = require_admin();
admin_require_post();

$kind = (($_POST['kind'] ?? '') === 'booking') ? 'booking' : 'inquiry';
$id = (int)($_POST['id'] ?? 0);
$op = (string)($_POST['op'] ?? '');
$value = (string)($_POST['value'] ?? '');

// Only go back to a lead page inside this admin area
$back = (string)($_POST['back'] ?? '');
if (!preg_match('~^view\.php\?kind=(inquiry|booking)&id=\d+$~', $back)) {
    $back = 'index.php';
}

$messages = [
    'read' => 'Marked as read.', 'unread' => 'Marked as unread.', 'archive' => 'Lead archived.', 'unarchive' => 'Lead restored.',
    'spam' => 'Marked as spam and archived.', 'notspam' => 'Restored from spam.', 'status' => 'Status updated.',
    'follow_up' => 'Follow-up reminder saved.', 'note' => 'Note added.',
];

try {
    $ok = $id > 0 && isset($messages[$op]) && lead_apply(admin_pdo(), $kind, $id, $op, $value, $admin['id']);
} catch (PDOException $e) {
    error_log('Lead action failed: ' . $e->getMessage());
    $ok = false;
}

// After "mark unread" go back to the list (opening a lead marks it read again)
if ($ok && $op === 'unread') {
    $back = 'index.php';
}

admin_flash($ok ? 'success' : 'error', $ok ? $messages[$op] : 'That change could not be saved. Please check the value and try again.');
admin_redirect('leads/' . $back);
