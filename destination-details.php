<?php
/**
 * Club Yatri - Destination Detail Page (database destinations + hotels in that destination)
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/destinations-db.php';

$slug = trim((string)($_GET['slug'] ?? ''));
$destination = $slug !== '' ? destination_find($slug) : null;

if ($destination === null) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    exit;
}

$destHighlights = destination_highlights($destination['highlights'] ?? '');
$destImg = hotel_image_url($destination['hero_image'] ?? null);
$destSource = destination_safe_source($destination['source_url'] ?? '');
$destHotels = hotels_search(['q' => '', 'type' => '', 'category' => '', 'location' => $destination['name'], 'page' => 1], 6, 0);
$destHotelsTotal = (int)$destination['hotels_count'];
$destRelated = destinations_related($destination);

$pageTitle = $destination['meta_title'] ?: ($destination['name'] . ' - Hotels & Travel Guide');
$pageDescription = $destination['meta_description'] ?: hotel_excerpt($destination['description'] ?? '', 160);
$breadcrumbTitle = $destination['name'];
$parentTitle = 'Destinations';
$parentUrl = url('destinations.php');

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<!-- Destination banner -->
<section class="destination-banner" <?php echo $destImg !== '' ? 'style="--dest-bg: url(\'' . e($destImg) . '\');"' : ''; ?>>
  <div class="container">
    <div class="sub-tag destination-banner-tag"><?php echo $destination['location_type'] === 'DOMESTIC' ? 'India' : 'International &middot; ' . e($destination['country']); ?></div>
    <h1 class="destination-banner-title"><?php echo e($destination['name']); ?></h1>
    <?php if (!empty($destination['tagline'])): ?>
      <p class="destination-banner-sub"><?php echo e($destination['tagline']); ?></p>
    <?php endif; ?>
  </div>
</section>

<section class="section" style="padding-top: 50px; padding-bottom: 40px;">
  <div class="container">
    <div class="grid-2" style="align-items: start; gap: 48px; grid-template-columns: 1.6fr 1fr;">

      <div>
        <h2 class="heading-serif" style="font-size: 1.6rem; margin-bottom: 14px;">About <?php echo e($destination['name']); ?></h2>
        <p style="font-size: 1.05rem; color: var(--color-text-muted); line-height: 1.85; margin-bottom: 28px;">
          <?php echo e($destination['description']); ?>
        </p>

        <?php if ($destHighlights): ?>
          <h3 class="heading-serif" style="font-size: 1.25rem; margin-bottom: 14px;">Top Attractions</h3>
          <ul class="destination-highlights">
            <?php foreach ($destHighlights as $h): ?>
              <li><?php echo e($h); ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <aside>
        <div class="card-luxury" style="padding: 26px;">
          <h3 class="heading-serif" style="font-size: 1.2rem; margin-bottom: 14px;">At a Glance</h3>
          <ul class="hotel-facts">
            <li><span>Country</span><strong><?php echo e($destination['country']); ?></strong></li>
            <li><span>Region</span><strong><?php echo e(destination_type_label($destination['location_type'])); ?></strong></li>
            <li><span>Hotels in Network</span><strong><?php echo number_format($destHotelsTotal); ?></strong></li>
            <?php if (!empty($destination['best_time'])): ?>
              <li><span>Best Time to Visit</span><strong><?php echo e($destination['best_time']); ?></strong></li>
            <?php endif; ?>
          </ul>
          <?php if ($destHotelsTotal > 0): ?>
            <a href="<?php echo e(url('resort-network.php?location=' . urlencode($destination['name']) . '#network-hotels')); ?>" class="btn btn-gold" style="width: 100%; justify-content: center; margin-top: 20px;">
              Browse Hotels in <?php echo e($destination['name']); ?>
            </a>
          <?php endif; ?>
          <a href="<?php echo url('callback-request.php'); ?>" class="btn btn-outline-gold" style="width: 100%; justify-content: center; margin-top: 10px;">
            Request a Callback
          </a>
        </div>

        <?php if ($destSource !== ''): ?>
          <p class="destination-credit">
            Photo: <a href="<?php echo e($destSource); ?>" target="_blank" rel="noopener nofollow">Wikimedia Commons</a>
          </p>
        <?php endif; ?>
      </aside>
    </div>
  </div>
</section>

<?php if ($destHotels): ?>
<section class="section" style="background: var(--color-background-soft); padding-top: 60px; padding-bottom: 70px;">
  <div class="container">
    <div class="section-title-wrap" style="margin-bottom: 36px;">
      <div class="sub-tag">Where to Stay</div>
      <h2 class="section-title heading-serif">Hotels in <span style="color: var(--color-primary);"><?php echo e($destination['name']); ?></span></h2>
    </div>
    <div class="grid-3 hotel-grid">
      <?php foreach ($destHotels as $hotel): ?>
        <?php include __DIR__ . '/includes/hotel-card.php'; ?>
      <?php endforeach; ?>
    </div>
    <?php if ($destHotelsTotal > count($destHotels)): ?>
      <div style="text-align: center; margin-top: 36px;">
        <a href="<?php echo e(url('resort-network.php?location=' . urlencode($destination['name']) . '#network-hotels')); ?>" class="btn-red btn-lg">
          View All <?php echo number_format($destHotelsTotal); ?> Hotels in <?php echo e($destination['name']); ?> &rarr;
        </a>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<?php if ($destRelated): ?>
<section class="section" style="padding-top: 60px; padding-bottom: 80px;">
  <div class="container">
    <div class="section-title-wrap" style="margin-bottom: 36px;">
      <div class="sub-tag">Keep Exploring</div>
      <h2 class="section-title heading-serif">More <span style="color: var(--color-primary);">Destinations</span></h2>
    </div>
    <div class="grid-3 hotel-grid">
      <?php foreach ($destRelated as $dest): ?>
        <?php include __DIR__ . '/includes/destination-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
