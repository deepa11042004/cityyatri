<?php
/**
 * Club Yatri - Frequently Asked Questions
 */

declare(strict_types=1);

$pageTitle = 'Frequently Asked Questions & Ownership Guide';
$pageDescription = 'Everything you need to know about Club Yatri vacation passes, seasonal studio tiers, points banking, booking policies, and global exchange.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/data/faqs.php';

$breadcrumbTitle = 'FAQs';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 60px; padding-bottom: 40px; text-align: center;">
  <div class="container">
    <div class="sub-tag">Knowledge Base & Guidelines</div>
    <h1 class="heading-serif" style="font-size: clamp(2.2rem, 4vw, 3.6rem); margin-bottom: 16px;">
      Frequently Asked <span class="gold-text">Questions</span>
    </h1>
    <p style="color: var(--text-secondary); max-width: 740px; margin: 0 auto; font-size: 1.1rem;">
      Clear, transparent answers regarding Club Yatri vacation ownership passes, points accumulation, seasonal categories, and exchange bylaws.
    </p>
  </div>
</section>

<section class="section" style="padding-top: 10px; padding-bottom: 80px;">
  <div class="container">
    <div class="faq-list">
      <?php foreach ($faqsList as $idx => $faq): ?>
        <?php 
          $isOpen = ($idx === 0);
          include __DIR__ . '/includes/faq-item.php'; 
        ?>
      <?php endforeach; ?>
    </div>

    <div style="background: rgba(22, 38, 60, 0.6); border: 1.5px solid var(--border-gold); border-radius: var(--radius-xl); padding: 40px; max-width: 860px; margin: 60px auto 0 auto; text-align: center;">
      <h3 class="heading-serif" style="font-size: 1.6rem; color: #fff; margin-bottom: 12px;">Have Further Inquiries?</h3>
      <p style="color: var(--text-secondary); margin-bottom: 24px; font-size: 0.95rem;">
        Our Senior Lifestyle Advisors are available 24/7 to answer custom portfolio questions.
      </p>
      <div style="display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
        <a href="<?php echo url('callback-request.php'); ?>" class="btn btn-gold">Schedule Concierge Callback</a>
        <a href="tel:<?php echo e(CONTACT_PHONE_TEL); ?>" class="btn btn-outline-gold">Call <?php echo e(CONTACT_PHONE_TOLL_FREE); ?></a>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
