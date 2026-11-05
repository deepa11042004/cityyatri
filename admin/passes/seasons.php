<?php
/**
 * Club Yatri Admin - Seasonal passes (the "Exclusive Pass Collection" cards on the website).
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/crud.php';
require_once __DIR__ . '/../../includes/content-db.php';
require_once __DIR__ . '/_tabs.php';

$pdo = admin_pdo();
$rows = $pdo->query('SELECT * FROM pass_seasons ORDER BY sort_order, id')->fetchAll();

admin_header('Seasonal passes', 'passes', [['Passes & Subscriptions', 'passes/index.php'], ['Seasonal passes', null]], 'The Green, Blue, Yellow and Red season passes shown as the Exclusive Pass Collection.');
passes_tabs('seasons');
?>
<div class="d-flex justify-content-end mb-3"><a class="btn btn-cy btn-sm" href="season-edit.php"><i class="bi bi-plus-lg"></i> Add seasonal pass</a></div>
<div class="cy-card">
  <?php if (!$rows): ?>
    <div class="empty-state"><i class="bi bi-sun"></i><strong>No seasonal passes yet.</strong><div class="mt-1">The Exclusive Pass Collection is hidden from the website until you add one.</div></div>
  <?php else: ?>
    <div class="table-responsive"><table class="table cy-table align-middle">
      <thead><tr><th style="width:70px">Image</th><th>Season pass</th><th>Season</th><th>Price shown</th><th>Order</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $n => $s): $edit = 'season-edit.php?id=' . (int)$s['id']; ?>
        <tr>
          <td><?php if ($s['image_path']): ?><img src="<?php echo e(content_image_url($s['image_path'])); ?>" alt="" loading="lazy" style="width:48px;height:80px;object-fit:cover;border-radius:8px;"><?php else: ?><span class="text-muted"><i class="bi bi-image"></i></span><?php endif; ?></td>
          <td><span class="d-inline-block rounded-circle me-1 align-middle" style="width:12px;height:12px;background:<?php echo e($s['color']); ?>"></span><a class="fw-bold text-decoration-none" href="<?php echo e($edit); ?>"><?php echo e($s['name']); ?></a></td>
          <td class="small"><?php echo e($s['season_label']); ?></td>
          <td class="small"><?php echo e($s['price_display']); ?></td>
          <td class="text-nowrap">
            <?php echo admin_action_button('season-action.php', ['id' => $s['id'], 'op' => 'up'], '', 'btn-light border', 'bi-arrow-up', '', 'Move up'); ?>
            <?php echo admin_action_button('season-action.php', ['id' => $s['id'], 'op' => 'down'], '', 'btn-light border', 'bi-arrow-down', '', 'Move down'); ?>
          </td>
          <td><?php echo $s['is_published'] ? '<span class="pill pill-converted">Published</span>' : '<span class="pill pill-follow_up">Hidden</span>'; ?></td>
          <td class="text-end text-nowrap">
            <a class="btn btn-sm btn-light border" href="<?php echo e($edit); ?>" title="Edit"><i class="bi bi-pencil"></i></a>
            <?php echo admin_action_button('season-action.php', ['id' => $s['id'], 'op' => $s['is_published'] ? 'unpublish' : 'publish'], '', 'btn-light border', $s['is_published'] ? 'bi-eye-slash' : 'bi-eye', '', $s['is_published'] ? 'Hide' : 'Publish'); ?>
            <?php echo admin_action_button('season-action.php', ['id' => $s['id'], 'op' => 'delete'], '', 'btn-outline-danger', 'bi-trash', 'Delete "' . $s['name'] . '"? This cannot be undone.', 'Delete'); ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody></table></div>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
