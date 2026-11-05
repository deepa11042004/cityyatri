<?php
/**
 * Club Yatri Admin - hotel picker for the itinerary form (JSON, admin only).
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

require_admin();
header('Content-Type: application/json; charset=utf-8');

$q = trim((string)($_GET['q'] ?? ''));
if (mb_strlen($q) < 2) {
    echo '[]';
    exit;
}
$like = '%' . addcslashes($q, '%_\\') . '%';
$st = admin_pdo()->prepare('SELECT id, name, location_name, country FROM hotels WHERE is_archived = 0 AND (name LIKE ? OR location_name LIKE ?) ORDER BY name LIMIT 15');
$st->execute([$like, $like]);
echo json_encode($st->fetchAll(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
