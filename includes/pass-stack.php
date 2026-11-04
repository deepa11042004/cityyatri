<?php
/** Club Yatri - stacked pass banner cards (shared by the homepage and the Passes page). */
$subscriptionPasses = [
    ['plan' => 'royal-bharat', 'years' => 3, 'title' => '3-Year Pass (Yatri Privilege Crown Asset)'],
    ['plan' => 'asian-signature', 'years' => 5, 'title' => '5-Year Pass (Yatri Privilege Crown Asset)'],
    ['plan' => 'imperial-world', 'years' => 10, 'title' => '10-Year Pass (Ultimate Global VIP Asset)'],
    ['plan' => 'privilege-crown', 'years' => 25, 'title' => '25-Year Pass (Yatri Privilege Crown Asset)'],
];
?>
<div class="pass-stack" id="passStack">
  <?php $passTotal = count($subscriptionPasses); ?>
  <?php foreach ($subscriptionPasses as $i => $p): ?>
    <?php if ($i > 0): ?><div class="pass-gap" aria-hidden="true"></div><?php endif; ?>
    <div class="pass-anchor" aria-hidden="true"></div>
    <article class="pass-card pass-card-img tone-<?php echo $i + 1; ?>" style="--i: <?php echo $i; ?>;">
      <button type="button" class="pass-zoom" aria-label="View <?php echo e($p['title']); ?> full screen">
        <img class="pass-banner" src="<?php echo e(url('images/passes/pass-' . (int)$p['years'] . '-year.jpg')); ?>" alt="<?php echo e($p['title']); ?>"<?php echo $i > 0 ? ' loading="lazy"' : ''; ?>>
      </button>
      <div class="pass-banner-bar">
        <a href="<?php echo url('booking-request.php?plan=' . urlencode($p['plan'])); ?>" class="btn-yellow pass-cta">
          Enquire <?php echo (int)$p['years']; ?>-Year Pass
        </a>
      </div>
    </article>
  <?php endforeach; ?>
  <div class="pass-tail" aria-hidden="true"></div>
</div>
