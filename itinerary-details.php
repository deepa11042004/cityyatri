<?php
/**
 * Club Yatri - Itinerary Detail Page (database itineraries): day-by-day plan, highlights, inclusions, photos.
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/itineraries-db.php';
require_once __DIR__ . '/includes/destinations-db.php';

$slug = trim((string)($_GET['slug'] ?? ''));
$it = $slug !== '' ? itinerary_find($slug) : null;

if ($it === null) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    exit;
}

$days = itinerary_days((int)$it['id']);
$images = itinerary_images((int)$it['id']);
$highlights = itinerary_list($it['highlights'] ?? '');
$inclusions = itinerary_list($it['inclusions'] ?? '');
$related = itineraries_related($it);
$destination = !empty($it['destination_slug']) ? destination_find((string)$it['destination_slug']) : null;
$heroImg = hotel_image_url($images[0] ?? ($it['hero_image'] ?? null));
$gallery = array_slice($images, 1);

$pageTitle = $it['title'] . ' - ' . itinerary_duration($it);
$pageDescription = hotel_excerpt($it['summary'] ?? '', 160);
$breadcrumbTitle = $it['title'];
$parentTitle = 'Itineraries';
$parentUrl = url('itinerary.php');

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="destination-banner" <?php echo $heroImg !== '' ? 'style="--dest-bg: url(\'' . e($heroImg) . '\');"' : ''; ?>>
  <div class="container">
    <?php if (!empty($it['badge_text'])): ?>
      <div class="sub-tag destination-banner-tag"><?php echo e($it['badge_text']); ?></div>
    <?php endif; ?>
    <h1 class="destination-banner-title itinerary-banner-title"><?php echo e($it['title']); ?></h1>
    <p class="destination-banner-sub"><?php echo e($it['location_label']); ?></p>
  </div>
</section>

<section class="section" style="padding-top: 40px; padding-bottom: 20px;">
  <div class="container">
    <ul class="itinerary-facts" aria-label="Itinerary at a glance">
      <li><span>Duration</span><strong><?php echo e(itinerary_duration($it)); ?></strong></li>
      <li><span>Journey Style</span><strong><?php echo e($it['category']); ?></strong></li>
      <li><span>Best Time</span><strong><?php echo e($it['best_time']); ?></strong></li>
      <li><span>Region</span><strong><?php echo $it['location_type'] === 'DOMESTIC' ? 'India' : 'International'; ?></strong></li>
    </ul>
  </div>
</section>

<section class="section" style="padding-top: 30px; padding-bottom: 50px;">
  <div class="container">
    <div class="grid-2" style="align-items: start; gap: 48px; grid-template-columns: 1.6fr 1fr;">

      <div>
        <h2 class="heading-serif" style="font-size: 1.6rem; margin-bottom: 14px;">About This Journey</h2>
        <p style="font-size: 1.05rem; color: var(--color-text-muted); line-height: 1.85; margin-bottom: 30px;">
          <?php echo e($it['summary']); ?>
        </p>
        <?php if (!empty($it['overview'])): ?>
          <p style="font-size: 1.05rem; color: var(--color-text-muted); line-height: 1.85; margin: -12px 0 30px;"><?php echo e($it['overview']); ?></p>
        <?php endif; ?>

        <?php if ($highlights): ?>
          <h3 class="heading-serif" style="font-size: 1.25rem; margin-bottom: 14px;">Journey Highlights</h3>
          <ul class="destination-highlights" style="grid-template-columns: 1fr; margin-bottom: 36px;">
            <?php foreach ($highlights as $h): ?><li><?php echo e($h); ?></li><?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <?php if ($days): ?>
          <h2 class="heading-serif" style="font-size: 1.6rem; margin-bottom: 20px;">Day-by-Day Plan</h2>
          <ol class="itinerary-timeline">
            <?php foreach ($days as $d): ?>
              <li class="itinerary-day">
                <span class="itinerary-day-num" aria-hidden="true"><?php echo (int)$d['day_number']; ?></span>
                <div class="itinerary-day-body">
                  <div class="itinerary-day-label">Day <?php echo (int)$d['day_number']; ?></div>
                  <h3><?php echo e($d['title']); ?></h3>
                  <p><?php echo e($d['description']); ?></p>
                </div>
              </li>
            <?php endforeach; ?>
          </ol>
        <?php endif; ?>

        <?php if ($inclusions): ?>
          <h2 class="heading-serif" style="font-size: 1.6rem; margin: 40px 0 16px;">What's Included</h2>
          <ul class="itinerary-inclusions">
            <?php foreach ($inclusions as $inc): ?><li><?php echo e($inc); ?></li><?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <aside class="itinerary-aside">
        <div class="card-luxury" style="padding: 26px;">
          <h3 class="heading-serif" style="font-size: 1.2rem; margin-bottom: 6px;"><?php echo e(itinerary_duration($it)); ?></h3>
          <p style="color: var(--color-text-muted); font-size: 0.92rem; margin-bottom: 18px;"><?php echo e($it['location_label']); ?></p>
          <a href="<?php echo url('callback-request.php'); ?>" class="btn btn-gold" style="width: 100%; justify-content: center;">
            Enquire About This Journey
          </a>
          <a href="<?php echo e(url('booking-request.php?itinerary=' . urlencode($it['title']))); ?>" class="btn btn-outline-gold" style="width: 100%; justify-content: center; margin-top: 10px;">
            Reserve This Itinerary
          </a>
          <?php if ($destination): ?>
            <p style="margin-top: 18px; font-size: 0.92rem;">
              <a href="<?php echo e(url('destination-details.php?slug=' . urlencode($destination['slug']))); ?>" style="font-weight: 600;">
                Explore the <?php echo e($destination['name']); ?> destination guide &rarr;
              </a><br>
              <a href="<?php echo e(url('resort-network.php?location=' . urlencode($destination['name']) . '#network-hotels')); ?>" style="font-weight: 600;">
                Hotels in <?php echo e($destination['name']); ?> &rarr;
              </a>
            </p>
          <?php endif; ?>
        </div>
      </aside>
    </div>
  </div>
</section>

<?php if ($gallery): ?>
<section class="section" style="padding-top: 10px; padding-bottom: 50px;">
  <div class="container">
    <h2 class="heading-serif" style="font-size: 1.6rem; margin-bottom: 20px;">Photo Gallery</h2>
    <div class="itinerary-gallery">
      <?php foreach ($gallery as $i => $img): ?>
        <figure><img src="<?php echo e(hotel_image_url($img)); ?>" alt="<?php echo e($it['title'] . ' photo ' . ($i + 2)); ?>" loading="lazy" width="1600" height="1067"></figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($related): ?>
<section class="section" style="background: var(--color-background-soft); padding-top: 60px; padding-bottom: 80px;">
  <div class="container">
    <div class="section-title-wrap" style="margin-bottom: 36px;">
      <div class="sub-tag">Keep Exploring</div>
      <h2 class="section-title heading-serif">More <span style="color: var(--color-primary);">Itineraries</span></h2>
    </div>
    <div class="grid-3 hotel-grid">
      <?php foreach ($related as $it2): $itMain = $it; $it = $it2; ?>
        <?php include __DIR__ . '/includes/itinerary-card.php'; ?>
      <?php $it = $itMain; endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
