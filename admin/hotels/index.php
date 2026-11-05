<?php
/**
 * Club Yatri Admin - Hotels: list, search, filters, quick actions.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/crud.php';

$pdo = admin_pdo();
$perPage = 20;
$page = max(1, (int)($_GET['page'] ?? 1));

$q = trim((string)($_GET['q'] ?? ''));
$country = trim((string)($_GET['country'] ?? ''));
$dest = (int)($_GET['destination'] ?? 0);
$status = (string)($_GET['status'] ?? '');
$featured = (string)($_GET['featured'] ?? '');
$type = (string)($_GET['type'] ?? '');

$where = [];
$params = [];
switch ($status) {
    case 'published': $where[] = 'h.is_published = 1 AND h.is_archived = 0'; break;
    case 'draft': $where[] = 'h.is_published = 0 AND h.is_archived = 0'; break;
    case 'archived': $where[] = 'h.is_archived = 1'; break;
    case 'all': break;
    default: $where[] = 'h.is_archived = 0';
}
if ($q !== '') {
    $like = '%' . addcslashes($q, '%_\\') . '%';
    $where[] = '(h.name LIKE ? OR h.location_name LIKE ? OR h.city LIKE ? OR h.hotel_code LIKE ?)';
    array_push($params, $like, $like, $like, $like);
}
if ($country !== '') { $where[] = 'h.country = ?'; $params[] = $country; }
if ($dest > 0) { $where[] = 'h.destination_id = ?'; $params[] = $dest; }
if ($featured === '1') { $where[] = 'h.featured = 1'; }
if (in_array($type, ['DOMESTIC', 'INTERNATIONAL'], true)) { $where[] = 'h.location_type = ?'; $params[] = $type; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$st = $pdo->prepare("SELECT COUNT(*) FROM hotels h $whereSql");
$st->execute($params);
$total = (int)$st->fetchColumn();
$pages = max(1, (int)ceil($total / $perPage));
$page = min($page, $pages);

$st = $pdo->prepare("SELECT h.*, d.name AS destination_name FROM hotels h LEFT JOIN destinations d ON d.id = h.destination_id
                     $whereSql ORDER BY h.name ASC, h.id ASC LIMIT $perPage OFFSET " . (($page - 1) * $perPage));
$st->execute($params);
$rows = $st->fetchAll();

$countries = $pdo->query('SELECT DISTINCT country FROM hotels ORDER BY country')->fetchAll(PDO::FETCH_COLUMN);
$destinations = $pdo->query('SELECT id, name FROM destinations WHERE is_archived = 0 ORDER BY name')->fetchAll();

$keep = ['q' => $q, 'country' => $country, 'destination' => $dest ?: '', 'status' => $status, 'featured' => $featured, 'type' => $type];
$qs = static function (array $o = []) use ($keep): string {
    $m = array_filter(array_merge($keep, $o), static fn($v) => $v !== '' && $v !== null);
    return http_build_query($m);
};
$hasFilters = ($q . $country . ($dest ?: '') . $status . $featured . $type) !== '';
$tabs = ['' => 'Active', 'published' => 'Published', 'draft' => 'Drafts', 'archived' => 'Archived', 'all' => 'All'];

admin_header('Hotels', 'hotels', [['Hotels', null]], 'Hotels and resorts shown on the public Resort Network pages.');
?>
<div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
  <ul class="nav nav-pills gap-1">
    <?php foreach ($tabs as $k => $label): ?>
      <li class="nav-item"><a class="nav-link py-1 px-3 <?php echo $status === (string)$k ? 'active bg-danger' : 'text-dark bg-white border'; ?>" href="?<?php echo e($qs(['status' => $k, 'page' => ''])); ?>"><?php echo e($label); ?></a></li>
    <?php endforeach; ?>
  </ul>
  <div class="d-flex gap-2">
    <a class="btn btn-light border btn-sm" href="export.php?<?php echo e($qs()); ?>"><i class="bi bi-download"></i> Export CSV</a>
    <a class="btn btn-cy btn-sm" href="edit.php"><i class="bi bi-plus-lg"></i> Add hotel</a>
  </div>
</div>

<form class="cy-card cy-card-body mb-3" method="get" action="">
  <?php if ($status !== ''): ?><input type="hidden" name="status" value="<?php echo e($status); ?>"><?php endif; ?>
  <div class="filter-bar">
    <div style="grid-column: span 2;"><label for="q">Search</label>
      <input class="form-control" type="search" id="q" name="q" value="<?php echo e($q); ?>" placeholder="Hotel name, city, destination or code"></div>
    <div><label for="country">Country</label>
      <select class="form-select" id="country" name="country"><option value="">All countries</option>
        <?php foreach ($countries as $c): ?><option <?php echo $country === $c ? 'selected' : ''; ?>><?php echo e($c); ?></option><?php endforeach; ?></select></div>
    <div><label for="destination">Destination</label>
      <select class="form-select" id="destination" name="destination"><option value="">All destinations</option>
        <?php foreach ($destinations as $d): ?><option value="<?php echo (int)$d['id']; ?>" <?php echo $dest === (int)$d['id'] ? 'selected' : ''; ?>><?php echo e($d['name']); ?></option><?php endforeach; ?></select></div>
    <div><label for="type">Region</label>
      <select class="form-select" id="type" name="type"><option value="">India &amp; international</option>
        <option value="DOMESTIC" <?php echo $type === 'DOMESTIC' ? 'selected' : ''; ?>>India</option>
        <option value="INTERNATIONAL" <?php echo $type === 'INTERNATIONAL' ? 'selected' : ''; ?>>International</option></select></div>
    <div class="d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="featured" name="featured" value="1" <?php echo $featured === '1' ? 'checked' : ''; ?>><label class="form-check-label text-body text-capitalize fw-bold" for="featured" style="letter-spacing:0;font-size:.9rem;">Featured only</label></div></div>
    <div class="d-flex gap-2">
      <button class="btn btn-cy flex-grow-1" type="submit"><i class="bi bi-search"></i> Filter</button>
      <?php if ($hasFilters): ?><a class="btn btn-light border" href="?<?php echo e(http_build_query(array_filter(['status' => $status]))); ?>" title="Clear filters"><i class="bi bi-x-lg"></i></a><?php endif; ?>
    </div>
  </div>
</form>

<div class="cy-card">
  <?php if (!$rows): ?>
    <div class="empty-state"><i class="bi bi-buildings"></i><strong><?php echo $hasFilters ? 'No hotels match these filters.' : 'No hotels here yet.'; ?></strong>
      <div class="mt-2"><a class="btn btn-cy btn-sm" href="edit.php"><i class="bi bi-plus-lg"></i> Add the first hotel</a></div></div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table cy-table align-middle">
        <thead><tr><th style="width:84px">Photo</th><th>Hotel</th><th>Location</th><th>Destination</th><th>Type</th><th class="text-center">Featured</th><th>Status</th><th>Added</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $h):
            $edit = 'edit.php?id=' . (int)$h['id'];
            $loc = implode(', ', array_filter([$h['city'], $h['state'], $h['country']]));
            ?>
          <tr>
            <td><?php if ($h['image_path']): ?><img src="<?php echo e(url($h['image_path'])); ?>" alt="" loading="lazy" style="width:72px;height:54px;object-fit:cover;border-radius:10px;"><?php else: ?><span class="d-grid text-muted" style="width:72px;height:54px;place-items:center;background:#F1EEE8;border-radius:10px;"><i class="bi bi-image"></i></span><?php endif; ?></td>
            <td style="min-width:200px"><a class="fw-bold text-decoration-none" href="<?php echo e($edit); ?>"><?php echo e($h['name']); ?></a><div class="small text-muted"><?php echo e($h['hotel_code']); ?></div></td>
            <td class="small"><?php echo e($loc ?: $h['location_name']); ?></td>
            <td class="small"><?php echo e($h['destination_name'] ?? $h['location_name']); ?></td>
            <td class="small"><?php echo e(ucfirst(strtolower((string)$h['hotel_type']))); ?><div class="text-muted"><?php echo $h['category'] === 'INTERNAL' ? 'Club Yatri property' : 'Associated partner'; ?></div></td>
            <td class="text-center"><?php echo !empty($h['featured']) ? '<i class="bi bi-star-fill text-warning" title="Featured"></i>' : '<span class="text-muted">—</span>'; ?></td>
            <td><?php echo admin_status_pill($h); ?></td>
            <td class="small text-nowrap"><?php echo e(date('j M Y', strtotime((string)$h['created_at']))); ?></td>
            <td class="text-end text-nowrap">
              <a class="btn btn-sm btn-light border" href="<?php echo e($edit); ?>" title="Edit"><i class="bi bi-pencil"></i></a>
              <?php if ($h['is_published'] && !$h['is_archived']): ?><a class="btn btn-sm btn-light border" href="<?php echo e(url('resort-details.php?id=' . urlencode($h['slug']))); ?>" target="_blank" rel="noopener" title="View on website"><i class="bi bi-box-arrow-up-right"></i></a><?php endif; ?>
              <?php if (!$h['is_archived']): ?>
                <?php echo admin_action_button('action.php', ['id' => $h['id'], 'op' => $h['is_published'] ? 'unpublish' : 'publish', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-light border', $h['is_published'] ? 'bi-eye-slash' : 'bi-eye', '', $h['is_published'] ? 'Unpublish' : 'Publish'); ?>
                <?php echo admin_action_button('action.php', ['id' => $h['id'], 'op' => $h['featured'] ? 'unfeature' : 'feature', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-light border', $h['featured'] ? 'bi-star-fill text-warning' : 'bi-star', '', $h['featured'] ? 'Remove from featured' : 'Mark as featured'); ?>
                <?php echo admin_action_button('action.php', ['id' => $h['id'], 'op' => 'archive', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-light border', 'bi-archive', 'Archive "' . $h['name'] . '"? It is hidden from the website but can be restored later.', 'Archive'); ?>
              <?php else: ?>
                <?php echo admin_action_button('action.php', ['id' => $h['id'], 'op' => 'restore', 'back' => $_SERVER['REQUEST_URI']], 'Restore', 'btn-light border', 'bi-arrow-counterclockwise'); ?>
                <?php echo admin_action_button('action.php', ['id' => $h['id'], 'op' => 'delete', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-outline-danger', 'bi-trash', 'Permanently delete "' . $h['name'] . '" and its photo links? This cannot be undone.', 'Delete permanently'); ?>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-3 border-top">
      <div class="small text-muted">Showing <?php echo number_format(($page - 1) * $perPage + 1); ?>–<?php echo number_format(min($total, $page * $perPage)); ?> of <?php echo number_format($total); ?> hotels</div>
      <?php echo admin_pagination($page, $pages, static fn(int $p): string => '?' . $qs(['page' => $p > 1 ? $p : ''])); ?>
    </div>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
