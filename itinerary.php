<?php
/**
 * Club Yatri - Curated Itineraries & Multi-Day Expeditions
 */

declare(strict_types=1);

$pageTitle = 'Bespoke Curated Itineraries';
$pageDescription = 'Handcrafted luxury multi-day journeys across royal palaces, coastal catamarans, and Himalayan high passes with Club Yatri.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$breadcrumbTitle = 'Itineraries';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 60px; padding-bottom: 40px; text-align: center;">
  <div class="container">
    <div class="sub-tag">Bespoke Travel Journeys</div>
    <h1 class="heading-serif" style="font-size: clamp(2.2rem, 4vw, 3.6rem); margin-bottom: 16px;">
      Curated Member <span class="gold-text">Itineraries & Expeditions</span>
    </h1>
    <p style="color: var(--text-secondary); max-width: 720px; margin: 0 auto; font-size: 1.1rem;">
      Every journey with Club Yatri is an art form. Seamlessly redeem your holiday pass points for handcrafted multi-day vacations with private guides, master chefs, and VIP access.
    </p>
  </div>
</section>

<section class="section" style="padding-top: 20px; padding-bottom: 80px;">
  <div class="container">
    <div class="grid-2">
      
      <!-- Itinerary 1 -->
      <div class="card-luxury" style="padding: 36px;">
        <div class="card-badge" style="position: static; display: inline-block; margin-bottom: 16px;">Heritage Circuit • 7 Days / 6 Nights</div>
        <h2 class="card-title" style="font-size: 1.8rem;">The Grand Rajputana Royal Odyssey</h2>
        <p class="card-desc">
          Journey like royalty across Jaipur, Jodhpur, and Udaipur. Stay in restored 18th-century royal suites, dine privately inside palace ramparts, and enjoy private polo club access.
        </p>

        <div style="position: relative; padding-left: 36px; margin-top: 24px; border-left: 2px solid var(--border-gold);">
          <div style="margin-bottom: 20px;">
            <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--gold-primary); letter-spacing: 0.1em;">Day 1 - 2: Jaipur (The Pink City)</div>
            <div style="font-size: 1.05rem; color: #fff; font-weight: 600;">Private Amber Fort Twilight Tour & Royal Dining</div>
            <p style="font-size: 0.88rem; color: var(--text-secondary); line-height: 1.5;">VIP champagne greeting, vintage car transfer to heritage suites, and private evening viewing of the City Palace courtyards.</p>
          </div>
          <div style="margin-bottom: 20px;">
            <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--gold-primary); letter-spacing: 0.1em;">Day 3 - 4: Jodhpur (The Blue City)</div>
            <div style="font-size: 1.05rem; color: #fff; font-weight: 600;">Mehrangarh Private Rampart Walk & Rao Jodha Trails</div>
            <p style="font-size: 0.88rem; color: var(--text-secondary); line-height: 1.5;">Exclusive rooftop dining with Umaid Bhawan Palace in the background, accompanied by live sitar maestros.</p>
          </div>
          <div>
            <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--gold-primary); letter-spacing: 0.1em;">Day 5 - 7: Udaipur (City of Lakes)</div>
            <div style="font-size: 1.05rem; color: #fff; font-weight: 600;">Lake Pichola Royal Boat Cruise & Udaivilas Stays</div>
            <p style="font-size: 0.88rem; color: var(--text-secondary); line-height: 1.5;">Private solar boat cruise to Jagmandir Island, customized spa rituals, and celebratory farewell dinner on the palace terrace.</p>
          </div>
        </div>

        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
          <span style="color: var(--gold-light); font-weight: 700; font-size: 1.1rem;">Points: 9,500 Pts / Couple</span>
          <a href="<?php echo url('booking-request.php?itinerary=' . urlencode('Grand Rajputana Odyssey')); ?>" class="btn btn-gold">
            Reserve Itinerary
          </a>
        </div>
      </div>

      <!-- Itinerary 2 -->
      <div class="card-luxury" style="padding: 36px;">
        <div class="card-badge" style="position: static; display: inline-block; margin-bottom: 16px;">Island Sanctuary • 5 Days / 4 Nights</div>
        <h2 class="card-title" style="font-size: 1.8rem;">Maldivian Overwater & Manta Odyssey</h2>
        <p class="card-desc">
          A marine paradise in Baa Atoll. Drift over bioluminescent lagoons in an overwater villa, swim with majestic manta rays, and enjoy sandbank private chef barbecues.
        </p>

        <div style="position: relative; padding-left: 36px; margin-top: 24px; border-left: 2px solid var(--border-gold);">
          <div style="margin-bottom: 20px;">
            <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--gold-primary); letter-spacing: 0.1em;">Day 1: Seaplane Arrival & Sunset Dhoni</div>
            <div style="font-size: 1.05rem; color: #fff; font-weight: 600;">Panoramic Seaplane Flight & Sunset Champagne</div>
            <p style="font-size: 0.88rem; color: var(--text-secondary); line-height: 1.5;">Fly over azure coral rings directly to your overwater villa jetty. Welcome massage and sundowner cruise.</p>
          </div>
          <div style="margin-bottom: 20px;">
            <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--gold-primary); letter-spacing: 0.1em;">Day 2 - 3: Hanifaru Bay Biosphere</div>
            <div style="font-size: 1.05rem; color: #fff; font-weight: 600;">Snorkeling with Whale Sharks & Private Sandbank Picnic</div>
            <p style="font-size: 0.88rem; color: var(--text-secondary); line-height: 1.5;">Guided marine biologist expedition to feed and observe manta rays, followed by an isolated sandbank luxury lunch.</p>
          </div>
          <div>
            <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--gold-primary); letter-spacing: 0.1em;">Day 4 - 5: Wellness & Stargazing</div>
            <div style="font-size: 1.05rem; color: #fff; font-weight: 600;">Glass-Floor Spa Pavilion & Undersea Cellar Feast</div>
            <p style="font-size: 0.88rem; color: var(--text-secondary); line-height: 1.5;">Deep ocean sound therapy and a 6-course degustation dinner six meters beneath the Indian Ocean.</p>
          </div>
        </div>

        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
          <span style="color: var(--gold-light); font-weight: 700; font-size: 1.1rem;">Global Exchange: 8,000 Pts</span>
          <a href="<?php echo url('booking-request.php?itinerary=' . urlencode('Maldives Overwater Odyssey')); ?>" class="btn btn-gold">
            Reserve Itinerary
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
