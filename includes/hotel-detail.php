<?php
/**
 * Club Yatri - Network hotel detail view. Expects $hotel (row from the hotels table).
 */

declare(strict_types=1);

$hotelImages = hotel_images((int)$hotel['id']);
$hotelMap = hotel_safe_map_url($hotel['map_link'] ?? '');
$hotelStays = hotel_stay_types($hotel['stay_types'] ?? '');
$hotelRelated = hotels_related($hotel);
$hotelParas = array_filter(array_map('trim', preg_split('/\n\s*\n/', (string)($hotel['description'] ?? ''))));
?>
<section class="section" style="padding-top: 50px; padding-bottom: 70px;">
  <div class="container">
    <div class="grid-2" style="align-items: start; gap: 48px;">

      <!-- Gallery & quick facts -->
      <div>
        <div class="hotel-gallery-main">
          <?php if ($hotelImages): ?>
            <img id="hotelMainImage" src="<?php echo e(hotel_image_url($hotelImages[0])); ?>" alt="<?php echo e($hotel['name']); ?>">
          <?php else: ?>
            <div class="hotel-noimg hotel-noimg-lg"><span><?php echo e(mb_substr($hotel['name'], 0, 1)); ?></span></div>
          <?php endif; ?>
        </div>
        <?php if (count($hotelImages) > 1): ?>
          <div class="hotel-gallery-thumbs">
            <?php foreach ($hotelImages as $i => $img): ?>
              <button type="button" class="hotel-thumb<?php echo $i === 0 ? ' active' : ''; ?>" data-src="<?php echo e(hotel_image_url($img)); ?>" aria-label="Show photo <?php echo $i + 1; ?>">
                <img src="<?php echo e(hotel_image_url($img)); ?>" alt="" loading="lazy">
              </button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div class="card-luxury" style="padding: 26px; margin-top: 24px;">
          <h3 class="heading-serif" style="font-size: 1.25rem; margin-bottom: 14px;">Property Details</h3>
          <ul class="hotel-facts">
            <li><span>Destination</span><strong><?php echo e($hotel['location_name']); ?>, <?php echo e($hotel['country']); ?></strong></li>
            <li><span>Property Type</span><strong><?php echo e(hotel_type_label($hotel['hotel_type'])); ?></strong></li>
            <li><span>Category</span><strong><?php echo e(hotel_category_label($hotel['category'])); ?></strong></li>
            <?php if ($hotelStays): ?>
              <li><span>Stay Styles</span><strong><?php echo e(implode(', ', $hotelStays)); ?></strong></li>
            <?php endif; ?>
            <?php if (!empty($hotel['address'])): ?>
              <li><span>Address</span><strong><?php echo e($hotel['address']); ?></strong></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>

      <!-- Content & booking trigger -->
      <div>
        <div class="sub-tag"><?php echo e($hotel['location_type'] === 'DOMESTIC' ? 'India' : 'International'); ?> Network</div>
        <h1 class="heading-serif" style="font-size: clamp(1.9rem, 3.2vw, 2.6rem); margin-bottom: 14px;"><?php echo e($hotel['name']); ?></h1>
        <div class="card-location" style="font-size: 1rem; margin-bottom: 22px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
          <span><?php echo e($hotel['location_name']); ?>, <?php echo e($hotel['country']); ?></span>
        </div>

        <?php foreach ($hotelParas as $para): ?>
          <p style="font-size: 1.02rem; color: var(--color-text-muted); line-height: 1.8; margin-bottom: 16px;"><?php echo e($para); ?></p>
        <?php endforeach; ?>

        <div class="hotel-reserve-box">
          <h2 style="font-size: 1.25rem; margin-bottom: 8px;">Reserve a Stay at <?php echo e($hotel['name']); ?></h2>
          <p style="font-size: 0.92rem; color: var(--color-text-muted); margin-bottom: 18px;">
            Submit a request and your lifestyle concierge will confirm availability.
          </p>
          <a href="<?php echo url('booking-request.php?resort=' . urlencode($hotel['slug'])); ?>" class="btn btn-gold btn-lg" style="width: 100%; justify-content: center;">
            Request Booking for This Hotel
          </a>
        </div>
      </div>
    </div>

    <?php if ($hotelMap !== ''): ?>
      <div style="margin-top: 48px;">
        <h2 class="heading-serif" style="font-size: 1.5rem; margin-bottom: 16px;">Location</h2>
        <iframe class="hotel-map" src="<?php echo e($hotelMap); ?>" title="Map of <?php echo e($hotel['name']); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
      </div>
    <?php endif; ?>

    <?php if ($hotelRelated): ?>
      <div style="margin-top: 56px;">
        <h2 class="heading-serif" style="font-size: 1.6rem; margin-bottom: 22px;">More in <?php echo e($hotel['location_name']); ?></h2>
        <div class="grid-3 hotel-grid">
          <?php $mainHotel = $hotel; foreach ($hotelRelated as $hotel): ?>
            <?php include __DIR__ . '/hotel-card.php'; ?>
          <?php endforeach; $hotel = $mainHotel; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<script>
document.querySelectorAll('.hotel-thumb').forEach(function (btn) {
  btn.addEventListener('click', function () {
    var main = document.getElementById('hotelMainImage');
    if (main) { main.src = btn.getAttribute('data-src'); }
    document.querySelectorAll('.hotel-thumb').forEach(function (b) { b.classList.remove('active'); });
    btn.classList.add('active');
  });
});
</script>
