<?php
/**
 * Club Yatri Admin - Passes: row actions (POST + CSRF).
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
if (!str_starts_with($back, admin_url('passes/index.php'))) {
    $back = admin_url('passes/index.php');
}

$st = $pdo->prepare('SELECT id, name, image_path, is_archived FROM passes WHERE id = ?');
$st->execute([$id]);
$p = $st->fetch();
if (!$p) {
    admin_flash('error', 'That pass could not be found.');
    admin_redirect($back);
}

$name = '"' . $p['name'] . '"';
$msg = null;
try {
    switch ($op) {
        case 'publish':
            $pdo->prepare('UPDATE passes SET is_published = 1 WHERE id = ? AND is_archived = 0')->execute([$id]);
            $msg = "$name is now published.";
            break;
        case 'unpublish':
            $pdo->prepare('UPDATE passes SET is_published = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name is now hidden from the website.";
            break;
        case 'feature':
            $pdo->prepare('UPDATE passes SET featured = 1 WHERE id = ?')->execute([$id]);
            $msg = "$name is marked as most popular.";
            break;
        case 'unfeature':
            $pdo->prepare('UPDATE passes SET featured = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name is no longer featured.";
            break;
        case 'archive':
            $pdo->prepare('UPDATE passes SET is_archived = 1, is_published = 0, featured = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name was archived and hidden from the website.";
            break;
        case 'restore':
            $pdo->prepare('UPDATE passes SET is_archived = 0 WHERE id = ?')->execute([$id]);
            $msg = "$name was restored as a draft. Publish it when you are ready.";
            break;
        case 'delete':
            if (!(int)$p['is_archived']) {
                admin_flash('error', 'Archive the pass first. Only archived passes can be deleted permanently.');
                admin_redirect($back);
            }
            $pdo->beginTransaction();
            $pdo->prepare('DELETE FROM pass_benefits WHERE pass_id = ?')->execute([$id]);
            $pdo->prepare('DELETE FROM passes WHERE id = ?')->execute([$id]);
            $pdo->commit();
            if (!empty($p['image_path'])) {
                admin_delete_uploaded($pdo, (string)$p['image_path']);
            }
            $msg = "$name was deleted permanently.";
            break;
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Pass action failed: ' . $e->getMessage());
    $msg = null;
}

admin_flash($msg ? 'success' : 'error', $msg ?? 'That change could not be saved.');
admin_redirect($back);
