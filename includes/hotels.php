<?php
/**
 * Club Yatri - Hotel network data access (hotels + hotel_images tables).
 * Every function degrades to an empty result when the database is unavailable,
 * so pages that list hotels simply hide those sections.
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

const HOTELS_PER_PAGE = 24;

/** True once the admin migration has added the featured / display_order columns (keeps the site working before it runs). */
function hotels_admin_cols(): bool
{
    static $has = null;
    if ($has === null) {
        $has = false;
        try {
            if ($pdo = db()) {
                $has = (int)$pdo->query("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'hotels' AND column_name IN ('featured', 'display_order')")->fetchColumn() === 2;
            }
        } catch (PDOException $e) {
            error_log('Admin column check failed: ' . $e->getMessage());
        }
    }
    return $has;
}

/** Manual order from the admin (1, 2, 3...) first, then photographed hotels, then alphabetical. */
function hotels_order_sql(string $alias = ''): string
{
    $a = $alias !== '' ? $alias . '.' : '';
    return (hotels_admin_cols() ? "({$a}display_order = 0), {$a}display_order, " : '') . "({$a}image_path IS NULL), {$a}name";
}

function hotel_category_label(string $category): string
{
    return $category === 'INTERNAL' ? 'Club Yatri Property' : 'Associated Partner';
}

function hotel_type_label(string $type): string
{
    return ucfirst(strtolower($type));
}

/** Public URL for a stored image path (relative to the project root), or '' when none. */
function hotel_image_url(?string $path): string
{
    return ($path === null || $path === '') ? '' : url($path);
}

/** Only Google Maps embed URLs are allowed inside an iframe. */
function hotel_safe_map_url(?string $link): string
{
    $link = trim((string)$link);
    return str_starts_with($link, 'https://www.google.com/maps/embed') ? $link : '';
}

function hotel_excerpt(?string $text, int $length = 150): string
{
    $flat = trim((string)preg_replace('/\s+/', ' ', (string)$text));
    return mb_strlen($flat) > $length ? rtrim(mb_substr($flat, 0, $length - 1)) . '…' : $flat;
}

function hotel_stay_types(?string $raw): array
{
    $out = [];
    foreach (explode(';', (string)$raw) as $part) {
        $part = trim($part);
        if ($part !== '') {
            $out[] = ucwords(str_replace('-', ' ', $part));
        }
    }
    return $out;
}

/**
 * Returns full property facts (geographic setting, classification, star rating, amenities, airport and transit).
 */
function hotel_extended_facts(array $hotel): array
{
    static $cache = null;
    if ($cache === null) {
        $cacheFile = dirname(__DIR__) . '/data/hotels-extended.php';
        $cache = is_file($cacheFile) ? require $cacheFile : [];
    }

    $slug = (string)($hotel['slug'] ?? '');
    $cached = $cache[$slug] ?? [];

    return [
        'geographic_location' => !empty($hotel['geographic_location']) ? $hotel['geographic_location'] : ($cached['geographic_location'] ?? (($hotel['location_name'] ?? '') . ', ' . ($hotel['country'] ?? ''))),
        'resort_classification' => !empty($hotel['resort_classification']) ? $hotel['resort_classification'] : ($cached['resort_classification'] ?? 'Contemporary Luxury Hotel'),
        'hospitality_rating' => !empty($hotel['hospitality_rating']) ? $hotel['hospitality_rating'] : ($cached['hospitality_rating'] ?? '★★★★☆ (4-Star Premium)'),
        'amenities' => !empty($hotel['amenities']) ? $hotel['amenities'] : ($cached['amenities'] ?? 'Swimming Pool, Fine Dining Restaurant, Wellness Spa, 24/7 Concierge'),
        'airport_proximity' => !empty($hotel['airport_proximity']) ? $hotel['airport_proximity'] : ($cached['airport_proximity'] ?? 'Convenient proximity to regional international airport'),
        'transit_access' => !empty($hotel['transit_access']) ? $hotel['transit_access'] : ($cached['transit_access'] ?? 'Convenient access to nearest railway station & transit hub'),
    ];
}

/**
 * Normalised filters from the query string.
 * $forcedType ('DOMESTIC' | 'INTERNATIONAL' | '') locks the region for region pages.
 */
function hotel_filters(string $forcedType = ''): array
{
    $type = $forcedType !== '' ? $forcedType : strtoupper((string)($_GET['type'] ?? ''));
    if (!in_array($type, ['DOMESTIC', 'INTERNATIONAL'], true)) {
        $type = '';
    }
    $category = strtoupper((string)($_GET['category'] ?? ''));
    if (!in_array($category, ['INTERNAL', 'ASSOCIATED'], true)) {
        $category = '';
    }
    return [
        'q' => trim((string)($_GET['q'] ?? '')),
        'type' => $type,
        'location' => trim((string)($_GET['location'] ?? '')),
        'category' => $category,
        'page' => max(1, (int)($_GET['page'] ?? 1)),
    ];
}

function hotels_where(array $f, array &$params): string
{
    $where = ['is_published = 1'];
    if (!empty($f['type'])) {
        $where[] = 'location_type = ?';
        $params[] = $f['type'];
    }
    if (!empty($f['category'])) {
        $where[] = 'category = ?';
        $params[] = $f['category'];
    }
    if (!empty($f['location'])) {
        $where[] = 'location_name = ?';
        $params[] = $f['location'];
    }
    if (!empty($f['q'])) {
        $where[] = '(name LIKE ? OR location_name LIKE ? OR country LIKE ?)';
        $like = '%' . addcslashes($f['q'], '%_\\') . '%';
        array_push($params, $like, $like, $like);
    }
    return implode(' AND ', $where);
}

function hotels_query(string $sql, array $params): array
{
    try {
        if ($pdo = db()) {
            $st = $pdo->prepare($sql);
            $st->execute($params);
            return $st->fetchAll();
        }
    } catch (PDOException $e) {
        error_log('Hotels query failed: ' . $e->getMessage());
    }
    return [];
}

function hotels_search(array $f, int $limit = HOTELS_PER_PAGE, ?int $offset = null): array
{
    $params = [];
    $where = hotels_where($f, $params);
    $offset ??= ($f['page'] - 1) * $limit;
    return hotels_query(
        "SELECT * FROM hotels WHERE $where ORDER BY " . hotels_order_sql() . ' LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset,
        $params
    );
}

function hotels_count(array $f): int
{
    $params = [];
    $where = hotels_where($f, $params);
    $rows = hotels_query("SELECT COUNT(*) AS n FROM hotels WHERE $where", $params);
    return (int)($rows[0]['n'] ?? 0);
}

function hotels_locations(string $type = ''): array
{
    $params = [];
    $sql = 'SELECT DISTINCT location_name FROM hotels WHERE is_published = 1';
    if ($type !== '') {
        $sql .= ' AND location_type = ?';
        $params[] = $type;
    }
    return array_column(hotels_query($sql . ' ORDER BY location_name', $params), 'location_name');
}

function hotel_find(string $slug): ?array
{
    $rows = hotels_query('SELECT * FROM hotels WHERE slug = ? AND is_published = 1 LIMIT 1', [$slug]);
    return $rows[0] ?? null;
}

function hotel_images(int $hotelId): array
{
    return array_column(
        hotels_query('SELECT path FROM hotel_images WHERE hotel_id = ? ORDER BY sort_order', [$hotelId]),
        'path'
    );
}

/** path => alt text for photos that have one (set in the admin). */
function hotel_image_alts(int $hotelId): array
{
    $out = [];
    foreach (hotels_query('SELECT path, alt_text FROM hotel_images WHERE hotel_id = ?', [$hotelId]) as $r) {
        if (!empty($r['alt_text'])) {
            $out[$r['path']] = $r['alt_text'];
        }
    }
    return $out;
}

function hotels_related(array $hotel, int $limit = 3): array
{
    return hotels_query(
        'SELECT * FROM hotels WHERE is_published = 1 AND location_name = ? AND id <> ? ORDER BY ' . hotels_order_sql() . ' LIMIT ' . (int)$limit,
        [$hotel['location_name'], $hotel['id']]
    );
}

/**
 * Homepage hotels: the ones marked "Featured" in the admin; until any are marked,
 * one photographed hotel from each of the busiest locations.
 */
function hotels_featured(int $limit = 8): array
{
    if (hotels_admin_cols()) {
        $picked = hotels_query(
            'SELECT * FROM hotels WHERE is_published = 1 AND featured = 1 ORDER BY ' . hotels_order_sql() . ' LIMIT ' . (int)$limit,
            []
        );
        if ($picked) {
            return $picked;
        }
    }
    return hotels_query(
        'SELECT h.* FROM hotels h JOIN (
            SELECT location_name, MIN(id) AS id FROM hotels
            WHERE is_published = 1 AND image_path IS NOT NULL
            GROUP BY location_name ORDER BY COUNT(*) DESC, location_name LIMIT ' . (int)$limit . '
         ) t ON t.id = h.id ORDER BY h.location_type, h.location_name',
        []
    );
}

/**
 * Club Yatri's own properties (partner type "Club Yatri property") with photos, in the admin's display order.
 * Used where the site used to show a hard-coded list of sample resorts.
 */
function hotels_club_properties(string $type = '', int $limit = 9): array
{
    $params = [];
    $sql = "SELECT * FROM hotels WHERE is_published = 1 AND category = 'INTERNAL' AND image_path IS NOT NULL";
    if (in_array($type, ['DOMESTIC', 'INTERNATIONAL'], true)) {
        $sql .= ' AND location_type = ?';
        $params[] = $type;
    }
    return hotels_query($sql . ' ORDER BY ' . hotels_order_sql() . ' LIMIT ' . (int)$limit, $params);
}

/** Slim list used by the booking request dropdown. */
function hotels_options(): array
{
    return hotels_query('SELECT slug, name, location_name, country FROM hotels WHERE is_published = 1 ORDER BY name', []);
}

function hotels_total(): int
{
    return hotels_count(['type' => '', 'category' => '', 'location' => '', 'q' => '']);
}
