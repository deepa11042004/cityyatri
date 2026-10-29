<?php
/**
 * Club Yatri - Privacy Policy
 */

declare(strict_types=1);

$pageTitle = 'Privacy Policy';
$pageDescription = 'Privacy and data protection commitment by Club Yatri Hotel and Resort Pvt Ltd.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$breadcrumbTitle = 'Privacy Policy';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 60px; padding-bottom: 80px;">
  <div class="container" style="max-width: 900px;">
    <div class="sub-tag">Data Governance</div>
    <h1 class="heading-serif" style="font-size: 2.6rem; margin-bottom: 16px;">Privacy Policy</h1>
    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 36px;">Effective Date: January 1, 2026 | Last Updated: October 2026</p>

    <div class="card-luxury" style="padding: 40px; line-height: 1.8; color: var(--text-secondary); font-size: 0.98rem;">
      <h3 style="color: #fff; margin-bottom: 12px;">1. Information We Collect</h3>
      <p style="margin-bottom: 24px;">
        <?php echo e(LEGAL_ENTITY_NAME); ?> collects information necessary to issue holiday pass deeds, fulfill resort bookings, provide concierge services, and communicate with subscribers. This includes full names, telephone numbers, executive email addresses, identification documents required by hotel check-in regulations, and holiday preferences.
      </p>

      <h3 style="color: #fff; margin-bottom: 12px;">2. Use of Subscriber Information</h3>
      <p style="margin-bottom: 24px;">
        Your personal data is used solely to maintain your points ledger, process domestic and international exchange reservations, assign personal lifestyle butlers, and deliver authorized subscription documents. We do not sell, rent, or trade subscriber contact information to unauthorized third-party marketing entities.
      </p>

      <h3 style="color: #fff; margin-bottom: 12px;">3. Data Security & Storage</h3>
      <p style="margin-bottom: 24px;">
        All subscriber communications and transaction records are encrypted using industry-standard protocols. Physical identification documents provided for resort verification are handled strictly under Indian hospitality regulatory standards and deleted upon statutory expiry.
      </p>

      <h3 style="color: #fff; margin-bottom: 12px;">4. Privacy Officer Contact</h3>
      <p style="margin: 0;">
        For questions or requests concerning your data records, please write to our Data Privacy Officer at <strong style="color: var(--gold-light);"><?php echo e(CONTACT_EMAIL); ?></strong> or our registered office at <?php echo e(CONTACT_ADDRESS); ?>.
      </p>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
