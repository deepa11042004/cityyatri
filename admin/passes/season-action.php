<?php
/**
 * Club Yatri Admin - Seasonal passes: publish / hide / reorder / delete (POST + CSRF).
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/crud.php';

require_admin();
admin_require_post();

$pdo = admin_pdo();
$id = (int)($_POST['id'] ?? 0);
$op = (string)($_POST['op'] ?? '');

$st = $pdo->prepare('SELECT id, name, image_path, sort_order FROM pass_seasons WHERE id = ?');
$st->execute([$id]);
$s = $st->fetch();
if (!$s) {
    admin_flash('error', 'That seasonal pass could not be found.');
    admin_redirect('passes/seasons.php');
}

$msg = null;
try {
    switch ($op) {
        case 'publish':
            $pdo->prepare('UPDATE pass_seasons SET is_published = 1 WHERE id = ?')->execute([$id]);
            $msg = '"' . $s['name'] . '" is now published.';
            break;
        case 'unpublish':
            $pdo->prepare('UPDATE pass_seasons SET is_published = 0 WHERE id = ?')->execute([$id]);
            $msg = '"' . $s['name'] . '" is now hidden from the website.';
            break;
        case 'up':
        case 'down':
            // renumber 1..n, then swap with the neighbour
            $ids = $pdo->query('SELECT id FROM pass_seasons ORDER BY sort_order, id')->fetchAll(PDO::FETCH_COLUMN);
            $pos = array_search((string)$id, array_map('strval', $ids), true);
            $swap = $op === 'up' ? $pos - 1 : $pos + 1;
            if ($pos !== false && isset($ids[$swap])) {
                [$ids[$pos], $ids[$swap]] = [$ids[$swap], $ids[$pos]];
            }
            foreach ($ids as $n => $sid) {
                $pdo->prepare('UPDATE pass_seasons SET sort_order = ? WHERE id = ?')->execute([$n + 1, $sid]);
            }
            $msg = 'Order updated.';
            break;
        case 'delete':
            $pdo->prepare('DELETE FROM pass_seasons WHERE id = ?')->execute([$id]);
            if (!empty($s['image_path'])) {
                admin_delete_uploaded($pdo, (string)$s['image_path']);
            }
            $msg = '"' . $s['name'] . '" was deleted.';
            break;
    }
} catch (PDOException $e) {
    error_log('Season action failed: ' . $e->getMessage());
    $msg = null;
}

admin_flash($msg ? 'success' : 'error', $msg ?? 'That change could not be saved.');
admin_redirect('passes/seasons.php');
