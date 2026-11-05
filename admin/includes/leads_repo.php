<?php
/**
 * Club Yatri Admin - Leads data layer.
 *
 * A "lead" is either an enquiry (table inquiries, extended for the admin) or a booking request (table bookings,
 * left untouched; its admin state lives in booking_lead_state). Both are listed together.
 */

declare(strict_types=1);

const LEAD_STATUSES = [
    'new' => 'New',
    'contacted' => 'Contacted',
    'follow_up' => 'Follow-up',
    'converted' => 'Converted',
    'lost' => 'Lost',
];

const LEAD_SOURCES = [
    'contact' => 'Contact form',
    'callback' => 'Callback request',
    'pass_enquiry' => 'Pass enquiry',
    'enquiry_popup' => 'Pass allocation pop-up',
    'newsletter' => 'Newsletter',
    'booking' => 'Booking request',
];

function lead_source_label(string $source): string
{
    if ($source === 'callback_request') {
        return LEAD_SOURCES['callback'];
    }
    if ($source === 'pass_inquiry') {
        return LEAD_SOURCES['pass_enquiry'];
    }
    return LEAD_SOURCES[$source] ?? ucfirst(str_replace('_', ' ', $source));
}

function lead_status_label(string $status): string
{
    return LEAD_STATUSES[$status] ?? ucfirst(str_replace('_', ' ', $status));
}

/** Unified lead rows (inquiries + bookings). Used as a derived table named l. */
function leads_base_sql(): string
{
    return "SELECT 'inquiry' AS kind, i.id AS id, CONCAT('INQ-', i.id) AS ref, i.full_name AS name, i.phone AS phone, i.email AS email,
                   COALESCE(i.source, i.type) AS source, COALESCE(i.subject, '') AS subject,
                   COALESCE(i.status, 'new') AS status, i.is_read AS is_read, i.is_archived AS is_archived, i.is_spam AS is_spam,
                   i.follow_up_at AS follow_up_at, i.created_at AS created_at,
                   CONCAT_WS(' | ', NULLIF(i.city, ''), NULLIF(i.pass_tier, ''), NULLIF(i.preferred_time, ''), NULLIF(i.notes, '')) AS details
            FROM inquiries i
            UNION ALL
            SELECT 'booking', b.id, b.booking_code, b.guest_name, b.guest_phone, b.guest_email,
                   'booking', CONCAT('Booking: ', b.destination_name, ' / ', b.room_type),
                   COALESCE(s.lead_status, 'new'), COALESCE(s.is_read, 0), COALESCE(s.is_archived, 0), COALESCE(s.is_spam, 0),
                   s.follow_up_at, b.created_at,
                   CONCAT_WS(' | ', b.destination_name, NULLIF(b.resort_name, ''), b.room_type, b.season, CONCAT('check-in ', b.checkin_date),
                             CONCAT(b.duration_nights, ' nights'), CONCAT(b.guests_count, ' guests'), NULLIF(b.special_requests, ''))
            FROM bookings b LEFT JOIN booking_lead_state s ON s.booking_id = b.id";
}

/** Build WHERE + params from the query string. */
function leads_filters(array $in): array
{
    $where = [];
    $params = [];

    $view = (string)($in['view'] ?? 'active');
    $where[] = match ($view) {
        'archived' => 'l.is_archived = 1',
        'spam' => 'l.is_spam = 1',
        'all' => '1 = 1',
        default => 'l.is_archived = 0 AND l.is_spam = 0',
    };

    $q = trim((string)($in['q'] ?? ''));
    if ($q !== '') {
        $like = '%' . addcslashes($q, '%_\\') . '%';
        $where[] = '(l.name LIKE ? OR l.email LIKE ? OR l.phone LIKE ? OR l.ref LIKE ?)';
        array_push($params, $like, $like, $like, $like);
    }
    $source = (string)($in['source'] ?? '');
    if ($source !== '') {
        if ($source === 'callback') {
            $where[] = "l.source IN ('callback', 'callback_request')";
        } elseif ($source === 'pass_enquiry') {
            $where[] = "l.source IN ('pass_enquiry', 'pass_inquiry')";
        } else {
            $where[] = 'l.source = ?';
            $params[] = $source;
        }
    }
    $status = (string)($in['status'] ?? '');
    if (isset(LEAD_STATUSES[$status])) {
        $where[] = 'l.status = ?';
        $params[] = $status;
    }
    foreach (['from' => '>=', 'to' => '<='] as $key => $op) {
        $d = (string)($in[$key] ?? '');
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
            $where[] = "DATE(l.created_at) $op ?";
            $params[] = $d;
        }
    }
    $read = (string)($in['read'] ?? '');
    if ($read === 'unread') {
        $where[] = 'l.is_read = 0';
    } elseif ($read === 'read') {
        $where[] = 'l.is_read = 1';
    }
    return [implode(' AND ', $where), $params];
}

/** @return array{rows:array,total:int} */
function leads_fetch(PDO $pdo, array $in, int $limit = 20, int $offset = 0): array
{
    [$where, $params] = leads_filters($in);
    $base = leads_base_sql();

    $st = $pdo->prepare("SELECT COUNT(*) FROM ($base) l WHERE $where");
    $st->execute($params);
    $total = (int)$st->fetchColumn();

    $limitSql = $limit > 0 ? ' LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset : '';
    $st = $pdo->prepare("SELECT l.* FROM ($base) l WHERE $where ORDER BY l.created_at DESC, l.id DESC$limitSql");
    $st->execute($params);
    return ['rows' => $st->fetchAll(), 'total' => $total];
}

/** Full record for the detail page, with a normalised admin state. */
function lead_get(PDO $pdo, string $kind, int $id): ?array
{
    if ($kind === 'booking') {
        $st = $pdo->prepare('SELECT b.*, COALESCE(s.lead_status, \'new\') AS lead_status, COALESCE(s.is_read, 0) AS is_read,
                                    COALESCE(s.is_archived, 0) AS is_archived, COALESCE(s.is_spam, 0) AS is_spam, s.follow_up_at
                             FROM bookings b LEFT JOIN booking_lead_state s ON s.booking_id = b.id WHERE b.id = ?');
        $st->execute([$id]);
        $r = $st->fetch();
        return $r ? $r + ['kind' => 'booking'] : null;
    }
    $st = $pdo->prepare('SELECT i.*, COALESCE(i.status, \'new\') AS lead_status FROM inquiries i WHERE i.id = ?');
    $st->execute([$id]);
    $r = $st->fetch();
    return $r ? $r + ['kind' => 'inquiry'] : null;
}

function lead_notes(PDO $pdo, string $kind, int $id): array
{
    $st = $pdo->prepare('SELECT n.*, a.name AS admin_name FROM lead_notes n LEFT JOIN admin_users a ON a.id = n.admin_id
                         WHERE n.lead_type = ? AND n.lead_id = ? ORDER BY n.created_at DESC, n.id DESC');
    $st->execute([$kind, $id]);
    return $st->fetchAll();
}

/**
 * Apply one admin action to a lead.
 * ops: read | unread | archive | unarchive | spam | notspam | status | follow_up | note
 */
function lead_apply(PDO $pdo, string $kind, int $id, string $op, string $value = '', ?int $adminId = null): bool
{
    if (!in_array($kind, ['inquiry', 'booking'], true) || !lead_get($pdo, $kind, $id)) {
        return false;
    }

    if ($op === 'note') {
        $value = trim($value);
        if ($value === '') {
            return false;
        }
        $pdo->prepare('INSERT INTO lead_notes (lead_type, lead_id, admin_id, note) VALUES (?,?,?,?)')
            ->execute([$kind, $id, $adminId, mb_substr($value, 0, 4000)]);
        return true;
    }

    // column => value for the chosen op
    $set = match ($op) {
        'read' => ['is_read' => 1],
        'unread' => ['is_read' => 0],
        'archive' => ['is_archived' => 1],
        'unarchive' => ['is_archived' => 0, 'is_spam' => 0],
        'spam' => ['is_spam' => 1, 'is_archived' => 1],
        'notspam' => ['is_spam' => 0, 'is_archived' => 0],
        'status' => isset(LEAD_STATUSES[$value]) ? [$kind === 'booking' ? 'lead_status' : 'status' => $value] : null,
        'follow_up' => ['follow_up_at' => ($value !== '' && strtotime($value) !== false) ? date('Y-m-d H:i:s', strtotime($value)) : null],
        default => null,
    };
    if ($set === null) {
        return false;
    }

    $assign = implode(', ', array_map(static fn($c) => "`$c` = ?", array_keys($set)));
    if ($kind === 'booking') {
        $pdo->prepare('INSERT IGNORE INTO booking_lead_state (booking_id) VALUES (?)')->execute([$id]);
        $pdo->prepare("UPDATE booking_lead_state SET $assign WHERE booking_id = ?")->execute([...array_values($set), $id]);
    } else {
        $pdo->prepare("UPDATE inquiries SET $assign WHERE id = ?")->execute([...array_values($set), $id]);
    }
    return true;
}

/** wa.me link: 10-digit Indian numbers get the 91 prefix. */
function lead_whatsapp_url(string $phone): string
{
    $d = preg_replace('/\D+/', '', $phone) ?? '';
    if (strlen($d) === 10) {
        $d = '91' . $d;
    } elseif (strlen($d) === 11 && $d[0] === '0') {
        $d = '91' . substr($d, 1);
    }
    return $d === '' ? '' : 'https://wa.me/' . $d;
}

/** Keep the current filters when building links. */
function leads_query_string(array $in, array $override = []): string
{
    $keep = ['q', 'source', 'status', 'from', 'to', 'read', 'view'];
    $out = [];
    foreach ($keep as $k) {
        $v = array_key_exists($k, $override) ? $override[$k] : ($in[$k] ?? '');
        if ($v !== '' && $v !== null && !($k === 'view' && $v === 'active')) {
            $out[$k] = $v;
        }
    }
    if (isset($override['page']) && (int)$override['page'] > 1) {
        $out['page'] = (int)$override['page'];
    }
    return http_build_query($out);
}
