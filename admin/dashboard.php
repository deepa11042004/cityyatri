<?php
/**
 * Club Yatri Admin - Dashboard (every number comes from the database).
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/leads_repo.php';

$pdo = admin_pdo();

// ---- Statistics --------------------------------------------------------------------
$hotels = admin_count($pdo, 'SELECT COUNT(*) FROM hotels');
$hotelsPub = admin_count($pdo, 'SELECT COUNT(*) FROM hotels WHERE is_published = 1');
$destinations = admin_count($pdo, 'SELECT COUNT(*) FROM destinations');
$itineraries = admin_count($pdo, 'SELECT COUNT(*) FROM itineraries');
$activePasses = admin_count($pdo, 'SELECT COUNT(*) FROM passes WHERE is_published = 1 AND is_archived = 0');
$roomCategories = admin_count($pdo, 'SELECT COUNT(*) FROM room_categories WHERE is_published = 1 AND is_archived = 0');

$base = leads_base_sql();
$leadsTotal = admin_count($pdo, "SELECT COUNT(*) FROM ($base) l WHERE l.is_spam = 0");
$leadsNew = admin_count($pdo, "SELECT COUNT(*) FROM ($base) l WHERE l.is_spam = 0 AND l.is_archived = 0 AND l.status = 'new'");
$leadsUnread = admin_unread_leads($pdo);

// ---- Chart data: last 30 days, and by source ----------------------------------------
$days = [];
for ($i = 29; $i >= 0; $i--) {
    $days[date('Y-m-d', strtotime("-$i day"))] = 0;
}
$st = $pdo->query("SELECT DATE(l.created_at) d, COUNT(*) n FROM ($base) l
                   WHERE l.is_spam = 0 AND l.created_at >= (CURDATE() - INTERVAL 29 DAY) GROUP BY DATE(l.created_at)");
foreach ($st->fetchAll() as $r) {
    if (isset($days[$r['d']])) {
        $days[$r['d']] = (int)$r['n'];
    }
}
$bySource = [];
foreach ($pdo->query("SELECT l.source, COUNT(*) n FROM ($base) l WHERE l.is_spam = 0 GROUP BY l.source ORDER BY n DESC")->fetchAll() as $r) {
    $label = lead_source_label((string)$r['source']);
    $bySource[$label] = ($bySource[$label] ?? 0) + (int)$r['n'];
}

// ---- Lists ---------------------------------------------------------------------------
$recent = leads_fetch($pdo, ['view' => 'active'], 8, 0)['rows'];
$followups = $pdo->query("SELECT l.* FROM ($base) l WHERE l.follow_up_at IS NOT NULL AND l.is_archived = 0 AND l.is_spam = 0
                          AND l.status IN ('new', 'contacted', 'follow_up') ORDER BY l.follow_up_at ASC LIMIT 6")->fetchAll();
$newHotels = $pdo->query('SELECT id, slug, name, location_name, country, created_at FROM hotels ORDER BY id DESC LIMIT 5')->fetchAll();
$newDestinations = $pdo->query('SELECT id, slug, name, country, created_at FROM destinations ORDER BY id DESC LIMIT 5')->fetchAll();

$user = require_admin();
$hour = (int)date('G');
$greet = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

admin_header('Dashboard', 'dashboard', [], $greet . ', ' . explode(' ', $user['name'])[0] . '. Here is what is happening at Club Yatri.');
?>
<div class="row g-3 mb-4">
  <?php
  $cards = [
      ['Total Hotels', number_format($hotels), 'bi-buildings', 'stat-coral', null, $hotelsPub . ' published'],
      ['Destinations', number_format($destinations), 'bi-geo-alt', 'stat-teal', null, ''],
      ['Itineraries', number_format($itineraries), 'bi-map', 'stat-amber', null, ''],
      ['Active Passes', number_format($activePasses), 'bi-ticket-perforated', 'stat-sky', 'passes/index.php', 'published holiday passes'],
      ['Room Categories', number_format($roomCategories), 'bi-door-open', 'stat-lime', 'rooms/index.php', 'published on the website'],
      ['Total Enquiries', number_format($leadsTotal), 'bi-inbox', 'stat-violet', 'leads/index.php?view=all', 'incl. booking requests'],
      ['New Enquiries', number_format($leadsNew), 'bi-stars', 'stat-coral', 'leads/index.php?status=new', 'not yet contacted'],
      ['Unread Enquiries', number_format($leadsUnread), 'bi-envelope-open', 'stat-violet', 'leads/index.php?read=unread', 'waiting to be opened'],
  ];
  foreach ($cards as [$label, $num, $icon, $tone, $href, $sub]): ?>
    <div class="col-6 col-md-4 col-xxl-3">
      <<?php echo $href ? 'a href="' . e(admin_url($href)) . '"' : 'div'; ?> class="stat-card <?php echo e($tone); ?> h-100">
        <span class="stat-icon"><i class="bi <?php echo e($icon); ?>"></i></span>
        <span><span class="stat-num d-block"><?php echo e($num); ?></span><span class="stat-label d-block"><?php echo e($label); ?></span></span>
      </<?php echo $href ? 'a' : 'div'; ?>>
    </div>
  <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
  <div class="col-lg-8">
    <div class="cy-card h-100">
      <div class="cy-card-head"><h2>Enquiries, last 30 days</h2><span class="text-muted small"><?php echo number_format(array_sum($days)); ?> total</span></div>
      <div class="cy-card-body"><div style="position: relative; height: 280px;"><canvas id="chartDays" aria-label="Enquiries per day, last 30 days" role="img"></canvas></div></div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="cy-card h-100">
      <div class="cy-card-head"><h2>Enquiries by source</h2></div>
      <div class="cy-card-body">
        <?php if (!$bySource): ?>
          <div class="empty-state py-4"><i class="bi bi-pie-chart"></i>No enquiries yet.</div>
        <?php else: ?>
          <div style="position: relative; height: 280px;"><canvas id="chartSource" aria-label="Enquiries by source" role="img"></canvas></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-lg-8">
    <div class="cy-card h-100">
      <div class="cy-card-head"><h2>Recent enquiries</h2><a class="btn btn-sm btn-light border" href="<?php echo e(admin_url('leads/index.php')); ?>">View all</a></div>
      <?php if (!$recent): ?>
        <div class="empty-state"><i class="bi bi-inbox"></i>No enquiries yet. They will appear here the moment a visitor submits a form.</div>
      <?php else: ?>
        <div class="table-responsive"><table class="table cy-table">
          <thead><tr><th>Customer</th><th>Source</th><th>Received</th><th>Status</th></tr></thead>
          <tbody>
          <?php foreach ($recent as $r): ?>
            <tr class="<?php echo (int)$r['is_read'] ? '' : 'is-unread'; ?>">
              <td><a class="text-decoration-none text-reset" href="<?php echo e(admin_url('leads/view.php?kind=' . $r['kind'] . '&id=' . (int)$r['id'])); ?>"><?php echo e($r['name'] !== '' ? $r['name'] : '(no name)'); ?></a>
                <div class="small text-muted"><?php echo e($r['phone'] ?: $r['email']); ?></div></td>
              <td><span class="src src-<?php echo e($r['source']); ?>"><?php echo e(lead_source_label((string)$r['source'])); ?></span></td>
              <td class="small text-nowrap"><?php echo e(admin_ago((string)$r['created_at'])); ?></td>
              <td><span class="pill pill-<?php echo e($r['status']); ?>"><?php echo e(lead_status_label((string)$r['status'])); ?></span></td>
            </tr>
          <?php endforeach; ?>
          </tbody></table></div>
      <?php endif; ?>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="cy-card h-100">
      <div class="cy-card-head"><h2>Pending follow-ups</h2></div>
      <div class="cy-card-body">
        <?php if (!$followups): ?>
          <p class="text-muted mb-0">No follow-ups are scheduled. Set a reminder on any lead and it will show up here.</p>
        <?php else: foreach ($followups as $f):
            $overdue = strtotime((string)$f['follow_up_at']) < time(); ?>
          <a class="d-flex justify-content-between align-items-start gap-2 text-decoration-none text-reset note-item" href="<?php echo e(admin_url('leads/view.php?kind=' . $f['kind'] . '&id=' . (int)$f['id'])); ?>">
            <span><strong><?php echo e($f['name'] ?: '(no name)'); ?></strong><br><small><?php echo e(lead_source_label((string)$f['source'])); ?></small></span>
            <small class="text-end <?php echo $overdue ? 'text-danger fw-bold' : ''; ?>"><?php echo $overdue ? 'Overdue<br>' : ''; ?><?php echo e(date('j M, g:i a', strtotime((string)$f['follow_up_at']))); ?></small>
          </a>
        <?php endforeach; endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="cy-card h-100">
      <div class="cy-card-head"><h2>Recently added hotels</h2></div>
      <ul class="list-group list-group-flush">
        <?php foreach ($newHotels as $h): ?>
          <li class="list-group-item d-flex justify-content-between gap-2"><span><?php echo e($h['name']); ?><br><small class="text-muted"><?php echo e($h['location_name'] . ', ' . $h['country']); ?></small></span><small class="text-muted text-nowrap"><?php echo e(admin_ago((string)$h['created_at'])); ?></small></li>
        <?php endforeach; ?>
        <?php if (!$newHotels): ?><li class="list-group-item text-muted">No hotels yet.</li><?php endif; ?>
      </ul>
    </div>
  </div>
  <div class="col-md-6">
    <div class="cy-card h-100">
      <div class="cy-card-head"><h2>Recently added destinations</h2></div>
      <ul class="list-group list-group-flush">
        <?php foreach ($newDestinations as $d): ?>
          <li class="list-group-item d-flex justify-content-between gap-2"><span><?php echo e($d['name']); ?><br><small class="text-muted"><?php echo e($d['country']); ?></small></span><small class="text-muted text-nowrap"><?php echo e(admin_ago((string)$d['created_at'])); ?></small></li>
        <?php endforeach; ?>
        <?php if (!$newDestinations): ?><li class="list-group-item text-muted">No destinations yet.</li><?php endif; ?>
      </ul>
    </div>
  </div>
</div>

<div class="cy-card">
  <div class="cy-card-head"><h2>Quick actions</h2></div>
  <div class="cy-card-body d-flex flex-wrap gap-2">
    <a class="btn btn-cy" href="<?php echo e(admin_url('leads/index.php?read=unread')); ?>"><i class="bi bi-envelope-open"></i> Open unread enquiries</a>
    <a class="btn btn-cy-gold" href="<?php echo e(admin_url('leads/export.php')); ?>"><i class="bi bi-download"></i> Export all leads (CSV)</a>
    <a class="btn btn-light border" href="<?php echo e(url('index.php')); ?>" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View website</a>
  </div>
</div>
<?php
$labels = array_map(static fn($d) => date('j M', strtotime($d)), array_keys($days));
$js = '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script><script>
(function () {
  if (!window.Chart) { return; }
  Chart.defaults.font.family = "Google Sans, Plus Jakarta Sans, sans-serif";
  var days = document.getElementById("chartDays");
  if (days) {
    new Chart(days, { type: "line",
      data: { labels: ' . json_encode($labels) . ', datasets: [{ label: "Enquiries", data: ' . json_encode(array_values($days)) . ',
        borderColor: "#EA2224", backgroundColor: "rgba(234,34,36,0.12)", fill: true, tension: 0.35, pointRadius: 3, pointBackgroundColor: "#EA2224" }] },
      options: { maintainAspectRatio: false, plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: "#F0EBE2" } }, x: { grid: { display: false }, ticks: { maxTicksLimit: 8 } } } } });
  }
  var src = document.getElementById("chartSource");
  if (src) {
    new Chart(src, { type: "doughnut",
      data: { labels: ' . json_encode(array_keys($bySource)) . ', datasets: [{ data: ' . json_encode(array_values($bySource)) . ',
        backgroundColor: ["#EA2224", "#FFB300", "#4FC4BE", "#8B7BEA", "#F2766B", "#4B9CE2", "#8CBF2E"], borderWidth: 2, borderColor: "#fff" }] },
      options: { maintainAspectRatio: false, cutout: "62%", plugins: { legend: { position: "bottom", labels: { boxWidth: 12, padding: 14 } } } } });
  }
})();
</script>';
admin_footer($js);
