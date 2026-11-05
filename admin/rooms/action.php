<?php
/**
 * Club Yatri Admin - Rooms: row actions (POST + CSRF).
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
if (!str_starts_with($back, admin_url('rooms/index.php'))) {
    $back = admin_url('rooms/index.php');
}

$st = $pdo->prepare('SELECT id, title, image_path, is_archived FROM room_categories WHERE id = ?');
$st->execute([$id]);
$r = $st->fetch();
if (!$r) {
    admin_flash('error', 'That room category could not be found.');
    admin_redirect($back);
}

$name = '"' . $r['title'] . '"';
$msg = null;
try {
    switch ($op) {
        case 'publish':
            $pdo->prepare('UPDATE room_categories SET is_published = 1 WHERE id = ? AND is_archived = 0')->execute([$id]);
            $msg = "$name is now published.";
            break;
        case 'unpublish':
            $pdo->prepare('UPDATE room_categories SET is_published = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name is now hidden from the website.";
            break;
        case 'feature':
            $pdo->prepare('UPDATE room_categories SET featured = 1 WHERE id = ?')->execute([$id]);
            $msg = "$name is featured.";
            break;
        case 'unfeature':
            $pdo->prepare('UPDATE room_categories SET featured = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name is no longer featured.";
            break;
        case 'archive':
            $pdo->prepare('UPDATE room_categories SET is_archived = 1, is_published = 0, featured = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name was archived and hidden from the website.";
            break;
        case 'restore':
            $pdo->prepare('UPDATE room_categories SET is_archived = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name was restored as a draft. Publish it when you are ready.";
            break;
        case 'delete':
            if (!(int)$r['is_archived']) {
                admin_flash('error', 'Archive the room category first. Only archived ones can be deleted permanently.');
                admin_redirect($back);
            }
            $p = $pdo->prepare('SELECT path FROM room_images WHERE room_id = ?');
            $p->execute([$id]);
            $files = $p->fetchAll(PDO::FETCH_COLUMN);
            $files[] = (string)$r['image_path'];
            $pdo->beginTransaction();
            $pdo->prepare('DELETE FROM room_images WHERE room_id = ?')->execute([$id]);
            $pdo->prepare('DELETE FROM room_amenities WHERE room_id = ?')->execute([$id]);
            $pdo->prepare('DELETE FROM room_categories WHERE id = ?')->execute([$id]);
            $pdo->commit();
            foreach ($files as $f) {
                if ($f !== '' && !preg_match('~^https?://~i', $f)) {
                    admin_delete_uploaded($pdo, $f);
                }
            }
            $msg = "$name was deleted permanently.";
            break;
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Room action failed: ' . $e->getMessage());
    $msg = null;
}

admin_flash($msg ? 'success' : 'error', $msg ?? 'That change could not be saved.');
admin_redirect($back);
