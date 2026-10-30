<?php
/**
 * Club Yatri - International Luxury Resorts & Global Exchange
 */

declare(strict_types=1);

$pageTitle = 'International Luxury Resorts & Global Exchange';
$pageDescription = 'Access premier worldwide resorts in Thailand, Bali, Dubai, and the Maldives through Club Yatri holiday pass exchange.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/data/resorts.php';

$breadcrumbTitle = 'International Resorts';
$parentTitle = 'Destinations';
$parentUrl = url('destinations.php');

$internationalResorts = array_filter($resortsList, fn($r) => $r['region'] === 'International');

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 60px; padding-bottom: 40px; text-align: center;">
  <div class="container">
    <div class="sub-tag">Global Exchange Privileges</div>
    <h1 class="heading-serif" style="font-size: clamp(2.2rem, 4vw, 3.6rem); margin-bottom: 16px;">
      International <span class="gold-text">Escapes & Sanctuaries</span>
    </h1>
    <p style="color: var(--text-secondary); max-width: 720px; margin: 0 auto; font-size: 1.1rem;">
      Redeem your Club Yatri holiday points across top-tier international partner resorts in Dubai, Bali, Phuket, and the Maldives with zero global exchange hurdles.
    </p>
  </div>
</section>

<section class="section" style="padding-top: 20px; padding-bottom: 80px;">
  <div class="container">
    <div class="grid-3">
      <?php foreach ($internationalResorts as $resort): ?>
        <?php include __DIR__ . '/includes/resort-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
$dirType = 'INTERNATIONAL';
$dirHeading = 'More Hotels <span style="color: var(--color-primary);">Around the World</span>';
include __DIR__ . '/includes/hotel-directory.php';
?>

<!-- Global Exchange Explanation -->
<section class="section" style="background: var(--bg-secondary); padding-top: 60px; padding-bottom: 60px;">
  <div class="container" style="max-width: 860px; text-align: center;">
    <div class="sub-tag">How It Works</div>
    <h2 class="heading-serif" style="font-size: 2rem; margin-bottom: 16px;">Seamless International Exchange</h2>
    <p style="color: var(--text-secondary); line-height: 1.7; margin-bottom: 24px;">
      Members holding the <strong>Yatri Asian Signature Pass</strong>, <strong>Yatri Imperial World Pass</strong>, or <strong>Yatri Privilege Crown Asset</strong> can deposit their annual points into the international pool. Your dedicated Senior Butler coordinates resort dates, visas, and private airport transfers.
    </p>
    <a href="<?php echo url('booking-request.php?type=international_exchange'); ?>" class="btn btn-gold">
      Request International Exchange Consultation
    </a>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
