<?php
/**
 * Club Yatri - Reusable Testimonial Card Component
 * Expects $testimonial (array)
 */

declare(strict_types=1);

if (!isset($testimonial) || !is_array($testimonial)) {
    return;
}
?>
<div class="card-luxury" style="padding: 32px 28px;">
  <div style="display: flex; gap: 4px; color: var(--gold-primary); margin-bottom: 16px; font-size: 1.1rem;">
    <?php echo render_stars((int)($testimonial['rating'] ?? 5)); ?>
  </div>
  <p style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.7; font-style: italic; margin-bottom: 24px;">
    "<?php echo e($testimonial['quote']); ?>"
  </p>
  <div style="display: flex; align-items: center; gap: 14px;">
    <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--gold-gradient-soft); border: 1.5px solid var(--border-gold); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--gold-primary);">
      <?php echo e($testimonial['initials']); ?>
    </div>
    <div>
      <div style="font-weight: 700; color: #fff;"><?php echo e($testimonial['author']); ?></div>
      <div style="font-size: 0.8rem; color: var(--gold-primary);"><?php echo e($testimonial['tier']); ?> • <?php echo e($testimonial['city']); ?></div>
    </div>
  </div>
</div>
