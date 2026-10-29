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

<section class="section" style="padding-top: 60px; padding-bottom: 40px; text-align: center;">
  <div class="container">
    <div class="sub-tag">Vacation Ownership Framework</div>
    <h1 class="heading-serif" style="font-size: clamp(2.2rem, 4vw, 3.6rem); margin-bottom: 16px;">
      Club Yatri <span class="gold-text">Holiday Passes</span>
    </h1>
    <p style="color: var(--text-secondary); max-width: 760px; margin: 0 auto; font-size: 1.1rem;">
      Lock in five-star luxury vacations for 3 to 25 years. Benefit from guaranteed points pools, seasonal studio tiers, and zero holiday tariff inflation.
    </p>
  </div>
</section>

<!-- Pass Cards Grid -->
<section class="section" style="padding-top: 20px; padding-bottom: 60px;">
  <div class="container">
    <div class="grid-4">
      <?php foreach ($subscriptionPlans as $plan): ?>
        <?php include __DIR__ . '/includes/subscription-card.php'; ?>
      <?php endforeach; ?>
    </div>
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

    <div class="grid-4">
      <?php foreach ($seasonalStudioCategories as $season): ?>
        <div class="card-luxury" style="padding: 32px 24px;">
          <div style="display: inline-block; padding: 4px 14px; border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 700; color: #fff; background: <?php echo e($season['badge_color']); ?>; margin-bottom: 14px;">
            <?php echo e($season['period']); ?>
          </div>
          <h3 style="font-size: 1.3rem; color: #fff; margin-bottom: 12px;"><?php echo e($season['name']); ?></h3>
          <p style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.6;">
            <?php echo e($season['description']); ?>
          </p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
