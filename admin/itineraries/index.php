<?php
/**
 * Club Yatri Admin - Itineraries: list, search, filters, quick actions.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/crud.php';

$pdo = admin_pdo();
$perPage = 20;
$page = max(1, (int)($_GET['page'] ?? 1));

$q = trim((string)($_GET['q'] ?? ''));
$dest = (int)($_GET['destination'] ?? 0);
$category = trim((string)($_GET['category'] ?? ''));
$status = (string)($_GET['status'] ?? '');
$featured = (string)($_GET['featured'] ?? '');

$where = [];
$params = [];
switch ($status) {
    case 'published': $where[] = 'i.is_published = 1 AND i.is_archived = 0'; break;
    case 'draft': $where[] = 'i.is_published = 0 AND i.is_archived = 0'; break;
    case 'archived': $where[] = 'i.is_archived = 1'; break;
    case 'all': break;
    default: $where[] = 'i.is_archived = 0';
}
if ($q !== '') {
    $like = '%' . addcslashes($q, '%_\\') . '%';
    $where[] = '(i.title LIKE ? OR i.location_label LIKE ? OR i.category LIKE ?)';
    array_push($params, $like, $like, $like);
}
if ($dest > 0) { $where[] = 'EXISTS (SELECT 1 FROM itinerary_destinations x WHERE x.itinerary_id = i.id AND x.destination_id = ?)'; $params[] = $dest; }
if ($category !== '') { $where[] = 'i.category = ?'; $params[] = $category; }
if ($featured === '1') { $where[] = 'i.featured = 1'; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$st = $pdo->prepare("SELECT COUNT(*) FROM itineraries i $whereSql");
$st->execute($params);
$total = (int)$st->fetchColumn();
$pages = max(1, (int)ceil($total / $perPage));
$page = min($page, $pages);

$st = $pdo->prepare("SELECT i.*, (SELECT GROUP_CONCAT(d.name ORDER BY x.sort_order SEPARATOR ', ') FROM itinerary_destinations x JOIN destinations d ON d.id = x.destination_id WHERE x.itinerary_id = i.id) AS dest_names
                     FROM itineraries i $whereSql ORDER BY (i.display_order = 0), i.display_order, i.title LIMIT $perPage OFFSET " . (($page - 1) * $perPage));
$st->execute($params);
$rows = $st->fetchAll();

$destinations = $pdo->query('SELECT id, name FROM destinations WHERE is_archived = 0 ORDER BY name')->fetchAll();
$categories = $pdo->query("SELECT DISTINCT category FROM itineraries WHERE category <> '' ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

$keep = ['q' => $q, 'destination' => $dest ?: '', 'category' => $category, 'status' => $status, 'featured' => $featured];
$qs = static fn(array $o = []): string => http_build_query(array_filter(array_merge($keep, $o), static fn($v) => $v !== '' && $v !== null));
$hasFilters = ($q . ($dest ?: '') . $category . $status . $featured) !== '';
$tabs = ['' => 'Active', 'published' => 'Published', 'draft' => 'Drafts', 'archived' => 'Archived', 'all' => 'All'];

admin_header('Itineraries', 'itineraries', [['Itineraries', null]], 'Day-by-day holiday plans shown on the Itineraries pages.');
?>
<div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
  <ul class="nav nav-pills gap-1">
    <?php foreach ($tabs as $k => $label): ?>
      <li class="nav-item"><a class="nav-link py-1 px-3 <?php echo $status === (string)$k ? 'active bg-danger' : 'text-dark bg-white border'; ?>" href="?<?php echo e($qs(['status' => $k, 'page' => ''])); ?>"><?php echo e($label); ?></a></li>
    <?php endforeach; ?>
  </ul>
  <a class="btn btn-cy btn-sm" href="edit.php"><i class="bi bi-plus-lg"></i> Add itinerary</a>
</div>

<form class="cy-card cy-card-body mb-3" method="get" action="">
  <?php if ($status !== ''): ?><input type="hidden" name="status" value="<?php echo e($status); ?>"><?php endif; ?>
  <div class="filter-bar">
    <div style="grid-column: span 2;"><label for="q">Search</label><input class="form-control" type="search" id="q" name="q" value="<?php echo e($q); ?>" placeholder="Title, location or category"></div>
    <div><label for="destination">Destination</label><select class="form-select" id="destination" name="destination"><option value="">All destinations</option>
      <?php foreach ($destinations as $d): ?><option value="<?php echo (int)$d['id']; ?>" <?php echo $dest === (int)$d['id'] ? 'selected' : ''; ?>><?php echo e($d['name']); ?></option><?php endforeach; ?></select></div>
    <div><label for="category">Category</label><select class="form-select" id="category" name="category"><option value="">All categories</option>
      <?php foreach ($categories as $c): ?><option <?php echo $category === $c ? 'selected' : ''; ?>><?php echo e($c); ?></option><?php endforeach; ?></select></div>
    <div class="d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="featured" name="featured" value="1" <?php echo $featured === '1' ? 'checked' : ''; ?>><label class="form-check-label text-body text-capitalize fw-bold" for="featured" style="letter-spacing:0;font-size:.9rem;">Featured only</label></div></div>
    <div class="d-flex gap-2"><button class="btn btn-cy flex-grow-1" type="submit"><i class="bi bi-search"></i> Filter</button>
      <?php if ($hasFilters): ?><a class="btn btn-light border" href="?<?php echo e(http_build_query(array_filter(['status' => $status]))); ?>" title="Clear filters"><i class="bi bi-x-lg"></i></a><?php endif; ?></div>
  </div>
</form>

<div class="cy-card">
  <?php if (!$rows): ?>
    <div class="empty-state"><i class="bi bi-map"></i><strong><?php echo $hasFilters ? 'No itineraries match these filters.' : 'No itineraries here yet.'; ?></strong>
      <div class="mt-2"><a class="btn btn-cy btn-sm" href="edit.php"><i class="bi bi-plus-lg"></i> Add an itinerary</a></div></div>
  <?php else: ?>
    <div class="table-responsive"><table class="table cy-table align-middle">
      <thead><tr><th style="width:84px">Cover</th><th>Itinerary</th><th>Destination(s)</th><th>Duration</th><th>Category</th><th class="text-center">Featured</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $it): $edit = 'edit.php?id=' . (int)$it['id']; ?>
        <tr>
          <td><?php if ($it['hero_image']): ?><img src="<?php echo e(url($it['hero_image'])); ?>" alt="" loading="lazy" style="width:72px;height:54px;object-fit:cover;border-radius:10px;"><?php else: ?><span class="d-grid text-muted" style="width:72px;height:54px;place-items:center;background:#F1EEE8;border-radius:10px;"><i class="bi bi-image"></i></span><?php endif; ?></td>
          <td style="min-width:200px"><a class="fw-bold text-decoration-none" href="<?php echo e($edit); ?>"><?php echo e($it['title']); ?></a><div class="small text-muted"><?php echo e((string)$it['location_label']); ?></div></td>
          <td class="small"><?php echo e((string)($it['dest_names'] ?? '—')); ?></td>
          <td class="small text-nowrap"><?php echo (int)$it['duration_days']; ?>D / <?php echo (int)$it['duration_nights']; ?>N</td>
          <td class="small"><?php echo e((string)$it['category']); ?></td>
          <td class="text-center"><?php echo !empty($it['featured']) ? '<i class="bi bi-star-fill text-warning" title="Featured"></i>' : '<span class="text-muted">—</span>'; ?></td>
          <td><?php echo admin_status_pill($it); ?></td>
          <td class="text-end text-nowrap">
            <a class="btn btn-sm btn-light border" href="<?php echo e($edit); ?>" title="Edit"><i class="bi bi-pencil"></i></a>
            <?php if ($it['is_published'] && !$it['is_archived']): ?><a class="btn btn-sm btn-light border" href="<?php echo e(url('itinerary-details.php?slug=' . urlencode($it['slug']))); ?>" target="_blank" rel="noopener" title="View on website"><i class="bi bi-box-arrow-up-right"></i></a><?php endif; ?>
            <?php if (!$it['is_archived']): ?>
              <?php echo admin_action_button('action.php', ['id' => $it['id'], 'op' => $it['is_published'] ? 'unpublish' : 'publish', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-light border', $it['is_published'] ? 'bi-eye-slash' : 'bi-eye', '', $it['is_published'] ? 'Unpublish' : 'Publish'); ?>
              <?php echo admin_action_button('action.php', ['id' => $it['id'], 'op' => $it['featured'] ? 'unfeature' : 'feature', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-light border', $it['featured'] ? 'bi-star-fill text-warning' : 'bi-star', '', $it['featured'] ? 'Remove from featured' : 'Mark as featured'); ?>
              <?php echo admin_action_button('action.php', ['id' => $it['id'], 'op' => 'archive', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-light border', 'bi-archive', 'Archive "' . $it['title'] . '"? It is hidden from the website and can be restored later.', 'Archive'); ?>
            <?php else: ?>
              <?php echo admin_action_button('action.php', ['id' => $it['id'], 'op' => 'restore', 'back' => $_SERVER['REQUEST_URI']], 'Restore', 'btn-light border', 'bi-arrow-counterclockwise'); ?>
              <?php echo admin_action_button('action.php', ['id' => $it['id'], 'op' => 'delete', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-outline-danger', 'bi-trash', 'Permanently delete "' . $it['title'] . '", its days and photos? This cannot be undone.', 'Delete permanently'); ?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody></table></div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-3 border-top">
      <div class="small text-muted">Showing <?php echo number_format(($page - 1) * $perPage + 1); ?>–<?php echo number_format(min($total, $page * $perPage)); ?> of <?php echo number_format($total); ?> itineraries</div>
      <?php echo admin_pagination($page, $pages, static fn(int $p): string => '?' . $qs(['page' => $p > 1 ? $p : ''])); ?>
    </div>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
