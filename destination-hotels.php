<?php
/**
 * Club Yatri - HTML fragment: the hotels of one destination (used by the destination map).
 *   GET /destination-hotels.php?slug=jaipur
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/destinations-db.php';

header('Content-Type: text/html; charset=utf-8');

$dest = destination_find(trim((string)($_GET['slug'] ?? '')));
if ($dest === null) {
    http_response_code(404);
    echo '<p class="dest-result-empty">Destination not found.</p>';
    exit;
}

$total = (int)$dest['hotels_count'];
$hotelsList = hotels_search(['q' => '', 'type' => '', 'category' => '', 'location' => $dest['name'], 'page' => 1], 6, 0);
$guideUrl = url('destination-details.php?slug=' . urlencode($dest['slug']));
$allUrl = url('resort-network.php?location=' . urlencode($dest['name']) . '#network-hotels');
?>
<div class="dest-result-head">
  <div>
    <div class="sub-tag"><?php echo e($dest['country']); ?></div>
    <h3 class="dest-result-title">Hotels in <span><?php echo e($dest['name']); ?></span></h3>
    <p class="dest-result-sub"><?php echo e(hotel_excerpt($dest['description'] ?? '', 200)); ?></p>
  </div>
  <div class="dest-result-actions">
    <a href="<?php echo e($guideUrl); ?>" class="btn btn-gold">Destination Guide</a>
    <?php if ($total > 0): ?>
      <a href="<?php echo e($allUrl); ?>" class="btn btn-outline-gold">All <?php echo number_format($total); ?> Hotels</a>
    <?php endif; ?>
  </div>
</div>

<?php if ($hotelsList): ?>
  <div class="grid-3 hotel-grid">
    <?php foreach ($hotelsList as $hotel): ?>
      <?php include __DIR__ . '/includes/hotel-card.php'; ?>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <p class="dest-result-empty">No hotels are listed for this destination yet.</p>
<?php endif; ?>
