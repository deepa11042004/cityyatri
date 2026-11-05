<?php
/**
 * Club Yatri Admin - Leads & Enquiries: list with search, filters and pagination.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/leads_repo.php';

$pdo = admin_pdo();
$perPage = 20;
$page = max(1, (int)($_GET['page'] ?? 1));
$result = leads_fetch($pdo, $_GET, $perPage, ($page - 1) * $perPage);
$rows = $result['rows'];
$total = $result['total'];
$pages = max(1, (int)ceil($total / $perPage));
if ($page > $pages) {
    $page = $pages;
}

$view = (string)($_GET['view'] ?? 'active');
$tabs = ['active' => 'Active', 'archived' => 'Archived', 'spam' => 'Spam', 'all' => 'All'];
$qs = static fn(array $o = []): string => leads_query_string($_GET, $o);
$hasFilters = array_filter(['q' => $_GET['q'] ?? '', 'source' => $_GET['source'] ?? '', 'status' => $_GET['status'] ?? '', 'from' => $_GET['from'] ?? '', 'to' => $_GET['to'] ?? '', 'read' => $_GET['read'] ?? '']) !== [];

admin_header('Leads & Enquiries', 'leads', [['Leads & Enquiries', null]], 'Every enquiry and booking request from the website, in one place.');
?>
<div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
  <ul class="nav nav-pills gap-1">
    <?php foreach ($tabs as $k => $label): ?>
      <li class="nav-item"><a class="nav-link py-1 px-3 <?php echo $view === $k ? 'active bg-danger' : 'text-dark bg-white border'; ?>" href="?<?php echo e($qs(['view' => $k])); ?>"><?php echo e($label); ?></a></li>
    <?php endforeach; ?>
  </ul>
  <a class="btn btn-cy-gold btn-sm" href="export.php?<?php echo e($qs()); ?>"><i class="bi bi-download"></i> Export CSV <span class="text-muted fw-normal">(<?php echo number_format($total); ?>)</span></a>
</div>

<form class="cy-card cy-card-body mb-3" method="get" action="">
  <?php if ($view !== 'active'): ?><input type="hidden" name="view" value="<?php echo e($view); ?>"><?php endif; ?>
  <div class="filter-bar">
    <div style="grid-column: span 2;">
      <label for="q">Search</label>
      <input class="form-control" type="search" id="q" name="q" value="<?php echo e($_GET['q'] ?? ''); ?>" placeholder="Name, email, phone or reference">
    </div>
    <div>
      <label for="source">Source</label>
      <select class="form-select" id="source" name="source">
        <option value="">All sources</option>
        <?php foreach (LEAD_SOURCES as $k => $label): ?><option value="<?php echo e($k); ?>" <?php echo ($_GET['source'] ?? '') === $k ? 'selected' : ''; ?>><?php echo e($label); ?></option><?php endforeach; ?>
      </select>
    </div>
    <div>
      <label for="status">Status</label>
      <select class="form-select" id="status" name="status">
        <option value="">All statuses</option>
        <?php foreach (LEAD_STATUSES as $k => $label): ?><option value="<?php echo e($k); ?>" <?php echo ($_GET['status'] ?? '') === $k ? 'selected' : ''; ?>><?php echo e($label); ?></option><?php endforeach; ?>
      </select>
    </div>
    <div>
      <label for="read">Read state</label>
      <select class="form-select" id="read" name="read">
        <option value="">Read &amp; unread</option>
        <option value="unread" <?php echo ($_GET['read'] ?? '') === 'unread' ? 'selected' : ''; ?>>Unread only</option>
        <option value="read" <?php echo ($_GET['read'] ?? '') === 'read' ? 'selected' : ''; ?>>Read only</option>
      </select>
    </div>
    <div><label for="from">From</label><input class="form-control" type="date" id="from" name="from" value="<?php echo e($_GET['from'] ?? ''); ?>"></div>
    <div><label for="to">To</label><input class="form-control" type="date" id="to" name="to" value="<?php echo e($_GET['to'] ?? ''); ?>"></div>
    <div class="d-flex gap-2">
      <button class="btn btn-cy flex-grow-1" type="submit"><i class="bi bi-search"></i> Filter</button>
      <?php if ($hasFilters): ?><a class="btn btn-light border" href="?<?php echo e($qs(['q' => '', 'source' => '', 'status' => '', 'from' => '', 'to' => '', 'read' => ''])); ?>" title="Clear filters"><i class="bi bi-x-lg"></i></a><?php endif; ?>
    </div>
  </div>
</form>

<div class="cy-card">
  <?php if (!$rows): ?>
    <div class="empty-state"><i class="bi bi-inbox"></i>
      <strong><?php echo $hasFilters ? 'No leads match these filters.' : 'No leads here yet.'; ?></strong>
      <div class="mt-1"><?php echo $hasFilters ? 'Try clearing a filter.' : 'New website enquiries will appear here as soon as they arrive.'; ?></div>
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table cy-table align-middle">
        <thead><tr>
          <th>Ref</th><th>Customer</th><th>Contact</th><th>Source</th><th>Subject</th><th>Received</th><th>Status</th><th class="text-end">Actions</th>
        </tr></thead>
        <tbody>
        <?php foreach ($rows as $r):
            $unread = !(int)$r['is_read'];
            $wa = lead_whatsapp_url((string)$r['phone']);
            $href = 'view.php?kind=' . urlencode($r['kind']) . '&id=' . (int)$r['id'];
            ?>
          <tr class="<?php echo $unread ? 'is-unread' : ''; ?>">
            <td><a class="text-decoration-none" href="<?php echo e($href); ?>"><?php echo e($r['ref']); ?></a></td>
            <td><?php echo e($r['name'] !== '' ? $r['name'] : '(no name)'); ?></td>
            <td class="small">
              <?php if ($r['phone'] !== ''): ?><div><i class="bi bi-telephone text-muted"></i> <?php echo e($r['phone']); ?></div><?php endif; ?>
              <?php if ($r['email'] !== ''): ?><div class="text-muted"><?php echo e($r['email']); ?></div><?php endif; ?>
            </td>
            <td><span class="src src-<?php echo e($r['source']); ?>"><?php echo e(lead_source_label((string)$r['source'])); ?></span></td>
            <td class="small" style="max-width: 240px;"><?php echo e(mb_strimwidth((string)$r['subject'], 0, 60, '…')); ?></td>
            <td class="small text-nowrap" title="<?php echo e($r['created_at']); ?>"><?php echo e(date('j M Y, g:i a', strtotime((string)$r['created_at']))); ?><div class="text-muted"><?php echo e(admin_ago((string)$r['created_at'])); ?></div></td>
            <td>
              <span class="pill pill-<?php echo e($r['status']); ?>"><?php echo e(lead_status_label((string)$r['status'])); ?></span>
              <?php if ($r['follow_up_at']): ?><div class="small text-muted mt-1"><i class="bi bi-alarm"></i> <?php echo e(date('j M, g:i a', strtotime((string)$r['follow_up_at']))); ?></div><?php endif; ?>
            </td>
            <td class="text-end text-nowrap">
              <a class="btn btn-sm btn-light border" href="<?php echo e($href); ?>" title="Open"><i class="bi bi-eye"></i></a>
              <?php if ($r['phone'] !== ''): ?><a class="btn btn-sm btn-light border" href="tel:<?php echo e(preg_replace('/[^\d+]/', '', (string)$r['phone'])); ?>" title="Call"><i class="bi bi-telephone"></i></a><?php endif; ?>
              <?php if ($wa !== ''): ?><a class="btn btn-sm btn-light border" href="<?php echo e($wa); ?>" target="_blank" rel="noopener" title="WhatsApp"><i class="bi bi-whatsapp text-success"></i></a><?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-3 border-top">
      <div class="small text-muted">Showing <?php echo number_format(($page - 1) * $perPage + 1); ?>–<?php echo number_format(min($total, $page * $perPage)); ?> of <?php echo number_format($total); ?></div>
      <?php if ($pages > 1):
          $win = array_values(array_unique(array_filter([1, $page - 1, $page, $page + 1, $pages], static fn($p) => $p >= 1 && $p <= $pages)));
          sort($win);
          ?>
        <nav aria-label="Pages"><ul class="pagination pagination-sm mb-0">
          <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>"><a class="page-link" href="?<?php echo e($qs(['page' => $page - 1])); ?>">&laquo;</a></li>
          <?php $prev = 0; foreach ($win as $p): if ($p - $prev > 1): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; $prev = $p; ?>
            <li class="page-item <?php echo $p === $page ? 'active' : ''; ?>"><a class="page-link" href="?<?php echo e($qs(['page' => $p])); ?>"><?php echo $p; ?></a></li>
          <?php endforeach; ?>
          <li class="page-item <?php echo $page >= $pages ? 'disabled' : ''; ?>"><a class="page-link" href="?<?php echo e($qs(['page' => $page + 1])); ?>">&raquo;</a></li>
        </ul></nav>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
