<?php
/**
 * Club Yatri - Reusable Gallery Card Component
 * Expects $image (array with url, caption, location)
 */

declare(strict_types=1);

if (!isset($item) || !is_array($item)) {
    return;
}
?>
<div class="card-luxury" style="overflow: hidden;">
  <div class="card-img-wrap" style="height: 280px;">
    <img src="<?php echo e($item['url']); ?>" alt="<?php echo e($item['caption']); ?>" loading="lazy">
    <?php if (!empty($item['badge'])): ?>
      <span class="card-badge"><?php echo e($item['badge']); ?></span>
    <?php endif; ?>
  </div>
  <div class="card-body" style="padding: 18px 22px;">
    <div class="card-location" style="margin-bottom: 4px;">
      <span><?php echo e($item['location']); ?></span>
    </div>
    <h4 style="font-size: 1.1rem; color: #fff; margin: 0;"><?php echo e($item['caption']); ?></h4>
  </div>
</div>
