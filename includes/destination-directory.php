<?php
/**
 * Club Yatri - Searchable, paginated directory of destinations (database).
 */

declare(strict_types=1);

require_once __DIR__ . '/destinations-db.php';

$ddFilters = destination_filters();
$ddTotalAll = destinations_total();
if ($ddTotalAll === 0) {
    return; // database empty or unavailable: hide the section
}

$ddTotal = destinations_count($ddFilters);
$ddPages = max(1, (int)ceil($ddTotal / DESTINATIONS_PER_PAGE));
$ddFilters['page'] = min($ddFilters['page'], $ddPages);
$ddItems = destinations_search($ddFilters);
$ddSelf = basename((string)parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH));

$ddLink = function (int $page) use ($ddFilters, $ddSelf): string {
    $q = array_filter([
        'q' => $ddFilters['q'],
        'type' => $ddFilters['type'],
        'page' => $page > 1 ? $page : '',
    ], fn($v) => $v !== '' && $v !== null);
    return url($ddSelf) . ($q ? '?' . http_build_query($q) : '') . '#destination-directory';
};
?>
<section class="section" id="destination-directory" style="background: var(--color-background-soft); padding-top: 0; padding-bottom: 80px;">
  <div class="container">
    <div class="section-title-wrap">
      <p class="section-desc">
        <?php echo e(number_format($ddTotalAll)); ?> destinations across India and the world, each with the hotels available in the Club Yatri network.
      </p>
    </div>

    <form class="hotel-filter destination-filter" method="GET" action="<?php echo e(url($ddSelf)); ?>#destination-directory">
      <div class="hotel-filter-field hotel-filter-search">
        <label for="destSearch">Search</label>
        <input type="search" id="destSearch" name="q" value="<?php echo e($ddFilters['q']); ?>" placeholder="Destination, country or attraction">
      </div>
      <div class="hotel-filter-field">
        <label for="destType">Region</label>
        <select id="destType" name="type">
          <option value="">All Regions</option>
          <option value="DOMESTIC" <?php echo $ddFilters['type'] === 'DOMESTIC' ? 'selected' : ''; ?>>India</option>
          <option value="INTERNATIONAL" <?php echo $ddFilters['type'] === 'INTERNATIONAL' ? 'selected' : ''; ?>>International</option>
        </select>
      </div>
      <div class="hotel-filter-actions">
        <button type="submit" class="btn btn-gold">Search</button>
        <a href="<?php echo e(url($ddSelf)); ?>#destination-directory" class="btn btn-outline-gold">Reset</a>
      </div>
    </form>

    <p class="hotel-result-count" role="status">
      Showing <?php echo number_format(count($ddItems)); ?> of <?php echo number_format($ddTotal); ?> destinations
      <?php if ($ddPages > 1): ?>(page <?php echo $ddFilters['page']; ?> of <?php echo $ddPages; ?>)<?php endif; ?>
    </p>

    <?php if (!$ddItems): ?>
      <div class="card-luxury" style="padding: 40px; text-align: center;">
        <p style="color: var(--color-text-muted);">No destinations match your search. Try a different name or clear the filters.</p>
      </div>
    <?php else: ?>
      <div class="grid-3 hotel-grid">
        <?php foreach ($ddItems as $dest): ?>
          <?php include __DIR__ . '/destination-card.php'; ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($ddPages > 1):
      $from = max(1, $ddFilters['page'] - 2);
      $to = min($ddPages, $ddFilters['page'] + 2);
    ?>
      <nav class="hotel-pagination" aria-label="Destination pages">
        <?php if ($ddFilters['page'] > 1): ?>
          <a href="<?php echo e($ddLink($ddFilters['page'] - 1)); ?>" rel="prev">&larr; Previous</a>
        <?php endif; ?>
        <?php if ($from > 1): ?>
          <a href="<?php echo e($ddLink(1)); ?>">1</a><?php if ($from > 2): ?><span>…</span><?php endif; ?>
        <?php endif; ?>
        <?php for ($p = $from; $p <= $to; $p++): ?>
          <a href="<?php echo e($ddLink($p)); ?>" <?php echo $p === $ddFilters['page'] ? 'class="current" aria-current="page"' : ''; ?>><?php echo $p; ?></a>
        <?php endfor; ?>
        <?php if ($to < $ddPages): ?>
          <?php if ($to < $ddPages - 1): ?><span>…</span><?php endif; ?><a href="<?php echo e($ddLink($ddPages)); ?>"><?php echo $ddPages; ?></a>
        <?php endif; ?>
        <?php if ($ddFilters['page'] < $ddPages): ?>
          <a href="<?php echo e($ddLink($ddFilters['page'] + 1)); ?>" rel="next">Next &rarr;</a>
        <?php endif; ?>
      </nav>
    <?php endif; ?>
  </div>
</section>
