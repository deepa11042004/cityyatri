<?php
/**
 * Club Yatri - Resort Amenities & Lifestyle Perks
 */

declare(strict_types=1);

$pageTitle = 'Luxury Resort Amenities & Privileges';
$pageDescription = 'Experience 5-star resort amenities with Club Yatri: private plunge pools, Ayurvedic wellness spas, private yacht charters, and 24/7 lifestyle butlers.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/data/amenities.php';

$breadcrumbTitle = 'Amenities';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 60px; padding-bottom: 40px; text-align: center;">
  <div class="container">
    <div class="sub-tag">World-Class Hospitality Standards</div>
    <h1 class="heading-serif" style="font-size: clamp(2.2rem, 4vw, 3.6rem); margin-bottom: 16px;">
      Signature Resort <span class="gold-text">Amenities & Perks</span>
    </h1>
    <p style="color: var(--text-secondary); max-width: 720px; margin: 0 auto; font-size: 1.1rem;">
      Every Club Yatri vacation is enriched by uncompromised facilities designed to rejuvenate body, mind, and spirit.
    </p>
  </div>
</section>

<section class="section" style="padding-top: 20px; padding-bottom: 80px;">
  <div class="container">
    <div class="grid-4">
      <?php foreach ($amenitiesList as $amenity): ?>
        <div class="card-luxury" style="padding: 32px 24px; text-align: left;">
          <div style="font-size: 2.2rem; margin-bottom: 16px;">
            <?php echo e($amenity['icon']); ?>
          </div>
          <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--gold-primary); font-weight: 700; letter-spacing: 0.08em; display: block; margin-bottom: 8px;">
            <?php echo e($amenity['category']); ?>
          </span>
          <h3 style="font-size: 1.25rem; color: #fff; margin-bottom: 12px;">
            <?php echo e($amenity['title']); ?>
          </h3>
          <p style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.6;">
            <?php echo e($amenity['description']); ?>
          </p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
