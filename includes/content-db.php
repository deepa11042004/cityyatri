<?php
/**
 * Club Yatri - Content that is managed in the admin: passes, seasonal passes, rooms, testimonials.
 *
 * Each loader returns data in the same shape the old data/*.php files used, so the page templates are unchanged.
 * They return an empty array when the table is missing, empty or the database is down, and the data files then
 * keep serving their built-in content.
 */

declare(strict_types=1);

require_once __DIR__ . '/settings.php';

function content_rows(string $sql, array $params = []): array
{
    try {
        if ($pdo = db()) {
            $st = $pdo->prepare($sql);
            $st->execute($params);
            return $st->fetchAll();
        }
    } catch (PDOException $e) {
        // table not created yet, or database unavailable
    }
    return [];
}

/**
 * True once a table holds any rows at all (published or not). Used to decide whether the admin is in charge of
 * a kind of content: if so, an empty published list means "show nothing", not "fall back to the built-in content".
 */
function content_has_rows(string $table, string $where = '1 = 1'): bool
{
    static $memo = [];
    $key = $table . '|' . $where;
    if (!isset($memo[$key])) {
        $memo[$key] = false;
        try {
            if ($pdo = db()) {
                $memo[$key] = (int)$pdo->query("SELECT COUNT(*) FROM `$table` WHERE $where")->fetchColumn() > 0;
            }
        } catch (PDOException $e) {
            // table missing
        }
    }
    return $memo[$key];
}

/** A stored image reference as a URL: full URLs stay as they are, site paths get the base path. */
function content_image_url(?string $path): string
{
    $path = trim((string)$path);
    if ($path === '') {
        return '';
    }
    return preg_match('~^https?://~i', $path) ? $path : url($path);
}

// ---- Passes ---------------------------------------------------------------------------------

/** Published passes in the shape of data/subscriptions.php ($subscriptionPlans), keyed by slug. */
function content_pass_plans(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $cache = [];
    $rows = content_rows('SELECT * FROM passes WHERE is_published = 1 AND is_archived = 0 ORDER BY (display_order = 0), display_order, duration_years, id');
    foreach ($rows as $r) {
        $benefits = content_rows('SELECT title, description, icon FROM pass_benefits WHERE pass_id = ? ORDER BY sort_order, id', [$r['id']]);
        $cache[$r['slug']] = [
            'id' => $r['slug'],
            'title' => $r['name'],
            'short_title' => $r['short_name'] !== '' ? $r['short_name'] : $r['name'],
            'tenure' => $r['tenure_label'] !== '' ? $r['tenure_label'] : $r['duration_years'] . ' Years',
            'years' => (int)$r['duration_years'],
            'annual_nights' => (int)$r['annual_nights'],
            'points_annual' => (int)$r['annual_points'],
            'badge' => (string)$r['category'],
            'featured' => (bool)$r['featured'],
            'description' => (string)$r['short_description'],
            'full_description' => (string)($r['full_description'] ?? ''),
            'resorts_access' => (string)($r['resorts_access'] ?? ''),
            'banking_years' => (string)($r['banking_years'] ?? ''),
            'dining_discount' => (string)($r['dining_discount'] ?? ''),
            'concierge' => (string)($r['concierge'] ?? ''),
            'advance_booking' => (string)($r['advance_booking'] ?? ''),
            'display_price' => (string)($r['display_price'] ?? ''),
            'terms' => (string)($r['terms'] ?? ''),
            'image' => (string)($r['image_path'] ?? ''),
            'image_alt' => (string)($r['image_alt'] ?? ''),
            'meta_title' => (string)($r['meta_title'] ?? ''),
            'meta_description' => (string)($r['meta_description'] ?? ''),
            'benefits' => $benefits,
            'highlight_features' => array_column($benefits, 'title'),
        ];
    }
    return $cache;
}

/** Seasonal passes ("Exclusive Pass Collection") in display order. */
function content_pass_seasons(): array
{
    return content_rows('SELECT * FROM pass_seasons WHERE is_published = 1 ORDER BY sort_order, id');
}

/** Core terms shown under the pass collection: list of [title, text]. */
function content_pass_core_terms(array $fallback): array
{
    $raw = site_setting('pass_core_terms');
    $list = $raw !== '' ? json_decode($raw, true) : null;
    return is_array($list) ? $list : $fallback;   // an empty saved list means "no terms", not "use the built-in ones"
}

function content_pass_checklist(array $fallback): array
{
    $raw = site_setting('pass_checklist');
    if ($raw === '' && !site_setting_exists('pass_checklist')) {
        return $fallback;
    }
    return array_values(array_filter(array_map('trim', preg_split('/[\r\n]+/', $raw) ?: [])));
}

/** Benefit icons offered in the admin, with the symbol shown on the website. */
const CONTENT_ICONS = [
    'check-circle' => '✔', 'star' => '★', 'gift' => '🎁', 'airplane' => '✈', 'house-heart' => '🏡', 'shield-check' => '🛡',
    'cup-hot' => '☕', 'people' => '👥', 'calendar-check' => '📅', 'geo-alt' => '📍', 'award' => '🏅', 'gem' => '💎',
    'key' => '🔑', 'heart' => '❤', 'ticket-perforated' => '🎟', 'currency-rupee' => '₹',
];

function content_icon_symbol(?string $icon): string
{
    return CONTENT_ICONS[(string)$icon] ?? CONTENT_ICONS['check-circle'];
}

// ---- Rooms ----------------------------------------------------------------------------------

/** Published rooms in the shape of data/rooms.php ($roomCategories), keyed by slug. */
function content_rooms(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $cache = [];
    $rows = content_rows('SELECT * FROM room_categories WHERE is_published = 1 AND is_archived = 0 ORDER BY (display_order = 0), display_order, id');
    foreach ($rows as $r) {
        $am = content_rows('SELECT name FROM room_amenities WHERE room_id = ? ORDER BY sort_order, id', [$r['id']]);
        $gallery = content_rows('SELECT path, alt_text FROM room_images WHERE room_id = ? ORDER BY sort_order, id', [$r['id']]);
        $capacity = (string)$r['capacity'];
        if ($capacity === '' && (int)$r['max_adults'] > 0) {
            $capacity = (int)$r['max_adults'] . ' Adult' . ((int)$r['max_adults'] === 1 ? '' : 's')
                . ((int)$r['max_children'] > 0 ? ' + ' . (int)$r['max_children'] . ' Child' . ((int)$r['max_children'] === 1 ? '' : 'ren') : '');
        }
        $cache[$r['slug']] = [
            'id' => $r['slug'],
            'title' => $r['title'],
            'capacity' => $capacity,
            'vibe' => (string)$r['short_description'],
            'area' => (string)$r['area'],
            'bed_type' => (string)$r['bed_type'],
            'points_night' => (int)$r['points_night'],
            'image' => content_image_url($r['image_path']),
            'description' => (string)$r['description'],
            'amenities' => array_column($am, 'name'),
            'gallery' => array_map(static fn($g) => ['url' => content_image_url($g['path']), 'alt' => (string)($g['alt_text'] ?? '')], $gallery),
        ];
    }
    return $cache;
}

// ---- Testimonials ---------------------------------------------------------------------------

function content_initials(string $name): string
{
    $parts = preg_split('/[\s&]+/', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $i = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $i .= mb_strtoupper(mb_substr($p, 0, 1));
    }
    return $i !== '' ? $i : '?';
}

/** Published written testimonials in the shape of data/testimonials.php. */
function content_testimonials(): array
{
    $out = [];
    foreach (content_rows("SELECT * FROM testimonials WHERE type = 'text' AND is_published = 1 ORDER BY featured DESC, (display_order = 0), display_order, id") as $r) {
        $out[] = [
            'author' => $r['name'],
            'tier' => (string)$r['detail'],
            'city' => (string)$r['location'],
            'initials' => content_initials((string)$r['name']),
            'rating' => (int)($r['rating'] ?: 5),
            'quote' => (string)$r['body'],
            'image' => content_image_url($r['profile_image'] ?? ''),
        ];
    }
    return $out;
}

/** Published video testimonials in the shape of data/video-testimonials.php. */
function content_video_testimonials(): array
{
    $out = [];
    foreach (content_rows("SELECT * FROM testimonials WHERE type = 'video' AND is_published = 1 ORDER BY featured DESC, (display_order = 0), display_order, id") as $r) {
        $v = [
            'name' => $r['name'],
            'detail' => trim(implode(' • ', array_filter([(string)$r['detail'], (string)$r['location']]))),
            'quote' => (string)($r['description'] ?? ''),
            'title' => (string)($r['video_title'] ?? ''),
            'poster' => (string)($r['thumbnail_path'] ?? ''),
        ];
        match ($r['video_source']) {
            'youtube' => $v['youtube'] = (string)$r['video_ref'],
            'vimeo' => $v['vimeo'] = (string)$r['video_ref'],
            default => $v['mp4'] = (string)$r['video_path'],
        };
        $out[] = $v;
    }
    return $out;
}

/** Pull the video id out of a YouTube or Vimeo link (or return '' if it is not one). */
function content_video_ref(string $source, string $url): string
{
    $url = trim($url);
    if ($source === 'youtube') {
        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $url)) {
            return $url;
        }
        if (preg_match('~^https?://(?:www\.|m\.)?(?:youtube\.com/(?:watch\?(?:[^#]*&)?v=|embed/|shorts/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~i', $url, $m)) {
            return $m[1];
        }
    }
    if ($source === 'vimeo') {
        if (preg_match('/^\d{6,12}$/', $url)) {
            return $url;
        }
        if (preg_match('~^https?://(?:www\.)?(?:vimeo\.com/(?:channels/[^/]+/|groups/[^/]+/videos/)?|player\.vimeo\.com/video/)(\d{6,12})~i', $url, $m)) {
            return $m[1];
        }
    }
    return '';
}
