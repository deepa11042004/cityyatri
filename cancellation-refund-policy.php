<?php
/**
 * Club Yatri - Cancellation & Refund Policy
 */

declare(strict_types=1);

$pageTitle = 'Cancellation & Refund Policy';
$pageDescription = 'Transparent cancellation timelines, point credit reinstatement, and cooling-off refund rules for Club Yatri vacation passes.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$breadcrumbTitle = 'Cancellation & Refunds';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 60px; padding-bottom: 80px;">
  <div class="container" style="max-width: 900px;">
    <div class="sub-tag">Fair Hospitality Conduct</div>
    <h1 class="heading-serif" style="font-size: 2.6rem; margin-bottom: 16px;">Cancellation & Refund Policy</h1>
    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 36px;">Club Yatri Hotel and Resort Pvt Ltd • Transparent Bylaws</p>

    <div class="card-luxury" style="padding: 40px; line-height: 1.8; color: var(--text-secondary); font-size: 0.98rem;">
      <h3 style="color: #fff; margin-bottom: 12px;">1. Statutory Cooling-Off Period</h3>
      <p style="margin-bottom: 24px;">
        Every newly purchased Club Yatri holiday pass includes a statutory <strong>10-day cooling-off window</strong> from the date of contract execution. If you elect to cancel your pass within this window, a 100% refund of your enrollment fee will be processed (less statutory stamp duty / registration documentation out-of-pockets).
      </p>

      <h3 style="color: #fff; margin-bottom: 12px;">2. Stay Cancellation & Point Reinstatement</h3>
      <p style="margin-bottom: 24px;">
        For confirmed resort vacation reservations:
        <br>• <strong>Notice > 21 Days prior to check-in:</strong> 100% of deducted vacation points are reinstated to your member ledger without penalty.
        <br>• <strong>Notice 8 to 20 Days prior:</strong> 75% of points are reinstated; 25% administrative fee.
        <br>• <strong>Notice < 7 Days or No-Show:</strong> Points allocated for the reserved week are forfeited due to committed partner resort inventory.
      </p>

      <h3 style="color: #fff; margin-bottom: 12px;">3. Force Majeure & Severe Weather</h3>
      <p style="margin-bottom: 24px;">
        In the event of natural disruptions (landslides, tropical storms, flight cancellations), our Concierge Desk will rebook or fully credit 100% of your points for an alternative date without penalty.
      </p>

      <h3 style="color: #fff; margin-bottom: 12px;">4. Refund Disbursement Process</h3>
      <p style="margin: 0;">
        Approved statutory refunds are credited directly to the originating bank account or payment method within 7 to 10 working days.
      </p>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
