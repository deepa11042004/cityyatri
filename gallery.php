<?php
/**
 * Club Yatri - Visual Resort Gallery
 */

declare(strict_types=1);

$pageTitle = 'Luxury Resort & Estate Photo Gallery';
$pageDescription = 'Browse the visual splendor of Club Yatri: private beach cabanas, lakeside havelis, Himalayan snow chalets, and overwater atolls.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$breadcrumbTitle = 'Gallery';

$galleryItems = [
    [
        'url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
        'caption' => 'The Azure Sands Villa Plunge Pool',
        'location' => 'Varca Beach, South Goa',
        'badge' => 'Beachfront'
    ],
    [
        'url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
        'caption' => 'Udaivilas Haveli Courtyards at Twilight',
        'location' => 'Lake Pichola, Udaipur',
        'badge' => 'Heritage'
    ],
    [
        'url' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=800&q=80',
        'caption' => 'Solang Valley Timber Chalet in Winter',
        'location' => 'Manali, Himachal Pradesh',
        'badge' => 'Himalayan'
    ],
    [
        'url' => 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=800&q=80',
        'caption' => 'Overwater Villa Sunset Pier',
        'location' => 'Baa Atoll, Maldives',
        'badge' => 'Global Exchange'
    ],
    [
        'url' => 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?auto=format&fit=crop&w=800&q=80',
        'caption' => 'Ayurvedic Herb Garden & Palm Cabanas',
        'location' => 'Mararikulam, Kerala',
        'badge' => 'Wellness'
    ],
    [
        'url' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=800&q=80',
        'caption' => 'Palm Jumeirah Infinity Sky Deck',
        'location' => 'Dubai, UAE',
        'badge' => 'Urban Penthouse'
    ],
    [
        'url' => 'https://images.unsplash.com/photo-1599661046827-dacff0c0f09a?auto=format&fit=crop&w=800&q=80',
        'caption' => 'Amer Fort Royal Polo Pavilions',
        'location' => 'Jaipur, Rajasthan',
        'badge' => 'Heritage'
    ],
    [
        'url' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80',
        'caption' => 'Ayung River Gorge Rainforest Villa',
        'location' => 'Ubud, Bali',
        'badge' => 'Rainforest'
    ],
    [
        'url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
        'caption' => 'Private Catamaran Sunset Cruise',
        'location' => 'Arabian Sea, South Goa',
        'badge' => 'Expedition'
    ]
];

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 60px; padding-bottom: 40px; text-align: center;">
  <div class="container">
    <div class="sub-tag">Visual Splendor</div>
    <h1 class="heading-serif" style="font-size: clamp(2.2rem, 4vw, 3.6rem); margin-bottom: 16px;">
      Resort & Sanctuary <span class="gold-text">Photo Gallery</span>
    </h1>
    <p style="color: var(--text-secondary); max-width: 720px; margin: 0 auto; font-size: 1.1rem;">
      Experience the architectural elegance, tranquil waters, and majestic landscapes that define the Club Yatri vacation experience.
    </p>
  </div>
</section>

<section class="section" style="padding-top: 20px; padding-bottom: 80px;">
  <div class="container">
    <div class="grid-3">
      <?php foreach ($galleryItems as $item): ?>
        <?php include __DIR__ . '/includes/gallery-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
