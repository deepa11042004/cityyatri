<?php
/**
 * Club Yatri - Holiday Pass Specifications & Details
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/data/subscriptions.php';

$planId = $_GET['id'] ?? 'imperial-world';
$plan = $subscriptionPlans[$planId] ?? $subscriptionPlans['imperial-world'];

$pageTitle = $plan['title'];
$pageDescription = 'Detailed specifications, annual points, room entitlements, and exchange privileges for ' . $plan['title'];

$breadcrumbTitle = $plan['short_title'];
$parentTitle = 'Holiday Passes';
$parentUrl = url('passes-subscriptions.php');

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="section" style="padding-top: 60px; padding-bottom: 80px;">
  <div class="container">
    <div class="grid-2" style="align-items: start; gap: 48px;">
      
      <!-- Left Overview -->
      <div>
        <div class="sub-tag">Vacation Pass Dossier</div>
        <h1 class="heading-serif" style="font-size: 2.4rem; margin-bottom: 16px;"><?php echo e($plan['title']); ?></h1>
        <p style="font-size: 1.05rem; color: var(--text-secondary); line-height: 1.8; margin-bottom: 28px;">
          <?php echo e($plan['description']); ?>
        </p>

        <div class="card-luxury" style="padding: 28px; margin-bottom: 24px;">
          <h3 class="heading-serif" style="font-size: 1.3rem; color: #fff; margin-bottom: 16px;">Key Pass Specifications</h3>
          <ul style="list-style: none; display: flex; flex-direction: column; gap: 12px; font-size: 0.95rem; color: var(--text-secondary);">
            <li style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
              <span>Vacation Tenure:</span>
              <strong style="color: #fff;"><?php echo e($plan['tenure']); ?></strong>
            </li>
            <li style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
              <span>Annual Vacation Nights:</span>
              <strong style="color: var(--gold-light);"><?php echo e((string)$plan['annual_nights']); ?> Nights / Year</strong>
            </li>
            <li style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
              <span>Annual Points Credit:</span>
              <strong style="color: #fff;"><?php echo number_format($plan['points_annual']); ?> Points</strong>
            </li>
            <li style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
              <span>Points Banking Window:</span>
              <strong style="color: #fff;">Up to <?php echo e((string)$plan['banking_years']); ?> Years</strong>
            </li>
            <li style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
              <span>Resort Access Network:</span>
              <strong style="color: #fff;"><?php echo e($plan['resorts_access']); ?></strong>
            </li>
            <li style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
              <span>Dining & Spa Privileges:</span>
              <strong style="color: var(--teal-accent);"><?php echo e($plan['dining_discount']); ?> Discount</strong>
            </li>
            <li style="display: flex; justify-content: space-between; padding-top: 4px;">
              <span>Dedicated Concierge:</span>
              <strong style="color: #fff;"><?php echo e($plan['concierge']); ?></strong>
            </li>
          </ul>
        </div>
      </div>

      <!-- Right Form / Enrolment -->
      <div>
        <div class="card-luxury" style="padding: 36px; border: 1.5px solid var(--border-gold);">
          <div class="sub-tag" style="margin-bottom: 12px;">Priority Enrollment</div>
          <h3 class="heading-serif" style="font-size: 1.6rem; color: #fff; margin-bottom: 8px;">Request Allocation Dossier</h3>
          <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 24px;">
            A confidential prospectus and seasonal availability calendar for the <?php echo e($plan['short_title']); ?> will be dispatched immediately.
          </p>

          <form action="<?php echo url('actions/submit-inquiry.php'); ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="pass_tier" value="<?php echo e($plan['title']); ?>">
            
            <div class="form-group">
              <label>Full Legal Name *</label>
              <input type="text" name="full_name" class="form-control" required placeholder="Full Name">
            </div>

            <div class="form-group">
              <label>Telephone Number *</label>
              <input type="tel" name="phone" class="form-control" required placeholder="+91 98765 43210">
            </div>

            <div class="form-group">
              <label>Executive Email Address *</label>
              <input type="email" name="email" class="form-control" required placeholder="executive@company.com">
            </div>

            <div class="form-group">
              <label>City of Residence</label>
              <input type="text" name="city" class="form-control" placeholder="e.g. Mumbai, New Delhi, Bengaluru">
            </div>

            <div class="form-group">
              <label>Additional Notes or Queries</label>
              <textarea name="notes" class="form-control" rows="2" placeholder="Tell us about your upcoming travel plans..."></textarea>
            </div>

            <button type="submit" class="btn btn-gold btn-lg" style="width: 100%;">
              Submit VIP Pass Inquiry
            </button>
          </form>
        </div>
      </div>

    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
