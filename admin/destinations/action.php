<?php
/**
 * Club Yatri Admin - Destinations: row actions (POST + CSRF).
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
if (!str_starts_with($back, admin_url('destinations/index.php'))) {
    $back = admin_url('destinations/index.php');
}

$st = $pdo->prepare('SELECT id, name, slug, hero_image, is_archived FROM destinations WHERE id = ?');
$st->execute([$id]);
$d = $st->fetch();
if (!$d) {
    admin_flash('error', 'That destination could not be found.');
    admin_redirect($back);
}

$name = '"' . $d['name'] . '"';
$msg = null;
try {
    switch ($op) {
        case 'publish':
            $pdo->prepare('UPDATE destinations SET is_published = 1 WHERE id = ? AND is_archived = 0')->execute([$id]);
            $msg = "$name is now published.";
            break;
        case 'unpublish':
            $pdo->prepare('UPDATE destinations SET is_published = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name is now hidden from the website.";
            break;
        case 'feature':
            $pdo->prepare('UPDATE destinations SET featured = 1 WHERE id = ?')->execute([$id]);
            $msg = "$name is featured and will be listed first.";
            break;
        case 'unfeature':
            $pdo->prepare('UPDATE destinations SET featured = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name is no longer featured.";
            break;
        case 'archive':
            $pdo->prepare('UPDATE destinations SET is_archived = 1, is_published = 0, featured = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name was archived and hidden from the website.";
            break;
        case 'restore':
            $pdo->prepare('UPDATE destinations SET is_archived = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name was restored as a draft. Publish it when you are ready.";
            break;
        case 'delete':
            if (!(int)$d['is_archived']) {
                admin_flash('error', 'Archive the destination first. Only archived destinations can be deleted permanently.');
                admin_redirect($back);
            }
            // never break the website's relationships: hotels and itineraries must be moved first
            $hotels = admin_count($pdo, 'SELECT COUNT(*) FROM hotels WHERE destination_id = ?', [$id]);
            $itins = admin_count($pdo, 'SELECT COUNT(*) FROM itinerary_destinations WHERE destination_id = ?', [$id])
                + admin_count($pdo, 'SELECT COUNT(*) FROM itineraries WHERE destination_slug = ?', [$d['slug']]);
            if ($hotels > 0 || $itins > 0) {
                admin_flash('error', "$name cannot be deleted: $hotels hotel(s) and $itins itinerary link(s) still use it. Move or remove them first, or keep it archived.");
                admin_redirect($back);
            }
            $p = $pdo->prepare('SELECT path FROM destination_images WHERE destination_id = ?');
            $p->execute([$id]);
            $paths = $p->fetchAll(PDO::FETCH_COLUMN);
            $paths[] = (string)$d['hero_image'];
            $pdo->beginTransaction();
            $pdo->prepare('DELETE FROM destination_map WHERE slug = ?')->execute([$d['slug']]);
            $pdo->prepare('DELETE FROM destination_images WHERE destination_id = ?')->execute([$id]);
            $pdo->prepare('DELETE FROM destinations WHERE id = ?')->execute([$id]);
            $pdo->commit();
            foreach ($paths as $path) {
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
    error_log('Destination action failed: ' . $e->getMessage());
    $msg = null;
}

admin_flash($msg ? 'success' : 'error', $msg ?? 'That change could not be saved.');
admin_redirect($back);
