<?php
/**
 * Club Yatri - Reusable Destination Card (database destinations)
 * Expects $dest (row from destinations with hotels_count)
 */

declare(strict_types=1);

if (!isset($dest) || !is_array($dest)) {
    return;
}
$destImg = hotel_image_url($dest['hero_image'] ?? null);
$destUrl = url('destination-details.php?slug=' . urlencode($dest['slug']));
$destChips = array_slice(destination_highlights($dest['highlights'] ?? ''), 0, 3);
?>
<article class="card-luxury destination-card">
  <a class="card-img-wrap" href="<?php echo e($destUrl); ?>" tabindex="-1" aria-hidden="true">
    <?php if ($destImg !== ''): ?>
      <img src="<?php echo e($destImg); ?>" alt="<?php echo e($dest['name']); ?>" loading="lazy" width="1400" height="933">
    <?php else: ?>
      <div class="hotel-noimg"><span><?php echo e(mb_substr($dest['name'], 0, 1)); ?></span></div>
    <?php endif; ?>
    <span class="card-badge"><?php echo e(destination_type_label($dest['location_type'])); ?></span>
  </a>
  <div class="card-body">
    <div class="card-location">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
      <span><?php echo e($dest['country']); ?></span>
    </div>
    <h3 class="card-title"><a href="<?php echo e($destUrl); ?>" style="color: inherit;"><?php echo e($dest['name']); ?></a></h3>
    <p class="card-desc"><?php echo e(hotel_excerpt($dest['description'] ?? '', 130)); ?></p>
    <?php if ($destChips): ?>
      <div class="card-amenities">
        <?php foreach ($destChips as $chip): ?>
          <span class="amenity-chip"><?php echo e($chip); ?></span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <div class="card-footer">
      <span class="hotel-type-tag"><?php echo (int)$dest['hotels_count']; ?> <?php echo (int)$dest['hotels_count'] === 1 ? 'Hotel' : 'Hotels'; ?></span>
      <a href="<?php echo e($destUrl); ?>" class="btn btn-gold btn-sm">Explore</a>
    </div>
  </div>
</article>
