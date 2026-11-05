<?php
/**
 * Club Yatri Admin - Testimonials: written and video testimonials.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/crud.php';
require_once __DIR__ . '/../../includes/content-db.php';

$pdo = admin_pdo();
$type = (string)($_GET['type'] ?? '');
$status = (string)($_GET['status'] ?? '');
$where = [];
if (in_array($type, ['text', 'video'], true)) { $where[] = "type = '$type'"; }
if ($status === 'published') { $where[] = 'is_published = 1'; } elseif ($status === 'draft') { $where[] = 'is_published = 0'; }
$rows = $pdo->query('SELECT * FROM testimonials' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY type, featured DESC, (display_order = 0), display_order, id')->fetchAll();
$counts = ['' => (int)$pdo->query('SELECT COUNT(*) FROM testimonials')->fetchColumn(), 'text' => (int)$pdo->query("SELECT COUNT(*) FROM testimonials WHERE type = 'text'")->fetchColumn(), 'video' => (int)$pdo->query("SELECT COUNT(*) FROM testimonials WHERE type = 'video'")->fetchColumn()];
$tabs = ['' => 'All', 'text' => 'Written', 'video' => 'Video'];
$rowsOnly = static fn(): string => $_SERVER['REQUEST_URI'];

admin_header('Testimonials', 'testimonials', [['Testimonials', null]], 'What members say about Club Yatri, shown on the homepage.');
?>
<div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
  <ul class="nav nav-pills gap-1">
    <?php foreach ($tabs as $k => $label): ?>
      <li class="nav-item"><a class="nav-link py-1 px-3 <?php echo $type === (string)$k ? 'active bg-danger' : 'text-dark bg-white border'; ?>" href="?<?php echo e(http_build_query(array_filter(['type' => $k, 'status' => $status]))); ?>"><?php echo e($label); ?> <span class="opacity-75">(<?php echo $counts[$k]; ?>)</span></a></li>
    <?php endforeach; ?>
  </ul>
  <div class="d-flex gap-2">
    <a class="btn btn-cy btn-sm" href="edit.php?type=text"><i class="bi bi-plus-lg"></i> Add written testimonial</a>
    <a class="btn btn-cy-gold btn-sm" href="edit.php?type=video"><i class="bi bi-camera-video"></i> Add video testimonial</a>
  </div>
</div>

<div class="cy-card">
  <?php if (!$rows): ?>
    <div class="empty-state"><i class="bi bi-chat-quote"></i><strong>No testimonials here yet.</strong></div>
  <?php else: ?>
    <div class="table-responsive"><table class="table cy-table align-middle">
      <thead><tr><th style="width:64px"></th><th>Member</th><th>Type</th><th>What they said</th><th class="text-center">Featured</th><th>Order</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $t): $edit = 'edit.php?id=' . (int)$t['id'];
          $thumb = $t['type'] === 'video' ? ($t['thumbnail_path'] ?: '') : ($t['profile_image'] ?: ''); ?>
        <tr>
          <td><?php if ($thumb): ?><img src="<?php echo e(content_image_url($thumb)); ?>" alt="" loading="lazy" style="width:48px;height:48px;object-fit:cover;border-radius:50%;"><?php else: ?><span class="d-grid text-white fw-bold" style="width:48px;height:48px;place-items:center;border-radius:50%;background:#EA2224;"><?php echo e(content_initials((string)$t['name'])); ?></span><?php endif; ?></td>
          <td style="min-width:160px"><a class="fw-bold text-decoration-none" href="<?php echo e($edit); ?>"><?php echo e($t['name']); ?></a><div class="small text-muted"><?php echo e(implode(' • ', array_filter([(string)$t['detail'], (string)$t['location']]))); ?></div></td>
          <td><?php echo $t['type'] === 'video' ? '<span class="src src-booking"><i class="bi bi-camera-video"></i> ' . e(ucfirst((string)$t['video_source'])) . '</span>' : '<span class="src">Written' . ($t['rating'] ? ' · ' . str_repeat('★', (int)$t['rating']) : '') . '</span>'; ?></td>
          <td class="small" style="max-width:340px"><?php echo e(mb_strimwidth((string)($t['type'] === 'video' ? ($t['video_title'] ?: $t['description']) : $t['body']), 0, 110, '…')); ?></td>
          <td class="text-center"><?php echo !empty($t['featured']) ? '<i class="bi bi-star-fill text-warning" title="Featured"></i>' : '<span class="text-muted">—</span>'; ?></td>
          <td class="text-nowrap">
            <?php echo admin_action_button('action.php', ['id' => $t['id'], 'op' => 'up', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-light border', 'bi-arrow-up', '', 'Move up'); ?>
            <?php echo admin_action_button('action.php', ['id' => $t['id'], 'op' => 'down', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-light border', 'bi-arrow-down', '', 'Move down'); ?>
          </td>
          <td><?php echo $t['is_published'] ? '<span class="pill pill-converted">Published</span>' : '<span class="pill pill-follow_up">Hidden</span>'; ?></td>
          <td class="text-end text-nowrap">
            <a class="btn btn-sm btn-light border" href="<?php echo e($edit); ?>" title="Edit"><i class="bi bi-pencil"></i></a>
            <?php echo admin_action_button('action.php', ['id' => $t['id'], 'op' => $t['is_published'] ? 'unpublish' : 'publish', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-light border', $t['is_published'] ? 'bi-eye-slash' : 'bi-eye', '', $t['is_published'] ? 'Hide' : 'Publish'); ?>
            <?php echo admin_action_button('action.php', ['id' => $t['id'], 'op' => $t['featured'] ? 'unfeature' : 'feature', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-light border', $t['featured'] ? 'bi-star-fill text-warning' : 'bi-star', '', $t['featured'] ? 'Remove from featured' : 'Mark as featured'); ?>
            <?php echo admin_action_button('action.php', ['id' => $t['id'], 'op' => 'delete', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-outline-danger', 'bi-trash', 'Delete the testimonial from ' . $t['name'] . '? This cannot be undone.', 'Delete'); ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody></table></div>
  <?php endif; ?>
</div>
<p class="small text-muted mt-3">The homepage shows the first three written testimonials (featured ones first) and every published video.</p>
<?php admin_footer(); ?>
