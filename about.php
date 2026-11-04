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
        <p style="color: var(--text-secondary); line-height: 1.8; margin-bottom: 18px;">
          "Some stories are not written on paper, but in the books of beautiful moments spent with your loved ones."
        </p>
        <p style="color: var(--text-secondary); line-height: 1.8; margin-bottom: 18px;">
          "When you step out, weary of the world's hustle and bustle, holding your family's hand in search of peace, your desire isn't just for a hotel room—it is a search for a place where every restriction melts away and only carefree smiles walk alongside you. We don't just build a place to stay; we curate a sanctuary where every little and big happiness of your loved ones is looked after with utmost devotion."
        </p>
        <p style="color: var(--text-secondary); line-height: 1.8; margin-bottom: 18px;">
          "Because time is slipping away fast like sand, and all that remains behind are priceless memories—the bubbling laughter of children, quiet evenings with your spouse where silence speaks volumes, and the peerless peace found in nature's lap. Every single resort, every corner, and every effort at Club Yatri is dedicated to keeping these special moments of your family alive and eternal."
        </p>
        <h3 class="heading-serif" style="font-size: 1.4rem; margin: 26px 0 12px;">Why was Club Yatri founded?</h3>
        <p style="color: var(--text-secondary); line-height: 1.8; margin-bottom: 18px;">
          "For years, we witnessed how families on vacations were forced to navigate hidden rules, rigid restrictions, and constant compromises. To erase that stress and burden forever, and to give vacations back their true meaning of freedom and love, we gave birth to 'Club Yatri'. Our singular and unwavering promise is to bring you and your family the world's finest destinations with absolute transparency, dignity, and trust."
        </p>
        <p style="color: var(--text-secondary); line-height: 1.8; margin-bottom: 18px;">
          "Because when it comes to your own people, nothing in this world matters more than peace, love, and dreams."
        </p>
        <p style="color: var(--color-primary); font-weight: 700; margin-bottom: 28px;">
          — Club Yatri, your very own companion.
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
        <img src="<?php echo e(url('images/about/family-beach.jpg')); ?>" alt="A family walking hand in hand on a quiet beach at sunset" style="width: 100%; height: 100%; object-fit: cover; display: block;">
      </div>
    </div>
  </div>
</section>

<!-- Pillars of Excellence -->
<section class="section arc-section" style="background: var(--bg-secondary); padding-top: 80px; padding-bottom: 80px;">
  <div class="container">
    <div class="section-title-wrap">
      <div class="sub-tag">Our Guarantees</div>
      <h2 class="section-title heading-serif">The 4 Pillars of <span class="gold-text">Club Yatri</span></h2>
      <p class="section-desc">Designed for your family's safety, comfort, and unforgettable memories.</p>
    </div>

    <div class="arc-carousel" id="pillarArc" aria-roledescription="carousel" aria-label="The 4 Pillars of Club Yatri">
      <div class="arc-stage">
        <article class="arc-card arc-tone-1" tabindex="0" aria-label="Pillar 1: Family Safety &amp; Care">
          <svg class="arc-icon" viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" aria-hidden="true"><path d="M12 2.5l2.2 5.6 5.8-2.1-2.1 5.8 5.6 2.2-5.6 2.2 2.1 5.8-5.8-2.1L12 24l-2.2-5.6-5.8 2.1 2.1-5.8L.5 12.5l5.6-2.2L4 4.5l5.8 2.1z" transform="scale(.92) translate(1 -1)"/></svg>
          <div class="arc-kicker">Pillar 1 &middot; Family Safety &amp; Care</div>
          <h3 class="arc-title">Kid-Friendly &amp; Safe Spaces</h3>
          <p class="arc-sub">24/7 Medical &amp; Security Support</p>
          <p class="arc-text">Dedicated kids' play zones, safe swimming pools, child-proof room setups, and a 24/7 doctor-on-call for immediate assistance during emergencies.</p>
        </article>
        <article class="arc-card arc-tone-2" tabindex="0" aria-label="Pillar 2: Ultimate Comfort &amp; Dining">
          <svg class="arc-icon" viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" aria-hidden="true"><path d="M12 2.5l2.2 5.6 5.8-2.1-2.1 5.8 5.6 2.2-5.6 2.2 2.1 5.8-5.8-2.1L12 24l-2.2-5.6-5.8 2.1 2.1-5.8L.5 12.5l5.6-2.2L4 4.5l5.8 2.1z" transform="scale(.92) translate(1 -1)"/></svg>
          <div class="arc-kicker">Pillar 2 &middot; Ultimate Comfort &amp; Dining</div>
          <h3 class="arc-title">Tailored Family Dining &amp; Suites</h3>
          <p class="arc-sub">Home-Style Food &amp; 24/7 Service</p>
          <p class="arc-text">Customized low-spice meals for children and elders, pure vegetarian/Jain options, spacious family suites, and round-the-clock in-room dining.</p>
        </article>
        <article class="arc-card arc-tone-3" tabindex="0" aria-label="Pillar 3: Hassle-Free Experience">
          <svg class="arc-icon" viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" aria-hidden="true"><path d="M12 2.5l2.2 5.6 5.8-2.1-2.1 5.8 5.6 2.2-5.6 2.2 2.1 5.8-5.8-2.1L12 24l-2.2-5.6-5.8 2.1 2.1-5.8L.5 12.5l5.6-2.2L4 4.5l5.8 2.1z" transform="scale(.92) translate(1 -1)"/></svg>
          <div class="arc-kicker">Pillar 3 &middot; Hassle-Free Experience</div>
          <h3 class="arc-title">Seamless Travel &amp; Concierge</h3>
          <p class="arc-sub">Door-to-Door Pickup &amp; Instant Check-in</p>
          <p class="arc-text">Door-to-door luxury transfers, zero-waiting room check-in upon arrival, and a dedicated personal relationship manager for your entire trip.</p>
        </article>
        <article class="arc-card arc-tone-4" tabindex="0" aria-label="Pillar 4: Unforgettable Memories &amp; Savings">
          <svg class="arc-icon" viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" aria-hidden="true"><path d="M12 2.5l2.2 5.6 5.8-2.1-2.1 5.8 5.6 2.2-5.6 2.2 2.1 5.8-5.8-2.1L12 24l-2.2-5.6-5.8 2.1 2.1-5.8L.5 12.5l5.6-2.2L4 4.5l5.8 2.1z" transform="scale(.92) translate(1 -1)"/></svg>
          <div class="arc-kicker">Pillar 4 &middot; Unforgettable Memories &amp; Savings</div>
          <h3 class="arc-title">Family Activities &amp; Zero Hidden Fees</h3>
          <p class="arc-sub">Flexible Dates &amp; Transparent Pricing</p>
          <p class="arc-text">Evening family bonfires, indoor gaming, complimentary family photo sessions, and absolute freedom to reschedule dates without heavy penalties.</p>
        </article>
      </div>
      <div class="arc-controls">
        <button type="button" class="arc-btn" data-dir="-1" aria-label="Previous pillar">&#8592;</button>
        <div class="arc-dots" role="tablist" aria-label="Choose a pillar"></div>
        <button type="button" class="arc-btn" data-dir="1" aria-label="Next pillar">&#8594;</button>
      </div>
      <p class="arc-hint">Drag or swipe to explore</p>
    </div>

    <!-- Member vs Non-Member -->
    <div class="pillar-block">
      <h3 class="pillar-block-title">Member vs Non-Member Comparison</h3>
      <div class="pillar-table-wrap">
        <table class="pillar-table">
          <thead><tr><th>Features &amp; Privileges</th><th>Subscribed Members</th><th>Non-Subscribers</th></tr></thead>
          <tbody>
            <tr><th scope="row">Booking Access &amp; Rates</th><td>Priority allocation pool, guaranteed price hike protection, and transparent subscriber tariffs.</td><td>Standard guest booking terms with seasonal dynamic surge pricing.</td></tr>
            <tr><th scope="row">Rollover Night</th><td>Unused nights never expire! Accumulate and combine together in the final year for a long holiday.</td><td>Standard single-season booking; no carry-forward or accumulation facility.</td></tr>
            <tr><th scope="row">Check-in &amp; Desk</th><td>Fast-track VIP check-in desk, dedicated luggage unloading support, and arrival lounge access.</td><td>Standard reception queue and general check-in process.</td></tr>
            <tr><th scope="row">Room &amp; Safety</th><td>Preferred room/suite views, child-safe room essentials check, and custom pillow/bed choices.</td><td>Standard room inventory allocation based on immediate availability.</td></tr>
            <tr><th scope="row">Dining &amp; Occasions</th><td>Peak-hour priority table seating, custom spice/temperature coordination, and cake-cutting setup.</td><td>Standard dining arrangements based on live restaurant availability.</td></tr>
            <tr><th scope="row">Local &amp; Departure Care</th><td>Dedicated itinerary, trusted transport, smooth check-out, and car windshield cleaning support.</td><td>Standard property information and standard departure formalities.</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Priority privileges -->
    <div class="pillar-block">
      <h3 class="pillar-block-title">For Subscribed Members (Priority Privileges)</h3>
      <ul class="pillar-list">
        <li><strong>Rollover Night &amp; Priority Pool:</strong> Unused subscription nights never expire and combine in the final year for a long holiday. Priority allocation pool access during peak seasons, Diwali, and New Year.</li>
        <li><strong>Price Hike Protection &amp; VIP Check-in:</strong> Protection from steep seasonal surge pricing with transparent subscriber tariffs. Express check-in desk, luggage unloading assistance, and welcome lounge access.</li>
        <li><strong>Room Safety &amp; Peak Dining:</strong> First choice on preferred room views/suites with child-safe room checks. Priority table seating during peak dinner hours and custom meal spice coordination.</li>
      </ul>
    </div>

    <!-- Complete package -->
    <div class="pillar-block">
      <h3 class="pillar-block-title">Club Yatri: The Complete Subscriber Privilege Package</h3>
      <ul class="pillar-list pillar-list-pkg">
        <li><strong>1. Stay &amp; Rollover Protection</strong><span>Subscribed room accommodations backed by our exclusive Rollover Night rule. Unused nights never expire; they accumulate year after year and combine together in your subscription's final year for one unforgettable long holiday. <em>(Subject to advance booking and room availability)</em></span></li>
        <li><strong>2. Travel Ticketing Assistance</strong><span>Dedicated assistance and coordination support for booking your family's train, bus, or flight tickets through trusted travel partners and platforms.</span></li>
        <li><strong>3. Verified Taxi &amp; Local Guide Coordination</strong><span>Reliable local taxi arrangements for pick-up/drop and sightseeing, along with experienced local guide coordination to ensure a safe and informative family tour.</span></li>
        <li><strong>4. Curated Sightseeing Itinerary</strong><span>Tailor-made local sightseeing itineraries, scenic viewpoints guidance, and family-related planning designed to make your holiday peaceful and hassle-free.</span></li>
        <li><strong>5. Resort Activities &amp; Family Entertainment</strong><span>Access to curated indoor board games, family recreation zones, evening bonfire setups (where applicable), and priority slot bookings for trusted partner adventure activities nearby.</span></li>
        <li><strong>6. Arrival, Front Desk &amp; Luggage Support</strong><span>Fast-track express check-in desk upon arrival, welcome lounge access for elders and kids, and dedicated parking-bay luggage unloading assistance.</span></li>
        <li><strong>7. Room Comfort &amp; Child Safety Care</strong><span>Preferred room views, child-safe room essentials checks (such as sharp-corner guards), and custom pillow/bed comfort choices prior to arrival.</span></li>
        <li><strong>8. Peak Dining &amp; Celebration Coordination</strong><span>Priority table seating during peak dinner hours, custom meal spice/temperature coordination for toddlers and elders, and dedicated assistance for birthday or anniversary setups.</span></li>
        <li><strong>9. Departure &amp; Return Care</strong><span>Smooth check-out processing, priority luggage loading, and complimentary vehicle windshield/glass cleaning support before your family starts the return mountain journey.</span></li>
        <li><strong>10. Emergency Medical &amp; First-Aid Assistance</strong><span>Immediate first-aid support at the front desk, coordination with trusted local medical practitioners or clinics, and emergency pharmacy delivery assistance during your stay.</span></li>
      </ul>
    </div>
  </div>
</section>

<script>
/* Draggable arc carousel (The 4 Pillars): a ring of the 4 cards plus 4 clones, so both sides of the arc are always filled */
(function () {
  var root = document.getElementById('pillarArc');
  if (!root) { return; }
  var stage = root.querySelector('.arc-stage');
  var real = Array.prototype.slice.call(stage.querySelectorAll('.arc-card'));
  var R = real.length;
  var clones = real.map(function (c) {
    var k = c.cloneNode(true);
    k.setAttribute('aria-hidden', 'true'); k.setAttribute('tabindex', '-1'); k.removeAttribute('aria-label');
    stage.appendChild(k); return k;
  });
  var cards = real.concat(clones), N = cards.length;
  var dotsBox = root.querySelector('.arc-dots');
  var pos = 0, dragging = false, moved = false, startX = 0, startPos = 0, prev = [];
  function mod(v, m) { return ((v % m) + m) % m; }
  var dots = real.map(function (c, i) {
    var b = document.createElement('button');
    b.type = 'button'; b.className = 'arc-dot'; b.setAttribute('aria-label', 'Show pillar ' + (i + 1));
    b.addEventListener('click', function () { toReal(i); });
    dotsBox.appendChild(b); return b;
  });
  function spacing() { return cards[0].offsetWidth * 0.86; }
  function render() {
    var sp = spacing(), active = mod(Math.round(pos), R);
    cards.forEach(function (c, i) {
      var o = mod(i - pos + N / 2, N) - N / 2, ao = Math.abs(o);
      var x = o * sp, y = Math.pow(ao, 1.6) * 26, rot = o * 9;
      var fade = Math.min(1, Math.max(0.3, 1 - Math.max(0, ao - 1.2) * 0.45)) * Math.min(1, Math.max(0, (3.1 - ao) / 0.6));
      var jumped = prev[i] !== undefined && Math.abs(o - prev[i]) > N / 2;
      c.style.transition = jumped ? 'none' : '';
      prev[i] = o;
      c.style.transform = 'translate(' + x.toFixed(1) + 'px,' + y.toFixed(1) + 'px) rotate(' + rot.toFixed(2) + 'deg) scale(' + (1 - Math.min(ao, 2.5) * 0.04).toFixed(3) + ')';
      c.style.opacity = fade.toFixed(2);
      c.style.zIndex = String(100 - Math.round(ao * 10));
      c.style.pointerEvents = fade < 0.05 ? 'none' : '';
      c.classList.toggle('is-active', mod(i, R) === active && ao < 0.5);
    });
    dots.forEach(function (d, i) { d.classList.toggle('is-on', i === active); });
  }
  function go(p) { pos = p; root.classList.add('is-settling'); render(); }
  function toReal(j) {
    var d = mod(j - Math.round(pos), R);
    go(Math.round(pos) + (d > R / 2 ? d - R : d));
  }
  root.querySelectorAll('.arc-btn').forEach(function (b) {
    b.addEventListener('click', function () { go(Math.round(pos) + parseInt(b.getAttribute('data-dir'), 10)); });
  });
  stage.addEventListener('pointerdown', function (e) {
    dragging = true; moved = false; startX = e.clientX; startPos = pos;
    root.classList.remove('is-settling'); root.classList.add('is-dragging');
    try { stage.setPointerCapture(e.pointerId); } catch (err) {}
  });
  stage.addEventListener('pointermove', function (e) {
    if (!dragging) { return; }
    var dx = e.clientX - startX;
    if (Math.abs(dx) > 5) { moved = true; }
    pos = startPos - dx / spacing(); render();
  });
  function end() {
    if (!dragging) { return; }
    dragging = false; root.classList.remove('is-dragging');
    go(Math.round(pos));
  }
  stage.addEventListener('pointerup', end);
  stage.addEventListener('pointercancel', end);
  cards.forEach(function (c, i) {
    c.addEventListener('click', function () {
      if (moved) { return; }
      var d = mod(i - pos + N / 2, N) - N / 2;
      if (Math.abs(d) > 0.5) { go(Math.round(pos + d)); }
    });
  });
  real.forEach(function (c, i) {
    c.addEventListener('focus', function () { if (mod(Math.round(pos), R) !== i) { toReal(i); } });
  });
  root.addEventListener('keydown', function (e) {
    if (e.key === 'ArrowRight') { go(Math.round(pos) + 1); e.preventDefault(); }
    if (e.key === 'ArrowLeft') { go(Math.round(pos) - 1); e.preventDefault(); }
  });
  window.addEventListener('resize', render);
  go(0);
})();
</script>

<!-- Legal & Trust Statement -->
<section class="section" style="padding-top: 60px; padding-bottom: 80px;">
  <div class="container" style="max-width: 900px; text-align: center;">
    <div class="sub-tag">Corporate Stewardship</div>
    <h3 class="heading-serif" style="font-size: 2rem; color: var(--color-text); margin-bottom: 16px;">Registered & Regulated Standards</h3>
    <p style="color: var(--text-secondary); line-height: 1.7; margin-bottom: 24px;">
      <?php echo e(LEGAL_ENTITY_NAME); ?> operates in compliance with relevant consumer hospitality guidelines and timeshare exchange statutory frameworks in India. All subscriber agreements are backed by physical resort deed rights and clear allocation protocols.
    </p>
    <a href="<?php echo url('contact.php'); ?>" class="btn btn-outline-gold">Connect with Corporate Relations</a>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
