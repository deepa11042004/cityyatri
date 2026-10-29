<?php
/**
 * Club Yatri - Member Dashboard (reads member, points and bookings from the database)
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/db.php';

if (empty($_SESSION['member_authenticated'])) {
    header('Location: ' . url('login.php'), true, 302);
    exit;
}

$member = null;
$bookings = [];
$dbError = false;
try {
    if ($pdo = db()) {
        $st = $pdo->prepare('SELECT * FROM members WHERE member_id = ? OR email = ? LIMIT 1');
        $st->execute([$_SESSION['member_id'] ?? '', $_SESSION['member_email'] ?? '']);
        $member = $st->fetch() ?: null;

        if ($member) {
            $st = $pdo->prepare('SELECT * FROM bookings WHERE member_id = ? OR guest_email = ? ORDER BY created_at DESC LIMIT 20');
            $st->execute([$member['member_id'], $member['email']]);
            $bookings = $st->fetchAll();
        }
    } else {
        $dbError = true;
    }
} catch (PDOException $e) {
    error_log('Dashboard query failed: ' . $e->getMessage());
    $dbError = true;
}

$pageTitle = 'My Dashboard | Club Yatri';
$pageDescription = 'Your Club Yatri membership, points balance and booking requests.';
$breadcrumbTitle = 'Dashboard';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';

$card = 'background:#FFFFFF;border:1.5px solid var(--color-border);border-radius:var(--radius-card);padding:24px;box-shadow:var(--shadow-card);';
?>

<section class="section" style="padding-top: 40px; padding-bottom: 80px; background: var(--color-background-soft);">
  <div class="container" style="max-width: 1000px;">

    <?php if ($dbError): ?>
      <div style="<?php echo $card; ?>">
        <h1 class="heading-serif" style="font-size:1.6rem;">Dashboard unavailable</h1>
        <p style="color: var(--color-text-muted);">We couldn't reach the membership database. Please try again shortly.</p>
      </div>
    <?php elseif (!$member): ?>
      <div style="<?php echo $card; ?>">
        <h1 class="heading-serif" style="font-size:1.6rem;">Welcome, <?php echo e($_SESSION['member_name'] ?? 'Member'); ?></h1>
        <p style="color: var(--color-text-muted);">No membership record is linked to this account yet. Please contact Concierge Support at <?php echo e(CONTACT_PHONE_TOLL_FREE); ?>.</p>
      </div>
    <?php else:
      $total = max(1, (int)$member['total_annual_points']);
      $pct = min(100, (int)round($member['points_balance'] / $total * 100));
    ?>
      <div style="margin-bottom: 24px;">
        <div class="sub-tag" style="margin-bottom: 8px;"><?php echo e($member['pass_tier']); ?></div>
        <h1 class="heading-serif" style="font-size: 1.85rem;">Welcome back, <?php echo e($member['full_name']); ?></h1>
        <p style="color: var(--color-text-muted);">Member ID <?php echo e($member['member_id']); ?> · <?php echo e(ucfirst($member['status'])); ?> · Member since <?php echo e(date('j M Y', strtotime($member['membership_year_start']))); ?></p>
      </div>

      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-bottom:28px;">
        <div style="<?php echo $card; ?>">
          <div style="font-size:.85rem;color:var(--color-text-muted);">Points Balance</div>
          <div style="font-size:1.9rem;font-weight:700;"><?php echo number_format((int)$member['points_balance']); ?></div>
          <div style="height:8px;background:var(--color-background-soft);border-radius:4px;margin-top:10px;">
            <div style="height:8px;width:<?php echo $pct; ?>%;background:var(--color-primary);border-radius:4px;"></div>
          </div>
          <div style="font-size:.8rem;color:var(--color-text-muted);margin-top:6px;">of <?php echo number_format($total); ?> annual points</div>
        </div>
        <div style="<?php echo $card; ?>">
          <div style="font-size:.85rem;color:var(--color-text-muted);">Nights Remaining</div>
          <div style="font-size:1.9rem;font-weight:700;"><?php echo (int)$member['nights_balance']; ?></div>
        </div>
        <div style="<?php echo $card; ?>">
          <div style="font-size:.85rem;color:var(--color-text-muted);">Contact</div>
          <div style="font-size:.95rem;"><?php echo e($member['email']); ?><br><?php echo e($member['phone']); ?></div>
        </div>
      </div>

      <div style="<?php echo $card; ?>">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
          <h2 class="heading-serif" style="font-size:1.35rem;margin:0;">Your Booking Requests</h2>
          <a href="<?php echo url('booking-request.php'); ?>" class="btn btn-primary">New Request</a>
        </div>
        <?php if (!$bookings): ?>
          <p style="color: var(--color-text-muted);">No booking requests yet.</p>
        <?php else: ?>
          <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:.92rem;">
              <thead>
                <tr style="text-align:left;border-bottom:1.5px solid var(--color-border);">
                  <th style="padding:8px;">Code</th><th style="padding:8px;">Destination</th><th style="padding:8px;">Room</th>
                  <th style="padding:8px;">Check-in</th><th style="padding:8px;">Nights</th><th style="padding:8px;">Points</th><th style="padding:8px;">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($bookings as $b): ?>
                  <tr style="border-bottom:1px solid var(--color-border);">
                    <td style="padding:8px;"><?php echo e($b['booking_code']); ?></td>
                    <td style="padding:8px;"><?php echo e($b['destination_name']); ?></td>
                    <td style="padding:8px;"><?php echo e($b['room_type']); ?></td>
                    <td style="padding:8px;"><?php echo e(date('j M Y', strtotime($b['checkin_date']))); ?></td>
                    <td style="padding:8px;"><?php echo (int)$b['duration_nights']; ?></td>
                    <td style="padding:8px;"><?php echo number_format((int)$b['points_deducted']); ?></td>
                    <td style="padding:8px;"><?php echo e(ucfirst($b['status'])); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
