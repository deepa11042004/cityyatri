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
  <link rel="icon" href="<?php echo e(site_favicon_url()); ?>">

  <!-- Core Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&display=swap" rel="stylesheet">

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
  <?php $flashOk = $flash['type'] === 'success'; ?>
  <div id="flashPopup" role="alertdialog" aria-modal="true" aria-labelledby="flashPopupMsg" style="position:fixed;inset:0;z-index:99999;display:flex;align-items:center;justify-content:center;padding:20px;background:rgba(20,16,12,.55);">
    <div style="background:#fff;border-radius:24px;max-width:440px;width:100%;padding:36px 30px 28px;text-align:center;box-shadow:0 30px 80px rgba(0,0,0,.35);">
      <div style="width:68px;height:68px;margin:0 auto 18px;border-radius:50%;display:grid;place-items:center;font-size:2rem;font-weight:700;color:#fff;background:<?php echo $flashOk ? '#00A98B' : '#EF4444'; ?>;"><?php echo $flashOk ? '&#10003;' : '!'; ?></div>
      <h3 style="margin:0 0 10px;font-size:1.35rem;color:#1a1a1a;"><?php echo $flashOk ? 'Message sent' : 'Something went wrong'; ?></h3>
      <p id="flashPopupMsg" style="margin:0 0 22px;color:#555;line-height:1.55;"><?php echo e($flash['message']); ?></p>
      <button type="button" id="flashPopupClose" style="border:0;border-radius:999px;padding:11px 34px;font-weight:700;background:#FFB400;color:#1a1a1a;cursor:pointer;">OK</button>
    </div>
  </div>
  <script>
    (function () {
      var p = document.getElementById('flashPopup');
      function close() { if (p) { p.remove(); } }
      document.getElementById('flashPopupClose').addEventListener('click', close);
      p.addEventListener('click', function (e) { if (e.target === p) close(); });
      document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
      document.getElementById('flashPopupClose').focus();
    })();
  </script>
<?php endif; ?>
