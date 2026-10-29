<?php
/**
 * Club Yatri - Room & Suite Categories Overview
 */

declare(strict_types=1);

$pageTitle = 'Luxury Rooms & Suites Portfolio';
$pageDescription = 'Discover Deluxe Rooms, Super Deluxe Executive Suites, Luxury 1-BHK Havelis, and Privilege Crown Pool Villas with Club Yatri.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/data/rooms.php';

$breadcrumbTitle = 'Rooms & Suites';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 60px; padding-bottom: 40px; text-align: center;">
  <div class="container">
    <div class="sub-tag">Accommodations Standards</div>
    <h1 class="heading-serif" style="font-size: clamp(2.2rem, 4vw, 3.6rem); margin-bottom: 16px;">
      Signature Accommodations & <span class="gold-text">Villas</span>
    </h1>
    <p style="color: var(--text-secondary); max-width: 720px; margin: 0 auto; font-size: 1.1rem;">
      Every suite and private villa in the Club Yatri network is calibrated to rigorous 5-star hospitality benchmarks, boasting panoramic vistas, marble en-suites, and tailored in-room services.
    </p>
  </div>
</section>

<section class="section" style="padding-top: 20px; padding-bottom: 80px;">
  <div class="container">
    <div class="grid-2" style="gap: 36px;">
      <?php foreach ($roomCategories as $room): ?>
        <?php include __DIR__ . '/includes/room-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
