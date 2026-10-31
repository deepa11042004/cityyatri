<?php
/**
 * Club Yatri - Itineraries data access (itineraries, itinerary_days, itinerary_images).
 * Every function degrades to an empty result when the database is unavailable.
 */

declare(strict_types=1);

require_once __DIR__ . '/hotels.php';

function itinerary_type_filter(): string
{
    $type = strtoupper((string)($_GET['type'] ?? ''));
    return in_array($type, ['DOMESTIC', 'INTERNATIONAL'], true) ? $type : '';
}

/** Featured journeys first, then alphabetical. */
function itineraries_list(string $type = '', int $limit = 60): array
{
    $params = [];
    $sql = 'SELECT * FROM itineraries WHERE is_published = 1';
    if ($type !== '') {
        $sql .= ' AND location_type = ?';
        $params[] = $type;
    }
    return hotels_query($sql . ' ORDER BY featured DESC, title LIMIT ' . (int)$limit, $params);
}

function itineraries_count(string $type = ''): int
{
    $params = [];
    $sql = 'SELECT COUNT(*) AS n FROM itineraries WHERE is_published = 1';
    if ($type !== '') {
        $sql .= ' AND location_type = ?';
        $params[] = $type;
    }
    return (int)(hotels_query($sql, $params)[0]['n'] ?? 0);
}

function itinerary_find(string $slug): ?array
{
    return hotels_query('SELECT * FROM itineraries WHERE slug = ? AND is_published = 1 LIMIT 1', [$slug])[0] ?? null;
}

function itinerary_days(int $id): array
{
    return hotels_query('SELECT day_number, title, description FROM itinerary_days WHERE itinerary_id = ? ORDER BY day_number', [$id]);
}

function itinerary_images(int $id): array
{
    return array_column(hotels_query('SELECT path FROM itinerary_images WHERE itinerary_id = ? ORDER BY sort_order', [$id]), 'path');
}

function itineraries_related(array $it, int $limit = 3): array
{
    return hotels_query(
        'SELECT * FROM itineraries WHERE is_published = 1 AND id <> ?
         ORDER BY (location_type = ?) DESC, featured DESC, title LIMIT ' . (int)$limit,
        [$it['id'], $it['location_type']]
    );
}

/** Splits a "; "-separated column into a clean list. */
function itinerary_list(?string $raw): array
{
    return array_values(array_filter(array_map('trim', explode(';', (string)$raw)), fn($v) => $v !== ''));
}

function itinerary_duration(array $it): string
{
    $d = (int)$it['duration_days'];
    $n = (int)$it['duration_nights'];
    return $d . ($d === 1 ? ' Day' : ' Days') . ' / ' . $n . ($n === 1 ? ' Night' : ' Nights');
}
