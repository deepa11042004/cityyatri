<?php
/**
 * Club Yatri - Action: Submit General / Pass Inquiry
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('index.php'));
    exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($csrf)) {
    set_flash_message('error', 'Security token mismatch. Please refresh and try again.');
    header('Location: ' . url('index.php'));
    exit;
}

$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$fullName = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$city = trim($_POST['city'] ?? '');
$passTier = trim($_POST['pass_tier'] ?? '');
$notes = trim($_POST['notes'] ?? '');
$inquiryType = trim($_POST['inquiry_type'] ?? 'pass_inquiry');

if (!$email) {
    set_flash_message('error', 'Please provide a valid corporate or personal email address.');
    header('Location: ' . url('index.php'));
    exit;
}

// Log inquiry safely to file in storage/
$logEntry = [
    'timestamp' => date('Y-m-d H:i:s'),
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
    'type' => $inquiryType,
    'full_name' => $fullName,
    'email' => $email,
    'phone' => $phone,
    'city' => $city,
    'pass_tier' => $passTier,
    'notes' => $notes
];

db_insert('INSERT INTO inquiries (type, full_name, email, phone, city, pass_tier, notes, ip_address) VALUES (?,?,?,?,?,?,?,?)',
    [$inquiryType, $fullName, $email, $phone, $city, $passTier, $notes, $_SERVER['REMOTE_ADDR'] ?? null]);

$logFile = __DIR__ . '/../storage/inquiries.log';
file_put_contents($logFile, json_encode($logEntry) . PHP_EOL, FILE_APPEND | LOCK_EX);

set_flash_message('success', 'Thank you! Your inquiry has been registered with priority. A Senior Lifestyle Concierge will contact you within 30 minutes.');
header('Location: ' . url('index.php'));
exit;
