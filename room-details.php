<?php
/**
 * Club Yatri - Room Category Details
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/data/rooms.php';

$roomId = $_GET['id'] ?? 'luxury-suite';
$room = $roomCategories[$roomId] ?? $roomCategories['luxury-suite'];

$pageTitle = $room['title'] . ' Details';
$pageDescription = 'Detailed specifications, capacity, and luxury amenities for ' . $room['title'] . ' at Club Yatri resorts.';

$breadcrumbTitle = $room['title'];
$parentTitle = 'Rooms & Suites';
$parentUrl = url('rooms.php');

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 60px; padding-bottom: 80px;">
  <div class="container">
    <div class="grid-2" style="align-items: start; gap: 48px;">
      
      <!-- Media & Specs -->
      <div>
        <div style="border-radius: var(--radius-xl); overflow: hidden; border: 1.5px solid var(--border-gold); box-shadow: var(--shadow-lg); margin-bottom: 24px;">
          <img src="<?php echo e($room['image']); ?>" alt="<?php echo e($room['title']); ?>" style="width: 100%; height: 420px; object-fit: cover; display: block;">
        </div>

        <div class="card-luxury" style="padding: 28px;">
          <h3 class="heading-serif" style="font-size: 1.35rem; color: #fff; margin-bottom: 16px;">Key Suite Attributes</h3>
          <ul style="list-style: none; display: flex; flex-direction: column; gap: 12px; font-size: 0.95rem; color: var(--text-secondary);">
            <li style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
              <span>Guest Capacity:</span>
              <strong style="color: #fff;"><?php echo e($room['capacity']); ?></strong>
            </li>
            <li style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
              <span>Living Dimensions:</span>
              <strong style="color: #fff;"><?php echo e($room['area']); ?></strong>
            </li>
            <li style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
              <span>Bedding Configuration:</span>
              <strong style="color: #fff;"><?php echo e($room['bed_type']); ?></strong>
            </li>
            <li style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
              <span>Points Requirement:</span>
              <strong style="color: var(--gold-light);"><?php echo format_inr($room['points_night']); ?> Pts / Night</strong>
            </li>
          </ul>
        </div>
      </div>

      <!-- Content & Booking Trigger -->
      <div>
        <div class="sub-tag">Accommodations Profile</div>
        <h1 class="heading-serif" style="font-size: 2.6rem; margin-bottom: 16px;"><?php echo e($room['title']); ?></h1>
        <p style="font-size: 1.05rem; color: var(--text-secondary); line-height: 1.8; margin-bottom: 28px;">
          <?php echo e($room['description']); ?>
        </p>

        <h3 class="heading-serif" style="font-size: 1.4rem; color: #fff; margin-bottom: 16px;">Included In-Room Privileges</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 36px;">
          <?php foreach ($room['amenities'] as $amenity): ?>
            <div style="display: flex; align-items: center; gap: 10px; font-size: 0.92rem; color: var(--text-secondary);">
              <span style="color: var(--gold-primary); font-weight: 700;">✓</span>
              <span><?php echo e($amenity); ?></span>
            </div>
          <?php endforeach; ?>
        </div>

        <div style="background: rgba(22, 38, 60, 0.7); border: 1px solid var(--border-gold); border-radius: var(--radius-lg); padding: 32px;">
          <h4 style="font-size: 1.2rem; color: #fff; margin-bottom: 8px;">Reserve This Accommodation</h4>
          <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 20px;">
            Available across all national resorts with eligible Yatri holiday passes.
          </p>
          <a href="<?php echo url('booking-request.php?room=' . urlencode($room['id'])); ?>" class="btn btn-gold btn-lg" style="width: 100%;">
            Reserve Suite with Your Holiday Pass
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
