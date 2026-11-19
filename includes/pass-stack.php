<?php
/**
 * Club Yatri - 3D Rotating & Flipping Pass Showcase
 * Based on luxury 3D card rotating on pedestal (Vaulta-style interactive scroll showcase)
 */
require_once __DIR__ . '/content-db.php';

$subscriptionPasses = [
    ['image' => 'images/passes/pass-3-year.png', 'plan' => 'royal-bharat', 'years' => 3, 'title' => '3-Year Pass (Yatri Privilege Crown Asset)', 'badge' => 'Flagship Luxury Asset', 'subtitle' => '6 Nights / 7 Days / Year • Mid-List & Premium 3 to 5-Star Properties'],
    ['image' => 'images/passes/pass-5-year.png', 'plan' => 'asian-signature', 'years' => 5, 'title' => '5-Year Pass (Yatri Privilege Crown Asset)', 'badge' => 'International & Domestic Asset', 'subtitle' => '6 Nights / 7 Days / Year • India, Asian Countries & Middle East Access'],
    ['image' => 'images/passes/pass-10-year.png', 'plan' => 'imperial-world', 'years' => 10, 'title' => '10-Year Pass (Ultimate Global VIP Asset)', 'badge' => 'Ultimate Global VIP Asset', 'subtitle' => '6 Nights / 7 Days / Year • Premium 4 & 5-Star Luxury Properties Worldwide'],
    ['image' => 'images/passes/pass-25-year.png', 'plan' => 'privilege-crown', 'years' => 25, 'title' => '25-Year Pass (Yatri Privilege Crown Asset)', 'badge' => 'Generational Family Deed', 'subtitle' => '150 Cumulative Nights • Inheritable Multi-Generational 5-Star Holiday Asset'],
];

if (content_has_rows('passes')) {
    $dbPlans = content_pass_plans();
    $mapped = [];
    foreach ($dbPlans as $slug => $pl) {
        $mapped[] = [
            'plan' => $slug,
            'years' => (int)$pl['years'],
            'title' => (string)$pl['title'],
            'image' => (string)($pl['image'] ?? ''),
            'alt' => (string)($pl['image_alt'] ?? ''),
            'desc' => (string)($pl['description'] ?? ''),
            'badge' => !empty($pl['badge']) ? (string)$pl['badge'] : ($pl['years'] . '-Year Luxury Asset'),
            'subtitle' => '6 Nights / 7 Days / Year • 100% Inflation-Free Guaranteed Stays',
        ];
    }
    if (!empty($mapped)) {
        $subscriptionPasses = $mapped;
    }
}
?>

<div class="pass-showcase-wrapper" id="passShowcaseWrapper">
  <!-- Dedicated Scroll Track for interactive scroll-driven card progression -->
  <div class="pass-showcase-track" id="passShowcaseTrack">
    
    <!-- Pinned Showcase Stage -->
    <div class="pass-showcase-stage" id="passShowcaseStage">
      
      <!-- Top Stepper / Tab Navigator -->
      <nav class="pass-stage-tabs" aria-label="Pass Tiers" id="passStageTabs">
        <?php foreach ($subscriptionPasses as $i => $p): ?>
          <button type="button" 
                  class="pass-stage-tab<?php echo $i === 0 ? ' is-active' : ''; ?>" 
                  data-index="<?php echo $i; ?>"
                  aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>">
            <span class="tab-year-pill"><?php echo (int)$p['years']; ?>Y</span>
            <span class="tab-name"><?php echo (int)$p['years']; ?>-Year Pass</span>
          </button>
        <?php endforeach; ?>
      </nav>

      <!-- 3D Perspective Arena -->
      <div class="pass-3d-arena" id="pass3DArena">
        
        <!-- Left / Right Navigation Controls -->
        <button type="button" class="pass-3d-arrow pass-arrow-prev" id="passArrowPrev" aria-label="Previous Pass Tier" title="Previous pass">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </button>
        <button type="button" class="pass-3d-arrow pass-arrow-next" id="passArrowNext" aria-label="Next Pass Tier" title="Next pass">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </button>

        <!-- 3D Card Stack Scene -->
        <div class="pass-3d-stage" id="pass3DStage">
          <?php foreach ($subscriptionPasses as $i => $p): ?>
            <?php 
              $passImg = !empty($p['image']) ? (string)$p['image'] : 'images/passes/pass-' . (int)$p['years'] . '-year.png'; 
            ?>
            <div class="pass-3d-card-unit<?php echo $i === 0 ? ' is-active' : ''; ?>" 
                 data-index="<?php echo $i; ?>" 
                 data-title="<?php echo e($p['title']); ?>" 
                 data-plan="<?php echo e($p['plan']); ?>" 
                 data-years="<?php echo (int)$p['years']; ?>"
                 data-badge="<?php echo e($p['badge']); ?>"
                 data-subtitle="<?php echo e($p['subtitle']); ?>">
              
              <!-- 3D Rotator Component -->
              <div class="pass-card-rotator-3d" id="rotator_<?php echo $i; ?>">
                
                <!-- Front Side: Pass Artwork -->
                <div class="pass-card-side pass-card-front">
                  <img src="<?php echo e(content_image_url($passImg)); ?>" 
                       alt="<?php echo e(($p['alt'] ?? '') !== '' ? $p['alt'] : $p['title']); ?>" 
                       class="pass-card-artwork"
                       draggable="false">
                  <div class="pass-card-glare"></div>
                </div>

                <!-- Back Side: Luxury Metallic VIP Pass Back Face -->
                <div class="pass-card-side pass-card-back">
                  <div class="pass-back-content">
                    <div class="pass-back-top">
                      <div class="pass-back-crest">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ffb300" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polygon points="12 2 15 9 22 12 15 15 12 22 9 15 2 12 9 9 12 2" fill="rgba(255,179,0,0.3)"></polygon></svg>
                      </div>
                      <div class="pass-back-brand">CLUB YATRI</div>
                      <div class="pass-back-tier-name"><?php echo (int)$p['years']; ?>-YEAR CROWN ASSET</div>
                    </div>
                    
                    <div class="pass-back-magbar"></div>
                    
                    <div class="pass-back-meta-row">
                      <div class="pass-back-chip-box">
                        <div class="pass-chip-pattern"></div>
                        <span class="pass-chip-text">VIP</span>
                      </div>
                      <div class="pass-back-sig-panel">
                        <span class="sig-label">OFFICIAL MEMBER DEED</span>
                        <span class="sig-script">Club Yatri Privilege</span>
                      </div>
                    </div>

                    <div class="pass-back-specs">
                      <div><span>TENURE:</span> <strong><?php echo (int)$p['years']; ?> YEARS</strong></div>
                      <div><span>ALLOTMENT:</span> <strong>6N / 7D ANNUALLY</strong></div>
                      <div><span>NETWORK:</span> <strong>PREMIER 3 TO 5-STAR</strong></div>
                    </div>

                    <div class="pass-back-bottom">
                      <div class="pass-back-code">CY-<?php echo (int)$p['years']; ?>Y-<?php echo strtoupper(substr(md5($p['plan']), 0, 8)); ?></div>
                      <div class="pass-back-hotline">24/7 Concierge: 1800-209-9287 • www.clubyatri.com</div>
                    </div>
                  </div>
                  <div class="pass-card-glare"></div>
                </div>

              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- 3D Luxury Pedestal with Iridescent Rings & Dynamic Floor Shadow -->
        <div class="pass-pedestal-platform" aria-hidden="true">
          <div class="pedestal-surface-glow">
            <div class="pedestal-rainbow-ring"></div>
            <div class="pedestal-inner-glow"></div>
            <div class="card-dynamic-shadow" id="cardDynamicShadow"></div>
          </div>
          <div class="pedestal-metallic-rim"></div>
          <div class="pedestal-base-shadow"></div>
        </div>

      </div>

      <!-- Live Dynamic Pass Details & Action Bar -->
      <div class="pass-showcase-footer">
        <div class="pass-footer-badge" id="passActiveBadge"><?php echo e($subscriptionPasses[0]['badge']); ?></div>
        <h3 class="pass-footer-title" id="passActiveTitle"><?php echo e($subscriptionPasses[0]['title']); ?></h3>
        <p class="pass-footer-sub" id="passActiveSub"><?php echo e($subscriptionPasses[0]['subtitle']); ?></p>
        
        <div class="pass-footer-cta-wrap">
          <a href="<?php echo url('booking-request.php?plan=' . urlencode($subscriptionPasses[0]['plan'])); ?>" 
             class="btn-yellow pass-footer-btn" 
             id="passActiveCta">
            Enquire <?php echo (int)$subscriptionPasses[0]['years']; ?>-Year Pass
          </a>
        </div>

        <!-- Scroll Indicator Hint -->
        <div class="pass-scroll-instruction">
          <span class="scroll-mouse-icon"><span class="scroll-mouse-dot"></span></span>
          <span class="scroll-instruction-text">Scroll to flip passes &bull; Click tabs to switch</span>
        </div>
      </div>

    </div>
  </div>
</div>
