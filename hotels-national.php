<?php
/**
 * Club Yatri - National Luxury Resorts Portfolio
 */

declare(strict_types=1);

$pageTitle = 'Domestic Luxury Resorts in India';
$pageDescription = 'Explore Club Yatri handpicked national resort properties across Goa, Udaipur, Manali, Jaipur, Kerala, and Rishikesh.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/data/resorts.php';

$breadcrumbTitle = 'National Resorts';
$parentTitle = 'Destinations';
$parentUrl = url('destinations.php');

$nationalResorts = array_filter($resortsList, fn($r) => $r['region'] === 'National');

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 60px; padding-bottom: 40px; text-align: center;">
  <div class="container">
    <div class="sub-tag">The Bharat Luxury Collection</div>
    <h1 class="heading-serif" style="font-size: clamp(2.2rem, 4vw, 3.6rem); margin-bottom: 16px;">
      National Resort <span class="gold-text">Sanctuaries & Palaces</span>
    </h1>
    <p style="color: var(--text-secondary); max-width: 720px; margin: 0 auto; font-size: 1.1rem;">
      From the serene white sands of South Goa to the royal marble havelis of Udaipur and snowbound chalets of Manali, explore India with guaranteed 5-star allocations.
    </p>
  </div>
</section>

<section class="section" style="padding-top: 20px; padding-bottom: 80px;">
  <div class="container">
    <div class="grid-3">
      <?php foreach ($nationalResorts as $resort): ?>
        <?php include __DIR__ . '/includes/resort-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
