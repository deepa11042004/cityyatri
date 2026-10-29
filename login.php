<?php
/**
 * Club Yatri - Conventional Username & Password Login Page
 * Light Hospitality Theme
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

// If already authenticated, redirect to home page
if (!empty($_SESSION['member_authenticated'])) {
    header('Location: ' . url('index.php'));
    exit;
}

$pageTitle = 'Subscriber Login | Club Yatri';
$pageDescription = 'Sign in with your username and password to access your Club Yatri subscriber account.';

$breadcrumbTitle = 'Sign In';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 50px; padding-bottom: 80px; background: var(--color-background-soft);">
  <div class="container" style="max-width: 500px;">
    
    <div style="background: #FFFFFF; border: 1.5px solid var(--color-border); border-radius: var(--radius-card); padding: 44px 36px; box-shadow: var(--shadow-card);">
      
      <div style="text-align: center; margin-bottom: 28px;">
        <img src="<?php echo e(BASE_PATH); ?>/assets/images/logo/club-yatri-logo.png" alt="Club Yatri" width="56" height="56" style="margin: 0 auto 12px auto; display: block;">
        <div class="sub-tag" style="margin-bottom: 8px;">Subscriber Portal</div>
        <h1 class="heading-serif" style="font-size: 1.85rem; color: var(--color-text); margin-bottom: 8px;">Sign In to Your Account</h1>
        <p style="font-size: 0.92rem; color: var(--color-text-muted); line-height: 1.5;">
          Enter your username and password to manage your vacation preferences and inquiries.
        </p>
      </div>

      <form action="<?php echo url('actions/authenticate.php'); ?>" method="POST" id="loginForm">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

        <!-- Username / Email Field -->
        <div class="form-group">
          <label for="username">Username or Email Address *</label>
          <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username or email" required autofocus>
        </div>

        <!-- Password Field with Show/Hide Toggle -->
        <div class="form-group">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <label for="password" style="margin-bottom: 0;">Password *</label>
            <button type="button" id="togglePasswordBtn" style="background: none; border: none; font-size: 0.8rem; color: var(--color-primary); font-weight: 600; cursor: pointer;">
              Show
            </button>
          </div>
          <div style="position: relative;">
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
          </div>
        </div>

        <!-- Remember Me Checkbox -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; font-size: 0.85rem;">
          <label style="display: flex; align-items: center; gap: 8px; color: var(--color-text-muted); cursor: pointer;">
            <input type="checkbox" name="remember" style="accent-color: var(--color-primary);">
            <span>Remember me</span>
          </label>
          <a href="<?php echo url('contact.php'); ?>" style="color: var(--color-primary); font-weight: 600;">Need help signing in?</a>
        </div>

        <button type="submit" class="btn-red btn-lg" style="width: 100%;">
          Sign In
        </button>
      </form>

      <!-- Support Assistance -->
      <div style="margin-top: 24px; padding: 14px; background: var(--color-background-warm); border: 1px solid var(--color-accent); border-radius: var(--radius-sm); font-size: 0.82rem; color: var(--color-text-muted); line-height: 1.5; text-align: center;">
        Need assistance with your subscriber account?<br>
        Call Concierge Support at <a href="tel:<?php echo e(CONTACT_PHONE_TEL); ?>" style="color: var(--color-primary); font-weight: 700;"><?php echo e(CONTACT_PHONE_TOLL_FREE); ?></a>
      </div>

    </div>

  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const pwdInput = document.getElementById('password');
  const toggleBtn = document.getElementById('togglePasswordBtn');

  if (toggleBtn && pwdInput) {
    toggleBtn.addEventListener('click', function() {
      if (pwdInput.type === 'password') {
        pwdInput.type = 'text';
        toggleBtn.textContent = 'Hide';
      } else {
        pwdInput.type = 'password';
        toggleBtn.textContent = 'Show';
      }
    });
  }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
