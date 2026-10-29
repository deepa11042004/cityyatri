<?php
/**
 * Club Yatri - About Us & Brand Heritage
 */

declare(strict_types=1);

$pageTitle = 'About Us & Brand Heritage';
$pageDescription = 'Learn about Club Yatri Hotel and Resort Pvt Ltd, our hospitality ethos, 25-year holiday ownership standards, and our institutional integrity.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$breadcrumbTitle = 'About Us';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<!-- Header Banner -->
<section class="section" style="padding-top: 60px; padding-bottom: 40px; text-align: center;">
  <div class="container">
    <div class="sub-tag">The Ethos of Yatri (यात्री)</div>
    <h1 class="heading-serif" style="font-size: clamp(2.2rem, 4vw, 3.6rem); margin-bottom: 20px;">
      Redefining Vacation Ownership with <span class="gold-text">Honour & Integrity</span>
    </h1>
    <p style="color: var(--text-secondary); max-width: 760px; margin: 0 auto; font-size: 1.15rem; line-height: 1.7;">
      The ancient Sanskrit term <em>"Yatri"</em> captures the sacred spirit of the voyager — one who journeys not merely for diversion, but to deepen connection with family, culture, and nature.
    </p>
  </div>
</section>

<!-- Story & Vision -->
<section class="section" style="padding-top: 20px; padding-bottom: 80px;">
  <div class="container">
    <div class="grid-2" style="align-items: center;">
      <div>
        <div class="sub-tag">Our Foundation</div>
        <h2 class="heading-serif" style="font-size: 2.2rem; margin-bottom: 20px;">
          Institutional Transparency in Luxury Hospitality
        </h2>
        <p style="color: var(--text-secondary); line-height: 1.8; margin-bottom: 20px;">
          For decades, Indian vacationers experienced timeshare models fraught with hidden blackout dates, declining resort maintenance, and rigid calendar weeks. <?php echo e(LEGAL_ENTITY_NAME); ?> was founded by senior hospitality directors and asset trustees with a singular mission: to bring institutional transparency, liquidity, and true luxury to vacation ownership.
        </p>
        <p style="color: var(--text-secondary); line-height: 1.8; margin-bottom: 28px;">
          By establishing direct equity ownership across prime boutique estates and partnering with global timeshare exchanges, we ensure that every member receives guaranteed five-star inventory, dedicated butler care, and complete protection against hotel inflation.
        </p>
        <div style="display: flex; gap: 32px; flex-wrap: wrap;">
          <div>
            <div class="stat-number" style="font-size: 2.2rem;">4,500+</div>
            <div class="stat-label">Member Families</div>
          </div>
          <div>
            <div class="stat-number" style="font-size: 2.2rem;">98.4%</div>
            <div class="stat-label">Retention & Satisfaction</div>
          </div>
          <div>
            <div class="stat-number" style="font-size: 2.2rem;">200+</div>
            <div class="stat-label">Resort Portfolio</div>
          </div>
        </div>
      </div>

      <div style="border-radius: var(--radius-xl); overflow: hidden; border: 1.5px solid var(--border-gold); box-shadow: var(--shadow-lg), var(--shadow-gold);">
        <img src="https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1000&q=80" alt="Club Yatri Luxury Sanctuary" style="width: 100%; height: 100%; object-fit: cover; display: block;">
      </div>
    </div>
  </div>
</section>

<!-- Pillars of Excellence -->
<section class="section" style="background: var(--bg-secondary); padding-top: 80px; padding-bottom: 80px;">
  <div class="container">
    <div class="section-title-wrap">
      <div class="sub-tag">Our Guarantees</div>
      <h2 class="section-title heading-serif">The Four Pillars of <span class="gold-text">Club Yatri</span></h2>
    </div>

    <div class="grid-4">
      <div class="card-luxury" style="padding: 32px 24px;">
        <h3 style="font-size: 1.25rem; color: var(--gold-light); margin-bottom: 12px;">Zero Blackout Guarantee</h3>
        <p style="font-size: 0.92rem; color: var(--text-secondary); line-height: 1.6;">
          We maintain guaranteed allocation pools during Diwali, Christmas, New Year, and summer holidays without blackout restrictions for our Sovereign and Crest tiers.
        </p>
      </div>
      <div class="card-luxury" style="padding: 32px 24px;">
        <h3 style="font-size: 1.25rem; color: var(--gold-light); margin-bottom: 12px;">Uncompromising 5-Star QA</h3>
        <p style="font-size: 0.92rem; color: var(--text-secondary); line-height: 1.6;">
          Every property in the Club Yatri portfolio undergoes biannual 120-point inspections covering hygiene, culinary quality, and linen standards.
        </p>
      </div>
      <div class="card-luxury" style="padding: 32px 24px;">
        <h3 style="font-size: 1.25rem; color: var(--gold-light); margin-bottom: 12px;">Perpetual Family Inheritance</h3>
        <p style="font-size: 0.92rem; color: var(--text-secondary); line-height: 1.6;">
          Your vacation deed is an asset. Name children or relatives as legal successors with zero notary transfer friction or transfer penalties.
        </p>
      </div>
      <div class="card-luxury" style="padding: 32px 24px;">
        <h3 style="font-size: 1.25rem; color: var(--gold-light); margin-bottom: 12px;">Dedicated Senior Butler</h3>
        <p style="font-size: 0.92rem; color: var(--text-secondary); line-height: 1.6;">
          You never call a generic call center. A certified Lifestyle Concierge manages your vacation preferences, flight connections, and in-villa requests.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Legal & Trust Statement -->
<section class="section" style="padding-top: 60px; padding-bottom: 80px;">
  <div class="container" style="max-width: 900px; text-align: center;">
    <div class="sub-tag">Corporate Stewardship</div>
    <h3 class="heading-serif" style="font-size: 2rem; color: #fff; margin-bottom: 16px;">Registered & Regulated Standards</h3>
    <p style="color: var(--text-secondary); line-height: 1.7; margin-bottom: 24px;">
      <?php echo e(LEGAL_ENTITY_NAME); ?> operates in compliance with relevant consumer hospitality guidelines and timeshare exchange statutory frameworks in India. All subscriber agreements are backed by physical resort deed rights and clear allocation protocols.
    </p>
    <a href="<?php echo url('contact.php'); ?>" class="btn btn-outline-gold">Connect with Corporate Relations</a>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
