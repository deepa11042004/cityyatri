<?php
/**
 * Club Yatri - Destinations data access (destinations table, linked to hotels by name).
 * Every function degrades to an empty result when the database is unavailable.
 */

declare(strict_types=1);

require_once __DIR__ . '/hotels.php';

const DESTINATIONS_PER_PAGE = 24;

/** hotels in the network for each destination, computed live so it always matches the hotels table */
const DESTINATION_SELECT =
    'SELECT d.*, (SELECT COUNT(*) FROM hotels h WHERE h.location_name = d.name AND h.is_published = 1) AS hotels_count
     FROM destinations d';

function destination_filters(string $forcedType = ''): array
{
    $type = $forcedType !== '' ? $forcedType : strtoupper((string)($_GET['type'] ?? ''));
    if (!in_array($type, ['DOMESTIC', 'INTERNATIONAL'], true)) {
        $type = '';
    }
    return [
        'q' => trim((string)($_GET['q'] ?? '')),
        'type' => $type,
        'page' => max(1, (int)($_GET['page'] ?? 1)),
    ];
}

function destinations_where(array $f, array &$params): string
{
    $where = ['d.is_published = 1'];
    if (!empty($f['type'])) {
        $where[] = 'd.location_type = ?';
        $params[] = $f['type'];
    }
    if (!empty($f['q'])) {
        $where[] = '(d.name LIKE ? OR d.country LIKE ? OR d.highlights LIKE ?)';
        $like = '%' . addcslashes($f['q'], '%_\\') . '%';
        array_push($params, $like, $like, $like);
    }
    return implode(' AND ', $where);
}

function destinations_search(array $f, int $limit = DESTINATIONS_PER_PAGE): array
{
    $params = [];
    $where = destinations_where($f, $params);
    $offset = ($f['page'] - 1) * $limit;
    // Busiest destinations first, then alphabetical
    return hotels_query(
        DESTINATION_SELECT . " WHERE $where ORDER BY hotels_count DESC, d.name LIMIT " . (int)$limit . ' OFFSET ' . (int)$offset,
        $params
    );
}

function destinations_count(array $f): int
{
    $params = [];
    $where = destinations_where($f, $params);
    $rows = hotels_query("SELECT COUNT(*) AS n FROM destinations d WHERE $where", $params);
    return (int)($rows[0]['n'] ?? 0);
}

function destinations_total(): int
{
    return destinations_count(['type' => '', 'q' => '']);
}

function destination_find(string $slug): ?array
{
    $rows = hotels_query(DESTINATION_SELECT . ' WHERE d.slug = ? AND d.is_published = 1 LIMIT 1', [$slug]);
    return $rows[0] ?? null;
}

/** The destination a hotel belongs to (hotels.location_name = destinations.name). */
function destination_for_hotel(array $hotel): ?array
{
    $rows = hotels_query('SELECT slug, name FROM destinations WHERE name = ? AND is_published = 1 LIMIT 1', [$hotel['location_name'] ?? '']);
    return $rows[0] ?? null;
}

function destinations_popular(int $limit = 8): array
{
    return hotels_query(
        DESTINATION_SELECT . ' WHERE d.is_published = 1 AND d.hero_image IS NOT NULL
         ORDER BY hotels_count DESC, d.name LIMIT ' . (int)$limit,
        []
    );
}

function destinations_related(array $dest, int $limit = 3): array
{
    return hotels_query(
        DESTINATION_SELECT . ' WHERE d.is_published = 1 AND d.id <> ? AND d.hero_image IS NOT NULL
         ORDER BY (d.country = ?) DESC, (d.location_type = ?) DESC, hotels_count DESC, d.name LIMIT ' . (int)$limit,
        [$dest['id'], $dest['country'], $dest['location_type']]
    );
}

function destination_highlights(?string $raw): array
{
    return array_values(array_filter(array_map('trim', explode(';', (string)$raw)), fn($h) => $h !== ''));
}

function destination_type_label(string $type): string
{
    return $type === 'DOMESTIC' ? 'India' : 'International';
}

/** Photo credit links are only shown for Wikimedia Commons file pages. */
function destination_safe_source(?string $url): string
{
    $url = trim((string)$url);
    return str_starts_with($url, 'https://commons.wikimedia.org/') ? $url : '';
}

/** Destinations that have a pin on the globe artwork (positions are in artwork pixels, 727 x 479). */
function destination_map_pins(): array
{
    return hotels_query(
        'SELECT d.slug, d.name, d.country, d.location_type, m.map_x, m.map_y
         FROM destinations d JOIN destination_map m ON m.slug = d.slug
         WHERE d.is_published = 1 AND m.map_x IS NOT NULL
         ORDER BY m.map_y, d.name',
        []
    );
}
