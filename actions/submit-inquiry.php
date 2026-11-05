<?php
/**
 * Club Yatri - Action: Submit General / Pass Inquiry, footer newsletter and enquiry pop-up.
 *
 * inquiry_type selects the source: newsletter | enquiry_popup | pass_enquiry (default).
 * Works for normal POSTs and for fetch() (JSON response) without double-saving.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/leads.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('index.php'));
    exit;
}

$type = trim((string)($_POST['inquiry_type'] ?? ''));
$source = match ($type) {
    'newsletter' => 'newsletter',
    'enquiry_popup' => 'enquiry_popup',
    default => 'pass_enquiry',
};
$back = lead_return_url('index.php');

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    lead_finish(false, 'Security token mismatch. Please refresh and try again.', $back);
}

$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$fullName = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$city = trim($_POST['city'] ?? '');
$passTier = trim($_POST['pass_tier'] ?? '');
$notes = trim($_POST['notes'] ?? '');

if (!$email) {
    lead_finish(false, 'Please provide a valid corporate or personal email address.', $back);
}
if ($source !== 'newsletter' && ($fullName === '' || $phone === '')) {
    lead_finish(false, 'Please provide your name and phone number.', $back);
}

$result = lead_capture([
    'source' => $source,
    'type' => $source === 'pass_enquiry' ? 'pass_inquiry' : $source,
    'subject' => match ($source) {
        'newsletter' => 'Newsletter subscription',
        'enquiry_popup' => 'Pass allocation request',
        default => $passTier !== '' ? 'Pass enquiry: ' . $passTier : 'Pass enquiry',
    },
    'full_name' => $fullName,
    'email' => $email,
    'phone' => $phone,
    'city' => $city,
    'pass_tier' => $passTier,
    'notes' => $notes,
]);

if (!$result['ok']) {
    lead_finish(false, $result['error'] ?? 'We could not save your request. Please try again.', $back);
}

if (empty($result['spam']) && empty($result['duplicate'])) {
    $logEntry = ['timestamp' => date('Y-m-d H:i:s'), 'ip' => lead_client_ip(), 'type' => $source, 'full_name' => $fullName,
        'email' => $email, 'phone' => $phone, 'city' => $city, 'pass_tier' => $passTier, 'notes' => $notes];
    @file_put_contents(__DIR__ . '/../storage/inquiries.log', json_encode($logEntry) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

lead_finish(true, $source === 'newsletter'
    ? 'Welcome to the Club Yatri Journal! Your subscription has been recorded.'
    : 'Thank you! Your inquiry has been registered with priority. A Senior Lifestyle Concierge will contact you within 30 minutes.', $back);
