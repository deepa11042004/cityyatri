<?php
/**
 * Club Yatri - Action: Submit Vacation Booking Request
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('booking-request.php'));
    exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($csrf)) {
    set_flash_message('error', 'Security token mismatch. Please try again.');
    header('Location: ' . url('booking-request.php'));
    exit;
}

$fullName = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$destination = trim($_POST['destination'] ?? '');
$roomType = trim($_POST['room_type'] ?? '');
$season = trim($_POST['season'] ?? '');
$checkin = trim($_POST['checkin'] ?? '');
$nights = (int)($_POST['nights'] ?? 7);
$guests = trim($_POST['guests'] ?? '2');
$notes = trim($_POST['notes'] ?? '');

if (empty($fullName) || empty($phone) || !$email) {
    set_flash_message('error', 'Please complete all required fields (Name, Phone, Email).');
    header('Location: ' . url('booking-request.php'));
    exit;
}

$bookingId = 'CY-BK-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));

$logEntry = [
    'booking_id' => $bookingId,
    'timestamp' => date('Y-m-d H:i:s'),
    'full_name' => $fullName,
    'phone' => $phone,
    'email' => $email,
    'destination' => $destination,
    'room_type' => $roomType,
    'season' => $season,
    'checkin' => $checkin,
    'nights' => $nights,
    'guests' => $guests,
    'notes' => $notes
];

db_insert('INSERT INTO bookings (booking_code, guest_name, guest_phone, guest_email, destination_name, room_type, season, checkin_date, duration_nights, guests_count, special_requests) VALUES (?,?,?,?,?,?,?,?,?,?,?)',
    [$bookingId, $fullName, $phone, $email, $destination, $roomType, $season ?: 'Purple Studio', $checkin ?: date('Y-m-d'), $nights, (int)$guests ?: 2, $notes]);

$logFile = __DIR__ . '/../storage/bookings.log';
file_put_contents($logFile, json_encode($logEntry) . PHP_EOL, FILE_APPEND | LOCK_EX);

set_flash_message('success', "Booking request {$bookingId} logged! Your dedicated lifestyle concierge will verify resort inventory and confirm within 60 minutes.");
header('Location: ' . url('booking-request.php?confirmed=' . urlencode($bookingId)));
exit;
