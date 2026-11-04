<?php
/**
 * Club Yatri - Contact & VIP Concierge Desk
 */

declare(strict_types=1);

$pageTitle = 'Contact Concierge & Corporate Offices';
$pageDescription = 'Connect with Club Yatri 24/7 Lifestyle Concierge desk. Call toll-free, schedule an executive consultation, or visit our regional offices.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$breadcrumbTitle = 'Contact Us';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section about-hero" style="padding-top: 60px; padding-bottom: 40px; text-align: center;">
  <?php include __DIR__ . '/includes/leaves.php'; ?>
  <div class="container" style="position: relative; z-index: 1;">
    <div class="sub-tag">24/7 VIP Hospitality Services</div>
    <h1 class="heading-serif" style="font-size: clamp(2.2rem, 4vw, 3.6rem); margin-bottom: 16px;">
      Connect with Your <span class="gold-text">Lifestyle Concierge</span>
    </h1>
    <p style="color: var(--text-secondary); max-width: 700px; margin: 0 auto; font-size: 1.1rem;">
      Whether you wish to schedule a confidential holiday pass consultation, inquire about an upcoming holiday week, or explore corporate vacation suites, our team is at your disposal.
    </p>
  </div>
</section>

<!-- Intro -->
<section class="section" style="padding-top: 50px; padding-bottom: 10px; text-align: center; border-bottom: 0;">
  <div class="container" style="max-width: 860px;">
    <h2 class="heading-serif" style="font-size: clamp(1.7rem, 3vw, 2.4rem); margin-bottom: 16px;">
      We're Here Before, During and <span style="color: var(--color-primary);">After Your Stay</span>.
    </h2>
    <p style="color: var(--text-secondary); font-size: 1.08rem; line-height: 1.75; margin-bottom: 14px;">
      Planning a first holiday, choosing the right pass, or need help with a booking that is already confirmed? A dedicated member of the Club Yatri concierge team will listen, guide you and follow through until everything is sorted.
    </p>
    <p style="color: var(--text-secondary); font-size: 1.08rem; line-height: 1.75;">
      Call our toll-free line, or send us a message using the form below, and we will get back to you as soon as we can.
    </p>
  </div>
</section>

<section class="section" style="padding-top: 10px; padding-bottom: 80px;">
  <div class="container">
    <div class="grid-2" style="gap: 48px;">
      
      <!-- Details Card -->
      <div style="display: flex; flex-direction: column; gap: 24px;">
        <div class="card-luxury" style="padding: 32px;">
          <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 12px;">
            <div style="width: 44px; height: 44px; border-radius: var(--radius-sm); background: var(--gold-gradient-soft); border: 1px solid var(--border-gold); display: flex; align-items: center; justify-content: center; color: var(--gold-primary);">
              📞
            </div>
            <div>
              <h3 style="font-size: 1.2rem; color: var(--color-text);">Direct Concierge Hotlines</h3>
              <div style="color: var(--gold-light); font-weight: 700;"><?php echo e(CONTACT_PHONE_TOLL_FREE); ?> (Toll-Free India)</div>
            </div>
          </div>
          <p style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.6;">
            Operating 24 hours a day, 7 days a week for active members and prospective voyagers.
          </p>
        </div>

        <div class="card-luxury" style="padding: 32px;">
          <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 12px;">
            <div style="width: 44px; height: 44px; border-radius: var(--radius-sm); background: var(--gold-gradient-soft); border: 1px solid var(--border-gold); display: flex; align-items: center; justify-content: center; color: var(--gold-primary);">
              ✉️
            </div>
            <div>
              <h3 style="font-size: 1.2rem; color: var(--color-text);">Confidential Electronic Correspondence</h3>
              <div style="color: var(--gold-light); font-weight: 700;"><?php echo e(CONTACT_EMAIL); ?></div>
            </div>
          </div>
          <p style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.6;">
            Guaranteed response time within 60 minutes during standard business hours.
          </p>
        </div>

        <div class="card-luxury" style="padding: 32px;">
          <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 12px;">
            <div style="width: 44px; height: 44px; border-radius: var(--radius-sm); background: var(--gold-gradient-soft); border: 1px solid var(--border-gold); display: flex; align-items: center; justify-content: center; color: var(--gold-primary);">
              🏛️
            </div>
            <div>
              <h3 style="font-size: 1.2rem; color: var(--color-text);">Executive Corporate Offices</h3>
              <div style="color: var(--text-primary); font-size: 0.95rem;"><?php echo e(CONTACT_ADDRESS); ?></div>
            </div>
          </div>
          <p style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.6;">
            Regional Member Lounges: New Delhi (Connaught Place), Bengaluru (UB City), Dubai (DIFC Gate Precinct).
          </p>
        </div>
      </div>

      <!-- Interactive Form -->
      <div style="background: var(--bg-card); border: 1.5px solid var(--border-gold); border-radius: var(--radius-xl); padding: 40px; box-shadow: var(--shadow-lg);">
        <div class="sub-tag" style="margin-bottom: 12px;">Direct Inquiry</div>
        <h2 class="heading-serif" style="font-size: 1.8rem; margin-bottom: 24px; color: var(--color-text);">Schedule a Private Consultation</h2>
        
        <form action="<?php echo url('actions/submit-callback.php'); ?>" method="POST">
          <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
              <label>Your Name *</label>
              <input type="text" name="full_name" class="form-control" required placeholder="Full Name">
            </div>
            <div class="form-group">
              <label>Phone Number *</label>
              <input type="tel" name="phone" class="form-control" required placeholder="+91 98765 43210">
            </div>
          </div>

          <div class="form-group">
            <label>Corporate / Personal Email *</label>
            <input type="email" name="email" class="form-control" required placeholder="name@email.com">
          </div>

          <div class="form-group">
            <label>Preferred Time for Call</label>
            <select name="preferred_time" class="form-control">
              <option value="Morning">Morning (10 AM - 1 PM)</option>
              <option value="Afternoon">Afternoon (2 PM - 5 PM)</option>
              <option value="Evening">Evening (6 PM - 9 PM)</option>
            </select>
          </div>

          <div class="form-group">
            <label>Message / Specific Questions</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Tell us about your upcoming travel plans or holiday pass interest..."></textarea>
          </div>

          <button type="submit" class="btn btn-gold btn-lg" style="width: 100%;">
            Send Priority Request
          </button>
        </form>
      </div>

    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
