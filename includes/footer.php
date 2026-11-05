<?php
/**
 * Club Yatri - Global Luxury Footer Partial
 */

declare(strict_types=1);

require_once __DIR__ . '/leads.php';

global $socialLinks;
?>
  <!-- Global Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-top">
        <div class="footer-brand">
          <div class="brand-logo">
            <img src="<?php echo e(site_logo_url()); ?>" alt="Club Yatri Official Logo" class="brand-logo-img">
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
            <?php echo lead_honeypot(); ?>
            <input type="hidden" name="inquiry_type" value="newsletter">
            <input type="email" name="email" class="newsletter-input" placeholder="Your executive email address" required>
            <button type="submit" class="btn btn-gold btn-sm">Subscribe</button>
          </form>
          <div style="margin-top: 14px; font-size: 0.75rem; color: var(--color-ink-soft);">
            We respect your privacy. Strictly zero spam.
          </div>
          <div class="footer-social">
            <a href="<?php echo e($socialLinks['facebook']); ?>" target="_blank" rel="noopener" aria-label="Facebook" title="Facebook"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-7.5h2.6l.4-3h-3V8.6c0-.9.3-1.5 1.5-1.5h1.6V4.4c-.3 0-1.2-.1-2.3-.1-2.3 0-3.8 1.4-3.8 3.9v2.3H7.9v3h2.6V21h3z"/></svg></a>
            <a href="<?php echo e($socialLinks['instagram']); ?>" target="_blank" rel="noopener" aria-label="Instagram" title="Instagram"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><rect x="3.5" y="3.5" width="17" height="17" rx="5" fill="none" stroke="currentColor" stroke-width="1.9"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="1.9"/><circle cx="17.2" cy="6.8" r="1.2"/></svg></a>
            <a href="<?php echo e($socialLinks['linkedin']); ?>" target="_blank" rel="noopener" aria-label="LinkedIn" title="LinkedIn"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M5.1 9.2h3.1V19H5.1V9.2zM6.7 4.5a1.8 1.8 0 1 1 0 3.6 1.8 1.8 0 0 1 0-3.6zM10.2 9.2h3v1.3c.5-.9 1.6-1.6 3.1-1.6 3 0 3.7 2 3.7 4.6V19h-3.1v-4.7c0-1.1 0-2.5-1.5-2.5s-1.8 1.2-1.8 2.4V19h-3.1V9.2z"/></svg></a>
            <a href="<?php echo e($socialLinks['youtube']); ?>" target="_blank" rel="noopener" aria-label="YouTube" title="YouTube"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2C2 8.8 2 12 2 12s0 3.2.4 4.8a2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8c.4-1.6.4-4.8.4-4.8s0-3.2-.4-4.8zM10 15V9l5.2 3L10 15z"/></svg></a>
            <?php if (!empty($socialLinks['twitter'])): ?><a href="<?php echo e($socialLinks['twitter']); ?>" target="_blank" rel="noopener" aria-label="X (Twitter)" title="X (Twitter)"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M17.5 3h3.1l-6.8 7.7L21.8 21h-6.3l-4.9-6.4L5 21H1.9l7.2-8.3L1.5 3h6.4l4.4 5.9L17.5 3zm-1.1 16.2h1.7L6.9 4.7H5.1l11.3 14.5z"/></svg></a><?php endif; ?>
          </div>
        </div>
      </div>

      <div class="footer-disclaimer" role="note">
        <strong>Disclaimer:</strong>
        The hotels, resorts, villas, treehouses, houseboats, domes, and tents displayed on Club Yatri are curated from our directly managed properties, partner resorts, and verified network stays. All property images, logos, and descriptions are used for representation and informational purposes to help users explore and book their ideal stay. Club Yatri acts as a booking and hospitality network platform, and respective property rules, pricing, and policies apply at the time of check-in.
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
        <input type="hidden" name="inquiry_type" value="enquiry_popup">
        <?php echo lead_honeypot(); ?>
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
              <?php
              require_once __DIR__ . '/content-db.php';
              $footerPlans = content_has_rows('passes') ? content_pass_plans() : [
                  ['title' => '10-Year — Yatri Imperial World Pass', 'featured' => true], ['title' => '5-Year — Yatri Asian Signature Pass', 'featured' => false],
                  ['title' => '3-Year — Yatri Royal Bharat Pass', 'featured' => false], ['title' => '25-Year — Yatri Privilege Crown Asset', 'featured' => false],
              ];
              usort($footerPlans, static fn($x, $y) => (int)$y['featured'] <=> (int)$x['featured']);   // the popular one first
              foreach ($footerPlans as $fp): ?>
                <option value="<?php echo e($fp['title']); ?>"><?php echo e($fp['title']); ?><?php echo !empty($fp['featured']) ? ' (Most Popular)' : ''; ?></option>
              <?php endforeach; ?>
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
