<?php
/**
 * Club Yatri - Reusable FAQ Accordion Item Component
 * Expects $faq (array with question, answer) and optional $isOpen (bool)
 */

declare(strict_types=1);

if (!isset($faq) || !is_array($faq)) {
    return;
}
$openClass = !empty($isOpen) ? 'open' : '';
?>
<div class="faq-card <?php echo $openClass; ?>">
  <div class="faq-header">
    <h3 class="faq-question"><?php echo e($faq['question']); ?></h3>
    <span class="faq-toggle-icon">▼</span>
  </div>
  <div class="faq-body">
    <p class="faq-answer">
      <?php echo e($faq['answer']); ?>
    </p>
  </div>
</div>
