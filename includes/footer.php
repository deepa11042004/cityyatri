<?php
/**
 * Club Yatri - Global Luxury Footer Partial
 */

declare(strict_types=1);

global $socialLinks;
?>
  <!-- Global Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-top">
        <div class="footer-brand">
          <div class="brand-logo">
            <img src="<?php echo e(BASE_PATH); ?>/assets/images/logo/club-yatri-logo.png" alt="Club Yatri Official Logo" class="brand-logo-img">
            <div class="brand-name">CLUB <span>YATRI</span></div>
          </div>
          <p>
            The hallmark of luxury vacation ownership in India. Delivering 25 years of guaranteed 5-star holidays, bespoke lifestyle concierge, and global exchange privileges with <?php echo e(LEGAL_ENTITY_NAME); ?>.
          </p>
          <div style="color: var(--color-ink); font-weight: 700; font-size: 0.92rem; margin-bottom: 8px;">
            Toll-Free Concierge: <a href="tel:<?php echo e(CONTACT_PHONE_TEL); ?>" style="color: var(--color-primary-dark);"><?php echo e(CONTACT_PHONE_TOLL_FREE); ?></a>
          </div>
          <div style="color: var(--color-ink-soft); font-size: 0.82rem; line-height: 1.5;">
            Corporate Office: <?php echo e(CONTACT_ADDRESS); ?>
          </div>
        </div>

        <div>
          <h4 class="footer-heading">Explore Portfolio</h4>
          <ul class="footer-links">
            <li><a href="<?php echo url('index.php'); ?>" class="footer-link">Home</a></li>
            <li><a href="<?php echo url('passes-subscriptions.php'); ?>" class="footer-link">Passes &amp; Subscriptions</a></li>
            <li><a href="<?php echo url('resort-network.php'); ?>" class="footer-link">Resort Network Map</a></li>
            <li><a href="<?php echo url('hotels-national.php'); ?>" class="footer-link">National Resorts</a></li>
            <li><a href="<?php echo url('hotels-international.php'); ?>" class="footer-link">International Escapes</a></li>
            <li><a href="<?php echo url('rooms.php'); ?>" class="footer-link">Rooms &amp; Suites</a></li>
            <li><a href="<?php echo url('gallery.php'); ?>" class="footer-link">Photo Gallery</a></li>
          </ul>
        </div>

        <div>
          <h4 class="footer-heading">Subscriber Privileges</h4>
          <ul class="footer-links">
            <li><a href="<?php echo url('login.php'); ?>" class="footer-link">Subscriber Portal Login</a></li>
            <li><a href="<?php echo url('booking-request.php'); ?>" class="footer-link">Vacation Booking Request</a></li>
            <li><a href="<?php echo url('callback-request.php'); ?>" class="footer-link">Schedule VIP Callback</a></li>
            <li><a href="<?php echo url('resort-network.php'); ?>" class="footer-link">Global Exchange Network</a></li>
            <li><a href="<?php echo url('faq.php'); ?>" class="footer-link">Frequently Asked Questions</a></li>
            <li><a href="<?php echo url('about.php'); ?>" class="footer-link">About Our Company</a></li>
            <li><a href="<?php echo url('contact.php'); ?>" class="footer-link">Contact Concierge</a></li>
          </ul>
        </div>

        <div class="footer-newsletter">
          <h4 class="footer-heading">The Yatri Journal</h4>
          <p>Subscribe for privileged seasonal resort allocations, private previews, and luxury travel itineraries.</p>
          <form class="newsletter-form" id="newsletterForm" action="<?php echo url('actions/submit-inquiry.php'); ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="inquiry_type" value="newsletter">
            <input type="email" name="email" class="newsletter-input" placeholder="Your executive email address" required>
            <button type="submit" class="btn btn-gold btn-sm">Subscribe</button>
          </form>
          <div style="margin-top: 14px; font-size: 0.75rem; color: var(--color-ink-soft);">
            We respect your privacy. Strictly zero spam.
          </div>
          <div style="margin-top: 18px; display: flex; gap: 14px;">
            <a href="<?php echo e($socialLinks['facebook']); ?>" target="_blank" rel="noopener" style="color: var(--color-ink-soft); font-size: 0.85rem;">Facebook</a>
            <a href="<?php echo e($socialLinks['instagram']); ?>" target="_blank" rel="noopener" style="color: var(--color-ink-soft); font-size: 0.85rem;">Instagram</a>
            <a href="<?php echo e($socialLinks['linkedin']); ?>" target="_blank" rel="noopener" style="color: var(--color-ink-soft); font-size: 0.85rem;">LinkedIn</a>
            <a href="<?php echo e($socialLinks['youtube']); ?>" target="_blank" rel="noopener" style="color: var(--color-ink-soft); font-size: 0.85rem;">YouTube</a>
          </div>
        </div>
      </div>

      <div class="footer-bottom">
        <div>
          © <?php echo date('Y'); ?> <?php echo e(LEGAL_ENTITY_NAME); ?>. All Rights Reserved.
        </div>
        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
          <a href="<?php echo url('privacy-policy.php'); ?>" style="color: var(--color-ink-soft); font-size: 0.82rem;">Privacy Policy</a>
          <a href="<?php echo url('terms-and-conditions.php'); ?>" style="color: var(--color-ink-soft); font-size: 0.82rem;">Terms & Conditions</a>
          <a href="<?php echo url('subscription-terms.php'); ?>" style="color: var(--color-ink-soft); font-size: 0.82rem;">Subscription Terms</a>
          <a href="<?php echo url('booking-policy.php'); ?>" style="color: var(--color-ink-soft); font-size: 0.82rem;">Booking Policy</a>
          <a href="<?php echo url('cancellation-refund-policy.php'); ?>" style="color: var(--color-ink-soft); font-size: 0.82rem;">Cancellation & Refunds</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Reusable Lead Capture / Booking Modal -->
  <div class="modal-backdrop" id="inquiryModal">
    <div class="modal-dialog">
      <button class="modal-close-btn" data-close-modal aria-label="Close modal">✕</button>
      <div style="text-align: center; margin-bottom: 24px;">
        <div class="sub-tag">Priority Concierge Desk</div>
        <h3 class="heading-serif" style="font-size: 1.8rem; color: var(--color-text); margin-bottom: 8px;">Request Pass Allocation</h3>
        <p style="font-size: 0.92rem; color: var(--color-text-muted);">
          Our Senior Hospitality Consultant will arrange a private consultation and walk you through bespoke vacation passes.
        </p>
      </div>

      <form id="clubInquiryForm" action="<?php echo url('actions/submit-inquiry.php'); ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <div class="form-group">
            <label for="modalFullName">Full Name *</label>
            <input type="text" id="modalFullName" name="full_name" class="form-control" placeholder="e.g. Rahul Sharma" required>
          </div>
          <div class="form-group">
            <label for="modalPhone">Phone Number *</label>
            <input type="tel" id="modalPhone" name="phone" class="form-control" placeholder="e.g. +91 98765 43210" required>
          </div>
        </div>

        <div class="form-group">
          <label for="modalEmail">Email Address *</label>
          <input type="email" id="modalEmail" name="email" class="form-control" placeholder="e.g. rahul@company.com" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <div class="form-group">
            <label for="modalCity">City of Residence</label>
            <input type="text" id="modalCity" name="city" class="form-control" placeholder="e.g. Mumbai, Delhi">
          </div>
          <div class="form-group">
            <label for="modalTierInterest">Interested Holiday Pass</label>
            <select id="modalTierInterest" name="pass_tier" class="form-control">
              <option value="10-Year — Yatri Imperial World Pass">10-Year — Yatri Imperial World Pass (Most Popular)</option>
              <option value="5-Year — Yatri Asian Signature Pass">5-Year — Yatri Asian Signature Pass</option>
              <option value="3-Year — Yatri Royal Bharat Pass">3-Year — Yatri Royal Bharat Pass</option>
              <option value="25-Year — Yatri Privilege Crown Asset">25-Year — Yatri Privilege Crown Asset</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="modalNotes">Travel Preferences / Queries</label>
          <textarea id="modalNotes" name="notes" class="form-control" rows="2" placeholder="Preferred vacation destinations, upcoming holiday plans..."></textarea>
        </div>

        <button type="submit" class="btn-red btn-lg" style="width: 100%;">
          Submit Inquiry &amp; Request Dossier
        </button>
      </form>
    </div>
  </div>

  <?php include __DIR__ . '/activity-popup.php'; ?>

  <!-- JavaScript Assets -->
  <script src="<?php echo e(ASSETS_JS_PATH); ?>"></script>
  <?php if (file_exists(__DIR__ . '/../assets/js/custom.js')): ?>
  <script src="<?php echo e(ASSETS_CUSTOM_JS_PATH); ?>"></script>
  <?php endif; ?>
</body>
</html>
