<?php
/**
 * Club Yatri - Priority VIP Callback Request
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'Schedule a Priority VIP Callback';
$pageDescription = 'Request a direct telephone consultation with a Senior Hospitality Director from Club Yatri Hotel and Resort Pvt Ltd.';

$breadcrumbTitle = 'VIP Callback Request';
$parentTitle = 'Contact';
$parentUrl = url('contact.php');

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 60px; padding-bottom: 80px;">
  <div class="container" style="max-width: 680px;">
    
    <div style="background: var(--bg-card); border: 1.5px solid var(--border-gold); border-radius: var(--radius-xl); padding: 48px; box-shadow: var(--shadow-lg), var(--shadow-gold);">
      <div style="text-align: center; margin-bottom: 32px;">
        <div class="sub-tag" style="margin-bottom: 12px;">Executive Relations</div>
        <h1 class="heading-serif" style="font-size: 2.2rem; color: #fff; margin-bottom: 10px;">Schedule VIP Consultation</h1>
        <p style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.6;">
          Our Senior Hospitality Director will call you at your preferred time to provide personal guidance on vacation passes, points valuation, and seasonal room entitlements.
        </p>
      </div>

      <form action="<?php echo url('actions/submit-callback.php'); ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

        <div class="form-group">
          <label>Your Full Name *</label>
          <input type="text" name="full_name" class="form-control" required placeholder="e.g. Sonal Singhania">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <div class="form-group">
            <label>Direct Telephone Number *</label>
            <input type="tel" name="phone" class="form-control" required placeholder="+91 98765 43210">
          </div>
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="sonal@email.com">
          </div>
        </div>

        <div class="form-group">
          <label>Preferred Consultation Time Window</label>
          <select name="preferred_time" class="form-control">
            <option value="Morning">Morning (10:00 AM – 01:00 PM IST)</option>
            <option value="Afternoon" selected>Afternoon (02:00 PM – 05:00 PM IST)</option>
            <option value="Evening">Evening (06:00 PM – 09:00 PM IST)</option>
            <option value="Immediate">Immediate Priority (Within 30 Minutes)</option>
          </select>
        </div>

        <div class="form-group">
          <label>Discussion Topics or Specific Resort Inquiries</label>
          <textarea name="notes" class="form-control" rows="3" placeholder="Tell us which pass or holiday destinations interest you most..."></textarea>
        </div>

        <button type="submit" class="btn btn-gold btn-lg" style="width: 100%;">
          Confirm Consultation Appointment
        </button>
      </form>

      <div style="margin-top: 24px; text-align: center; font-size: 0.85rem; color: var(--text-muted);">
        Prefer immediate assistance? Call our toll-free concierge directly at 
        <a href="tel:<?php echo e(CONTACT_PHONE_TEL); ?>" style="color: var(--gold-light); font-weight: 700;"><?php echo e(CONTACT_PHONE_TOLL_FREE); ?></a>
      </div>
    </div>

  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
