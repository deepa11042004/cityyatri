<?php
/**
 * Club Yatri - Site settings stored in the database (table site_settings, edited in the admin Settings module).
 *
 * Every setting has a built-in default, so the site behaves exactly as before until something is changed in
 * the admin, and keeps working if the table does not exist yet or the database is unreachable.
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

/** @return array<string,string> all stored settings (read once per request) */
function site_settings_all(): array
{
    static $all = null;
    if ($all === null) {
        $all = [];
        try {
            if ($pdo = db()) {
                foreach ($pdo->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll() as $r) {
                    $all[(string)$r['setting_key']] = (string)$r['setting_value'];
                }
            }
        } catch (PDOException $e) {
            // table not created yet: the defaults below apply
        }
    }
    return $all;
}

/** A setting, or $default when it was never saved or is empty. */
function site_setting(string $key, string $default = ''): string
{
    $v = site_settings_all()[$key] ?? '';
    return $v !== '' ? $v : $default;
}

/** Whether a setting was explicitly saved (even as an empty string). */
function site_setting_exists(string $key): bool
{
    return array_key_exists($key, site_settings_all());
}

/** Save settings (admin only). Values are strings; null deletes the key. */
function site_settings_save(PDO $pdo, array $values): void
{
    foreach ($values as $k => $v) {
        if ($v === null) {
            $pdo->prepare('DELETE FROM site_settings WHERE setting_key = ?')->execute([$k]);
            continue;
        }
        $pdo->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
                       ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)')->execute([$k, (string)$v]);
    }
}

/** Public URL of the site logo / favicon (uploaded in the admin, else the original files). */
function site_logo_url(): string
{
    return url(site_setting('logo_path', 'assets/images/logo/club-yatri-logo.png'));
}

function site_favicon_url(): string
{
    return url(site_setting('favicon_path', site_setting('logo_path', 'assets/images/logo/club-yatri-logo.png')));
}

/** "tel:" number from a phone number as typed: 10-digit Indian numbers and 1800 numbers get +91. */
function site_phone_tel(string $display): string
{
    $digits = preg_replace('/\D+/', '', $display) ?? '';
    if ($digits === '') {
        return '';
    }
    if (str_starts_with(trim($display), '+')) {
        return '+' . $digits;
    }
    return '+' . (str_starts_with($digits, '91') && strlen($digits) > 10 ? $digits : '91' . ltrim($digits, '0'));
}

// ---- Secrets (SMTP password): encrypted at rest, never sent to the browser ------------------

function site_secret_key(): string
{
    $cfg = is_file(__DIR__ . '/db-config.php') ? (require __DIR__ . '/db-config.php') : [];
    $seed = (string)($cfg['app_key'] ?? '');
    if ($seed === '') {
        // no dedicated key configured: derive one from the database credentials (not stored in the database)
        $seed = implode('|', [$cfg['host'] ?? getenv('DB_HOST'), $cfg['name'] ?? getenv('DB_NAME'), $cfg['user'] ?? getenv('DB_USER'), $cfg['pass'] ?? getenv('DB_PASS'), 'club-yatri']);
    }
    return hash('sha256', $seed, true);
}

function site_secret_encrypt(string $plain): string
{
    if ($plain === '') {
        return '';
    }
    $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    return 'v1:' . base64_encode($nonce . sodium_crypto_secretbox($plain, $nonce, site_secret_key()));
}

function site_secret_decrypt(string $stored): string
{
    if (!str_starts_with($stored, 'v1:')) {
        return '';
    }
    $raw = base64_decode(substr($stored, 3), true);
    if ($raw === false || strlen($raw) <= SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) {
        return '';
    }
    $plain = sodium_crypto_secretbox_open(substr($raw, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES), substr($raw, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES), site_secret_key());
    return $plain === false ? '' : $plain;
}
