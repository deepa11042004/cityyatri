<?php
/**
 * Club Yatri - Reusable Subscription / Pass Card Component
 * Expects $plan (array)
 */

declare(strict_types=1);

if (!isset($plan) || !is_array($plan)) {
    return;
}
$isFeatured = !empty($plan['featured']);
?>
<div class="tier-card <?php echo $isFeatured ? 'featured' : ''; ?>">
  <?php if ($isFeatured): ?>
    <div class="tier-ribbon"><?php echo e($plan['badge']); ?></div>
  <?php endif; ?>
  
  <div class="tier-name"><?php echo e($plan['short_title']); ?></div>
  <div class="tier-subtitle"><?php echo e($plan['tenure']); ?> Vacation Tenure</div>

  <div class="tier-points">
    <div class="tier-points-val"><?php echo e((string)$plan['annual_nights']); ?> Nights</div>
    <div class="tier-points-label">Annual Allocation (<?php echo number_format($plan['points_annual']); ?> Points)</div>
  </div>

  <p style="font-size: 0.88rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 20px;">
    <?php echo e($plan['description']); ?>
  </p>

  <ul class="tier-features">
    <?php foreach ($plan['highlight_features'] as $feature): ?>
      <li class="tier-feature-item">
        <span class="tier-check-icon">✓</span>
        <span><?php echo e($feature); ?></span>
      </li>
    <?php endforeach; ?>
  </ul>

  <div style="margin-top: auto; display: flex; flex-direction: column; gap: 10px;">
    <a href="<?php echo url('pass-details.php?id=' . urlencode($plan['id'])); ?>" class="btn btn-outline-gold" style="width: 100%;">
      Pass Specifications
    </a>
    <a href="<?php echo url('booking-request.php?plan=' . urlencode($plan['id'])); ?>" class="btn <?php echo $isFeatured ? 'btn-gold' : 'btn-ghost'; ?>" style="width: 100%;">
      Enquire Allocation
    </a>
  </div>
</div>
