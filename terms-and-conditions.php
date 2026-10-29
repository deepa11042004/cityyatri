<?php
/**
 * Club Yatri - General Terms & Conditions
 */

declare(strict_types=1);

$pageTitle = 'Terms and Conditions';
$pageDescription = 'Website usage and subscription terms for Club Yatri Hotel and Resort Pvt Ltd.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$breadcrumbTitle = 'Terms & Conditions';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 60px; padding-bottom: 80px;">
  <div class="container" style="max-width: 900px;">
    <div class="sub-tag">Legal Framework</div>
    <h1 class="heading-serif" style="font-size: 2.6rem; margin-bottom: 16px;">Terms and Conditions</h1>
    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 36px;">Effective Date: January 1, 2026 | <?php echo e(LEGAL_ENTITY_NAME); ?></p>

    <div class="card-luxury" style="padding: 40px; line-height: 1.8; color: var(--text-secondary); font-size: 0.98rem;">
      <h3 style="color: #fff; margin-bottom: 12px;">1. Acceptance of Agreement</h3>
      <p style="margin-bottom: 24px;">
        By browsing this website, scheduling a consultation, or enrolling into a Club Yatri holiday pass, you enter into a binding agreement governed by the laws of India and the jurisdiction of Mumbai courts.
      </p>

      <h3 style="color: #fff; margin-bottom: 12px;">2. Vacation Ownership Nature</h3>
      <p style="margin-bottom: 24px;">
        Club Yatri passes represent contractual rights to reserve and occupy allocated luxury accommodations across partner resorts for designated night entitlements. Passes do not constitute undivided real estate fee-simple title in individual properties unless explicitly structured under registered perpetual deeds.
      </p>

      <h3 style="color: #fff; margin-bottom: 12px;">3. Intellectual Property</h3>
      <p style="margin-bottom: 24px;">
        All trademarks, architectural photography, text, logos, and UI designs on this platform are the proprietary intellectual property of <?php echo e(LEGAL_ENTITY_NAME); ?> and protected under Indian and international copyright treaties.
      </p>

      <h3 style="color: #fff; margin-bottom: 12px;">4. Amendments</h3>
      <p style="margin: 0;">
        We reserve the right to modify these terms periodically to reflect updated hospitality regulations and partner network expansions.
      </p>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
