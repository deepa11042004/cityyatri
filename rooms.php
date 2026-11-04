<?php
/**
 * Club Yatri - Room & Suite Categories Overview
 */

declare(strict_types=1);

$pageTitle = 'Stays That Feel Like Home, Only Better | Club Yatri';
$pageDescription = 'From cozy, comfortable rooms for a relaxing getaway to stunning private villas built for pure luxury, every stay is handpicked to give you breathtaking views, modern comforts, and memories you will cherish forever.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/data/rooms.php';

$breadcrumbTitle = 'Rooms & Suites';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section about-hero" style="padding-top: 60px; padding-bottom: 40px; text-align: center;">
  <?php include __DIR__ . '/includes/leaves.php'; ?>
  <div class="container" style="position: relative; z-index: 1;">
    <div class="sub-tag">Accommodations Standards</div>
    <h1 class="heading-serif" style="font-size: clamp(2.2rem, 4vw, 3.6rem); margin-bottom: 16px;">
      Stays That Feel Like <span class="gold-text" style="color: var(--color-primary);">Home, Only Better</span>
    </h1>
    <p style="color: var(--text-secondary); max-width: 820px; margin: 0 auto; font-size: 1.12rem; line-height: 1.75;">
      From cozy, comfortable rooms for a relaxing getaway to stunning private villas built for pure luxury, every stay is handpicked to give you breathtaking views, modern comforts, and memories you will cherish forever.
    </p>
  </div>
</section>

<!-- Intro -->
<section class="section" style="padding-top: 50px; padding-bottom: 10px; text-align: center; border-bottom: 0;">
  <div class="container" style="max-width: 860px;">
    <h2 class="heading-serif" style="font-size: clamp(1.7rem, 3vw, 2.4rem); margin-bottom: 16px;">
      Six Ways to Stay, <span style="color: var(--color-primary);">One Standard of Care</span>.
    </h2>
    <p style="color: var(--text-secondary); font-size: 1.08rem; line-height: 1.75; margin-bottom: 14px;">
      Whether you want a spacious family suite, a private villa with a pool, a treehouse in the hills, a houseboat on calm waters, a geodesic dome or a glamping tent under the stars, every Club Yatri room category is selected with your family's comfort and safety in mind.
    </p>
    <p style="color: var(--text-secondary); font-size: 1.08rem; line-height: 1.75;">
      Explore the categories below to compare room sizes, capacity and bed types, then pick the one that suits your next holiday.
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
