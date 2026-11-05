<?php
/**
 * Club Yatri - Action: Submit VIP Callback / Contact Request
 *
 * Used by the Contact Us form (form_source=contact) and the Request Callback page (form_source=callback).
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/leads.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('callback-request.php'));
    exit;
}

$source = (($_POST['form_source'] ?? '') === 'contact') ? 'contact' : 'callback';
$back = lead_return_url($source === 'contact' ? 'contact.php' : 'callback-request.php');

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    lead_finish(false, 'Security session expired. Please refresh the page and resubmit your request.', $back);
}

$fullName = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$preferredTime = trim($_POST['preferred_time'] ?? 'Morning');
$notes = trim($_POST['notes'] ?? '');

if ($fullName === '' || $phone === '') {
    lead_finish(false, 'Name and contact telephone number are required.', $back);
}

$result = lead_capture([
    'source' => $source,
    'type' => 'callback_request',
    'subject' => $source === 'contact' ? 'Contact form enquiry' : 'VIP callback request',
    'full_name' => $fullName,
    'email' => $email ?: '',
    'phone' => $phone,
    'preferred_time' => $preferredTime,
    'notes' => $notes,
]);

if (!$result['ok']) {
    lead_finish(false, $result['error'] ?? 'We could not save your request. Please try again.', $back);
}

if (empty($result['spam']) && empty($result['duplicate'])) {
    $logEntry = ['timestamp' => date('Y-m-d H:i:s'), 'type' => $source, 'full_name' => $fullName, 'phone' => $phone,
        'email' => $email, 'preferred_time' => $preferredTime, 'notes' => $notes];
    @file_put_contents(__DIR__ . '/../storage/callbacks.log', json_encode($logEntry) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

lead_finish(true, $source === 'contact'
    ? 'Thank you! Your message has reached our concierge team and we will get back to you shortly.'
    : 'VIP Callback scheduled! A Senior Hospitality Executive will call you during your selected time window.', $back);
