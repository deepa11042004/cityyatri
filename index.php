<?php
/**
 * Club Yatri - Flagship Homepage
 * Light Hospitality Theme: Rich Red (#EA2224) & Golden Yellow (#ffb300)
 * Model: Strictly Subscription-Based (Holiday Asset & Passes)
 * Legal Entity: Club Yatri Hotel and Resort Pvt Ltd
 */

declare(strict_types=1);

$pageTitle = 'Secure Your 25-Year Inflation-Free Luxury Holiday Asset | Club Yatri';
$pageDescription = 'Experience inflation-free luxury holiday passes with Club Yatri Hotel and Resort Pvt Ltd. 25-Year Flagship holiday asset, 4-color studio access, 200+ partner resorts worldwide.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/data/subscriptions.php';
require_once __DIR__ . '/data/resorts.php';
require_once __DIR__ . '/data/rooms.php';
require_once __DIR__ . '/data/testimonials.php';
require_once __DIR__ . '/data/faqs.php';
require_once __DIR__ . '/includes/hotels.php';
require_once __DIR__ . '/includes/destinations-db.php';
require_once __DIR__ . '/includes/itineraries-db.php';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<!-- 1. Hero Section (Clean Light Hospitality Appearance) -->
<section class="hero" id="homeHero">
  <!-- Rotating background slider (images/hero/*, resized copies of images/logo/1073.jpg and 177.jpg) -->
  <div class="hero-slides" id="heroSlides">
    <div class="hero-slide is-active" style="background-image: url('<?php echo e(BASE_PATH); ?>/images/hero/hero-1.jpg');"></div>
    <div class="hero-slide" style="background-image: url('<?php echo e(BASE_PATH); ?>/images/hero/hero-2.jpg');"></div>
    <div class="hero-slide" style="background-image: url('<?php echo e(BASE_PATH); ?>/images/hero/hero-3.jpg');"></div>
  </div>
  <div class="hero-overlay"></div>

  <div class="hero-controls" role="group" aria-label="Background slider">
    <button type="button" class="hero-arrow" id="heroPrev" aria-label="Previous background image">&#10094;</button>
    <div class="hero-dots" id="heroDots"></div>
    <button type="button" class="hero-arrow" id="heroNext" aria-label="Next background image">&#10095;</button>
  </div>
  
  <div class="container hero-content">
    <!-- Main Tagline in Golden-Yellow Highlight Box -->
    <div class="hero-tagline-highlight-box">
      <h2>Secure Your 25-Year Inflation-Free Luxury Holiday Asset</h2>
    </div>

    <!-- Quick Action Buttons -->
    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; margin-bottom: 40px;">
      <a href="#flagshipPassSection" class="btn-red btn-lg">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
        <span>Explore 25-Year Pass</span>
      </a>
      <a href="<?php echo url('callback-request.php'); ?>" class="btn-yellow btn-lg">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
        <span>Request Callback</span>
      </a>
    </div>

  </div>
</section>

<!-- 1b. Trust Stats (bento tiles; images are decorative) -->
<section class="section" id="homeStats" style="padding-top: 56px; padding-bottom: 0;">
  <div class="container">
    <div class="home-intro section-title-wrap">
      <div class="sub-tag">THE ART OF CURATED VACATION OWNERSHIP</div>

      <p class="section-desc">
        Lock in 25 years of guaranteed 5-star holidays across 200+ premier resorts in India and the world. 
        Completely shielded from hotel inflation, with 4-color seasonal studio flexibility and inheritable family asset rights with <?php echo e(LEGAL_ENTITY_NAME); ?>.
      </p>
    </div>

    <div class="bento-grid">
      <div class="bento-tile bento-stat red">
        <div class="stat-number">200+</div>
        <div class="stat-label">Handpicked Resorts</div>
      </div>
      <div class="bento-tile bento-img wide" role="presentation" style="background-image: url('https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80');"></div>
      <div class="bento-tile bento-stat">
        <div class="stat-number">25 Yrs</div>
        <div class="stat-label">Guaranteed Ownership</div>
      </div>

      <div class="bento-tile bento-img" role="presentation" style="background-image: url('https://images.unsplash.com/photo-1599661046827-dacff0c0f09a?auto=format&fit=crop&w=800&q=80');"></div>
      <div class="bento-tile bento-stat">
        <div class="stat-number">100%</div>
        <div class="stat-label">Inflation Protection</div>
      </div>
      <div class="bento-tile bento-img" role="presentation" style="background-image: url('https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?auto=format&fit=crop&w=800&q=80');"></div>
      <div class="bento-tile bento-stat red">
        <div class="stat-number">24/7</div>
        <div class="stat-label">Lifestyle Concierge</div>
      </div>
    </div>
  </div>
</section>

<!-- 2. Flagship 25-Year Pass Showcase (Center-Stage Luxury Card) -->
<section class="section" id="flagshipPassSection">
  <div class="container">
    <div class="section-title-wrap">
      <div class="sub-tag">Flagship Luxury Holiday Asset</div>
      <h2 class="section-title heading-serif">Center-Stage <span style="color: var(--color-primary);">25-Year Vacation Pass</span></h2>
      <p class="section-desc">
        The pinnacle of vacation ownership designed by <?php echo e(LEGAL_ENTITY_NAME); ?> to guarantee inflation-free luxury stays for a quarter-century.
      </p>
    </div>

    <!-- Center-Stage Luxury Card -->
    <div class="flagship-card-25y">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 24px;">
        <div style="flex: 1; min-width: 300px;">
          <div class="flagship-badge-tag">★ FLAGSHIP LUXURY ASSET ★</div>
          <h3 class="flagship-title">25-Year Pass <span>(Yatri Privilege Crown Asset)</span></h3>
          <p style="color: var(--color-text-muted); font-size: 1.05rem; line-height: 1.65; margin-bottom: 20px;">
            A perpetual 25-year inheritable holiday deed ensuring multi-generational 5-star vacation security, presidential suite allocations, and unrestricted global exchange privileges.
          </p>
        </div>
        <div style="text-align: right; background: #FFFFFF; padding: 22px 28px; border-radius: var(--radius-card); border: 1.5px solid var(--color-accent); box-shadow: var(--shadow-sm);">
          <span style="font-size: 0.8rem; text-transform: uppercase; color: var(--color-primary); font-weight: 700; letter-spacing: 0.08em; display: block;">Annual Allocation</span>
          <strong style="font-size: 2.2rem; color: var(--color-text); font-family: var(--font-serif-display); display: block;">35+ Nights / Yr</strong>
          <span style="font-size: 0.85rem; color: #047857; font-weight: 700;">Lifetime Rolling Points Banking</span>
        </div>
      </div>

      <!-- 4-Color Studio Logic Display -->
      <div style="margin: 32px 0 20px 0;">
        <h4 style="font-family: var(--font-serif-display); font-size: 1.25rem; color: var(--color-primary); margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
          Four Seasonal Access Categories
        </h4>

        <div class="studio-grid-4">
          <!-- Green Studio -->
          <div class="studio-chip-card green">
            <div class="studio-name" style="color: #047857;">🟢 Green Studio</div>
            <div class="studio-season">Off-Peak / Monsoon Season</div>
            <div class="studio-access">Full Access • Serene wellness retreats and quiet nature getaways with zero seasonal surcharges.</div>
          </div>

          <!-- Purple Studio -->
          <div class="studio-chip-card purple">
            <div class="studio-name" style="color: #6D28D9;">🟣 Purple Studio</div>
            <div class="studio-season">Regular Season Access</div>
            <div class="studio-access">Full Access • Pleasant autumn and spring weeks across heritage palaces and coastal escapes.</div>
          </div>

          <!-- Yellow Studio -->
          <div class="studio-chip-card yellow">
            <div class="studio-name" style="color: #B8860B;">🟡 Yellow Studio</div>
            <div class="studio-season">Peak / Weekend Season</div>
            <div class="studio-access">Priority Access • Extended holiday weekends and prime summer family vacations.</div>
          </div>

          <!-- Red Studio -->
          <div class="studio-chip-card red">
            <div class="studio-name" style="color: #EA2224;">🔴 Red Studio</div>
            <div class="studio-season">Super Peak / Festive &amp; New Year</div>
            <div class="studio-access">Guaranteed Priority • Diwali, Christmas, New Year celebrations, and premium snow weeks.</div>
          </div>
        </div>
      </div>

      <!-- Access Level Highlights -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 32px; padding: 20px; background: #FFFFFF; border-radius: var(--radius-sm); border: 1px solid var(--color-border); box-shadow: var(--shadow-sm);">
        <div style="display: flex; gap: 12px; align-items: center;">
          <div style="color: var(--color-primary); font-size: 1.4rem;">👑</div>
          <div>
            <strong style="color: var(--color-text); display: block; font-size: 0.95rem;">All-Seasons Priority Allocation</strong>
            <span style="color: var(--color-text-muted); font-size: 0.82rem;">Regular &amp; super-peak priority reservation</span>
          </div>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
          <div style="color: var(--color-primary); font-size: 1.4rem;">📜</div>
          <div>
            <strong style="color: var(--color-text); display: block; font-size: 0.95rem;">Lifetime Transferable Asset</strong>
            <span style="color: var(--color-text-muted); font-size: 0.82rem;">Inheritable by family or giftable with zero penalty</span>
          </div>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
          <div style="color: var(--color-primary); font-size: 1.4rem;">🏰</div>
          <div>
            <strong style="color: var(--color-text); display: block; font-size: 0.95rem;">Privilege Crown Suites</strong>
            <span style="color: var(--color-text-muted); font-size: 0.82rem;">Presidential suites, private pool villas &amp; havelis</span>
          </div>
        </div>
      </div>

      <!-- CTAs -->
      <div style="display: flex; gap: 16px; flex-wrap: wrap;">
        <a href="<?php echo url('booking-request.php?plan=privilege-crown'); ?>" class="btn-red btn-lg">
          Enquire 25-Year Flagship Pass
        </a>
        <a href="<?php echo url('pass-details.php?id=privilege-crown'); ?>" class="btn-yellow btn-lg">
          View Complete Allocation Schedule
        </a>
      </div>
    </div>
  </div>
</section>

<!-- 3. Subscription Passes (3-Year, 5-Year, 10-Year, 25-Year) -->
<?php
$subscriptionPasses = [
    [
        'plan' => 'royal-bharat', 'years' => 3,
        'tag' => 'FLAGSHIP LUXURY ASSET ★',
        'title' => '3-Year Pass (Yatri Privilege Crown Asset)',
        'intro' => 'Lock in 3 years of guaranteed 3, 4 & 5-star holiday security across 200+ premier resorts in India.',
        'allocation' => '6 Nights / 7 Days / Year',
        'allocation_note' => '(Standard All-Season Access — Blackout Dates Apply)',
        'fee' => '₹14,999 / Year',
        'fee_note' => '(Covers 24/7 dedicated travel manager, resort upkeep, and guaranteed 6N/7D booking access)',
        'policy_title' => 'BOOKING POLICY',
        'policy' => ['Advance Booking: 30 to 45 Days Prior Notice Required'],
        'policy_note' => '(Subject to Resort Availability & Excluding Blackout Dates)',
        'perks' => ['100% Inflation Free Stays', 'Fully Inheritable Family Asset', 'Priority Booking Access'],
        'featured' => false,
    ],
    [
        'plan' => 'asian-signature', 'years' => 5,
        'tag' => 'INTERNATIONAL & DOMESTIC ASSET ★',
        'title' => '5-Year Pass (Yatri Privilege Crown Asset)',
        'intro' => 'Lock in 5 years of guaranteed 3, 4 & 5-star holiday security across premier resorts in India, Asian Countries & Middle East.',
        'allocation' => '6 Nights / 7 Days / Year',
        'allocation_note' => '(Standard All-Season Access — Blackout Dates Apply)',
        'fee' => '₹14,999 / Year',
        'fee_note' => '(Covers 24/7 dedicated travel manager, resort upkeep, and guaranteed 6N/7D booking access)',
        'policy_title' => 'RESORT & BOOKING POLICY',
        'policy' => [
            'Destination Access: India, Asian Countries & Middle East',
            'Resort Category: Mid-List & Premium 3, 4 & 5-Star Properties',
            'Advance Booking: 30 to 45 Days Prior Notice Required',
        ],
        'policy_note' => '(Subject to Resort Availability & Excluding Blackout Dates)',
        'perks' => ['100% Inflation Free Stays', 'Fully Inheritable Family Asset', 'Priority Booking Access'],
        'featured' => true,
    ],
    [
        'plan' => 'imperial-world', 'years' => 10,
        'tag' => '★ ULTIMATE GLOBAL VIP ASSET ★',
        'title' => '10-Year Pass (Yatri Privilege Crown Asset)',
        'intro' => 'Lock in 10 years of guaranteed 3, 4 & 5-star holiday security across 500+ premier resorts in India, Asian Countries, Middle East, Europe & Americas.',
        'allocation' => '6 Nights / 7 Days / Year',
        'allocation_note' => '(VIP All-Season Access — Zero Blackout Dates & Unlimited Points Banking)',
        'fee' => '₹14,999 / Year',
        'fee_note' => '(Covers 24/7 dedicated travel manager, resort upkeep, and guaranteed 6N/7D booking access)',
        'policy_title' => 'RESORT & BOOKING POLICY',
        'policy' => [
            'Destination Access: Worldwide (India, Asia, Middle East, Europe & Americas)',
            'Resort Category: Premium & Luxury 3, 4 & 5-Star Properties',
            'VIP Booking Access: Priority Confirmation & Flexible Notice Period',
        ],
        'policy_note' => '(Subject to Resort Availability)',
        'perks' => ['100% Inflation Free Stays', 'Fully Inheritable Family Asset', 'Dedicated VIP Concierge & Priority Booking Access'],
        'featured' => false,
    ],
    [
        'plan' => 'privilege-crown', 'years' => 25,
        'tag' => 'FLAGSHIP LUXURY ASSET ★',
        'title' => '25-Year Pass (Yatri Privilege Crown Asset)',
        'intro' => 'Lock in 25 years of guaranteed 3, 4 & 5-star holiday security across 200+ premier resorts in India & worldwide.',
        'allocation' => '6 Nights / 7 Days / Year',
        'allocation_note' => '(Lifetime Points Banking Included)',
        'fee' => '₹14,999 / Year',
        'fee_note' => '(Covers 24/7 dedicated travel manager, resort upkeep, and guaranteed 6N/7D booking access)',
        'policy_title' => '',
        'policy' => [],
        'policy_note' => '',
        'perks' => ['100% Inflation Free Stays', 'Fully Inheritable Family Asset', 'Priority Booking Access'],
        'featured' => false,
    ],
];
?>
<section class="section" id="backendPassesSection">
  <div class="container">
    <div class="section-title-wrap">
      <div class="sub-tag">Tailored Vacation Plans</div>
      <h2 class="section-title heading-serif">Other <span style="color: var(--color-primary);">Subscription Passes</span></h2>
      <p class="section-desc">
        Curated vacation pass tiers calibrated to your annual holiday frequency and destination preferences.
      </p>
    </div>

    <div class="pass-stack" id="passStack">
      <?php $passTotal = count($subscriptionPasses); ?>
      <?php foreach ($subscriptionPasses as $i => $p): ?>
        <?php if ($i > 0): ?><div class="pass-gap" aria-hidden="true"></div><?php endif; ?>
        <div class="pass-anchor" aria-hidden="true"></div>
        <article class="pass-card tone-<?php echo $i + 1; ?><?php echo $p['featured'] ? ' is-featured' : ''; ?>" style="--i: <?php echo $i; ?>;">
          <div class="pass-head">
            <span class="pass-count"><?php echo $i + 1; ?>/<?php echo $passTotal; ?></span>
            <span class="pass-tag"><?php echo e($p['tag']); ?></span>
          </div>
          <h3 class="pass-title"><?php echo e($p['title']); ?></h3>
          <p class="pass-intro"><?php echo e($p['intro']); ?></p>

          <div class="pass-tiles">
            <div class="pass-tile">
              <div class="pass-label">ANNUAL ALLOCATION</div>
              <div class="pass-value"><?php echo e($p['allocation']); ?></div>
              <div class="pass-note"><?php echo e($p['allocation_note']); ?></div>
            </div>

            <div class="pass-tile">
              <div class="pass-label">Annual Subscription &amp; Concierge Fee</div>
              <div class="pass-value"><?php echo e($p['fee']); ?></div>
              <div class="pass-note"><?php echo e($p['fee_note']); ?></div>
            </div>

            <?php if ($p['policy']): ?>
              <div class="pass-tile">
                <div class="pass-label"><?php echo e($p['policy_title']); ?></div>
                <ul class="pass-policy">
                  <?php foreach ($p['policy'] as $line): ?>
                    <?php [$k, $v] = array_pad(explode(':', $line, 2), 2, ''); ?>
                    <li><strong><?php echo e($k); ?>:</strong><?php echo e($v); ?></li>
                  <?php endforeach; ?>
                </ul>
                <?php if ($p['policy_note'] !== ''): ?><div class="pass-note"><?php echo e($p['policy_note']); ?></div><?php endif; ?>
              </div>
            <?php endif; ?>

            <div class="pass-tile pass-tile-perks">
              <ul class="pass-perks">
                <?php foreach ($p['perks'] as $perk): ?>
                  <li><?php echo e($perk); ?></li>
                <?php endforeach; ?>
              </ul>
              <a href="<?php echo url('booking-request.php?plan=' . urlencode($p['plan'])); ?>" class="<?php echo $p['featured'] ? 'btn-red' : 'btn-yellow'; ?> pass-cta">
                Enquire <?php echo (int)$p['years']; ?>-Year Pass
              </a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
      <div class="pass-tail" aria-hidden="true"></div>
    </div>
  </div>
</section>

<script>
/* Stacked pass cards (scroll-linked): every card shrinks a little as it pins to the top, and cards further
   back in the stack shrink more as later cards slide up over them. */
(function () {
  var stack = document.getElementById('passStack');
  if (!stack) { return; }
  var cards = Array.prototype.slice.call(stack.querySelectorAll('.pass-card'));
  var anchors = Array.prototype.slice.call(stack.querySelectorAll('.pass-anchor'));
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)');
  var ticking = false;
  var OWN = 0.05, DEPTH = 0.04;

  function clamp(v) { return Math.min(1, Math.max(0, v)); }

  function update() {
    ticking = false;
    var sticky = getComputedStyle(cards[0]).position === 'sticky';
    if (!sticky || reduce.matches) {
      cards.forEach(function (c) { c.style.transform = ''; });
      return;
    }
    var vh = window.innerHeight;
    var pins = cards.map(function (c) { return parseFloat(getComputedStyle(c).top) || 0; });
    var nats = anchors.map(function (a) { return a.getBoundingClientRect().top; });   // where each card would be without sticky
    cards.forEach(function (card, i) {
      var own = clamp((pins[i] - nats[i]) / (vh * 0.55));            // shrink while pinned
      var deep = 0;                                                    // shrink as later cards arrive
      for (var j = i + 1; j < cards.length; j++) { deep += clamp(1 - (nats[j] - pins[j]) / (vh * 0.85)); }
      var scale = 1 - OWN * own - DEPTH * deep;
      card.style.transform = 'scale(' + scale.toFixed(4) + ')';
    });
  }
  function onScroll() { if (!ticking) { ticking = true; requestAnimationFrame(update); } }

  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll);
  update();
})();
</script>

<!-- 4. Interactive Destination Explorer (Manali, Goa, Udaipur, Jaipur, Thailand, Bali, Dubai) -->
<section class="section" id="resortMapExplorerSection">
  <div class="container">
    <?php include __DIR__ . '/includes/destination-map.php'; ?>

    <!-- Filtered Properties Grid -->
    <div class="grid-3" id="resortExplorerGrid">
      <?php foreach ($resortsList as $resort): ?>
        <?php include __DIR__ . '/includes/resort-card.php'; ?>
      <?php endforeach; ?>
    </div>

    <div style="text-align: center; margin-top: 40px;">
      <a href="<?php echo url('resort-network.php'); ?>" class="btn-yellow">
        Explore Complete Resort Network &rarr;
      </a>
    </div>
  </div>
</section>

<?php $popularDestinations = destinations_popular(8); ?>
<?php if ($popularDestinations): ?>
<!-- 4a. Popular Destinations (from the destinations database) -->
<section class="section" id="popularDestinationsSection">
  <div class="container">
    <div class="section-title-wrap">
      <div class="sub-tag">Destination Guide</div>
      <h2 class="section-title heading-serif">Popular <span style="color: var(--color-primary);">Destinations</span></h2>
      <p class="section-desc">
        Discover where our network is strongest, from <?php echo number_format(destinations_total()); ?> destinations across India and the world.
      </p>
    </div>

    <div class="grid-4 hotel-grid">
      <?php foreach ($popularDestinations as $dest): ?>
        <?php include __DIR__ . '/includes/destination-card.php'; ?>
      <?php endforeach; ?>
    </div>

    <div style="text-align: center; margin-top: 40px;">
      <a href="<?php echo url('destinations.php#destination-directory'); ?>" class="btn-yellow btn-lg">
        Explore All Destinations &rarr;
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

<?php $featuredHotels = hotels_featured(8); ?>
<?php if ($featuredHotels): ?>
<!-- 4b. Featured Network Hotels (from the hotels database) -->
<section class="section" id="featuredHotelsSection" style="background: var(--color-background-soft);">
  <div class="container">
    <div class="section-title-wrap">
      <div class="sub-tag">Our Hotel Network</div>
      <h2 class="section-title heading-serif">Featured <span style="color: var(--color-primary);">Network Hotels</span></h2>
      <p class="section-desc">
        A selection from <?php echo number_format(hotels_total()); ?> hotels and resorts across India and the world.
      </p>
    </div>

    <div class="grid-4 hotel-grid">
      <?php foreach ($featuredHotels as $hotel): ?>
        <?php include __DIR__ . '/includes/hotel-card.php'; ?>
      <?php endforeach; ?>
    </div>

    <div style="text-align: center; margin-top: 40px;">
      <a href="<?php echo url('resort-network.php#network-hotels'); ?>" class="btn-red btn-lg">
        Browse All Network Hotels &rarr;
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

<?php $homeItineraries = array_slice(itineraries_list(), 0, 6); ?>
<?php if ($homeItineraries): ?>
<!-- 4c. Curated Itineraries (from the itineraries database) -->
<section class="section" id="itinerariesSection">
  <div class="container">
    <div class="section-title-wrap">
      <div class="sub-tag">Curated Journeys</div>
      <h2 class="section-title heading-serif">Handcrafted <span style="color: var(--color-primary);">Itineraries</span></h2>
      <p class="section-desc">
        Day-by-day holidays across India and the world, planned end to end by your Club Yatri concierge.
      </p>
    </div>

    <div class="grid-3 hotel-grid">
      <?php foreach ($homeItineraries as $it): ?>
        <?php include __DIR__ . '/includes/itinerary-card.php'; ?>
      <?php endforeach; ?>
    </div>

    <div style="text-align: center; margin-top: 40px;">
      <a href="<?php echo url('itinerary.php#itinerary-list'); ?>" class="btn-red btn-lg">
        View All <?php echo number_format(itineraries_count()); ?> Itineraries &rarr;
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- 5. Room Categories Showcase (Clean Light Cards) -->
<section class="section" id="roomCategoriesSection" style="background: var(--color-background-soft);">
  <div class="container">
    <div class="section-title-wrap">
      <div class="sub-tag">Luxury Accommodation Suites</div>
      <h2 class="section-title heading-serif">Four Signature <span style="color: var(--color-primary);">Room Categories</span></h2>
      <p class="section-desc">
        Calibrated for private indulgence, from romantic couples' suites to opulent presidential pool villas.
      </p>
    </div>

    <div class="grid-4">
      <?php foreach ($roomCategories as $room): ?>
        <article class="card-luxury">
          <div class="card-img-wrap" style="height: 220px;">
            <img src="<?php echo e($room['image']); ?>" alt="<?php echo e($room['title']); ?>" loading="lazy">
            <span class="card-badge">
              <?php echo e($room['area']); ?>
            </span>
          </div>
          <div class="card-body">
            <h3 class="card-title" style="font-size: 1.15rem; margin-bottom: 6px;">
              <?php echo e($room['title']); ?>
            </h3>
            <p style="font-size: 0.82rem; color: var(--color-primary); font-weight: 600; margin-bottom: 12px;">
              👥 <?php echo e($room['capacity']); ?> | 🛏️ <?php echo e($room['bed_type']); ?>
            </p>
            <p class="card-desc" style="font-size: 0.88rem; line-height: 1.5; margin-bottom: 16px;">
              <?php echo e($room['description']); ?>
            </p>
            <div style="display: flex; gap: 8px;">
              <a href="<?php echo url('room-details.php?id=' . urlencode($room['id'])); ?>" class="btn btn-outline-gold btn-sm" style="flex: 1; text-align: center;">Details</a>
              <a href="<?php echo url('booking-request.php?room=' . urlencode($room['id'])); ?>" class="btn-red btn-sm" style="flex: 1; text-align: center;">Book</a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 5b. How Booking Works (process steps) -->
<section class="section" id="howBookingWorks">
  <div class="container">
    <div class="section-title-wrap">
      <div class="sub-tag">Simple Reservation Process</div>
      <h2 class="section-title heading-serif">How <span style="color: var(--color-primary);">Booking Works</span></h2>
      <p class="section-desc">
        Redeeming your holiday pass is a three-step request handled by your dedicated lifestyle concierge.
      </p>
    </div>

    <div class="process-visual" role="presentation"></div>

    <ol class="process-steps">
      <li class="process-step">
        <span class="process-num">01</span>
        <div>
          <strong>Choose your resort and room</strong>
          <p>Pick a destination and accommodation category from the Club Yatri resort network.</p>
        </div>
      </li>
      <li class="process-step">
        <span class="process-num">02</span>
        <div>
          <strong>Select season and dates</strong>
          <p>Choose your studio season, check-in date and length of stay.</p>
        </div>
      </li>
      <li class="process-step">
        <span class="process-num">03</span>
        <div>
          <strong>Submit your request</strong>
          <p>Send the booking request online and your concierge will confirm availability.</p>
        </div>
      </li>
    </ol>

    <div style="text-align: center; margin-top: 36px;">
      <a href="<?php echo url('booking-request.php'); ?>" class="btn-red btn-lg">Start a Booking Request</a>
    </div>
  </div>
</section>

<!-- 6. Booking Rules & Policy Notice (Transparency Box) -->
<section class="section" id="bookingNoticeSection">
  <div class="container">
    <div class="transparency-notice-box">
      <div class="notice-badge">
        <span>⚠️</span> RESERVATION PROTOCOLS &amp; TRANSPARENCY NOTICE
      </div>
      <h3 style="font-family: var(--font-serif-display); font-size: clamp(1.4rem, 2.5vw, 1.9rem); color: var(--color-text); margin-bottom: 22px;">
        Important Booking Rules For <span style="color: var(--color-primary);">Club Yatri Subscribers</span>
      </h3>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 18px;">
        <div class="notice-rule-item">
          <div style="font-size: 1.5rem; line-height: 1;">📌</div>
          <div>
            <strong>Minimum 45 Days Prior Advance Booking Notice Mandatory</strong>
            <p>
              To guarantee seamless five-star luxury room allocations and dedicated VIP butler preparation, all vacation pass booking requests must be lodged at least 45 calendar days prior to your intended check-in date.
            </p>
          </div>
        </div>

        <div class="notice-rule-item">
          <div style="font-size: 1.5rem; line-height: 1;">📌</div>
          <div>
            <strong>All Stay Confirmations Are Strictly Subject to Availability</strong>
            <p>
              Resort allocations and inventory allotments are confirmed on a first-come, first-served basis within seasonal studio allocations. Subscribers with the Flagship 25-Year Privilege Crown Asset receive the highest priority queueing.
            </p>
          </div>
        </div>
      </div>

      <div style="margin-top: 22px; padding-top: 18px; border-top: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
        <span style="font-size: 0.88rem; color: var(--color-text-muted);">
          Review complete holiday guidelines, cancellation rules, and studio conversion schedules:
        </span>
        <a href="<?php echo url('booking-policy.php'); ?>" style="color: var(--color-primary); font-weight: 700; text-decoration: underline; font-size: 0.92rem;">
          Read Complete Booking Policy &rarr;
        </a>
      </div>
    </div>
  </div>
</section>

<!-- 7. Trust & Legal Section -->
<section class="section" id="trustLegalSection" style="background: var(--color-background-soft);">
  <div class="container">
    <div class="section-title-wrap">
      <div class="sub-tag">Corporate Integrity &amp; Transparency</div>
      <h2 class="section-title heading-serif">Registered Entity: <span style="color: var(--color-primary);"><?php echo e(LEGAL_ENTITY_NAME); ?></span></h2>
      <p class="section-desc">
        Incorporated under the Companies Act with full statutory compliance, GST registration, and MSME certification.
      </p>
    </div>

    <!-- Corporate Badges Grid -->
    <div class="trust-badges-grid">
      <div class="trust-badge-card">
        <span class="badge-icon">🏛️</span>
        <strong>Corporate Identity (CIN)</strong>
        <span><?php echo e(CIN_NUMBER); ?></span>
      </div>

      <div class="trust-badge-card">
        <span class="badge-icon">📜</span>
        <strong>GSTIN Registration</strong>
        <span><?php echo e(GSTIN_NUMBER); ?></span>
      </div>

      <div class="trust-badge-card">
        <span class="badge-icon">🏅</span>
        <strong>MSME Certified Entity</strong>
        <span>Govt. of India Enterprise</span>
      </div>

      <div class="trust-badge-card">
        <span class="badge-icon">🛡️</span>
        <strong>Inflation-Hedged Stays</strong>
        <span>25-Year Guaranteed Equity</span>
      </div>
    </div>

    <!-- Verified Testimonials -->
    <div style="margin-top: 52px;">
      <h3 class="heading-serif" style="text-align: center; color: var(--color-text); font-size: 1.8rem; margin-bottom: 28px;">
        What Verified <span style="color: var(--color-primary);">Subscribers</span> Say
      </h3>
      <div class="grid-3">
        <?php foreach (array_slice($testimonialsList, 0, 3) as $testimonial): ?>
          <?php include __DIR__ . '/includes/testimonial-card.php'; ?>
        <?php endforeach; ?>
      </div>
    </div>

    <?php include __DIR__ . '/includes/video-testimonials.php'; ?>

    <!-- Top FAQs -->
    <div style="margin-top: 52px;">
      <h3 class="heading-serif" style="text-align: center; color: var(--color-text); font-size: 1.8rem; margin-bottom: 28px;">
        Frequently Asked <span style="color: var(--color-primary);">Questions</span>
      </h3>
      <div class="faq-list">
        <?php foreach (array_slice($faqsList, 0, 4) as $idx => $faq): ?>
          <?php 
            $isOpen = ($idx === 0);
            include __DIR__ . '/includes/faq-item.php'; 
          ?>
        <?php endforeach; ?>
      </div>
      <div style="text-align: center; margin-top: 28px;">
        <a href="<?php echo url('faq.php'); ?>" class="btn btn-outline-gold">
          View All Subscriber FAQs &rarr;
        </a>
      </div>
    </div>

  </div>
</section>

<!-- The destination map (includes/destination-map.php) shows a destination's hotels itself; hide the sample resorts while one is selected -->
<script>
document.addEventListener('destmap:select', function (e) {
  var grid = document.getElementById('resortExplorerGrid');
  if (grid) { grid.style.display = e.detail.slug === 'all' ? '' : 'none'; }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
