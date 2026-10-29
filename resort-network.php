<?php
/**
 * Club Yatri - Global Resort Network Architecture
 */

declare(strict_types=1);

$pageTitle = 'Global Resort Network & Exchange Architecture';
$pageDescription = 'Discover Club Yatri comprehensive network of 200+ partner resorts across India and 4,500+ affiliated worldwide properties.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/data/destinations.php';
require_once __DIR__ . '/data/resorts.php';

$breadcrumbTitle = 'Resort Network';
$parentTitle = 'Destinations';
$parentUrl = url('destinations.php');

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 60px; padding-bottom: 40px; text-align: center;">
  <div class="container">
    <div class="sub-tag">Asset-Backed Network</div>
    <h1 class="heading-serif" style="font-size: clamp(2.2rem, 4vw, 3.6rem); margin-bottom: 16px;">
      The Club Yatri <span class="gold-text">Resort Network</span>
    </h1>
    <p style="color: var(--text-secondary); max-width: 740px; margin: 0 auto; font-size: 1.1rem;">
      Combining equity-backed flagship retreats in prime Indian leisure corridors with strategic global exchange privileges across 100+ countries.
    </p>
  </div>
</section>

<!-- Network Metrics -->
<section class="section" style="padding-top: 10px; padding-bottom: 60px;">
  <div class="container">
    <div class="grid-4" style="margin-bottom: 60px;">
      <div class="card-luxury" style="padding: 32px 20px; text-align: center;">
        <div class="stat-number" style="font-size: 2.8rem;">85+</div>
        <div class="stat-label">Direct Indian Resorts</div>
      </div>
      <div class="card-luxury" style="padding: 32px 20px; text-align: center;">
        <div class="stat-number" style="font-size: 2.8rem;">4,500+</div>
        <div class="stat-label">Global Affiliated Estates</div>
      </div>
      <div class="card-luxury" style="padding: 32px 20px; text-align: center;">
        <div class="stat-number" style="font-size: 2.8rem;">100+</div>
        <div class="stat-label">Countries Accessible</div>
      </div>
      <div class="card-luxury" style="padding: 32px 20px; text-align: center;">
        <div class="stat-number" style="font-size: 2.8rem;">100%</div>
        <div class="stat-label">Inventory Guaranteed</div>
      </div>
    </div>

    <!-- Network Hubs Breakdown -->
    <div class="grid-2" style="gap: 40px;">
      <div class="card-luxury" style="padding: 36px;">
        <div class="sub-tag" style="margin-bottom: 14px;">National Direct Network</div>
        <h3 class="heading-serif" style="font-size: 1.6rem; color: #fff; margin-bottom: 16px;">Direct Equity & Long-Term Leases</h3>
        <p style="color: var(--text-secondary); line-height: 1.7; margin-bottom: 20px;">
          Club Yatri owns and long-term leases dedicated blocks of luxury villas and suites across key leisure markets in India. This guarantees that whether booking a summer trip to Manali or a winter celebration in Goa, inventory is preserved exclusively for our members without retail displacement.
        </p>
        <a href="<?php echo url('hotels-national.php'); ?>" class="btn btn-outline-gold">View Domestic Resorts →</a>
      </div>

      <div class="card-luxury" style="padding: 36px;">
        <div class="sub-tag" style="margin-bottom: 14px;">Worldwide Exchange Desk</div>
        <h3 class="heading-serif" style="font-size: 1.6rem; color: #fff; margin-bottom: 16px;">Global Affiliation Protocols</h3>
        <p style="color: var(--text-secondary); line-height: 1.7; margin-bottom: 20px;">
          Through recognized international exchange alliances, our members can deposit their annual points to holiday in Thailand, Bali, Dubai, the Maldives, Switzerland, and the United Kingdom. Our concierge handles the exchange paperwork from start to finish.
        </p>
        <a href="<?php echo url('hotels-international.php'); ?>" class="btn btn-gold">Explore International Escapes →</a>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
