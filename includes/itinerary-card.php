<?php
/**
 * Club Yatri - Reusable Itinerary Card (database itineraries)
 * Expects $it (row from the itineraries table)
 */

declare(strict_types=1);

if (!isset($it) || !is_array($it)) {
    return;
}
$itImg = hotel_image_url($it['hero_image'] ?? null);
$itUrl = url('itinerary-details.php?slug=' . urlencode($it['slug']));
?>
<article class="card-luxury itinerary-card">
  <a class="card-img-wrap" href="<?php echo e($itUrl); ?>" tabindex="-1" aria-hidden="true">
    <?php if ($itImg !== ''): ?>
      <img src="<?php echo e($itImg); ?>" alt="<?php echo e($it['title']); ?>" loading="lazy" width="1600" height="1067">
    <?php else: ?>
      <div class="hotel-noimg"><span><?php echo e(mb_substr($it['title'], 0, 1)); ?></span></div>
    <?php endif; ?>
    <?php if (!empty($it['badge_text'])): ?><span class="card-badge"><?php echo e($it['badge_text']); ?></span><?php endif; ?>
    <span class="card-points-tag"><?php echo (int)$it['duration_days']; ?>D / <?php echo (int)$it['duration_nights']; ?>N</span>
  </a>
  <div class="card-body">
    <div class="card-location">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
      <span><?php echo e($it['location_label']); ?></span>
    </div>
    <h3 class="card-title"><a href="<?php echo e($itUrl); ?>" style="color: inherit;"><?php echo e($it['title']); ?></a></h3>
    <p class="card-desc"><?php echo e(hotel_excerpt($it['summary'] ?? '', 140)); ?></p>
    <div class="card-footer">
      <span class="hotel-type-tag"><?php echo e($it['category']); ?></span>
      <a href="<?php echo e($itUrl); ?>" class="btn btn-gold btn-sm">View Itinerary</a>
    </div>
  </div>
</article>
