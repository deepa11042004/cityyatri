<?php
/**
 * Club Yatri - Two-Level Header (Top Utility Bar + Main Navigation Bar)
 * Level 1: Compact Red Utility Bar with Phone, Email, Socials & Subscriber Login
 * Level 2: Main White Navigation Bar with Official Logo (Only), Menu & CTA
 */

declare(strict_types=1);

global $socialLinks;
$isLoggedIn = !empty($_SESSION['member_authenticated']);
?>

<!-- Level 1: Top Utility Bar -->
<div class="top-utility-bar" id="topUtilityBar">
  <div class="container">
    <div class="utility-left">
      <?php if (defined('CONTACT_PHONE_TOLL_FREE') && CONTACT_PHONE_TOLL_FREE): ?>
        <a href="tel:<?php echo e(CONTACT_PHONE_TEL); ?>" class="utility-contact-link" aria-label="Call Toll-Free Concierge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
          </svg>
          <span>Concierge: <?php echo e(CONTACT_PHONE_TOLL_FREE); ?></span>
        </a>
      <?php endif; ?>

      <?php if (defined('CONTACT_EMAIL') && CONTACT_EMAIL): ?>
        <a href="mailto:<?php echo e(CONTACT_EMAIL); ?>" class="utility-contact-link" aria-label="Email Club Yatri">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
            <polyline points="22,6 12,13 2,6"></polyline>
          </svg>
          <span><?php echo e(CONTACT_EMAIL); ?></span>
        </a>
      <?php endif; ?>
    </div>

    <div class="utility-right">
      <div class="utility-social-links">
        <?php if (!empty($socialLinks['instagram'])): ?>
          <a href="<?php echo e($socialLinks['instagram']); ?>" target="_blank" rel="noopener noreferrer" class="utility-social-link" aria-label="Instagram">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
              <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
              <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
            </svg>
          </a>
        <?php endif; ?>

        <?php if (!empty($socialLinks['facebook'])): ?>
          <a href="<?php echo e($socialLinks['facebook']); ?>" target="_blank" rel="noopener noreferrer" class="utility-social-link" aria-label="Facebook">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
              <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
            </svg>
          </a>
        <?php endif; ?>

        <?php if (defined('CONTACT_WHATSAPP') && CONTACT_WHATSAPP): ?>
          <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', CONTACT_WHATSAPP); ?>?text=Hello%20Club%20Yatri%20Concierge,%20I%20would%20like%20to%20inquire%20about%20Holiday%20Passes." target="_blank" rel="noopener noreferrer" class="utility-social-link" aria-label="WhatsApp Concierge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
              <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
            </svg>
          </a>
        <?php endif; ?>
      </div>

      <!-- Dedicated Login Link (Small & Visible) -->
      <?php if ($isLoggedIn): ?>
        <a href="<?php echo url('actions/logout.php'); ?>" class="utility-login-btn">
          <span>Sign Out</span>
        </a>
      <?php else: ?>
        <a href="<?php echo url('login.php'); ?>" class="utility-login-btn">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
          <span>Subscriber Login</span>
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Level 2: Main Navigation Bar -->
<header class="navbar-main" id="mainNavbar">
  <div class="container">
    <!-- Brand Logo: Official Red & White Circular Logo ONLY (No text beside it as requested) -->
    <a href="<?php echo url('index.php'); ?>" class="brand-logo-only" aria-label="Club Yatri">
      <img src="<?php echo e(BASE_PATH); ?>/assets/images/logo/club-yatri-logo.png" alt="Club Yatri Logo" width="52" height="52">
    </a>

    <!-- Desktop Navigation Menu -->
    <nav aria-label="Main Navigation">
      <ul class="nav-menu">
        <li><a href="<?php echo url('index.php'); ?>" class="nav-link <?php echo isActive('index.php'); ?>">Home</a></li>
        <li><a href="<?php echo url('about.php'); ?>" class="nav-link <?php echo isActive('about.php'); ?>">About Us</a></li>
        <li><a href="<?php echo url('passes-subscriptions.php'); ?>" class="nav-link <?php echo isActive('passes-subscriptions.php'); ?>">Passes &amp; Subscriptions</a></li>
        <li><a href="<?php echo url('resort-network.php'); ?>" class="nav-link <?php echo isActive('resort-network.php'); ?>">Resort Network</a></li>
        <li><a href="<?php echo url('destinations.php'); ?>" class="nav-link <?php echo isActive('destinations.php'); ?>">Destinations</a></li>
        <li><a href="<?php echo url('rooms.php'); ?>" class="nav-link <?php echo isActive('rooms.php'); ?>">Rooms &amp; Suites</a></li>
        <li><a href="<?php echo url('contact.php'); ?>" class="nav-link <?php echo isActive('contact.php'); ?>">Contact Us</a></li>
      </ul>
    </nav>

    <!-- Nav Actions: Prominent Primary CTA Button -->
    <div class="nav-actions">
      <a href="<?php echo url('callback-request.php'); ?>" class="btn-red btn-sm">
        <span>Request a Callback</span>
      </a>

      <button class="mobile-toggle" aria-label="Open Mobile Navigation" id="mobileMenuToggle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
      </button>
    </div>
  </div>
</header>

<!-- Mobile Navigation Drawer -->
<div class="drawer-backdrop" id="drawerBackdrop"></div>
<aside class="mobile-nav-drawer" id="mobileDrawer" aria-label="Mobile Navigation Drawer">
  <div>
    <div class="drawer-header">
      <a href="<?php echo url('index.php'); ?>" class="brand-logo-only" aria-label="Club Yatri">
        <img src="<?php echo e(BASE_PATH); ?>/assets/images/logo/club-yatri-logo.png" alt="Club Yatri Logo" width="46" height="46">
      </a>
      <button class="drawer-close" id="drawerCloseBtn" aria-label="Close Mobile Navigation">✕</button>
    </div>
    <ul class="drawer-menu">
      <li><a href="<?php echo url('index.php'); ?>" class="drawer-link <?php echo isActive('index.php'); ?>">Home</a></li>
      <li><a href="<?php echo url('about.php'); ?>" class="drawer-link <?php echo isActive('about.php'); ?>">About Us</a></li>
      <li><a href="<?php echo url('passes-subscriptions.php'); ?>" class="drawer-link <?php echo isActive('passes-subscriptions.php'); ?>">Passes &amp; Subscriptions</a></li>
      <li><a href="<?php echo url('resort-network.php'); ?>" class="drawer-link <?php echo isActive('resort-network.php'); ?>">Resort Network</a></li>
      <li><a href="<?php echo url('destinations.php'); ?>" class="drawer-link <?php echo isActive('destinations.php'); ?>">Destinations</a></li>
      <li><a href="<?php echo url('rooms.php'); ?>" class="drawer-link <?php echo isActive('rooms.php'); ?>">Rooms &amp; Suites</a></li>
      <li><a href="<?php echo url('contact.php'); ?>" class="drawer-link <?php echo isActive('contact.php'); ?>">Contact Us</a></li>
      <li><a href="<?php echo url('login.php'); ?>" class="drawer-link <?php echo isActive('login.php'); ?>">Subscriber Login</a></li>
    </ul>
  </div>
  <div>
    <a href="<?php echo url('callback-request.php'); ?>" class="btn-red" style="width: 100%; margin-bottom: 12px; text-align: center;">
      Request a Callback
    </a>
    <p style="font-size: 0.8rem; color: var(--color-text-muted); text-align: center;">
      Toll-Free: <?php echo e(CONTACT_PHONE_TOLL_FREE); ?>
    </p>
  </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const toggleBtn = document.getElementById('mobileMenuToggle');
  const closeBtn = document.getElementById('drawerCloseBtn');
  const drawer = document.getElementById('mobileDrawer');
  const backdrop = document.getElementById('drawerBackdrop');

  function openDrawer() {
    if (drawer && backdrop) {
      drawer.classList.add('open');
      backdrop.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeDrawer() {
    if (drawer && backdrop) {
      drawer.classList.remove('open');
      backdrop.classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  if (toggleBtn) toggleBtn.addEventListener('click', openDrawer);
  if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
  if (backdrop) backdrop.addEventListener('click', closeDrawer);

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDrawer();
  });
});
</script>
