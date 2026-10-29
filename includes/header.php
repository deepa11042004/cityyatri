<?php
/**
 * Club Yatri - Global Head & Header Partial
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <?php include __DIR__ . '/seo.php'; ?>

  <!-- Official Favicon -->
  <link rel="icon" type="image/png" href="<?php echo e(BASE_PATH); ?>/assets/images/logo/club-yatri-logo.png">

  <!-- Core Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <script>document.documentElement.classList.add('js');</script>

  <!-- Stylesheets -->
  <link rel="stylesheet" href="<?php echo e(ASSETS_CSS_PATH); ?>">
  <?php if (file_exists(__DIR__ . '/../assets/css/custom.css')): ?>
  <link rel="stylesheet" href="<?php echo e(ASSETS_CUSTOM_CSS_PATH); ?>">
  <?php endif; ?>
</head>
<body>
<?php
// Display flash messages if any
$flash = get_flash_message();
if ($flash): ?>
  <div style="background: <?php echo $flash['type'] === 'success' ? 'rgba(0, 201, 167, 0.9)' : 'rgba(239, 68, 68, 0.9)'; ?>; color: #fff; text-align: center; padding: 12px 20px; font-weight: 600; font-size: 0.92rem; position: fixed; top: 0; left: 0; right: 0; z-index: 99999;">
    <?php echo e($flash['message']); ?>
  </div>
<?php endif; ?>
