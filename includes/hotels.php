<?php
/**
 * Club Yatri - Hotel network data access (hotels + hotel_images tables).
 * Every function degrades to an empty result when the database is unavailable,
 * so pages that list hotels simply hide those sections.
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

const HOTELS_PER_PAGE = 24;

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
    // Hotels with photos first, then alphabetical
    return hotels_query(
        "SELECT * FROM hotels WHERE $where ORDER BY (image_path IS NULL), name LIMIT " . (int)$limit . ' OFFSET ' . (int)$offset,
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

function hotels_related(array $hotel, int $limit = 3): array
{
    return hotels_query(
        'SELECT * FROM hotels WHERE is_published = 1 AND location_name = ? AND id <> ? ORDER BY (image_path IS NULL), name LIMIT ' . (int)$limit,
        [$hotel['location_name'], $hotel['id']]
    );
}

/** One photographed hotel from each of the busiest locations, for the homepage. */
function hotels_featured(int $limit = 8): array
{
    return hotels_query(
        'SELECT h.* FROM hotels h JOIN (
            SELECT location_name, MIN(id) AS id FROM hotels
            WHERE is_published = 1 AND image_path IS NOT NULL
            GROUP BY location_name ORDER BY COUNT(*) DESC, location_name LIMIT ' . (int)$limit . '
         ) t ON t.id = h.id ORDER BY h.location_type, h.location_name',
        []
    );
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
