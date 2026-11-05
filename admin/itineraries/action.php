<?php
/**
 * Club Yatri Admin - Itineraries: row actions (POST + CSRF).
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
if (!str_starts_with($back, admin_url('itineraries/index.php'))) {
    $back = admin_url('itineraries/index.php');
}

$st = $pdo->prepare('SELECT id, title, hero_image, is_archived FROM itineraries WHERE id = ?');
$st->execute([$id]);
$it = $st->fetch();
if (!$it) {
    admin_flash('error', 'That itinerary could not be found.');
    admin_redirect($back);
}

$name = '"' . $it['title'] . '"';
$msg = null;
try {
    switch ($op) {
        case 'publish':
            $pdo->prepare('UPDATE itineraries SET is_published = 1 WHERE id = ? AND is_archived = 0')->execute([$id]);
            $msg = "$name is now published.";
            break;
        case 'unpublish':
            $pdo->prepare('UPDATE itineraries SET is_published = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name is now hidden from the website.";
            break;
        case 'feature':
            $pdo->prepare('UPDATE itineraries SET featured = 1 WHERE id = ?')->execute([$id]);
            $msg = "$name is featured.";
            break;
        case 'unfeature':
            $pdo->prepare('UPDATE itineraries SET featured = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name is no longer featured.";
            break;
        case 'archive':
            $pdo->prepare('UPDATE itineraries SET is_archived = 1, is_published = 0, featured = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name was archived and hidden from the website.";
            break;
        case 'restore':
            $pdo->prepare('UPDATE itineraries SET is_archived = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name was restored as a draft. Publish it when you are ready.";
            break;
        case 'delete':
            if (!(int)$it['is_archived']) {
                admin_flash('error', 'Archive the itinerary first. Only archived itineraries can be deleted permanently.');
                admin_redirect($back);
            }
            $paths = $pdo->prepare('SELECT path FROM itinerary_images WHERE itinerary_id = ? UNION SELECT image_path FROM itinerary_days WHERE itinerary_id = ? AND image_path IS NOT NULL');
            $paths->execute([$id, $id]);
            $files = $paths->fetchAll(PDO::FETCH_COLUMN);
            $files[] = (string)$it['hero_image'];
            $pdo->beginTransaction();
            $pdo->prepare('DELETE FROM itinerary_days WHERE itinerary_id = ?')->execute([$id]);
            $pdo->prepare('DELETE FROM itinerary_images WHERE itinerary_id = ?')->execute([$id]);
            $pdo->prepare('DELETE FROM itineraries WHERE id = ?')->execute([$id]);   // destination / hotel links cascade
            $pdo->commit();
            foreach ($files as $path) {
                if ($path !== '') {
                    admin_delete_uploaded($pdo, $path);
                }
            }
            $msg = "$name was deleted permanently.";
            break;
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Itinerary action failed: ' . $e->getMessage());
    $msg = null;
}

admin_flash($msg ? 'success' : 'error', $msg ?? 'That change could not be saved.');
admin_redirect($back);
