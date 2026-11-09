<?php
/**
 * Club Yatri - hotel directory fragments (HTML), loaded when a country or city is expanded.
 *   GET ?level=cities&country=X&sort=count|alpha
 *   GET ?level=hotels&country=X&city=Y
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/world-hotels.php';

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: public, max-age=300');

$level = (string)($_GET['level'] ?? '');
$country = trim((string)($_GET['country'] ?? ''));
$city = trim((string)($_GET['city'] ?? ''));
$sort = hd_sort();

if ($country === '' || mb_strlen($country) > 100 || mb_strlen($city) > 150) {
    http_response_code(400);
    exit;
}

if ($level === 'cities') {
    $cities = hd_cities($country, $sort);
    if (!$cities) {
        echo '<p class="hd-empty">No cities found.</p>';
        exit;
    }
    echo '<ul class="hd-list">';
    foreach ($cities as $c) {
        echo '<li class="hd-node" data-country="' . e($country) . '" data-city="' . e($c['city']) . '">'
            . '<button type="button" class="hd-toggle" aria-expanded="false"><span class="hd-sign" aria-hidden="true">+</span>'
            . '<span class="hd-name">' . e($c['city']) . '</span> <span class="hd-count">' . e(hd_count_label((int)$c['n'])) . '</span></button>'
            . '<div class="hd-children" hidden></div></li>';
    }
    echo '</ul>';
} elseif ($level === 'hotels' && $city !== '') {
    $hotels = hd_hotels($country, $city);
    if (!$hotels) {
        echo '<p class="hd-empty">No hotels found.</p>';
        exit;
    }
    echo '<ul class="hd-hotels">';
    foreach ($hotels as $h) {
        echo '<li>' . e($h) . '</li>';
    }
    echo '</ul>';
} else {
    http_response_code(400);
}
