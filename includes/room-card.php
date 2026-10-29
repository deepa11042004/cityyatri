<?php
/**
 * Club Yatri - Reusable Room Card Component
 * Expects $room (array)
 */

declare(strict_types=1);

if (!isset($room) || !is_array($room)) {
    return;
}
?>
<article class="card-luxury">
  <div class="card-img-wrap">
    <img src="<?php echo e($room['image']); ?>" alt="<?php echo e($room['title']); ?>" loading="lazy">
    <span class="card-badge"><?php echo e($room['capacity']); ?></span>
    <span class="card-points-tag"><?php echo format_inr($room['points_night']); ?> Pts / Night</span>
  </div>
  <div class="card-body">
    <div class="card-location">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
      <span><?php echo e($room['area']); ?> • <?php echo e($room['bed_type']); ?></span>
    </div>
    <h3 class="card-title"><?php echo e($room['title']); ?></h3>
    <p class="card-desc"><?php echo e($room['description']); ?></p>

    <div class="card-amenities">
      <?php foreach (array_slice($room['amenities'], 0, 4) as $amenity): ?>
        <span class="amenity-chip"><?php echo e($amenity); ?></span>
      <?php endforeach; ?>
    </div>

    <div class="card-footer">
      <a href="<?php echo url('room-details.php?id=' . urlencode($room['id'])); ?>" class="btn btn-outline-gold btn-sm">View Details</a>
      <a href="<?php echo url('booking-request.php?room=' . urlencode($room['id'])); ?>" class="btn btn-gold btn-sm">Reserve Suite</a>
    </div>
  </div>
</article>
