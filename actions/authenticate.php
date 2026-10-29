<?php
/**
 * Club Yatri - Action: Conventional Username & Password Authentication
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('login.php'));
    exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($csrf)) {
    set_flash_message('error', 'Authentication session expired. Please try again.');
    header('Location: ' . url('login.php'));
    exit;
}

$username = trim((string)($_POST['username'] ?? ''));
$password = (string)($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    set_flash_message('error', 'Please enter both your username/email and password.');
    header('Location: ' . url('login.php'));
    exit;
}

// Configured authorized users for authentication
// Secure bcrypt hashes verified via password_verify()
$validUsers = [
    'subscriber' => [
        'name' => 'Subscriber Member',
        'email' => 'subscriber@clubyatri.com',
        'hash' => password_hash('Yatri@2026', PASSWORD_DEFAULT),
    ],
    'subscriber@clubyatri.com' => [
        'name' => 'Subscriber Member',
        'email' => 'subscriber@clubyatri.com',
        'hash' => password_hash('Yatri@2026', PASSWORD_DEFAULT),
    ],
    'admin' => [
        'name' => 'Concierge Administrator',
        'email' => 'concierge@clubyatri.com',
        'hash' => password_hash('Yatri@2026', PASSWORD_DEFAULT),
    ],
];

$dbMember = null;
try {
    if ($pdo = db()) {
        $st = $pdo->prepare("SELECT * FROM members WHERE (email = ? OR member_id = ?) AND status = 'active'");
        $st->execute([$username, $username]);
        $dbMember = $st->fetch() ?: null;
    }
} catch (PDOException $e) {
    error_log('DB auth lookup failed: ' . $e->getMessage());
}
$matchedUser = null;
$lookupKey = strtolower($username);
if (isset($validUsers[$lookupKey])) {
    $matchedUser = $validUsers[$lookupKey];
}

if ($dbMember !== null) {
    $matchedUser = ['name' => $dbMember['full_name'], 'email' => $dbMember['email'], 'hash' => $dbMember['password_hash'], 'member_id' => $dbMember['member_id']];
}

if ($matchedUser !== null && password_verify($password, $matchedUser['hash'])) {
    // Regenerate session ID for session fixation protection
    session_regenerate_id(true);

    $_SESSION['member_authenticated'] = true;
    $_SESSION['member_name'] = $matchedUser['name'];
    $_SESSION['member_email'] = $matchedUser['email'];
    $_SESSION['member_id'] = $matchedUser['member_id'] ?? null;

    set_flash_message('success', 'You have successfully signed in. Welcome to Club Yatri.');
    header('Location: ' . url(!empty($matchedUser['member_id']) ? 'dashboard.php' : 'index.php'));
    exit;
} else {
    // Generic error message without revealing if username exists
    set_flash_message('error', 'Invalid username or password. Please verify your credentials or contact Concierge Support.');
    header('Location: ' . url('login.php'));
    exit;
}
