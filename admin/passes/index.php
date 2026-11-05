<?php
/**
 * Club Yatri Admin - Passes & Subscriptions: list of holiday passes.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/crud.php';
require_once __DIR__ . '/../../includes/content-db.php';
require_once __DIR__ . '/_tabs.php';

$pdo = admin_pdo();
$status = (string)($_GET['status'] ?? '');
$where = match ($status) {
    'published' => 'is_published = 1 AND is_archived = 0',
    'draft' => 'is_published = 0 AND is_archived = 0',
    'archived' => 'is_archived = 1',
    'all' => '1 = 1',
    default => 'is_archived = 0',
};
$rows = $pdo->query("SELECT p.*, (SELECT COUNT(*) FROM pass_benefits b WHERE b.pass_id = p.id) AS benefit_count FROM passes p WHERE $where
                     ORDER BY (display_order = 0), display_order, duration_years, id")->fetchAll();
$tabs = ['' => 'Active', 'published' => 'Published', 'draft' => 'Drafts', 'archived' => 'Archived', 'all' => 'All'];

admin_header('Passes & Subscriptions', 'passes', [['Passes & Subscriptions', null]], 'The holiday membership plans shown on the website.');
passes_tabs('passes');
?>
<div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
  <ul class="nav nav-pills gap-1">
    <?php foreach ($tabs as $k => $label): ?>
      <li class="nav-item"><a class="nav-link py-1 px-3 <?php echo $status === (string)$k ? 'active bg-danger' : 'text-dark bg-white border'; ?>" href="?<?php echo e($k !== '' ? 'status=' . $k : ''); ?>"><?php echo e($label); ?></a></li>
    <?php endforeach; ?>
  </ul>
  <a class="btn btn-cy btn-sm" href="edit.php"><i class="bi bi-plus-lg"></i> Add pass</a>
</div>

<div class="cy-card">
  <?php if (!$rows): ?>
    <div class="empty-state"><i class="bi bi-ticket-perforated"></i><strong>No passes here yet.</strong>
      <div class="mt-2"><a class="btn btn-cy btn-sm" href="edit.php"><i class="bi bi-plus-lg"></i> Add a pass</a></div></div>
  <?php else: ?>
    <div class="table-responsive"><table class="table cy-table align-middle">
      <thead><tr><th style="width:110px">Image</th><th>Pass</th><th>Duration</th><th>Price shown</th><th class="text-center">Nights / yr</th><th class="text-center">Benefits</th><th class="text-center">Featured</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $p): $edit = 'edit.php?id=' . (int)$p['id']; ?>
        <tr>
          <td><?php if ($p['image_path']): ?><img src="<?php echo e(content_image_url($p['image_path'])); ?>" alt="" loading="lazy" style="width:100px;height:56px;object-fit:cover;border-radius:10px;"><?php else: ?><span class="d-grid text-muted" style="width:100px;height:56px;place-items:center;background:#F1EEE8;border-radius:10px;"><i class="bi bi-image"></i></span><?php endif; ?></td>
          <td style="min-width:200px"><a class="fw-bold text-decoration-none" href="<?php echo e($edit); ?>"><?php echo e($p['name']); ?></a><div class="small text-muted"><?php echo e((string)$p['category']); ?> &middot; /<?php echo e($p['slug']); ?></div></td>
          <td class="small text-nowrap"><?php echo (int)$p['duration_years']; ?> year<?php echo (int)$p['duration_years'] === 1 ? '' : 's'; ?></td>
          <td class="small"><?php echo e((string)($p['display_price'] ?? '—')); ?></td>
          <td class="text-center"><?php echo (int)$p['annual_nights']; ?></td>
          <td class="text-center"><?php echo (int)$p['benefit_count']; ?></td>
          <td class="text-center"><?php echo !empty($p['featured']) ? '<i class="bi bi-star-fill text-warning" title="Featured"></i>' : '<span class="text-muted">—</span>'; ?></td>
          <td><?php echo admin_status_pill($p); ?></td>
          <td class="text-end text-nowrap">
            <a class="btn btn-sm btn-light border" href="<?php echo e($edit); ?>" title="Edit"><i class="bi bi-pencil"></i></a>
            <?php if ($p['is_published'] && !$p['is_archived']): ?><a class="btn btn-sm btn-light border" href="<?php echo e(url('pass-details.php?id=' . urlencode($p['slug']))); ?>" target="_blank" rel="noopener" title="View on website"><i class="bi bi-box-arrow-up-right"></i></a><?php endif; ?>
            <?php if (!$p['is_archived']): ?>
              <?php echo admin_action_button('action.php', ['id' => $p['id'], 'op' => $p['is_published'] ? 'unpublish' : 'publish', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-light border', $p['is_published'] ? 'bi-eye-slash' : 'bi-eye', '', $p['is_published'] ? 'Unpublish' : 'Publish'); ?>
              <?php echo admin_action_button('action.php', ['id' => $p['id'], 'op' => $p['featured'] ? 'unfeature' : 'feature', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-light border', $p['featured'] ? 'bi-star-fill text-warning' : 'bi-star', '', $p['featured'] ? 'Remove from featured' : 'Mark as featured'); ?>
              <?php echo admin_action_button('action.php', ['id' => $p['id'], 'op' => 'archive', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-light border', 'bi-archive', 'Archive "' . $p['name'] . '"? It is hidden from the website and can be restored later.', 'Archive'); ?>
            <?php else: ?>
              <?php echo admin_action_button('action.php', ['id' => $p['id'], 'op' => 'restore', 'back' => $_SERVER['REQUEST_URI']], 'Restore', 'btn-light border', 'bi-arrow-counterclockwise'); ?>
              <?php echo admin_action_button('action.php', ['id' => $p['id'], 'op' => 'delete', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-outline-danger', 'bi-trash', 'Permanently delete "' . $p['name'] . '" and its benefits? This cannot be undone.', 'Delete permanently'); ?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody></table></div>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
