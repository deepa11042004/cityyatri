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

<section class="section" style="padding-top: 60px; padding-bottom: 0; text-align: center;">
  <div class="container">
    <div class="sub-tag">The Yatri Global Portfolio</div>
    <h1 class="heading-serif" style="font-size: clamp(2.2rem, 4vw, 3.6rem); margin-bottom: 16px;">
      Featured Luxury <span class="gold-text">Destinations</span>
    </h1>
    <p style="color: var(--text-secondary); max-width: 740px; margin: 0 auto 12px auto; font-size: 1.1rem;">
      From the sun-soaked shores of South Goa to the royal palaces of Udaipur and the overwater lagoons of the Maldives, your pass unlocks unconditional access.
    </p>
  </div>
</section>

<!-- Destination map: every destination as a pin -->
<section class="section" id="destinationMapSection" style="padding-top: 0; padding-bottom: 50px;">
  <div class="container">
    <?php include __DIR__ . '/includes/destination-map.php'; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/destination-directory.php'; ?>

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
