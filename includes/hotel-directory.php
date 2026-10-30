<?php
/**
 * Club Yatri - Searchable, paginated directory of network hotels.
 * Optional input: $dirType ('DOMESTIC' | 'INTERNATIONAL' | '') locks the region.
 *                 $dirHeading / $dirIntro override the section heading.
 */

declare(strict_types=1);

require_once __DIR__ . '/hotels.php';

$dirType = $dirType ?? '';
$dirFilters = hotel_filters($dirType);
$dirTotal = hotels_count($dirFilters);
$dirTotalAll = hotels_count(['type' => $dirType, 'category' => '', 'location' => '', 'q' => '']);

if ($dirTotalAll === 0) {
    return; // database empty or unavailable: hide the whole section
}

$dirPages = max(1, (int)ceil($dirTotal / HOTELS_PER_PAGE));
$dirFilters['page'] = min($dirFilters['page'], $dirPages);
$dirHotels = hotels_search($dirFilters);
$dirLocations = hotels_locations($dirFilters['type']);
$dirSelf = basename((string)parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH));

$dirLink = function (int $page) use ($dirFilters, $dirSelf, $dirType): string {
    $q = array_filter([
        'q' => $dirFilters['q'],
        'type' => $dirType === '' ? $dirFilters['type'] : '',
        'location' => $dirFilters['location'],
        'category' => $dirFilters['category'],
        'page' => $page > 1 ? $page : '',
    ], fn($v) => $v !== '' && $v !== null);
    return url($dirSelf) . ($q ? '?' . http_build_query($q) : '') . '#network-hotels';
};
?>
<section class="section" id="network-hotels" style="background: var(--color-background-soft); padding-top: 70px; padding-bottom: 80px;">
  <div class="container">
    <div class="section-title-wrap">
      <div class="sub-tag">Hotel &amp; Resort Directory</div>
      <h2 class="section-title heading-serif">
        <?php echo $dirHeading ?? 'Browse the <span style="color: var(--color-primary);">Hotel Network</span>'; ?>
      </h2>
      <p class="section-desc">
        <?php echo e($dirIntro ?? ('Explore ' . number_format($dirTotalAll) . ' hotels and resorts across the Club Yatri network.')); ?>
      </p>
    </div>

    <form class="hotel-filter" method="GET" action="<?php echo e(url($dirSelf)); ?>#network-hotels">
      <div class="hotel-filter-field hotel-filter-search">
        <label for="hotelSearch">Search</label>
        <input type="search" id="hotelSearch" name="q" value="<?php echo e($dirFilters['q']); ?>" placeholder="Hotel, city or country">
      </div>
      <?php if ($dirType === ''): ?>
        <div class="hotel-filter-field">
          <label for="hotelType">Region</label>
          <select id="hotelType" name="type">
            <option value="">All Regions</option>
            <option value="DOMESTIC" <?php echo $dirFilters['type'] === 'DOMESTIC' ? 'selected' : ''; ?>>India</option>
            <option value="INTERNATIONAL" <?php echo $dirFilters['type'] === 'INTERNATIONAL' ? 'selected' : ''; ?>>International</option>
          </select>
        </div>
      <?php endif; ?>
      <div class="hotel-filter-field">
        <label for="hotelLocation">Destination</label>
        <select id="hotelLocation" name="location">
          <option value="">All Destinations</option>
          <?php foreach ($dirLocations as $loc): ?>
            <option value="<?php echo e($loc); ?>" <?php echo $dirFilters['location'] === $loc ? 'selected' : ''; ?>><?php echo e($loc); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="hotel-filter-field">
        <label for="hotelCategory">Category</label>
        <select id="hotelCategory" name="category">
          <option value="">All Categories</option>
          <option value="INTERNAL" <?php echo $dirFilters['category'] === 'INTERNAL' ? 'selected' : ''; ?>>Club Yatri Property</option>
          <option value="ASSOCIATED" <?php echo $dirFilters['category'] === 'ASSOCIATED' ? 'selected' : ''; ?>>Associated Partner</option>
        </select>
      </div>
      <div class="hotel-filter-actions">
        <button type="submit" class="btn btn-gold">Search</button>
        <a href="<?php echo e(url($dirSelf)); ?>#network-hotels" class="btn btn-outline-gold">Reset</a>
      </div>
    </form>

    <p class="hotel-result-count" role="status">
      Showing <?php echo number_format(count($dirHotels)); ?> of <?php echo number_format($dirTotal); ?> hotels
      <?php if ($dirPages > 1): ?>(page <?php echo $dirFilters['page']; ?> of <?php echo $dirPages; ?>)<?php endif; ?>
    </p>

    <?php if (!$dirHotels): ?>
      <div class="card-luxury" style="padding: 40px; text-align: center;">
        <p style="color: var(--color-text-muted);">No hotels match your search. Try a different destination or clear the filters.</p>
      </div>
    <?php else: ?>
      <div class="grid-3 hotel-grid">
        <?php foreach ($dirHotels as $hotel): ?>
          <?php include __DIR__ . '/hotel-card.php'; ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($dirPages > 1):
      $from = max(1, $dirFilters['page'] - 2);
      $to = min($dirPages, $dirFilters['page'] + 2);
    ?>
      <nav class="hotel-pagination" aria-label="Hotel pages">
        <?php if ($dirFilters['page'] > 1): ?>
          <a href="<?php echo e($dirLink($dirFilters['page'] - 1)); ?>" rel="prev">&larr; Previous</a>
        <?php endif; ?>
        <?php if ($from > 1): ?>
          <a href="<?php echo e($dirLink(1)); ?>">1</a><?php if ($from > 2): ?><span>…</span><?php endif; ?>
        <?php endif; ?>
        <?php for ($p = $from; $p <= $to; $p++): ?>
          <a href="<?php echo e($dirLink($p)); ?>" <?php echo $p === $dirFilters['page'] ? 'class="current" aria-current="page"' : ''; ?>><?php echo $p; ?></a>
        <?php endfor; ?>
        <?php if ($to < $dirPages): ?>
          <?php if ($to < $dirPages - 1): ?><span>…</span><?php endif; ?><a href="<?php echo e($dirLink($dirPages)); ?>"><?php echo $dirPages; ?></a>
        <?php endif; ?>
        <?php if ($dirFilters['page'] < $dirPages): ?>
          <a href="<?php echo e($dirLink($dirFilters['page'] + 1)); ?>" rel="next">Next &rarr;</a>
        <?php endif; ?>
      </nav>
    <?php endif; ?>
  </div>
</section>
