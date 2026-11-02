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

/**
 * Returns formatted region with continent and location part (e.g., "International: Southeast Asia").
 */
function itinerary_region_label(array $it): string
{
    if (!empty($it['region'])) {
        return (string)$it['region'];
    }

    $slug = (string)($it['slug'] ?? '');
    $destSlug = (string)($it['destination_slug'] ?? '');
    $loc = strtolower((string)($it['location_label'] ?? ''));

    // Southeast Asia
    if (in_array($destSlug, ['phuket', 'krabi', 'bali', 'singapore', 'bangkok', 'pattaya', 'thailand', 'indonesia', 'malaysia'], true)
        || str_contains($slug, 'phuket') || str_contains($slug, 'krabi') || str_contains($slug, 'bali') || str_contains($slug, 'singapore')
        || str_contains($loc, 'thailand') || str_contains($loc, 'indonesia') || str_contains($loc, 'singapore') || str_contains($loc, 'malaysia')) {
        return 'International: Southeast Asia';
    }

    // Middle East
    if ($destSlug === 'dubai' || str_contains($slug, 'dubai') || str_contains($loc, 'dubai') || str_contains($loc, 'emirates') || str_contains($loc, 'uae')) {
        return 'International: Middle East';
    }

    // South Asia / Indian Ocean
    if ($destSlug === 'maldives' || str_contains($slug, 'maldives') || str_contains($loc, 'maldives') || str_contains($loc, 'sri lanka')) {
        return 'International: South Asia (Indian Ocean)';
    }

    // Europe
    if (in_array($destSlug, ['london', 'paris', 'rome', 'switzerland', 'spain', 'turkey', 'istanbul', 'georgia'], true)
        || str_contains($loc, 'united kingdom') || str_contains($loc, 'france') || str_contains($loc, 'italy') || str_contains($loc, 'switzerland')) {
        return 'International: Europe';
    }

    // Americas
    if (in_array($destSlug, ['las-vegas', 'united-states-of-america'], true) || str_contains($loc, 'united states') || str_contains($loc, 'nevada')) {
        return 'International: North America';
    }

    if (($it['location_type'] ?? '') === 'INTERNATIONAL') {
        return 'International: Overseas';
    }

    // Domestic regions (India)
    if (str_contains($loc, 'kashmir') || str_contains($loc, 'srinagar') || str_contains($loc, 'gulmarg') || str_contains($loc, 'himachal')
        || str_contains($loc, 'manali') || str_contains($loc, 'shimla') || str_contains($loc, 'uttarakhand') || str_contains($loc, 'rishikesh')) {
        return 'India: Northern Himalayas';
    }

    if (str_contains($loc, 'rajasthan') || str_contains($loc, 'udaipur') || str_contains($loc, 'jaipur') || str_contains($loc, 'jodhpur') || str_contains($loc, 'jaisalmer')) {
        return 'India: Western Rajasthan';
    }

    if (str_contains($loc, 'goa') || str_contains($loc, 'maharashtra') || str_contains($loc, 'mumbai') || str_contains($loc, 'pune')) {
        return 'India: Western Coast';
    }

    if (str_contains($loc, 'kerala') || str_contains($loc, 'munnar') || str_contains($loc, 'alleppey') || str_contains($loc, 'kochi')
        || str_contains($loc, 'karnataka') || str_contains($loc, 'bangalore') || str_contains($loc, 'tamil nadu')) {
        return 'India: Southern Coast & Backwaters';
    }

    return 'India: National Network';
}
