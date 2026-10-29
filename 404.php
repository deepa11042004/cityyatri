<?php
/**
 * Club Yatri - 404 Page Not Found
 */

declare(strict_types=1);

http_response_code(404);

$pageTitle = 'Page Not Found (404)';
$pageDescription = 'The requested destination or page could not be located in the Club Yatri portfolio.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$breadcrumbTitle = '404 Not Found';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 80px; padding-bottom: 100px; text-align: center;">
  <div class="container" style="max-width: 680px;">
    
    <div style="font-family: var(--font-serif-display); font-size: clamp(4rem, 8vw, 7rem); font-weight: 800; color: var(--gold-light); line-height: 1; margin-bottom: 20px;">
      404
    </div>

    <div class="sub-tag" style="margin-bottom: 16px;">Destination Uncharted</div>
    <h1 class="heading-serif" style="font-size: 2.2rem; color: #fff; margin-bottom: 16px;">
      The Requested Sanctuary Does Not Exist
    </h1>
    <p style="color: var(--text-secondary); line-height: 1.7; font-size: 1.05rem; margin-bottom: 36px;">
      The path you followed may have been updated, relocated, or temporarily reserved. Allow us to redirect you to our premier resort collections.
    </p>

    <div style="display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
      <a href="<?php echo url('index.php'); ?>" class="btn btn-gold btn-lg">Return to Home</a>
      <a href="<?php echo url('destinations.php'); ?>" class="btn btn-outline-gold btn-lg">Browse Destinations</a>
      <a href="<?php echo url('contact.php'); ?>" class="btn btn-ghost btn-lg">Concierge Help</a>
    </div>

  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
