<?php
/**
 * Club Yatri - Reusable Breadcrumbs Component
 * Driven by $breadcrumbTitle and optional $parentTitle & $parentUrl
 */

declare(strict_types=1);

$currentTitle = $breadcrumbTitle ?? ($pageTitle ?? 'Page');
$parentT = $parentTitle ?? 'Home';
$parentU = $parentUrl ?? url('index.php');
?>
<nav aria-label="Breadcrumb" style="padding: 14px 0; background: var(--color-background-soft); border-bottom: 1px solid var(--color-border);">
  <div class="container">
    <ol style="display: flex; align-items: center; gap: 8px; list-style: none; margin: 0; padding: 0; font-size: 0.85rem; color: var(--color-text-muted); flex-wrap: wrap;">
      <li>
        <a href="<?php echo e($parentU); ?>" style="color: var(--color-primary); font-weight: 600; text-decoration: none;">
          <?php echo e($parentT); ?>
        </a>
      </li>
      <li style="color: var(--color-border-subtle); font-weight: bold;">/</li>
      <?php if (!empty($grandParentTitle) && !empty($grandParentUrl)): ?>
        <li>
          <a href="<?php echo e($grandParentUrl); ?>" style="color: var(--gold-primary); text-decoration: none;">
            <?php echo e($grandParentTitle); ?>
          </a>
        </li>
        <li style="color: var(--text-muted);">/</li>
      <?php endif; ?>
      <li style="color: var(--text-secondary); font-weight: 600;" aria-current="page">
        <?php echo e($currentTitle); ?>
      </li>
    </ol>
  </div>
</nav>
