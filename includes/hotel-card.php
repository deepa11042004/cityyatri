<?php
/**
 * Club Yatri - Reusable Network Hotel Card (database hotels)
 * Expects $hotel (row from the hotels table)
 */

declare(strict_types=1);

if (!isset($hotel) || !is_array($hotel)) {
    return;
}
$hotelImg = hotel_image_url($hotel['image_path'] ?? null);
$hotelUrl = url('resort-details.php?id=' . urlencode($hotel['slug']));
?>
<article class="card-luxury hotel-card">
  <a class="card-img-wrap" href="<?php echo e($hotelUrl); ?>" tabindex="-1" aria-hidden="true">
    <?php if ($hotelImg !== ''): ?>
      <img src="<?php echo e($hotelImg); ?>" alt="<?php echo e($hotel['name']); ?>" loading="lazy" width="1000" height="667">
    <?php else: ?>
      <div class="hotel-noimg"><span><?php echo e(mb_substr($hotel['name'], 0, 1)); ?></span></div>
    <?php endif; ?>
    <span class="card-badge"><?php echo e(hotel_category_label($hotel['category'])); ?></span>
  </a>
  <div class="card-body">
    <div class="card-location">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
      <span><?php echo e($hotel['location_name']); ?>, <?php echo e($hotel['country']); ?></span>
    </div>
    <h3 class="card-title"><a href="<?php echo e($hotelUrl); ?>" style="color: inherit;"><?php echo e($hotel['name']); ?></a></h3>
    <p class="card-desc"><?php echo e(hotel_excerpt($hotel['description'] ?? '')); ?></p>
    <div class="card-footer">
      <span class="hotel-type-tag"><?php echo e(hotel_type_label($hotel['hotel_type'])); ?></span>
      <div style="display: flex; gap: 8px;">
        <a href="<?php echo e($hotelUrl); ?>" class="btn btn-outline-gold btn-sm">Details</a>
        <a href="<?php echo url('booking-request.php?resort=' . urlencode($hotel['slug'])); ?>" class="btn btn-gold btn-sm">Reserve</a>
      </div>
    </div>
  </div>
</article>
