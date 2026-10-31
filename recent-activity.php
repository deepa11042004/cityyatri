<?php
/**
 * Club Yatri - Recent booking activity (JSON) for the bottom-left activity popup.
 *
 * Only REAL activity from the bookings table is returned, anonymised: no names, contact details or
 * booking codes - just the place that was requested and how long ago.
 *
 *   GET /recent-activity.php            -> real bookings from the last 30 days
 *   GET /recent-activity.php?preview=1  -> clearly labelled SAMPLE items, to preview the popup design
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/db.php';

header('Content-Type: application/json; charset=utf-8');

function activity_ago(int $minutes): string
{
    if ($minutes < 2) {
        return 'just now';
    }
    if ($minutes < 60) {
        return $minutes . ' minutes ago';
    }
    if ($minutes < 1440) {
        $h = intdiv($minutes, 60);
        return $h . ($h === 1 ? ' hour ago' : ' hours ago');
    }
    $d = intdiv($minutes, 1440);
    return $d . ($d === 1 ? ' day ago' : ' days ago');
}

$items = [];

if (($_GET['preview'] ?? '') === '1') {
    header('Cache-Control: no-store');
    $items = [
        ['text' => 'A guest requested a stay at Azure Sands Villa & Spa', 'when' => '10 minutes ago', 'sample' => true],
        ['text' => 'A guest booked Udaivilas Haveli & Royal Retreat', 'when' => '2 hours ago', 'sample' => true],
        ['text' => 'A guest requested a stay at The Cedar Mist Sanctuary', 'when' => '1 day ago', 'sample' => true],
    ];
} else {
    header('Cache-Control: public, max-age=60');
    try {
        if ($pdo = db()) {
            $rows = $pdo->query(
                "SELECT destination_name, status, TIMESTAMPDIFF(MINUTE, created_at, NOW()) AS mins
                 FROM bookings
                 WHERE status IN ('requested', 'confirmed', 'completed')
                   AND destination_name <> ''
                   AND created_at >= (NOW() - INTERVAL 30 DAY)
                 ORDER BY created_at DESC
                 LIMIT 8"
            )->fetchAll();
            foreach ($rows as $r) {
                $name = trim((string)$r['destination_name']);
                $items[] = [
                    'text' => ($r['status'] === 'requested' ? 'A guest requested a stay at ' : 'A guest booked ') . $name,
                    'when' => activity_ago(max(0, (int)$r['mins'])),
                    'sample' => false,
                ];
            }
        }
    } catch (PDOException $e) {
        error_log('Recent activity query failed: ' . $e->getMessage());
    }
}

echo json_encode(['items' => $items], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
