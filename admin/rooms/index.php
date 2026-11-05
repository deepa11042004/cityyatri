<?php
/**
 * Club Yatri Admin - Rooms & Suites: list of room categories.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/crud.php';
require_once __DIR__ . '/../../includes/content-db.php';

$pdo = admin_pdo();
$status = (string)($_GET['status'] ?? '');
$q = trim((string)($_GET['q'] ?? ''));
$where = [match ($status) {
    'published' => 'is_published = 1 AND is_archived = 0',
    'draft' => 'is_published = 0 AND is_archived = 0',
    'archived' => 'is_archived = 1',
    'all' => '1 = 1',
    default => 'is_archived = 0',
}];
$params = [];
if ($q !== '') {
    $like = '%' . addcslashes($q, '%_\\') . '%';
    $where[] = '(title LIKE ? OR area LIKE ? OR bed_type LIKE ?)';
    array_push($params, $like, $like, $like);
}
$st = $pdo->prepare('SELECT r.*, (SELECT COUNT(*) FROM room_images i WHERE i.room_id = r.id) AS gallery_count FROM room_categories r WHERE ' . implode(' AND ', $where) . ' ORDER BY (display_order = 0), display_order, id');
$st->execute($params);
$rows = $st->fetchAll();
$tabs = ['' => 'Active', 'published' => 'Published', 'draft' => 'Drafts', 'archived' => 'Archived', 'all' => 'All'];

admin_header('Rooms & Suites', 'rooms', [['Rooms & Suites', null]], 'The accommodation categories shown on the Rooms & Suites pages.');
?>
<div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
  <ul class="nav nav-pills gap-1">
    <?php foreach ($tabs as $k => $label): ?>
      <li class="nav-item"><a class="nav-link py-1 px-3 <?php echo $status === (string)$k ? 'active bg-danger' : 'text-dark bg-white border'; ?>" href="?<?php echo e(http_build_query(array_filter(['status' => $k, 'q' => $q]))); ?>"><?php echo e($label); ?></a></li>
    <?php endforeach; ?>
  </ul>
  <form method="get" class="d-flex gap-2"><?php if ($status !== ''): ?><input type="hidden" name="status" value="<?php echo e($status); ?>"><?php endif; ?>
    <input class="form-control form-control-sm" type="search" name="q" value="<?php echo e($q); ?>" placeholder="Search rooms" aria-label="Search rooms"><button class="btn btn-sm btn-light border" type="submit"><i class="bi bi-search"></i></button></form>
  <a class="btn btn-cy btn-sm" href="edit.php"><i class="bi bi-plus-lg"></i> Add room category</a>
</div>

<div class="cy-card">
  <?php if (!$rows): ?>
    <div class="empty-state"><i class="bi bi-door-open"></i><strong>No room categories here yet.</strong><div class="mt-2"><a class="btn btn-cy btn-sm" href="edit.php"><i class="bi bi-plus-lg"></i> Add one</a></div></div>
  <?php else: ?>
    <div class="table-responsive"><table class="table cy-table align-middle">
      <thead><tr><th style="width:96px">Photo</th><th>Room category</th><th>Capacity</th><th>Size</th><th class="text-center">Photos</th><th class="text-center">Featured</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): $edit = 'edit.php?id=' . (int)$r['id'];
          $cap = $r['capacity'] !== '' ? $r['capacity'] : ((int)$r['max_adults'] ? (int)$r['max_adults'] . ' adults' . ((int)$r['max_children'] ? ' + ' . (int)$r['max_children'] . ' children' : '') : '—'); ?>
        <tr>
          <td><?php if ($r['image_path']): ?><img src="<?php echo e(content_image_url($r['image_path'])); ?>" alt="" loading="lazy" style="width:84px;height:56px;object-fit:cover;border-radius:10px;"><?php else: ?><span class="d-grid text-muted" style="width:84px;height:56px;place-items:center;background:#F1EEE8;border-radius:10px;"><i class="bi bi-image"></i></span><?php endif; ?></td>
          <td style="min-width:200px"><a class="fw-bold text-decoration-none" href="<?php echo e($edit); ?>"><?php echo e($r['title']); ?></a><div class="small text-muted"><?php echo e(mb_strimwidth((string)$r['short_description'], 0, 70, '…')); ?></div></td>
          <td class="small"><?php echo e($cap); ?></td>
          <td class="small"><?php echo e((string)$r['area']); ?></td>
          <td class="text-center"><?php echo 1 + (int)$r['gallery_count']; ?></td>
          <td class="text-center"><?php echo !empty($r['featured']) ? '<i class="bi bi-star-fill text-warning" title="Featured"></i>' : '<span class="text-muted">—</span>'; ?></td>
          <td><?php echo admin_status_pill($r); ?></td>
          <td class="text-end text-nowrap">
            <a class="btn btn-sm btn-light border" href="<?php echo e($edit); ?>" title="Edit"><i class="bi bi-pencil"></i></a>
            <?php if ($r['is_published'] && !$r['is_archived']): ?><a class="btn btn-sm btn-light border" href="<?php echo e(url('room-details.php?id=' . urlencode($r['slug']))); ?>" target="_blank" rel="noopener" title="View on website"><i class="bi bi-box-arrow-up-right"></i></a><?php endif; ?>
            <?php if (!$r['is_archived']): ?>
              <?php echo admin_action_button('action.php', ['id' => $r['id'], 'op' => $r['is_published'] ? 'unpublish' : 'publish', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-light border', $r['is_published'] ? 'bi-eye-slash' : 'bi-eye', '', $r['is_published'] ? 'Unpublish' : 'Publish'); ?>
              <?php echo admin_action_button('action.php', ['id' => $r['id'], 'op' => $r['featured'] ? 'unfeature' : 'feature', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-light border', $r['featured'] ? 'bi-star-fill text-warning' : 'bi-star', '', $r['featured'] ? 'Remove from featured' : 'Mark as featured'); ?>
              <?php echo admin_action_button('action.php', ['id' => $r['id'], 'op' => 'archive', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-light border', 'bi-archive', 'Archive "' . $r['title'] . '"? It is hidden from the website and can be restored later.', 'Archive'); ?>
            <?php else: ?>
              <?php echo admin_action_button('action.php', ['id' => $r['id'], 'op' => 'restore', 'back' => $_SERVER['REQUEST_URI']], 'Restore', 'btn-light border', 'bi-arrow-counterclockwise'); ?>
              <?php echo admin_action_button('action.php', ['id' => $r['id'], 'op' => 'delete', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-outline-danger', 'bi-trash', 'Permanently delete "' . $r['title'] . '", its photos and amenities? This cannot be undone.', 'Delete permanently'); ?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody></table></div>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
