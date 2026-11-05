<?php
/** Club Yatri - stacked pass banner cards (shared by the homepage and the Passes page). */
require_once __DIR__ . '/content-db.php';
// Built-in list: used until passes are managed in the admin (Passes & Subscriptions)
$subscriptionPasses = [
    ['image' => null, 'plan' => 'royal-bharat', 'years' => 3, 'title' => '3-Year Pass (Yatri Privilege Crown Asset)'],
    ['image' => null, 'plan' => 'asian-signature', 'years' => 5, 'title' => '5-Year Pass (Yatri Privilege Crown Asset)'],
    ['image' => null, 'plan' => 'imperial-world', 'years' => 10, 'title' => '10-Year Pass (Ultimate Global VIP Asset)'],
    ['image' => null, 'plan' => 'privilege-crown', 'years' => 25, 'title' => '25-Year Pass (Yatri Privilege Crown Asset)'],
];
if (content_has_rows('passes')) {
    $subscriptionPasses = [];
    foreach (content_pass_plans() as $slug => $pl) {
        $subscriptionPasses[] = ['plan' => $slug, 'years' => $pl['years'], 'title' => $pl['title'], 'image' => $pl['image'], 'alt' => $pl['image_alt'], 'desc' => $pl['description']];
    }
}
?>
<div class="pass-stack" id="passStack">
  <?php $passTotal = count($subscriptionPasses); ?>
  <?php foreach ($subscriptionPasses as $i => $p): ?>
    <?php if ($i > 0): ?><div class="pass-gap" aria-hidden="true"></div><?php endif; ?>
    <div class="pass-anchor" aria-hidden="true"></div>
    <article class="pass-card pass-card-img tone-<?php echo $i + 1; ?>" style="--i: <?php echo $i; ?>;">
      <?php $passImg = $p['image'] === null ? 'images/passes/pass-' . (int)$p['years'] . '-year.jpg' : (string)$p['image']; ?>
      <?php if ($passImg !== ''): ?>
      <button type="button" class="pass-zoom" aria-label="View <?php echo e($p['title']); ?> full screen">
        <img class="pass-banner" src="<?php echo e(content_image_url($passImg)); ?>" alt="<?php echo e(($p['alt'] ?? '') !== '' ? $p['alt'] : $p['title']); ?>"<?php echo $i > 0 ? ' loading="lazy"' : ''; ?>>
      </button>
      <?php else: ?>
      <div class="pass-text-card">
        <h3><?php echo e($p['title']); ?></h3>
        <?php if (!empty($p['desc'])): ?><p><?php echo e($p['desc']); ?></p><?php endif; ?>
      </div>
      <?php endif; ?>
      <div class="pass-banner-bar">
        <a href="<?php echo url('booking-request.php?plan=' . urlencode($p['plan'])); ?>" class="btn-yellow pass-cta">
          <?php echo (int)$p['years'] > 0 ? 'Enquire ' . (int)$p['years'] . '-Year Pass' : 'Enquire Now'; ?>
        </a>
      </div>
    </article>
  <?php endforeach; ?>
  <div class="pass-tail" aria-hidden="true"></div>
</div>
