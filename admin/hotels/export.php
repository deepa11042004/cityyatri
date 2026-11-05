<?php
/**
 * Club Yatri Admin - Hotels: CSV export of the current filters.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

require_admin();
$pdo = admin_pdo();

$where = [];
$params = [];
$status = (string)($_GET['status'] ?? '');
switch ($status) {
    case 'published': $where[] = 'h.is_published = 1 AND h.is_archived = 0'; break;
    case 'draft': $where[] = 'h.is_published = 0 AND h.is_archived = 0'; break;
    case 'archived': $where[] = 'h.is_archived = 1'; break;
    case 'all': break;
    default: $where[] = 'h.is_archived = 0';
}
$q = trim((string)($_GET['q'] ?? ''));
if ($q !== '') {
    $like = '%' . addcslashes($q, '%_\\') . '%';
    $where[] = '(h.name LIKE ? OR h.location_name LIKE ? OR h.city LIKE ? OR h.hotel_code LIKE ?)';
    array_push($params, $like, $like, $like, $like);
}
if (($c = trim((string)($_GET['country'] ?? ''))) !== '') {
    $where[] = 'h.country = ?';
    $params[] = $c;
}
if ((int)($_GET['destination'] ?? 0) > 0) {
    $where[] = 'h.destination_id = ?';
    $params[] = (int)$_GET['destination'];
}
if (($_GET['featured'] ?? '') === '1') {
    $where[] = 'h.featured = 1';
}
if (in_array($_GET['type'] ?? '', ['DOMESTIC', 'INTERNATIONAL'], true)) {
    $where[] = 'h.location_type = ?';
    $params[] = $_GET['type'];
}

$st = $pdo->prepare('SELECT h.*, d.name AS destination_name FROM hotels h LEFT JOIN destinations d ON d.id = h.destination_id '
    . ($where ? 'WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY h.name');
$st->execute($params);

// Stop spreadsheet formula injection from text fields
$cell = static function ($v): string {
    $v = (string)$v;
    return ($v !== '' && strpos("=+-@\t\r", $v[0]) !== false) ? "'" . $v : $v;
};

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="club-yatri-hotels-' . date('Ymd-His') . '.csv"');
$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF");
fputcsv($out, ['Code', 'Name', 'Slug', 'Destination', 'Country', 'State', 'City', 'Region', 'Property type', 'Partner type', 'Featured', 'Status',
    'Address', 'Map link', 'Latitude', 'Longitude', 'Amenities', 'Added'], ',', '"', '');
while ($h = $st->fetch()) {
    fputcsv($out, array_map($cell, [
        $h['hotel_code'], $h['name'], $h['slug'], $h['destination_name'] ?? $h['location_name'], $h['country'], $h['state'], $h['city'],
        $h['location_type'] === 'DOMESTIC' ? 'India' : 'International', ucfirst(strtolower((string)$h['hotel_type'])),
        $h['category'] === 'INTERNAL' ? 'Club Yatri property' : 'Associated partner', $h['featured'] ? 'Yes' : 'No',
        $h['is_archived'] ? 'Archived' : ($h['is_published'] ? 'Published' : 'Draft'), $h['address'], $h['map_link'],
        $h['latitude'], $h['longitude'], $h['amenities'], $h['created_at'],
    ]), ',', '"', '');
}
fclose($out);
exit;
