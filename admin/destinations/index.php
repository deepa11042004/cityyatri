<?php
/**
 * Club Yatri Admin - Destinations: list, search, filters, quick actions.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/crud.php';

$pdo = admin_pdo();
$perPage = 20;
$page = max(1, (int)($_GET['page'] ?? 1));

$q = trim((string)($_GET['q'] ?? ''));
$country = trim((string)($_GET['country'] ?? ''));
$type = (string)($_GET['type'] ?? '');
$status = (string)($_GET['status'] ?? '');
$featured = (string)($_GET['featured'] ?? '');

$where = [];
$params = [];
switch ($status) {
    case 'published': $where[] = 'd.is_published = 1 AND d.is_archived = 0'; break;
    case 'draft': $where[] = 'd.is_published = 0 AND d.is_archived = 0'; break;
    case 'archived': $where[] = 'd.is_archived = 1'; break;
    case 'all': break;
    default: $where[] = 'd.is_archived = 0';
}
if ($q !== '') {
    $like = '%' . addcslashes($q, '%_\\') . '%';
    $where[] = '(d.name LIKE ? OR d.country LIKE ? OR d.region LIKE ?)';
    array_push($params, $like, $like, $like);
}
if ($country !== '') { $where[] = 'd.country = ?'; $params[] = $country; }
if (in_array($type, ['DOMESTIC', 'INTERNATIONAL'], true)) { $where[] = 'd.location_type = ?'; $params[] = $type; }
if ($featured === '1') { $where[] = 'd.featured = 1'; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$st = $pdo->prepare("SELECT COUNT(*) FROM destinations d $whereSql");
$st->execute($params);
$total = (int)$st->fetchColumn();
$pages = max(1, (int)ceil($total / $perPage));
$page = min($page, $pages);

$st = $pdo->prepare("SELECT d.*, (SELECT COUNT(*) FROM hotels h WHERE h.destination_id = d.id AND h.is_archived = 0) AS hotel_count
                     FROM destinations d $whereSql ORDER BY d.name ASC LIMIT $perPage OFFSET " . (($page - 1) * $perPage));
$st->execute($params);
$rows = $st->fetchAll();
$countries = $pdo->query('SELECT DISTINCT country FROM destinations ORDER BY country')->fetchAll(PDO::FETCH_COLUMN);

$keep = ['q' => $q, 'country' => $country, 'type' => $type, 'status' => $status, 'featured' => $featured];
$qs = static fn(array $o = []): string => http_build_query(array_filter(array_merge($keep, $o), static fn($v) => $v !== '' && $v !== null));
$hasFilters = ($q . $country . $type . $status . $featured) !== '';
$tabs = ['' => 'Active', 'published' => 'Published', 'draft' => 'Drafts', 'archived' => 'Archived', 'all' => 'All'];

admin_header('Destinations', 'destinations', [['Destinations', null]], 'Places shown on the Destinations pages and the map.');
?>
<div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
  <ul class="nav nav-pills gap-1">
    <?php foreach ($tabs as $k => $label): ?>
      <li class="nav-item"><a class="nav-link py-1 px-3 <?php echo $status === (string)$k ? 'active bg-danger' : 'text-dark bg-white border'; ?>" href="?<?php echo e($qs(['status' => $k, 'page' => ''])); ?>"><?php echo e($label); ?></a></li>
    <?php endforeach; ?>
  </ul>
  <a class="btn btn-cy btn-sm" href="edit.php"><i class="bi bi-plus-lg"></i> Add destination</a>
</div>

<form class="cy-card cy-card-body mb-3" method="get" action="">
  <?php if ($status !== ''): ?><input type="hidden" name="status" value="<?php echo e($status); ?>"><?php endif; ?>
  <div class="filter-bar">
    <div style="grid-column: span 2;"><label for="q">Search</label><input class="form-control" type="search" id="q" name="q" value="<?php echo e($q); ?>" placeholder="Name, country or region"></div>
    <div><label for="country">Country</label><select class="form-select" id="country" name="country"><option value="">All countries</option>
      <?php foreach ($countries as $c): ?><option <?php echo $country === $c ? 'selected' : ''; ?>><?php echo e($c); ?></option><?php endforeach; ?></select></div>
    <div><label for="type">Region</label><select class="form-select" id="type" name="type"><option value="">India &amp; international</option>
      <option value="DOMESTIC" <?php echo $type === 'DOMESTIC' ? 'selected' : ''; ?>>India</option><option value="INTERNATIONAL" <?php echo $type === 'INTERNATIONAL' ? 'selected' : ''; ?>>International</option></select></div>
    <div class="d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="featured" name="featured" value="1" <?php echo $featured === '1' ? 'checked' : ''; ?>><label class="form-check-label text-body text-capitalize fw-bold" for="featured" style="letter-spacing:0;font-size:.9rem;">Featured only</label></div></div>
    <div class="d-flex gap-2"><button class="btn btn-cy flex-grow-1" type="submit"><i class="bi bi-search"></i> Filter</button>
      <?php if ($hasFilters): ?><a class="btn btn-light border" href="?<?php echo e(http_build_query(array_filter(['status' => $status]))); ?>" title="Clear filters"><i class="bi bi-x-lg"></i></a><?php endif; ?></div>
  </div>
</form>

<div class="cy-card">
  <?php if (!$rows): ?>
    <div class="empty-state"><i class="bi bi-geo-alt"></i><strong><?php echo $hasFilters ? 'No destinations match these filters.' : 'No destinations here yet.'; ?></strong>
      <div class="mt-2"><a class="btn btn-cy btn-sm" href="edit.php"><i class="bi bi-plus-lg"></i> Add a destination</a></div></div>
  <?php else: ?>
    <div class="table-responsive"><table class="table cy-table align-middle">
      <thead><tr><th style="width:84px">Photo</th><th>Destination</th><th>Country / region</th><th>Type</th><th class="text-center">Hotels</th><th class="text-center">Featured</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $d): $edit = 'edit.php?id=' . (int)$d['id']; ?>
        <tr>
          <td><?php if ($d['hero_image']): ?><img src="<?php echo e(url($d['hero_image'])); ?>" alt="" loading="lazy" style="width:72px;height:54px;object-fit:cover;border-radius:10px;"><?php else: ?><span class="d-grid text-muted" style="width:72px;height:54px;place-items:center;background:#F1EEE8;border-radius:10px;"><i class="bi bi-image"></i></span><?php endif; ?></td>
          <td style="min-width:180px"><a class="fw-bold text-decoration-none" href="<?php echo e($edit); ?>"><?php echo e($d['name']); ?></a><div class="small text-muted"><?php echo e((string)$d['tagline']); ?></div></td>
          <td class="small"><?php echo e($d['country']); ?><?php echo $d['region'] ? '<div class="text-muted">' . e($d['region']) . '</div>' : ''; ?></td>
          <td class="small"><?php echo $d['location_type'] === 'DOMESTIC' ? 'India' : 'International'; ?></td>
          <td class="text-center"><a class="text-decoration-none" href="<?php echo e(admin_url('hotels/index.php?destination=' . (int)$d['id'])); ?>"><?php echo (int)$d['hotel_count']; ?></a></td>
          <td class="text-center"><?php echo !empty($d['featured']) ? '<i class="bi bi-star-fill text-warning" title="Featured"></i>' : '<span class="text-muted">—</span>'; ?></td>
          <td><?php echo admin_status_pill($d); ?></td>
          <td class="text-end text-nowrap">
            <a class="btn btn-sm btn-light border" href="<?php echo e($edit); ?>" title="Edit"><i class="bi bi-pencil"></i></a>
            <?php if ($d['is_published'] && !$d['is_archived']): ?><a class="btn btn-sm btn-light border" href="<?php echo e(url('destination-details.php?slug=' . urlencode($d['slug']))); ?>" target="_blank" rel="noopener" title="View on website"><i class="bi bi-box-arrow-up-right"></i></a><?php endif; ?>
            <?php if (!$d['is_archived']): ?>
              <?php echo admin_action_button('action.php', ['id' => $d['id'], 'op' => $d['is_published'] ? 'unpublish' : 'publish', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-light border', $d['is_published'] ? 'bi-eye-slash' : 'bi-eye', '', $d['is_published'] ? 'Unpublish' : 'Publish'); ?>
              <?php echo admin_action_button('action.php', ['id' => $d['id'], 'op' => $d['featured'] ? 'unfeature' : 'feature', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-light border', $d['featured'] ? 'bi-star-fill text-warning' : 'bi-star', '', $d['featured'] ? 'Remove from featured' : 'Mark as featured'); ?>
              <?php echo admin_action_button('action.php', ['id' => $d['id'], 'op' => 'archive', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-light border', 'bi-archive', 'Archive "' . $d['name'] . '"? It is hidden from the website' . ((int)$d['hotel_count'] ? ' (its ' . (int)$d['hotel_count'] . ' hotels stay listed in the Resort Network)' : '') . ' and can be restored later.', 'Archive'); ?>
            <?php else: ?>
              <?php echo admin_action_button('action.php', ['id' => $d['id'], 'op' => 'restore', 'back' => $_SERVER['REQUEST_URI']], 'Restore', 'btn-light border', 'bi-arrow-counterclockwise'); ?>
              <?php echo admin_action_button('action.php', ['id' => $d['id'], 'op' => 'delete', 'back' => $_SERVER['REQUEST_URI']], '', 'btn-outline-danger', 'bi-trash', 'Permanently delete "' . $d['name'] . '"? This cannot be undone.', 'Delete permanently'); ?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody></table></div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-3 border-top">
      <div class="small text-muted">Showing <?php echo number_format(($page - 1) * $perPage + 1); ?>–<?php echo number_format(min($total, $page * $perPage)); ?> of <?php echo number_format($total); ?> destinations</div>
      <?php echo admin_pagination($page, $pages, static fn(int $p): string => '?' . $qs(['page' => $p > 1 ? $p : ''])); ?>
    </div>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
