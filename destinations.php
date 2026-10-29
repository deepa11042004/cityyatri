<?php
/**
 * Club Yatri - Destinations Directory
 */

declare(strict_types=1);

$pageTitle = 'Luxury Destinations & Resort Hubs';
$pageDescription = 'Explore Club Yatri vacation hubs across Goa, Udaipur, Manali, Jaipur, Thailand, Bali, and Dubai with guaranteed 5-star resort allocations.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/data/destinations.php';
require_once __DIR__ . '/data/resorts.php';

$breadcrumbTitle = 'Destinations';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 60px; padding-bottom: 40px; text-align: center;">
  <div class="container">
    <div class="sub-tag">The Yatri Global Portfolio</div>
    <h1 class="heading-serif" style="font-size: clamp(2.2rem, 4vw, 3.6rem); margin-bottom: 16px;">
      Featured Luxury <span class="gold-text">Destinations</span>
    </h1>
    <p style="color: var(--text-secondary); max-width: 740px; margin: 0 auto 32px auto; font-size: 1.1rem;">
      From the sun-soaked shores of South Goa to the royal palaces of Udaipur and the overwater lagoons of the Maldives, your pass unlocks unconditional access.
    </p>

    <!-- Category Filter Tabs -->
    <div class="tabs-wrap">
      <button class="tab-btn active" data-category="all">All Destinations</button>
      <button class="tab-btn" data-category="national">Domestic (India)</button>
      <button class="tab-btn" data-category="international">International</button>
    </div>
  </div>
</section>

<!-- Destinations Hubs Grid -->
<section class="section" style="padding-top: 10px; padding-bottom: 60px;">
  <div class="container">
    <div class="grid-3">
      <?php foreach ($destinationsList as $dest): ?>
        <article class="card-luxury" data-category="<?php echo strtolower($dest['region']); ?>">
          <div class="card-img-wrap" style="height: 250px;">
            <img src="<?php echo e($dest['image']); ?>" alt="<?php echo e($dest['name']); ?>" loading="lazy">
            <span class="card-badge"><?php echo e($dest['region']); ?></span>
            <span class="card-points-tag"><?php echo e((string)$dest['resorts_count']); ?> Luxury Resorts</span>
          </div>
          <div class="card-body">
            <div class="card-location">
              <span><?php echo e($dest['tag']); ?></span>
            </div>
            <h3 class="card-title"><?php echo e($dest['name']); ?></h3>
            <p class="card-desc"><?php echo e($dest['description']); ?></p>

            <div class="card-footer">
              <a href="<?php echo url($dest['region'] === 'National' ? 'hotels-national.php' : 'hotels-international.php'); ?>" class="btn btn-outline-gold btn-sm" style="width: 100%;">
                Explore Properties in <?php echo e($dest['name']); ?> →
              </a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- All Resorts Grid Section -->
<section class="section" style="background: var(--bg-secondary); padding-top: 80px; padding-bottom: 80px;">
  <div class="container">
    <div class="section-title-wrap">
      <div class="sub-tag">Complete Roster</div>
      <h2 class="section-title heading-serif">All <span class="gold-text">Resorts & Estates</span></h2>
      <p class="section-desc">Handpicked properties offering guaranteed reservation rights for Club Yatri holiday owners.</p>
    </div>

    <div class="grid-3">
      <?php foreach ($resortsList as $resort): ?>
        <?php include __DIR__ . '/includes/resort-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
