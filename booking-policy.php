<?php
/**
 * Club Yatri - Reservation & Booking Policy
 */

declare(strict_types=1);

$pageTitle = 'Reservation & Booking Policy';
$pageDescription = 'Guidelines, check-in rules, advance notice windows, and resort protocols for Club Yatri vacation reservations.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$breadcrumbTitle = 'Booking Policy';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 60px; padding-bottom: 80px;">
  <div class="container" style="max-width: 900px;">
    <div class="sub-tag">Reservation Protocols</div>
    <h1 class="heading-serif" style="font-size: 2.6rem; margin-bottom: 16px;">Reservation & Booking Policy</h1>
    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 36px;">Club Yatri Hotel and Resort Pvt Ltd • Hospitality Standards</p>

    <div class="card-luxury" style="padding: 40px; line-height: 1.8; color: var(--text-secondary); font-size: 0.98rem;">
      <h3 style="color: #fff; margin-bottom: 12px;">1. Advance Reservation Windows</h3>
      <p style="margin-bottom: 24px;">
        To guarantee seamless accommodation readiness:
        <br>• <strong>Silver Voyager:</strong> Advance booking opens 60 days prior to check-in.
        <br>• <strong>Gold Sovereign:</strong> Advance booking opens 120 days prior to check-in.
        <br>• <strong>Platinum Crest & Privilege Crown:</strong> Guaranteed year-round 365-day advance booking priority.
      </p>

      <h3 style="color: #fff; margin-bottom: 12px;">2. Check-in & Identification Protocols</h3>
      <p style="margin-bottom: 24px;">
        Standard check-in is 2:00 PM IST; check-out is 11:00 AM IST. All adult guests must present government-issued photographic identification (Aadhaar Card, Passport, or Voter ID) along with the official Club Yatri Confirmation Voucher upon arrival.
      </p>

      <h3 style="color: #fff; margin-bottom: 12px;">3. Occupancy Capacities</h3>
      <p style="margin-bottom: 24px;">
        Room categories are strictly subject to safety and comfort limits: Deluxe Room (Max 2 Adults + 1 Child); Super Deluxe (Max 2 Adults + 2 Children); Luxury Suite / 1-BHK (Max 4 Adults); Privilege Crown Villa (Max 6 to 8 Adults). Extra bed charges apply where allowed.
      </p>

      <h3 style="color: #fff; margin-bottom: 12px;">4. Resort Etiquette & House Rules</h3>
      <p style="margin: 0;">
        Guests are expected to observe resort tranquility policies. Certain heritage properties and eco-sanctuaries have specific guidelines regarding private smoking, pet boarding, and pool hours.
      </p>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
