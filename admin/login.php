<?php
/**
 * Club Yatri Admin - sign in.
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if (admin_user()) {
    admin_redirect('dashboard.php');
}

const LOGIN_MAX_PER_EMAIL = 5;
const LOGIN_MAX_PER_IP = 12;
const LOGIN_WINDOW_MINUTES = 15;

$error = '';
$email = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $ip = substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
    $pdo = db();

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Your session expired. Please try again.';
    } elseif (!$pdo) {
        $error = 'The database is not reachable right now.';
    } else {
        // Brute-force protection: count recent failures for this email and for this IP
        $w = (int)LOGIN_WINDOW_MINUTES;
        $st = $pdo->prepare("SELECT COUNT(*) FROM admin_login_attempts WHERE succeeded = 0 AND email = ? AND attempted_at > (NOW() - INTERVAL $w MINUTE)");
        $st->execute([$email]);
        $byEmail = (int)$st->fetchColumn();
        $st = $pdo->prepare("SELECT COUNT(*) FROM admin_login_attempts WHERE succeeded = 0 AND ip = ? AND attempted_at > (NOW() - INTERVAL $w MINUTE)");
        $st->execute([$ip]);
        $byIp = (int)$st->fetchColumn();

        if ($byEmail >= LOGIN_MAX_PER_EMAIL || $byIp >= LOGIN_MAX_PER_IP) {
            $error = 'Too many failed attempts. Please wait ' . LOGIN_WINDOW_MINUTES . ' minutes and try again.';
        } else {
            $st = $pdo->prepare('SELECT id, name, email, password_hash FROM admin_users WHERE email = ? AND is_active = 1 LIMIT 1');
            $st->execute([$email]);
            $row = $st->fetch();
            // Always run password_verify so the response time does not reveal whether the email exists
            $hash = $row['password_hash'] ?? '$2y$12$abcdefghijklmnopqrstuuJ7nq8T0r3nV0m8hQ6y9m3B5oQw1b2eK';
            $ok = password_verify($password, $hash) && $row;

            $pdo->prepare('INSERT INTO admin_login_attempts (ip, email, succeeded) VALUES (?,?,?)')->execute([$ip, mb_substr($email, 0, 190), $ok ? 1 : 0]);

            if ($ok) {
                session_regenerate_id(true);
                $_SESSION['admin_id'] = (int)$row['id'];
                $_SESSION['admin_name'] = (string)$row['name'];
                $_SESSION['admin_email'] = (string)$row['email'];
                $_SESSION['admin_seen'] = time();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                if (password_needs_rehash($row['password_hash'], PASSWORD_DEFAULT)) {
                    $pdo->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?')->execute([password_hash($password, PASSWORD_DEFAULT), $row['id']]);
                }
                $pdo->prepare('UPDATE admin_users SET last_login_at = NOW() WHERE id = ?')->execute([$row['id']]);
                if (random_int(1, 20) === 1) {
                    $pdo->exec('DELETE FROM admin_login_attempts WHERE attempted_at < (NOW() - INTERVAL 30 DAY)');
                }
                admin_redirect('dashboard.php');
            }
            $error = 'Incorrect email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex,nofollow">
  <title>Sign in | Club Yatri Admin</title>
  <link rel="icon" type="image/png" href="<?php echo e(site_logo_url()); ?>">
  <link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?php echo e(admin_url('assets/admin.css')); ?>?v=<?php echo (int)@filemtime(__DIR__ . '/assets/admin.css'); ?>" rel="stylesheet">
</head>
<body class="admin-body">
  <div class="login-wrap">
    <div class="login-card">
      <img class="login-logo" src="<?php echo e(site_logo_url()); ?>" alt="Club Yatri" width="72" height="72">
      <h1 class="h4 text-center mb-1">Welcome back</h1>
      <p class="text-center text-muted mb-4">Sign in to the Club Yatri admin panel</p>

      <?php if ($error !== ''): ?>
        <div class="alert alert-danger py-2" role="alert"><i class="bi bi-exclamation-circle"></i> <?php echo e($error); ?></div>
      <?php endif; ?>

      <form method="post" action="" autocomplete="on" novalidate>
        <?php echo admin_csrf_field(); ?>
        <div class="mb-3">
          <label class="form-label fw-bold" for="email">Email</label>
          <input class="form-control form-control-lg" type="email" id="email" name="email" value="<?php echo e($email); ?>" autocomplete="username" required autofocus>
        </div>
        <div class="mb-4">
          <label class="form-label fw-bold" for="password">Password</label>
          <div class="input-group input-group-lg">
            <input class="form-control" type="password" id="password" name="password" autocomplete="current-password" required>
            <button class="btn btn-outline-secondary" type="button" id="togglePw" aria-label="Show password"><i class="bi bi-eye"></i></button>
          </div>
        </div>
        <button class="btn btn-cy btn-lg w-100" type="submit">Sign in</button>
      </form>
      <p class="text-center small text-muted mt-4 mb-0"><a href="<?php echo e(url('index.php')); ?>">&larr; Back to website</a></p>
    </div>
  </div>
  <script>
    document.getElementById('togglePw').addEventListener('click', function () {
      var i = document.getElementById('password');
      var show = i.type === 'password';
      i.type = show ? 'text' : 'password';
      this.querySelector('i').className = 'bi ' + (show ? 'bi-eye-slash' : 'bi-eye');
    });
  </script>
</body>
</html>
