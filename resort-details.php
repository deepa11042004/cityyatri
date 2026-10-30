<?php
/**
 * Club Yatri - Resort Details & Availability
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/data/resorts.php';

require_once __DIR__ . '/includes/hotels.php';

$resortId = $_GET['id'] ?? 'azure-sands-goa';

// Hotels imported into the database are addressed by slug; curated resorts keep their own layout.
$hotel = (isset($_GET['id']) && !isset($resortsList[$resortId])) ? hotel_find((string)$resortId) : null;
if ($hotel !== null) {
    $pageTitle = $hotel['name'] . ' - ' . $hotel['location_name'];
    $pageDescription = hotel_excerpt($hotel['description'] ?? '', 160);
    $breadcrumbTitle = $hotel['name'];
    $parentTitle = 'Resort Network';
    $parentUrl = url('resort-network.php');

    include __DIR__ . '/includes/header.php';
    include __DIR__ . '/includes/navbar.php';
    include __DIR__ . '/includes/breadcrumbs.php';
    include __DIR__ . '/includes/hotel-detail.php';
    include __DIR__ . '/includes/footer.php';
    return;
}

$resort = $resortsList[$resortId] ?? $resortsList['azure-sands-goa'];

$pageTitle = $resort['name'];
$pageDescription = $resort['name'] . ' in ' . $resort['location'] . ' - Luxury vacation ownership property with Club Yatri.';

$breadcrumbTitle = $resort['name'];
$parentTitle = 'Destinations';
$parentUrl = url('destinations.php');

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 60px; padding-bottom: 80px;">
  <div class="container">
    <div class="grid-2" style="align-items: start; gap: 48px;">
      
      <!-- Visuals & Quick Facts -->
      <div>
        <div style="border-radius: var(--radius-xl); overflow: hidden; border: 1.5px solid var(--border-gold); box-shadow: var(--shadow-lg); margin-bottom: 24px;">
          <img src="<?php echo e($resort['image']); ?>" alt="<?php echo e($resort['name']); ?>" style="width: 100%; height: 440px; object-fit: cover; display: block;">
        </div>

        <div class="card-luxury" style="padding: 28px;">
          <h3 class="heading-serif" style="font-size: 1.3rem; color: #fff; margin-bottom: 16px;">Property Highlights</h3>
          <ul style="list-style: none; display: flex; flex-direction: column; gap: 12px; font-size: 0.95rem; color: var(--text-secondary);">
            <li style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
              <span>Geographic Location:</span>
              <strong style="color: #fff;"><?php echo e($resort['location']); ?></strong>
            </li>
            <li style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
              <span>Resort Classification:</span>
              <strong style="color: var(--gold-light);"><?php echo e($resort['category']); ?></strong>
            </li>
            <li style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
              <span>Points per Night:</span>
              <strong style="color: var(--teal-accent);"><?php echo format_inr($resort['points_night']); ?> Pts</strong>
            </li>
            <li style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
              <span>Eligible Ownership Nights:</span>
              <strong style="color: #fff;"><?php echo e($resort['allotment']); ?></strong>
            </li>
            <li style="display: flex; justify-content: space-between; padding-top: 4px;">
              <span>Hospitality Rating:</span>
              <strong style="color: var(--gold-primary);"><?php echo render_stars((int)$resort['rating']); ?> (5-Star Luxury)</strong>
            </li>
          </ul>
        </div>
      </div>

      <!-- Content & Booking Trigger -->
      <div>
        <div class="sub-tag"><?php echo e($resort['region']); ?> Portfolio</div>
        <h1 class="heading-serif" style="font-size: 2.6rem; margin-bottom: 14px;"><?php echo e($resort['name']); ?></h1>
        <div class="card-location" style="font-size: 1rem; margin-bottom: 24px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
          <span><?php echo e($resort['location']); ?></span>
        </div>

        <p style="font-size: 1.05rem; color: var(--text-secondary); line-height: 1.8; margin-bottom: 24px;">
          <?php echo e($resort['details']); ?>
        </p>

        <h3 class="heading-serif" style="font-size: 1.4rem; color: #fff; margin-bottom: 16px;">Resort Amenities & Features</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 36px;">
          <?php foreach ($resort['amenities'] as $amenity): ?>
            <span class="amenity-chip" style="font-size: 0.88rem; padding: 6px 16px;"><?php echo e($amenity); ?></span>
          <?php endforeach; ?>
        </div>

        <div style="background: rgba(22, 38, 60, 0.7); border: 1.5px solid var(--border-gold); border-radius: var(--radius-xl); padding: 32px;">
          <h4 style="font-size: 1.3rem; color: #fff; margin-bottom: 8px;">Reserve Stay at <?php echo e($resort['name']); ?></h4>
          <p style="font-size: 0.92rem; color: var(--text-secondary); margin-bottom: 20px;">
            Redeem your annual pass points with instant confirmation.
          </p>
          <a href="<?php echo url('booking-request.php?resort=' . urlencode($resort['id'])); ?>" class="btn btn-gold btn-lg" style="width: 100%;">
            Reserve Vacation Week with Your Pass
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
