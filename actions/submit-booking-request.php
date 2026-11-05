<?php
/**
 * Club Yatri - Action: Submit Vacation Booking Request
 *
 * Saved to the bookings table (listed together with enquiries in the admin Leads module).
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/leads.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('booking-request.php'));
    exit;
}

$back = url('booking-request.php');

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    lead_finish(false, 'Security token mismatch. Please try again.', $back);
}

$fullName = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$destination = trim($_POST['destination'] ?? '');
$roomType = trim($_POST['room_type'] ?? '');
$season = trim($_POST['season'] ?? '');
$checkin = trim($_POST['checkin'] ?? '');
$nights = max(1, min(60, (int)($_POST['nights'] ?? 7)));
$guests = max(1, min(30, (int)($_POST['guests'] ?? 2)));
$notes = trim($_POST['notes'] ?? '');

if ($fullName === '' || $phone === '' || !$email) {
    lead_finish(false, 'Please complete all required fields (Name, Phone, Email).', $back);
}

// Bots fill the hidden trap field: pretend it worked, save nothing
if (trim((string)($_POST['website'] ?? '')) !== '') {
    lead_finish(true, 'Booking request received.', $back);
}

$d = DateTime::createFromFormat('Y-m-d', $checkin);
$checkinDate = ($d && $d->format('Y-m-d') === $checkin) ? $checkin : date('Y-m-d');

$pdo = db();
if (!$pdo) {
    lead_finish(false, 'We could not save your request right now. Please call us or try again shortly.', $back);
}
if (lead_rate_limited($pdo, 'booking')) {
    lead_finish(false, 'Too many requests from your connection. Please wait a few minutes and try again.', $back);
}

$bookingId = 'CY-BK-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));

try {
    $pdo->prepare('INSERT INTO bookings (booking_code, guest_name, guest_phone, guest_email, destination_name, room_type, season, checkin_date, duration_nights, guests_count, special_requests) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
        ->execute([$bookingId, mb_substr($fullName, 0, 128), mb_substr($phone, 0, 32), mb_substr($email, 0, 128),
            mb_substr($destination, 0, 128), mb_substr($roomType, 0, 64), mb_substr($season ?: 'Purple Studio', 0, 32),
            $checkinDate, $nights, $guests, mb_substr($notes, 0, 4000)]);
} catch (PDOException $e) {
    error_log('Booking insert failed: ' . $e->getMessage());
    lead_finish(false, 'We could not save your booking request. Please try again or call our concierge.', $back);
}

$logEntry = ['booking_id' => $bookingId, 'timestamp' => date('Y-m-d H:i:s'), 'full_name' => $fullName, 'phone' => $phone,
    'email' => $email, 'destination' => $destination, 'room_type' => $roomType, 'season' => $season, 'checkin' => $checkinDate,
    'nights' => $nights, 'guests' => $guests, 'notes' => $notes];
@file_put_contents(__DIR__ . '/../storage/bookings.log', json_encode($logEntry) . PHP_EOL, FILE_APPEND | LOCK_EX);

if (lead_wants_json()) {
    lead_finish(true, "Booking request {$bookingId} logged!", $back);
}
set_flash_message('success', "Booking request {$bookingId} logged! Your dedicated lifestyle concierge will verify resort inventory and confirm within 60 minutes.");
header('Location: ' . url('booking-request.php?confirmed=' . urlencode($bookingId)));
exit;
