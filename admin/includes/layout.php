<?php
/**
 * Club Yatri Admin - page layout (sidebar, top bar, breadcrumbs, flash toasts).
 *
 *   admin_header('Leads', 'leads', [['Leads', null]], 'Subtitle');  ...page...  admin_footer();
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

/** The nine agreed modules. 'ready' = the module exists; others are shown disabled until they are built. */
function admin_nav(): array
{
    $has = static fn(string $p): bool => is_file(__DIR__ . '/../' . $p);
    return [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'href' => 'dashboard.php', 'ready' => true],
        ['key' => 'hotels', 'label' => 'Hotels', 'icon' => 'bi-buildings', 'href' => 'hotels/index.php', 'ready' => $has('hotels/index.php')],
        ['key' => 'destinations', 'label' => 'Destinations', 'icon' => 'bi-geo-alt', 'href' => 'destinations/index.php', 'ready' => $has('destinations/index.php')],
        ['key' => 'itineraries', 'label' => 'Itineraries', 'icon' => 'bi-map', 'href' => 'itineraries/index.php', 'ready' => $has('itineraries/index.php')],
        ['key' => 'passes', 'label' => 'Passes & Subscriptions', 'icon' => 'bi-ticket-perforated', 'href' => 'passes/index.php', 'ready' => $has('passes/index.php')],
        ['key' => 'rooms', 'label' => 'Rooms & Suites', 'icon' => 'bi-door-open', 'href' => 'rooms/index.php', 'ready' => $has('rooms/index.php')],
        ['key' => 'leads', 'label' => 'Leads & Enquiries', 'icon' => 'bi-inbox', 'href' => 'leads/index.php', 'ready' => true],
        ['key' => 'testimonials', 'label' => 'Testimonials', 'icon' => 'bi-chat-quote', 'href' => 'testimonials/index.php', 'ready' => $has('testimonials/index.php')],
        ['key' => 'settings', 'label' => 'Settings', 'icon' => 'bi-gear', 'href' => 'settings/index.php', 'ready' => $has('settings/index.php')],
    ];
}

function admin_unread_leads(PDO $pdo): int
{
    return admin_count($pdo, 'SELECT COUNT(*) FROM inquiries WHERE is_read = 0 AND is_archived = 0 AND is_spam = 0')
        + admin_count($pdo, 'SELECT COUNT(*) FROM bookings b LEFT JOIN booking_lead_state s ON s.booking_id = b.id
                             WHERE COALESCE(s.is_read, 0) = 0 AND COALESCE(s.is_archived, 0) = 0 AND COALESCE(s.is_spam, 0) = 0');
}

/**
 * @param array<int,array{0:string,1:?string}> $crumbs  [label, href|null] after "Dashboard"
 */
function admin_header(string $title, string $active, array $crumbs = [], string $subtitle = '', string $extraHead = ''): void
{
    $user = require_admin();
    $pdo = admin_pdo();
    $unread = admin_unread_leads($pdo);
    $initial = strtoupper(mb_substr($user['name'], 0, 1));
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex,nofollow">
  <title><?php echo e($title); ?> | Club Yatri Admin</title>
  <link rel="icon" type="image/png" href="<?php echo e(site_logo_url()); ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?php echo e(admin_url('assets/admin.css')); ?>?v=<?php echo (int)@filemtime(__DIR__ . '/../assets/admin.css'); ?>" rel="stylesheet">
  <?php echo $extraHead; ?>
</head>
<body class="admin-body">
<div class="admin-shell">
  <aside class="admin-sidebar" id="adminSidebar" aria-label="Main navigation">
    <a class="admin-brand" href="<?php echo e(admin_url('dashboard.php')); ?>">
      <img src="<?php echo e(site_logo_url()); ?>" alt="Club Yatri" width="44" height="44">
      <span>CLUB <b>YATRI</b><small>Admin Panel</small></span>
    </a>
    <nav class="admin-nav">
      <?php foreach (admin_nav() as $item): ?>
        <?php if ($item['ready']): ?>
          <a class="admin-nav-link<?php echo $item['key'] === $active ? ' active' : ''; ?>" href="<?php echo e(admin_url($item['href'])); ?>">
            <i class="bi <?php echo e($item['icon']); ?>"></i><span><?php echo e($item['label']); ?></span>
            <?php if ($item['key'] === 'leads' && $unread > 0): ?><em class="admin-badge"><?php echo $unread > 99 ? '99+' : $unread; ?></em><?php endif; ?>
          </a>
        <?php else: ?>
          <span class="admin-nav-link disabled" title="Coming soon" aria-disabled="true">
            <i class="bi <?php echo e($item['icon']); ?>"></i><span><?php echo e($item['label']); ?></span><em class="admin-soon">Soon</em>
          </span>
        <?php endif; ?>
      <?php endforeach; ?>
    </nav>
    <div class="admin-sidebar-foot">
      <a href="<?php echo e(url('index.php')); ?>" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View website</a>
    </div>
  </aside>
  <div class="admin-backdrop" id="adminBackdrop"></div>

  <div class="admin-main">
    <header class="admin-topbar">
      <button class="admin-menu-btn" id="adminMenuBtn" type="button" aria-label="Toggle menu" aria-controls="adminSidebar"><i class="bi bi-list"></i></button>
      <div class="admin-topbar-title"><?php echo e($title); ?></div>
      <div class="dropdown ms-auto">
        <button class="admin-user" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          <span class="admin-avatar"><?php echo e($initial); ?></span>
          <span class="admin-user-name d-none d-sm-inline"><?php echo e($user['name']); ?></span>
          <i class="bi bi-chevron-down small"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
          <li><span class="dropdown-item-text small text-muted"><?php echo e($user['email']); ?></span></li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form method="post" action="<?php echo e(admin_url('logout.php')); ?>" class="px-3 py-1">
              <?php echo admin_csrf_field(); ?>
              <button class="btn btn-sm btn-outline-danger w-100" type="submit"><i class="bi bi-box-arrow-right"></i> Sign out</button>
            </form>
          </li>
        </ul>
      </div>
    </header>

    <main class="admin-content">
      <nav aria-label="breadcrumb" class="admin-crumbs">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo e(admin_url('dashboard.php')); ?>">Dashboard</a></li>
          <?php foreach ($crumbs as $c): ?>
            <?php if ($c[1]): ?><li class="breadcrumb-item"><a href="<?php echo e(admin_url($c[1])); ?>"><?php echo e($c[0]); ?></a></li>
            <?php else: ?><li class="breadcrumb-item active" aria-current="page"><?php echo e($c[0]); ?></li><?php endif; ?>
          <?php endforeach; ?>
        </ol>
      </nav>
      <div class="admin-page-head">
        <h1><?php echo e($title); ?></h1>
        <?php if ($subtitle !== ''): ?><p><?php echo e($subtitle); ?></p><?php endif; ?>
      </div>
<?php
}

function admin_footer(string $extraScripts = ''): void
{
    $flash = admin_take_flash();
    ?>
    </main>
    <footer class="admin-footer">&copy; <?php echo date('Y'); ?> Club Yatri Hotel and Resort Pvt Ltd &middot; Admin Panel</footer>
  </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" id="adminToasts" style="z-index: 2000;"></div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0"><h2 class="modal-title fs-5" id="confirmTitle">Please confirm</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <div class="modal-body" id="confirmBody">Are you sure?</div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmOk">Confirm</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>window.ADMIN_FLASH = <?php echo json_encode($flash, JSON_HEX_TAG | JSON_HEX_AMP); ?>;</script>
<script src="<?php echo e(admin_url('assets/admin.js')); ?>?v=<?php echo (int)@filemtime(__DIR__ . '/../assets/admin.js'); ?>"></script>
<?php echo $extraScripts; ?>
</body>
</html>
<?php
}
