<?php
/**
 * Club Yatri - Worldwide hotel directory (country > city > hotel), from the new_hotels table.
 */

declare(strict_types=1);

$pageTitle = 'Worldwide Hotel Directory';
$pageDescription = 'Browse the complete directory of hotels worldwide by country and city.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/world-hotels.php';

$sort = hd_sort();
$countries = hd_countries($sort);
$totalHotels = array_sum(array_map(static fn(array $c): int => (int)$c['n'], $countries));
$dataUrl = url('actions/hotel-directory-data.php');

$breadcrumbTitle = 'Hotel Directory';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section about-hero" style="padding-top: 60px; padding-bottom: 24px; text-align: center;">
  <?php include __DIR__ . '/includes/leaves.php'; ?>
  <div class="container" style="position: relative; z-index: 1;">
    <div class="sub-tag">Hotel Directory</div>
    <h1 class="heading-serif" style="font-size: clamp(2.2rem, 4vw, 3.4rem); margin-bottom: 16px;">
      Worldwide <span class="gold-text">Hotel Directory</span>
    </h1>
    <p style="color: var(--text-secondary); max-width: 740px; margin: 0 auto; font-size: 1.05rem;">
      <?php if ($totalHotels > 0): ?>
        <strong><?php echo number_format($totalHotels); ?></strong> hotels across <strong><?php echo number_format(count($countries)); ?></strong> countries. Open a country to see its cities, then a city to see its hotels.
      <?php else: ?>
        The hotel directory is not available right now. Please check back soon.
      <?php endif; ?>
    </p>
  </div>
</section>

<section class="section" style="padding-top: 10px; padding-bottom: 80px;">
  <div class="container">
    <?php if ($totalHotels > 0): ?>
      <div class="hd-wrap">
        <div class="hd-sortbar">
          <span class="hd-sortlabel">Sort by</span>
          <div class="hd-toggle-group" role="group" aria-label="Sort order">
            <a class="hd-sortbtn<?php echo $sort === 'count' ? ' is-active' : ''; ?>" href="?sort=count" aria-pressed="<?php echo $sort === 'count' ? 'true' : 'false'; ?>">Count</a>
            <a class="hd-sortbtn<?php echo $sort === 'alpha' ? ' is-active' : ''; ?>" href="?sort=alpha" aria-pressed="<?php echo $sort === 'alpha' ? 'true' : 'false'; ?>">Alphabetically</a>
          </div>
          <span class="hd-sorthint"><?php echo $sort === 'count' ? 'Most hotels first' : 'A to Z'; ?></span>
        </div>

        <div class="hd-root"><?php echo e(number_format($totalHotels)); ?> hotels</div>
        <ul class="hd-list hd-countries" id="hdTree" data-url="<?php echo e($dataUrl); ?>" data-sort="<?php echo e($sort); ?>">
          <?php foreach ($countries as $c): ?>
            <li class="hd-node" data-country="<?php echo e($c['country']); ?>">
              <button type="button" class="hd-toggle" aria-expanded="false"><span class="hd-sign" aria-hidden="true">+</span><span class="hd-name"><?php echo e($c['country']); ?></span> <span class="hd-count"><?php echo e(hd_count_label((int)$c['n'])); ?></span></button>
              <div class="hd-children" hidden></div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
  </div>
</section>

<script>
(function () {
  var tree = document.getElementById('hdTree');
  if (!tree) return;
  var base = tree.getAttribute('data-url'), sort = tree.getAttribute('data-sort');

  tree.addEventListener('click', function (ev) {
    var btn = ev.target.closest('.hd-toggle');
    if (!btn || !tree.contains(btn)) return;
    var node = btn.parentNode, box = node.querySelector(':scope > .hd-children'), sign = btn.querySelector('.hd-sign');
    var open = btn.getAttribute('aria-expanded') === 'true';
    if (open) {
      box.hidden = true; btn.setAttribute('aria-expanded', 'false'); sign.textContent = '+';
      return;
    }
    box.hidden = false; btn.setAttribute('aria-expanded', 'true'); sign.textContent = '−';
    if (box.getAttribute('data-loaded')) return;

    var country = node.getAttribute('data-country'), city = node.getAttribute('data-city');
    var q = city ? 'level=hotels&country=' + encodeURIComponent(country) + '&city=' + encodeURIComponent(city)
                 : 'level=cities&country=' + encodeURIComponent(country) + '&sort=' + encodeURIComponent(sort);
    box.innerHTML = '<p class="hd-empty">Loading&hellip;</p>';
    fetch(base + '?' + q, { headers: { 'X-Requested-With': 'fetch' } })
      .then(function (r) { if (!r.ok) throw new Error(); return r.text(); })
      .then(function (html) { box.innerHTML = html; box.setAttribute('data-loaded', '1'); })
      .catch(function () { box.innerHTML = '<p class="hd-empty">Could not load this list. Please try again.</p>'; });
  });
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
