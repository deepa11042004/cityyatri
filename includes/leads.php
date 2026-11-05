<?php
/**
 * Club Yatri - Public enquiry capture (feeds the admin "Leads & Enquiries" module).
 *
 * Every public form posts to an actions/ handler that calls lead_capture(). The helper adds the checks the
 * old handlers lacked: honeypot, per-IP rate limiting, duplicate protection and honest failure reporting.
 * It still works (without the new columns) if the admin migration has not been applied yet.
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

/** Hidden trap field: real visitors never see or fill it, bots usually do. */
function lead_honeypot(): string
{
    return '<div aria-hidden="true" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;">'
        . '<label>Leave this field empty <input type="text" name="website" value="" tabindex="-1" autocomplete="off"></label></div>';
}

function lead_client_ip(): string
{
    return substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
}

/** True when the caller is a fetch()/AJAX request that wants JSON back. */
function lead_wants_json(): bool
{
    return strtolower((string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'fetch'
        || str_contains((string)($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json');
}

/** Only internal page names are accepted as a redirect target (no open redirects). */
function lead_return_url(string $default): string
{
    $to = trim((string)($_POST['return_to'] ?? ''));
    if ($to !== '' && preg_match('~^[a-z0-9][a-z0-9_\-/]*\.php(\?[A-Za-z0-9_=&%\-]*)?$~i', $to)) {
        return url($to);
    }
    return url($default);
}

/** Finish a request: JSON for AJAX, flash message + redirect for a normal POST. */
function lead_finish(bool $ok, string $message, string $redirect): never
{
    if (lead_wants_json()) {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        http_response_code($ok ? 200 : 422);
        echo json_encode(['ok' => $ok, 'message' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }
    set_flash_message($ok ? 'success' : 'error', $message);
    header('Location: ' . $redirect);
    exit;
}

/** Record a submission and report whether this IP has exceeded the limit. */
function lead_rate_limited(PDO $pdo, string $formKey, int $max = 6, int $minutes = 10): bool
{
    $ip = lead_client_ip();
    try {
        $st = $pdo->prepare('SELECT COUNT(*) FROM form_submissions WHERE ip = ? AND created_at > (NOW() - INTERVAL ' . (int)$minutes . ' MINUTE)');
        $st->execute([$ip]);
        if ((int)$st->fetchColumn() >= $max) {
            return true;
        }
        $pdo->prepare('INSERT INTO form_submissions (ip, form_key) VALUES (?, ?)')->execute([$ip, $formKey]);
        // Housekeeping: keep the table small
        if (random_int(1, 40) === 1) {
            $pdo->exec('DELETE FROM form_submissions WHERE created_at < (NOW() - INTERVAL 2 DAY)');
        }
    } catch (PDOException $e) {
        error_log('Rate limit check skipped: ' . $e->getMessage());   // table not migrated yet: do not block visitors
    }
    return false;
}

/**
 * Save an enquiry.
 *
 * @param array{source:string,type?:string,full_name?:string,email?:string,phone?:string,city?:string,pass_tier?:string,
 *              preferred_time?:string,notes?:string,subject?:string,extra?:array} $in
 * @return array{ok:bool,duplicate?:bool,spam?:bool,error?:string,id?:int}
 */
function lead_capture(array $in): array
{
    // Honeypot: pretend it worked so bots learn nothing
    if (trim((string)($_POST['website'] ?? '')) !== '') {
        return ['ok' => true, 'spam' => true];
    }

    $pdo = db();
    if (!$pdo) {
        return ['ok' => false, 'error' => 'We could not save your request right now. Please call us or try again shortly.'];
    }

    $source = substr((string)$in['source'], 0, 40);
    if (lead_rate_limited($pdo, $source)) {
        return ['ok' => false, 'error' => 'Too many requests from your connection. Please wait a few minutes and try again.'];
    }

    $row = [
        'type' => substr((string)($in['type'] ?? $source), 0, 32),
        'full_name' => mb_substr(trim((string)($in['full_name'] ?? '')), 0, 128),
        'email' => mb_substr(trim((string)($in['email'] ?? '')), 0, 128),
        'phone' => mb_substr(trim((string)($in['phone'] ?? '')), 0, 32),
        'city' => mb_substr(trim((string)($in['city'] ?? '')), 0, 64),
        'pass_tier' => mb_substr(trim((string)($in['pass_tier'] ?? '')), 0, 64),
        'preferred_time' => mb_substr(trim((string)($in['preferred_time'] ?? '')), 0, 64),
        'notes' => mb_substr(trim((string)($in['notes'] ?? '')), 0, 4000),
        'ip_address' => lead_client_ip() ?: null,
    ];

    try {
        // Duplicate guard: same person + same form within 2 minutes (double click, AJAX + POST, refresh)
        $st = $pdo->prepare("SELECT id FROM inquiries WHERE COALESCE(source, type) = ? AND email = ? AND phone = ?
                             AND created_at > (NOW() - INTERVAL 2 MINUTE) LIMIT 1");
        $st->execute([$source, $row['email'], $row['phone']]);
        if ($existing = $st->fetchColumn()) {
            return ['ok' => true, 'duplicate' => true, 'id' => (int)$existing];
        }
    } catch (PDOException $e) {
        error_log('Duplicate check skipped: ' . $e->getMessage());
    }

    $extra = !empty($in['extra']) ? json_encode($in['extra'], JSON_UNESCAPED_UNICODE) : null;
    try {
        $pdo->prepare('INSERT INTO inquiries (type, full_name, email, phone, city, pass_tier, preferred_time, notes, ip_address, source, subject, extra)
                       VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
            ->execute([...array_values($row), $source, mb_substr((string)($in['subject'] ?? ''), 0, 190) ?: null, $extra]);
        return ['ok' => true, 'id' => (int)$pdo->lastInsertId()];
    } catch (PDOException $e) {
        error_log('Lead insert (new schema) failed: ' . $e->getMessage());
    }

    // Fallback for a database that has not had the admin migration yet
    try {
        $pdo->prepare('INSERT INTO inquiries (type, full_name, email, phone, city, pass_tier, preferred_time, notes, ip_address) VALUES (?,?,?,?,?,?,?,?,?)')
            ->execute(array_values($row));
        return ['ok' => true, 'id' => (int)$pdo->lastInsertId()];
    } catch (PDOException $e) {
        error_log('Lead insert failed: ' . $e->getMessage());
        return ['ok' => false, 'error' => 'We could not save your request right now. Please call us or try again shortly.'];
    }
}
