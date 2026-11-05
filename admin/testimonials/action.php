<?php
/**
 * Club Yatri Admin - Testimonials: row actions (POST + CSRF).
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
if (!str_starts_with($back, admin_url('testimonials/index.php'))) {
    $back = admin_url('testimonials/index.php');
}

$st = $pdo->prepare('SELECT * FROM testimonials WHERE id = ?');
$st->execute([$id]);
$t = $st->fetch();
if (!$t) {
    admin_flash('error', 'That testimonial could not be found.');
    admin_redirect($back);
}

$msg = null;
try {
    switch ($op) {
        case 'publish':
            $pdo->prepare('UPDATE testimonials SET is_published = 1 WHERE id = ?')->execute([$id]);
            $msg = 'Testimonial published.';
            break;
        case 'unpublish':
            $pdo->prepare('UPDATE testimonials SET is_published = 0 WHERE id = ?')->execute([$id]);
            $msg = 'Testimonial hidden from the website.';
            break;
        case 'feature':
            $pdo->prepare('UPDATE testimonials SET featured = 1 WHERE id = ?')->execute([$id]);
            $msg = 'Marked as featured (shown first).';
            break;
        case 'unfeature':
            $pdo->prepare('UPDATE testimonials SET featured = 0 WHERE id = ?')->execute([$id]);
            $msg = 'No longer featured.';
            break;
        case 'up':
        case 'down':
            // renumber the testimonials of the same type 1..n in their current order, then swap with the neighbour
            $ids = $pdo->prepare('SELECT id FROM testimonials WHERE type = ? ORDER BY featured DESC, (display_order = 0), display_order, id');
            $ids->execute([$t['type']]);
            $ids = array_map('intval', $ids->fetchAll(PDO::FETCH_COLUMN));
            $pos = array_search($id, $ids, true);
            $swap = $op === 'up' ? $pos - 1 : $pos + 1;
            if ($pos !== false && isset($ids[$swap])) {
                [$ids[$pos], $ids[$swap]] = [$ids[$swap], $ids[$pos]];
            }
            foreach ($ids as $n => $tid) {
                $pdo->prepare('UPDATE testimonials SET display_order = ? WHERE id = ?')->execute([$n + 1, $tid]);
            }
            $msg = 'Order updated (featured testimonials always come first).';
            break;
        case 'delete':
            $pdo->prepare('DELETE FROM testimonials WHERE id = ?')->execute([$id]);
            foreach ([$t['profile_image'], $t['thumbnail_path'], $t['video_path']] as $f) {
                if (!empty($f)) {
                    admin_delete_uploaded($pdo, (string)$f);
                }
            }
            $msg = 'Testimonial deleted.';
            break;
    }
} catch (PDOException $e) {
    error_log('Testimonial action failed: ' . $e->getMessage());
    $msg = null;
}

admin_flash($msg ? 'success' : 'error', $msg ?? 'That change could not be saved.');
admin_redirect($back);
