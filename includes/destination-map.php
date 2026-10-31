<?php
/**
 * Club Yatri - Destination world map: every destination in the database as a pin on the globe artwork.
 * Clicking a pin (or choosing a destination from the list) loads that destination's hotels below the map.
 * Dispatches a "destmap:select" event on document ({detail: {slug}}, slug === 'all' when cleared).
 */

declare(strict_types=1);

require_once __DIR__ . '/destinations-db.php';

$mapPins = destination_map_pins();
if (!$mapPins) {
    return; // no destinations in the database yet
}

$mapArtW = 727;   // pin positions are stored in these artwork pixels
$mapArtH = 479;
$mapJump = $mapPins;
usort($mapJump, fn($a, $b) => strcasecmp($a['name'], $b['name']));
?>
<div class="dest-dark" id="destMap" data-endpoint="<?php echo e(url('destination-hotels.php')); ?>">
  <div class="dest-wrap">
    <div class="dest-head">
      <div class="sub-tag">Curated Destination Network</div>
      <h2 class="section-title heading-serif">Touch Any Destination Pin to <span style="color: var(--color-primary);">Filter Resorts</span></h2>
    </div>
    <p class="section-desc dest-desc">
      Click or tap any destination pin below to reveal the Club Yatri hotels available there.
    </p>

    <div class="dest-zoom" role="group" aria-label="Map zoom">
      <button type="button" id="destZoomIn" aria-label="Zoom in">+</button>
      <button type="button" id="destZoomOut" aria-label="Zoom out" disabled>&minus;</button>
    </div>

    <!-- Globe artwork with the destination pins on it (pin positions are % of the artwork) -->
    <div class="dest-scroll" id="destScroll">
      <div class="dest-stage" role="group" aria-label="World map of Club Yatri destinations">
        <img class="dest-art" src="<?php echo e(BASE_PATH); ?>/images/dest-globe.png" alt="" width="1454" height="958" loading="lazy" decoding="async">
        <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
          <symbol id="destPinIcon" viewBox="0 0 24 34"><path fill-rule="evenodd" d="M12 0C5.4 0 0 5.3 0 11.8c0 8.7 10.1 20.4 11.1 21.6.5.6 1.3.6 1.8 0C13.9 32.2 24 20.5 24 11.8 24 5.3 18.6 0 12 0Zm0 6.7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Z"/></symbol>
        </svg>
        <?php foreach ($mapPins as $pin): ?>
        <button type="button" class="dest-pin" aria-pressed="false"
                data-destination="<?php echo e($pin['slug']); ?>"
                style="--x: <?php echo round((float)$pin['map_x'] / $mapArtW * 100, 2); ?>%; --y: <?php echo round((float)$pin['map_y'] / $mapArtH * 100, 2); ?>%;">
          <svg viewBox="0 0 24 34" aria-hidden="true" focusable="false"><use href="#destPinIcon"></use></svg>
          <span class="dest-pin-label"><?php echo e($pin['name']); ?></span>
        </button>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="dest-hint">
    <div class="dest-controls">
      <button type="button" class="dest-reset active" id="worldMapReset" data-destination="all">🌐 Show All</button>
      <label class="dest-jump">
        <span class="sr-only">Find a destination</span>
        <select id="destJump">
          <option value="">Find a destination&hellip; (<?php echo count($mapJump); ?>)</option>
          <?php foreach ($mapJump as $pin): ?>
            <option value="<?php echo e($pin['slug']); ?>"><?php echo e($pin['name']); ?><?php echo $pin['country'] !== $pin['name'] ? ', ' . e($pin['country']) : ''; ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <div class="dest-hint-text">
      👆 <em>Select any destination pin above, or pick one from the list. Use + to zoom in on crowded areas.</em>
    </div>
  </div>
</div><!-- /.dest-dark -->

<div class="dest-result" id="destResult" hidden aria-live="polite"></div>

<script>
(function () {
  var root = document.getElementById('destMap');
  if (!root) { return; }
  var pins = Array.prototype.slice.call(root.querySelectorAll('.dest-pin'));
  var resetBtn = document.getElementById('worldMapReset');
  var jump = document.getElementById('destJump');
  var result = document.getElementById('destResult');
  var scroller = document.getElementById('destScroll');
  var stage = root.querySelector('.dest-stage');
  var zoomIn = document.getElementById('destZoomIn');
  var zoomOut = document.getElementById('destZoomOut');
  var endpoint = root.getAttribute('data-endpoint');
  var levels = [1, 2, 3.2];
  var zoomIdx = 0;
  var active = 'all';
  var token = 0;

  function pinFor(slug) {
    for (var i = 0; i < pins.length; i++) { if (pins[i].getAttribute('data-destination') === slug) { return pins[i]; } }
    return null;
  }

  function centreOn(el) {
    if (!el || zoomIdx === 0) { return; }
    scroller.scrollTo({
      left: el.offsetLeft - scroller.clientWidth / 2,
      top: el.offsetTop - scroller.clientHeight / 2,
      behavior: 'smooth'
    });
  }

  function setZoom(idx, focusEl) {
    zoomIdx = Math.max(0, Math.min(levels.length - 1, idx));
    root.style.setProperty('--zoom', String(levels[zoomIdx]));
    root.classList.toggle('is-zoomed', zoomIdx > 0);
    zoomIn.disabled = zoomIdx === levels.length - 1;
    zoomOut.disabled = zoomIdx === 0;
    if (zoomIdx === 0) { scroller.scrollLeft = 0; scroller.scrollTop = 0; }
    if (focusEl) { requestAnimationFrame(function () { centreOn(focusEl); }); }
    else if (zoomIdx > 0) { requestAnimationFrame(centreIndia); }
  }

  // Zoom towards the busiest part of the map (India) when no pin is selected
  function centreIndia() {
    var p = pinFor('jaipur') || pins[0];
    if (!p) { return; }
    scroller.scrollTo({ left: p.offsetLeft - scroller.clientWidth / 2, top: p.offsetTop - scroller.clientHeight / 2 });
  }

  function select(slug, scrollToResult) {
    var mine = ++token;
    active = slug;
    pins.forEach(function (p) {
      var on = p.getAttribute('data-destination') === slug;
      p.classList.toggle('is-active', on);
      p.setAttribute('aria-pressed', on ? 'true' : 'false');
    });
    if (resetBtn) { resetBtn.classList.toggle('active', slug === 'all'); }
    if (jump) { jump.value = slug === 'all' ? '' : slug; }
    document.dispatchEvent(new CustomEvent('destmap:select', { detail: { slug: slug } }));

    if (slug === 'all') { result.hidden = true; result.innerHTML = ''; return; }

    result.hidden = false;
    result.innerHTML = '<p class="dest-result-empty">Loading hotels&hellip;</p>';
    fetch(endpoint + '?slug=' + encodeURIComponent(slug), { headers: { 'Accept': 'text/html' } })
      .then(function (r) { if (!r.ok) { throw new Error('http ' + r.status); } return r.text(); })
      .then(function (html) {
        if (mine !== token) { return; }
        result.innerHTML = html;
        if (scrollToResult) { result.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
      })
      .catch(function () {
        if (mine === token) { result.innerHTML = '<p class="dest-result-empty">Sorry, the hotels could not be loaded. Please try again.</p>'; }
      });
  }

  pins.forEach(function (pin) {
    pin.addEventListener('click', function () {
      var slug = pin.getAttribute('data-destination');
      select(active === slug ? 'all' : slug, active !== slug);
    });
  });
  if (resetBtn) { resetBtn.addEventListener('click', function () { select('all', false); }); }
  if (jump) {
    jump.addEventListener('change', function () {
      if (!jump.value) { select('all', false); return; }
      var pin = pinFor(jump.value);
      if (pin) { centreOn(pin); }
      select(jump.value, true);
    });
  }
  zoomIn.addEventListener('click', function () { setZoom(zoomIdx + 1, active !== 'all' ? pinFor(active) : null); });
  zoomOut.addEventListener('click', function () { setZoom(zoomIdx - 1, active !== 'all' ? pinFor(active) : null); });

  // On narrow screens the globe is wider than the screen and pans sideways: start centred on the pins
  if (scroller.scrollWidth > scroller.clientWidth && pins.length) {
    var lefts = pins.map(function (p) { return p.offsetLeft; });
    scroller.scrollLeft = (Math.min.apply(null, lefts) + Math.max.apply(null, lefts)) / 2 - scroller.clientWidth / 2;
  }
})();
</script>
