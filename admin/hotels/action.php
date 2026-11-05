<?php
/**
 * Club Yatri Admin - Hotels: row actions (POST + CSRF).
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/crud.php';

require_admin();
admin_require_post();

$pdo = admin_pdo();
$id = (int)($_POST['id'] ?? 0);
$op = (string)($_POST['op'] ?? '');

$back = (string)($_POST['back'] ?? '');
if (!str_starts_with($back, admin_url('hotels/index.php'))) {
    $back = admin_url('hotels/index.php');
}

$st = $pdo->prepare('SELECT id, name, is_archived FROM hotels WHERE id = ?');
$st->execute([$id]);
$hotel = $st->fetch();
if (!$hotel) {
    admin_flash('error', 'That hotel could not be found.');
    admin_redirect($back);
}

$name = '"' . $hotel['name'] . '"';
$msg = null;
try {
    switch ($op) {
        case 'publish':
            $pdo->prepare('UPDATE hotels SET is_published = 1 WHERE id = ? AND is_archived = 0')->execute([$id]);
            $msg = "$name is now published.";
            break;
        case 'unpublish':
            $pdo->prepare('UPDATE hotels SET is_published = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name is now hidden from the website.";
            break;
        case 'feature':
            $pdo->prepare('UPDATE hotels SET featured = 1 WHERE id = ?')->execute([$id]);
            $msg = "$name is featured on the homepage.";
            break;
        case 'unfeature':
            $pdo->prepare('UPDATE hotels SET featured = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name is no longer featured.";
            break;
        case 'archive':
            $pdo->prepare('UPDATE hotels SET is_archived = 1, is_published = 0, featured = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name was archived and hidden from the website.";
            break;
        case 'restore':
            $pdo->prepare('UPDATE hotels SET is_archived = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name was restored as a draft. Publish it when you are ready.";
            break;
        case 'delete':
            if (!(int)$hotel['is_archived']) {
                admin_flash('error', 'Archive the hotel first. Only archived hotels can be deleted permanently.');
                admin_redirect($back);
            }
            $p = $pdo->prepare('SELECT path FROM hotel_images WHERE hotel_id = ?');
            $p->execute([$id]);
            $paths = $p->fetchAll(PDO::FETCH_COLUMN);
            $pdo->beginTransaction();
            $pdo->prepare('DELETE FROM hotel_images WHERE hotel_id = ?')->execute([$id]);
            $pdo->prepare('DELETE FROM hotels WHERE id = ?')->execute([$id]);   // itinerary links go with it (cascade)
            $pdo->commit();
            foreach ($paths as $path) {
                admin_delete_uploaded($pdo, (string)$path);
            }
            $msg = "$name was deleted permanently.";
            break;
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Hotel action failed: ' . $e->getMessage());
    $msg = null;
}

admin_flash($msg ? 'success' : 'error', $msg ?? 'That change could not be saved.');
admin_redirect($back);
