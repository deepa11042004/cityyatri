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
<div class="hero-frame">
<section class="hero" id="homeHero">
  <!-- Rotating background slider (images/hero/*) -->
  <div class="hero-slides" id="heroSlides">
    <div class="hero-slide is-active" style="background-image: url('<?php echo e(BASE_PATH); ?>/images/hero/hero-2.jpg');"></div>
    <div class="hero-slide" style="background-image: url('<?php echo e(BASE_PATH); ?>/images/hero/hero-3.jpg');"></div>
    <div class="hero-slide" style="background-image: url('<?php echo e(BASE_PATH); ?>/images/hero/hero-4.jpg');"></div>
    <div class="hero-slide" style="background-image: url('<?php echo e(BASE_PATH); ?>/images/hero/hero-5.jpg');"></div>
    <div class="hero-slide" style="background-image: url('<?php echo e(BASE_PATH); ?>/images/hero/hero-6.jpg');"></div>
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
</div>

<!-- 1b. Trust Stats (bento tiles; images are decorative) -->
<section class="section" id="homeStats" style="padding-top: 56px; padding-bottom: 56px;">
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

<!-- 4. Interactive Destination Explorer (Manali, Goa, Udaipur, Jaipur, Thailand, Bali, Dubai) -->
<section class="section" id="resortMapExplorerSection">
  <div class="container">
    <?php include __DIR__ . '/includes/destination-map.php'; ?>

    <!-- Filtered Properties Grid -->
    <div class="card-slider" id="resortExplorerGrid">
      <button type="button" class="slider-arrow slider-prev" aria-label="Previous resorts" disabled>&#8592;</button>
      <div class="slider-track" tabindex="0" aria-label="Featured resorts">
      <?php foreach (hotels_club_properties('', 9) as $hotel): ?>
        <?php include __DIR__ . '/includes/hotel-card.php'; ?>
      <?php endforeach; ?>
      </div>
      <button type="button" class="slider-arrow slider-next" aria-label="Next resorts">&#8594;</button>
    </div>

    <div style="text-align: center; margin-top: 40px;">
      <a href="<?php echo url('resort-network.php'); ?>" class="btn-yellow">
        Explore Complete Resort Network &rarr;
      </a>
    </div>
  </div>
</section>

<?php $homeItineraries = itineraries_featured(6); ?>
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

      <!-- Exclusive Pass Collection (4 season passes) + core terms -->
      <?php
      $passCollection = [
          ['tone' => 'green', 'name' => 'Yatri Green Pass', 'season' => 'Off-Peak / Monsoon Season', 'price' => '₹6,66,450',
           'access' => 'Valid exclusively for off-peak and monsoon season travel with zero seasonal surcharges.',
           'blackout' => 'Christmas Week & New Year Week Excluded.',
           'experience' => 'Full Access • Serene wellness retreats and quiet nature getaways with zero seasonal surcharges.'],
          ['tone' => 'blue', 'name' => 'Yatri Blue Pass', 'season' => 'Mid Season', 'price' => '₹8,33,100',
           'access' => 'Valid for mid-season travel, ensuring smooth reservations away from peak crowds.',
           'blackout' => 'None.',
           'experience' => 'Full Access • Pleasant autumn and spring weeks across heritage palaces and coastal escapes.'],
          ['tone' => 'yellow', 'name' => 'Yatri Yellow Pass', 'season' => 'Peak Season', 'price' => '₹13,33,050',
           'access' => 'Valid for peak-season travel, offering priority privileges and guaranteed holiday access during high-demand periods.',
           'blackout' => 'None.',
           'experience' => 'Priority Access • Extended holiday weekends and prime summer family vacations.'],
          ['tone' => 'red', 'name' => 'Yatri Red Pass', 'season' => 'Super Peak Season', 'price' => '₹14,99,850',
           'access' => 'Valid for super peak season travel, delivering ultimate year-round flexibility and elite holiday placements.',
           'blackout' => 'None (Guaranteed priority access).',
           'experience' => 'Guaranteed Priority • Diwali, Christmas, New Year celebrations, and premium snow weeks.'],
      ];
      $passTerms = [
          ['Exclusive Network Access', 'Stays, reservations, and pass privileges are strictly restricted and applicable only to properties owned, managed, or directly tied-up within the Club Yatri network, governed by authorized management control.'],
          ['Validity & Usage', 'Valid for 25 Years with a total cumulative allocation of 150 Nights (6 Nights / 7 Days per year; unused nights lapse annually).'],
          ['Inflation-Free Stays', 'Covers accommodation costs exclusively (taxes, food, beverages, and incidentals are excluded).'],
          ['Inheritance Policy', 'Fully transferrable as a family asset once per lifetime upon official notification.'],
          ['Advanced Priority Booking', 'Exclusive 60-day advance booking window ahead of the general public.'],
          ['Points Banking', 'Lifetime points banking enabled, allowing structured carryover for a limited validity period.'],
          ['Annual Compliance', 'Timely payment of the Annual Subscription & Concierge Fee (ACF) is mandatory to maintain active pass benefits and booking rights.'],
      ];
      if (content_has_rows('pass_seasons')) {
          $passCollection = [];
          foreach (content_pass_seasons() as $ps) {
              $passCollection[] = ['tone' => $ps['slug'], 'name' => $ps['name'], 'season' => $ps['season_label'], 'image' => $ps['image_path'], 'alt' => $ps['image_alt'], 'price' => $ps['price_display']];
          }
      }
      $passTerms = content_pass_core_terms($passTerms);
      $passChecks = content_pass_checklist(['100% Inflation-Free Stays', 'Fully Inheritable Family Asset', 'Exclusive Network Privileges']);
      ?>
      <?php if ($passCollection || $passTerms): ?>
      <div class="pass-collection">
        <h4 class="pass-collection-title">Club Yatri — Exclusive Pass Collection</h4>

        <div class="pass-collection-grid">
          <?php foreach ($passCollection as $i => $pc): ?>
            <article class="pc-card pc-img pc-<?php echo e($pc['tone']); ?>">
              <button type="button" class="pass-zoom" aria-label="View <?php echo e($pc['name']); ?> full screen">
                <img src="<?php echo e(content_image_url($pc['image'] ?? ('images/passes/pc-' . $pc['tone'] . '.jpg'))); ?>" alt="<?php echo e(($pc['alt'] ?? '') !== '' ? $pc['alt'] : $pc['name'] . ' - ' . $pc['season']); ?>" loading="lazy">
              </button>
            </article>
          <?php endforeach; ?>
        </div>

        <?php if ($passTerms): ?>
        <div class="pass-terms">
          <h4 class="pass-terms-title">Core Terms &amp; Conditions <span>(Applicable to All Passes)</span></h4>
          <ul>
            <?php foreach ($passTerms as $term): ?>
              <li><strong><?php echo e($term[0]); ?>:</strong> <?php echo e($term[1]); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <?php if ($passChecks): ?>
        <ul class="pass-checks">
          <?php foreach ($passChecks as $chk): ?><li><?php echo e($chk); ?></li><?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>
      <?php endif; ?>

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
<section class="section" id="backendPassesSection">
  <div class="sky-clouds" aria-hidden="true">
    <img class="sky-cloud cl-a" src="<?php echo e(url('images/clouds/cloud1.png')); ?>" alt="" style="top: 4%; width: 19vw; animation-duration: 46s; animation-delay: 0s;">
    <img class="sky-cloud cl-b" src="<?php echo e(url('images/clouds/cloud2.png')); ?>" alt="" style="top: 12%; width: 22vw; animation-duration: 70s; animation-delay: -20s;">
    <img class="sky-cloud cl-c" src="<?php echo e(url('images/clouds/cloud1.png')); ?>" alt="" style="top: 24%; width: 15vw; animation-duration: 58s; animation-delay: -33s;">
    <img class="sky-cloud cl-a" src="<?php echo e(url('images/clouds/cloud2.png')); ?>" alt="" style="top: 36%; width: 25vw; animation-duration: 82s; animation-delay: -50s;">
    <img class="sky-cloud cl-b" src="<?php echo e(url('images/clouds/cloud1.png')); ?>" alt="" style="top: 48%; width: 22vw; animation-duration: 50s; animation-delay: -12s;">
    <img class="sky-cloud cl-c" src="<?php echo e(url('images/clouds/cloud2.png')); ?>" alt="" style="top: 60%; width: 20vw; animation-duration: 74s; animation-delay: -61s;">
    <img class="sky-cloud cl-a" src="<?php echo e(url('images/clouds/cloud1.png')); ?>" alt="" style="top: 72%; width: 18vw; animation-duration: 62s; animation-delay: -27s;">
    <img class="sky-cloud cl-b" src="<?php echo e(url('images/clouds/cloud2.png')); ?>" alt="" style="top: 84%; width: 23vw; animation-duration: 88s; animation-delay: -44s;">
  </div>
  <div class="container">
    <div class="section-title-wrap">
      <div class="sub-tag">Today's Special Drop</div>
      <h2 class="section-title heading-serif">Today's <span style="color: var(--color-primary);">Privilege Pass – Once It's Gone, It's Gone!</span></h2>
      <p class="pass-pitch-title">Today's Decision, Tomorrow's Best Memories – Join Now!</p>
      <p class="pass-pitch">Lock in your subscription today to unlock inflation-free 3- to 5-star stays, dedicated concierge care, and our absolute finest travel experiences for years to come.</p>
    </div>

    <?php include __DIR__ . '/includes/pass-stack.php'; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/pass-stack-scripts.php'; ?>





<!-- 5. Room Categories Showcase (Clean Light Cards) -->
<section class="section" id="roomCategoriesSection" style="background: var(--color-background-soft);">
  <div class="container">
    <div class="section-title-wrap">
      <div class="sub-tag">Luxury Accommodation Suites</div>
      <h2 class="section-title heading-serif">Six Signature <span style="color: var(--color-primary);">Room Categories</span></h2>
      <p class="section-desc">
        Calibrated for private indulgence, from romantic couples' suites to opulent presidential pool villas.
      </p>
    </div>

    <div class="card-slider" id="roomSlider">
      <button type="button" class="slider-arrow slider-prev" aria-label="Previous rooms" disabled>&#8592;</button>
      <div class="slider-track" tabindex="0" aria-label="Room categories">
      <?php foreach ($roomCategories as $room): ?>
        <article class="card-luxury slider-slide">
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
      <button type="button" class="slider-arrow slider-next" aria-label="Next rooms">&#8594;</button>
    </div>
  </div>
</section>

<script>
/* Card slider (room categories): arrows scroll one card at a time; swipe/drag works natively */
(function () {
  document.querySelectorAll('.card-slider').forEach(function (box) {
    var track = box.querySelector('.slider-track');
    var prev = box.querySelector('.slider-prev'), next = box.querySelector('.slider-next');
    function step() {
      var c = track.firstElementChild;
      return c ? c.getBoundingClientRect().width + parseFloat(getComputedStyle(track).columnGap || 24) : track.clientWidth;
    }
    function update() {
      prev.disabled = track.scrollLeft <= 2;
      next.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 2;
    }
    prev.addEventListener('click', function () { track.scrollBy({ left: -step(), behavior: 'smooth' }); });
    next.addEventListener('click', function () { track.scrollBy({ left: step(), behavior: 'smooth' }); });
    track.addEventListener('scroll', update, { passive: true });
    track.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowRight') { track.scrollBy({ left: step(), behavior: 'smooth' }); e.preventDefault(); }
      if (e.key === 'ArrowLeft') { track.scrollBy({ left: -step(), behavior: 'smooth' }); e.preventDefault(); }
    });
    window.addEventListener('resize', update);
    update();
  });
})();
</script>

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
        Current Experience by <span style="color: var(--color-primary);">Club Yatri Member</span>
      </h3>
      <div class="cy-slider" data-slider>
        <button type="button" class="cy-slider-btn prev" aria-label="Previous testimonials">&#8592;</button>
        <div class="cy-slider-track">
          <?php foreach ($testimonialsList as $testimonial): ?>
            <?php include __DIR__ . '/includes/testimonial-card.php'; ?>
          <?php endforeach; ?>
        </div>
        <button type="button" class="cy-slider-btn next" aria-label="Next testimonials">&#8594;</button>
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
