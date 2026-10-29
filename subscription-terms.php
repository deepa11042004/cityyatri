<?php
/**
 * Club Yatri - Subscription & Pass Terms
 */

declare(strict_types=1);

$pageTitle = 'Subscription Terms & Ownership Rules';
$pageDescription = 'Operational rules governing Club Yatri vacation passes, annual point disbursements, seasonal studio categories, and banking bylaws.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$breadcrumbTitle = 'Subscription Terms';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 60px; padding-bottom: 80px;">
  <div class="container" style="max-width: 900px;">
    <div class="sub-tag">Ownership Bylaws</div>
    <h1 class="heading-serif" style="font-size: 2.6rem; margin-bottom: 16px;">Subscription Terms & Bylaws</h1>
    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 36px;">Club Yatri Hotel and Resort Pvt Ltd • Vacation Ownership Code</p>

    <div class="card-luxury" style="padding: 40px; line-height: 1.8; color: var(--text-secondary); font-size: 0.98rem;">
      <h3 style="color: #fff; margin-bottom: 12px;">1. Annual Point Crediting</h3>
      <p style="margin-bottom: 24px;">
        Points are disbursed annually on the anniversary date of your subscription registration. Pass holders receive 100% of their tier allocation (e.g. 10,000 Pts for Royal Bharat; 22,000 Pts for Asian Signature; 38,000 Pts for Imperial World; 65,000 Pts for Privilege Crown).
      </p>

      <h3 style="color: #fff; margin-bottom: 12px;">2. Seasonal Studio Allocations</h3>
      <p style="margin-bottom: 24px;">
        Reservations are categorized into Green (Monsoon/Off-Peak), Purple (Regular Season), Yellow (Peak/Weekend), and Red (Super Peak Festive) studios. Red Studio booking windows require Flagship 25-Year Privilege Crown or Imperial World status, or a seasonal upgrade debit.
      </p>

      <h3 style="color: #fff; margin-bottom: 12px;">3. Banking and Borrowing Rules</h3>
      <p style="margin-bottom: 24px;">
        Unused points can be banked into subsequent subscription years (up to 2 years for 3-Year passes; 3 years for 5-Year passes; 4 years for 10-Year passes; lifetime rolling for 25-Year assets). Subscribers in good standing may borrow up to 50% of the upcoming year's points allotment for extended travel.
      </p>

      <h3 style="color: #fff; margin-bottom: 12px;">4. Gifting & Guest Certificates</h3>
      <p style="margin: 0;">
        Subscribers may assign guest certificates to close family members or corporate executives. Up to 3 guest certificates may be generated per subscription year without administrative surcharges.
      </p>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
