<?php
/**
 * Club Yatri - Vacation Booking Request Page
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/data/destinations.php';
require_once __DIR__ . '/data/resorts.php';
require_once __DIR__ . '/data/rooms.php';
require_once __DIR__ . '/data/subscriptions.php';

$pageTitle = 'Vacation Week Booking Request';
$pageDescription = 'Submit a vacation booking request to redeem your Club Yatri holiday pass allocation across premier national and international resorts.';

$breadcrumbTitle = 'Booking Request';

$prefillResort = $_GET['resort'] ?? '';
$prefillRoom = $_GET['room'] ?? '';
$prefillPlan = $_GET['plan'] ?? '';
// Homepage search panel: destination id, room category and season
if ($prefillRoom === '' && !empty($_GET['room_category'])) {
    $prefillRoom = (string)$_GET['room_category'];
}
$prefillSeason = ucfirst(strtolower((string)($_GET['season'] ?? ''))) . ' Studio';
$confirmedCode = $_GET['confirmed'] ?? '';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 60px; padding-bottom: 80px;">
  <div class="container" style="max-width: 840px;">
    
    <?php if ($confirmedCode): ?>
      <div style="background: rgba(0, 201, 167, 0.12); border: 1.5px solid var(--teal-accent); border-radius: var(--radius-xl); padding: 36px; text-align: center; margin-bottom: 40px;">
        <div style="font-size: 3rem; margin-bottom: 12px;">🎉</div>
        <h2 class="heading-serif" style="color: #fff; font-size: 1.8rem; margin-bottom: 8px;">Reservation Logged Successfully</h2>
        <div style="font-size: 1.1rem; color: var(--gold-light); font-weight: 700; margin-bottom: 14px;">
          Tracking Code: <?php echo e($confirmedCode); ?>
        </div>
        <p style="color: var(--text-secondary); max-width: 580px; margin: 0 auto; line-height: 1.6; font-size: 0.95rem;">
          Your vacation booking request has been registered in the priority allocation pool. Your Senior Lifestyle Concierge is verifying partner resort inventory and will connect with your confirmation voucher.
        </p>
      </div>
    <?php endif; ?>

    <div style="background: var(--bg-card); border: 1.5px solid var(--border-gold); border-radius: var(--radius-xl); padding: 48px; box-shadow: var(--shadow-lg);">
      <div class="sub-tag" style="margin-bottom: 10px;">Priority Member Allocation</div>
      <h1 class="heading-serif" style="font-size: 2.2rem; color: #fff; margin-bottom: 12px;">Vacation Reservation Desk</h1>
      <p style="color: var(--text-secondary); margin-bottom: 32px; font-size: 0.95rem; line-height: 1.6;">
        Submit your preferred dates, destination, and room category. Whether you are an existing pass holder or checking allocation availability, our hospitality desk processes requests within 60 minutes.
      </p>

      <form action="<?php echo url('actions/submit-booking-request.php'); ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
          <div class="form-group">
            <label>Full Legal Name *</label>
            <input type="text" name="full_name" class="form-control" required placeholder="e.g. Arvind Kapadia">
          </div>
          <div class="form-group">
            <label>Primary Phone Number *</label>
            <input type="tel" name="phone" class="form-control" required placeholder="+91 98765 43210">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
          <div class="form-group">
            <label>Email Address *</label>
            <input type="email" name="email" class="form-control" required placeholder="arvind@email.com">
          </div>
          <div class="form-group">
            <label>Subscription / Pass ID (If applicable)</label>
            <input type="text" name="member_id" class="form-control" placeholder="e.g. CY-8829-PLT or Prospective Owner">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
          <div class="form-group">
            <label>Selected Destination / Resort *</label>
            <select name="destination" class="form-control" required>
              <?php foreach ($resortsList as $resort): ?>
                <option value="<?php echo e($resort['name']); ?>" <?php echo ($prefillResort === $resort['id'] || ($prefillResort === '' && !empty($_GET['destination']) && $_GET['destination'] === ($resort['destination_id'] ?? ''))) ? 'selected' : ''; ?>>
                  <?php echo e($resort['name']); ?> (<?php echo e($resort['location']); ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Accommodation Category *</label>
            <select name="room_type" class="form-control" required>
              <?php foreach ($roomCategories as $room): ?>
                <option value="<?php echo e($room['title']); ?>" <?php echo ($prefillRoom === $room['id']) ? 'selected' : ''; ?>>
                  <?php echo e($room['title']); ?> (<?php echo e($room['capacity']); ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
          <div class="form-group">
            <label>Season Category</label>
            <select name="season" class="form-control">
              <option value="Purple Studio"<?php echo ($prefillSeason === 'Purple Studio') ? ' selected' : ''; ?>>Purple Studio (Regular)</option>
              <option value="Yellow Studio"<?php echo ($prefillSeason === 'Yellow Studio') ? ' selected' : ''; ?>>Yellow Studio (Peak/Weekend)</option>
              <option value="Red Studio"<?php echo ($prefillSeason === 'Red Studio') ? ' selected' : ''; ?>>Red Studio (Festive/Diwali)</option>
              <option value="Green Studio"<?php echo ($prefillSeason === 'Green Studio') ? ' selected' : ''; ?>>Green Studio (Off-Peak/Monsoon)</option>
            </select>
          </div>
          <div class="form-group">
            <label>Check-in Date *</label>
            <input type="date" name="checkin" class="form-control" required value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>">
          </div>
          <div class="form-group">
            <label>Duration of Stay</label>
            <select name="nights" class="form-control">
              <option value="7">7 Nights (Full Week)</option>
              <option value="4">4 Nights (Mid-Week)</option>
              <option value="14">14 Nights (Fortnight)</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label>Special Requests or Additional Guests</label>
          <textarea name="notes" class="form-control" rows="3" placeholder="Airport transfer requirements, extra beds, dietary requests..."></textarea>
        </div>

        <button type="submit" class="btn btn-gold btn-lg" style="width: 100%;">
          Submit Booking Request to Lifestyle Concierge
        </button>
      </form>
    </div>

  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
