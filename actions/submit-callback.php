<?php
/**
 * Club Yatri - Action: Submit VIP Callback Request
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('callback-request.php'));
    exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($csrf)) {
    set_flash_message('error', 'Security session expired. Please resubmit your callback request.');
    header('Location: ' . url('callback-request.php'));
    exit;
}

$fullName = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$preferredTime = trim($_POST['preferred_time'] ?? 'Morning');
$notes = trim($_POST['notes'] ?? '');

if (empty($fullName) || empty($phone)) {
    set_flash_message('error', 'Name and contact telephone number are required.');
    header('Location: ' . url('callback-request.php'));
    exit;
}

$logEntry = [
    'timestamp' => date('Y-m-d H:i:s'),
    'type' => 'callback_request',
    'full_name' => $fullName,
    'phone' => $phone,
    'email' => $email,
    'preferred_time' => $preferredTime,
    'notes' => $notes
];

db_insert('INSERT INTO inquiries (type, full_name, email, phone, preferred_time, notes, ip_address) VALUES (?,?,?,?,?,?,?)',
    ['callback_request', $fullName, $email ?: '', $phone, $preferredTime, $notes, $_SERVER['REMOTE_ADDR'] ?? null]);

$logFile = __DIR__ . '/../storage/callbacks.log';
file_put_contents($logFile, json_encode($logEntry) . PHP_EOL, FILE_APPEND | LOCK_EX);

set_flash_message('success', 'VIP Callback scheduled! A Senior Hospitality Executive will call you during your selected time window.');
header('Location: ' . url('callback-request.php'));
exit;
