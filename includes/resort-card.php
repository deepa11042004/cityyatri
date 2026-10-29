<?php
/**
 * Club Yatri - Reusable Resort Card Component
 * Expects $resort (array)
 */

declare(strict_types=1);

if (!isset($resort) || !is_array($resort)) {
    return;
}
?>
<article class="card-luxury resort-item-card" data-category="<?php echo e(strtolower(str_replace([' ', '&'], '', $resort['category']))); ?>" data-destination="<?php echo e($resort['destination_id'] ?? ''); ?>">
  <div class="card-img-wrap">
    <img src="<?php echo e($resort['image']); ?>" alt="<?php echo e($resort['name']); ?>" loading="lazy">
    <span class="card-badge"><?php echo e($resort['category']); ?></span>
    <span class="card-points-tag"><?php echo format_inr($resort['points_night']); ?> Pts / Nt</span>
  </div>
  <div class="card-body">
    <div class="card-location">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
      <span><?php echo e($resort['location']); ?></span>
    </div>
    <h3 class="card-title"><?php echo e($resort['name']); ?></h3>
    <p class="card-desc"><?php echo e($resort['summary']); ?></p>

    <div class="card-amenities">
      <?php foreach (array_slice($resort['amenities'], 0, 3) as $amenity): ?>
        <span class="amenity-chip"><?php echo e($amenity); ?></span>
      <?php endforeach; ?>
    </div>

    <!-- Direct Google Maps & Search Integration -->
    <div style="margin-bottom: 14px;">
      <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($resort['name'] . ' ' . $resort['location']); ?>" target="_blank" rel="noopener noreferrer" class="btn-map-search" title="View <?php echo e($resort['name']); ?> on Google Maps">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
        <span>View on Google Maps / Search</span>
      </a>
    </div>

    <div class="card-footer">
      <div>
        <span style="font-size: 0.72rem; color: var(--color-ink-soft); display: block;">Allotment</span>
        <strong style="color: var(--color-primary-dark); font-size: 0.85rem;"><?php echo e($resort['allotment']); ?></strong>
      </div>
      <div style="display: flex; gap: 8px;">
        <a href="<?php echo url('resort-details.php?id=' . urlencode($resort['id'])); ?>" class="btn btn-outline-gold btn-sm">Details</a>
        <a href="<?php echo url('booking-request.php?resort=' . urlencode($resort['id'])); ?>" class="btn btn-gold btn-sm">Reserve</a>
      </div>
    </div>
  </div>
</article>
