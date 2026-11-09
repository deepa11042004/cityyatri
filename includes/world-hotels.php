<?php
/**
 * Club Yatri - Worldwide hotel directory data access (not the paginated network list in hotel-directory.php) (table new_hotels: country > city > hotel).
 * Every function degrades to an empty result when the database or table is unavailable.
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

/** 'count' = most hotels first (countries and cities); 'alpha' = A to Z everywhere. Hotels are always A to Z. */
function hd_sort(?string $raw = null): string
{
    $raw = $raw ?? (string)($_GET['sort'] ?? 'count');
    return $raw === 'alpha' ? 'alpha' : 'count';
}

function hd_query(string $sql, array $params = []): array
{
    try {
        if ($pdo = db()) {
            $st = $pdo->prepare($sql);
            $st->execute($params);
            return $st->fetchAll();
        }
    } catch (PDOException $e) {
        error_log('Hotel directory query failed: ' . $e->getMessage());
    }
    return [];
}

/** @return array<int, array{country:string, n:int}> */
function hd_countries(string $sort): array
{
    $order = $sort === 'alpha' ? 'country ASC' : 'n DESC, country ASC';
    return hd_query("SELECT country, COUNT(*) AS n FROM new_hotels GROUP BY country ORDER BY $order");
}

/** @return array<int, array{city:string, n:int}> */
function hd_cities(string $country, string $sort): array
{
    $order = $sort === 'alpha' ? 'city ASC' : 'n DESC, city ASC';
    return hd_query("SELECT city, COUNT(*) AS n FROM new_hotels WHERE country = ? GROUP BY city ORDER BY $order", [$country]);
}

/** @return string[] */
function hd_hotels(string $country, string $city): array
{
    return array_column(hd_query('SELECT hotel_name FROM new_hotels WHERE country = ? AND city = ? ORDER BY hotel_name ASC', [$country, $city]), 'hotel_name');
}

function hd_count_label(int $n): string
{
    return '(' . number_format($n) . ($n === 1 ? ' hotel)' : ' hotels)');
}
