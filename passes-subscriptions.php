<?php
/**
 * Club Yatri - Passes & Subscriptions
 */

declare(strict_types=1);

$pageTitle = 'Holiday Passes & Vacation Subscriptions';
$pageDescription = 'Choose from Club Yatri 3-Year, 5-Year, 10-Year, and 25-Year vacation passes with guaranteed 5-star inventory and 4 seasonal studio categories.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/data/subscriptions.php';

$breadcrumbTitle = 'Holiday Passes';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section about-hero" style="padding-top: 60px; padding-bottom: 40px; text-align: center;">
  <?php include __DIR__ . '/includes/leaves.php'; ?>
  <div class="container" style="position: relative; z-index: 1;">
    <div class="sub-tag">Vacation Ownership Framework</div>
    <h1 class="heading-serif" style="font-size: clamp(2.2rem, 4vw, 3.6rem); margin-bottom: 16px;">
      Club Yatri <span class="gold-text">Holiday Passes</span>
    </h1>
    <p style="color: var(--text-secondary); max-width: 760px; margin: 0 auto; font-size: 1.1rem;">
      Lock in five-star luxury vacations for 3 to 25 years. Benefit from guaranteed points pools, seasonal studio tiers, and zero holiday tariff inflation.
    </p>
  </div>
</section>

<!-- Intro -->
<section class="section" style="padding-top: 50px; padding-bottom: 10px; text-align: center; border-bottom: 0;">
  <div class="container" style="max-width: 860px;">
    <h2 class="heading-serif" style="font-size: clamp(1.7rem, 3vw, 2.4rem); margin-bottom: 16px;">
      One Pass. <span style="color: var(--color-primary);">Years of Holidays</span>, Locked at Today's Price.
    </h2>
    <p style="color: var(--text-secondary); font-size: 1.08rem; line-height: 1.75; margin-bottom: 14px;">
      A Club Yatri pass is not a one-time booking. It is a long-term holiday plan for your family. Choose a 3, 5, 10 or 25-year pass, and enjoy 6 Nights / 7 Days every year at handpicked 3 to 5-star resorts, villas and cottages, without worrying about rising holiday tariffs.
    </p>
    <p style="color: var(--text-secondary); font-size: 1.08rem; line-height: 1.75;">
      Pick the tenure that fits how often you travel, compare the passes below, and speak to our concierge team whenever you are ready to book.
    </p>
  </div>
</section>

<!-- Pass Cards Grid -->
<section class="section" style="padding-top: 20px; padding-bottom: 60px;">
  <div class="container">
    <?php include __DIR__ . '/includes/pass-stack.php'; ?>
  </div>
</section>

<!-- The Four Seasonal Studio Tiers -->
<section class="section" style="background: var(--bg-secondary); padding-top: 80px; padding-bottom: 80px;">
  <div class="container">
    <div class="section-title-wrap">
      <div class="sub-tag">Seasonal Access Matrix</div>
      <h2 class="section-title heading-serif">The Four <span class="gold-text">Seasonal Studio Categories</span></h2>
      <p class="section-desc">
        Understanding our seasonal categories ensures 100% reservation availability without overcrowded blackouts.
      </p>
    </div>

    <?php
    $collection = [
        ['green', 'Yatri Green Pass', 'Off-Peak / Monsoon Season'],
        ['blue', 'Yatri Blue Pass', 'Mid Season'],
        ['yellow', 'Yatri Yellow Pass', 'Peak Season'],
        ['red', 'Yatri Red Pass', 'Super Peak Season'],
    ];
    ?>
    <div class="pass-collection-grid">
      <?php foreach ($collection as $c): ?>
        <article class="pc-card pc-img pc-<?php echo e($c[0]); ?>">
          <button type="button" class="pass-zoom" aria-label="View <?php echo e($c[1]); ?> full screen">
            <img src="<?php echo e(url('images/passes/pc-' . $c[0] . '.jpg')); ?>" alt="<?php echo e($c[1] . ' - ' . $c[2]); ?>" loading="lazy">
          </button>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/pass-stack-scripts.php'; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
