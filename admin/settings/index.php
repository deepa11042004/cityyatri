<?php
/**
 * Club Yatri Admin - Settings: general, contact, social, SEO, email and account.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/crud.php';
require_once __DIR__ . '/../../includes/settings.php';

$pdo = admin_pdo();
$admin = require_admin();

const SETTINGS_TABS = [
    'general' => ['General', 'bi-sliders'], 'contact' => ['Contact', 'bi-telephone'], 'social' => ['Social media', 'bi-share'],
    'seo' => ['SEO', 'bi-search'], 'email' => ['Email (SMTP)', 'bi-envelope-at'], 'account' => ['My account', 'bi-person-gear'],
];
$tab = (string)($_GET['tab'] ?? $_POST['tab'] ?? 'general');
if (!isset(SETTINGS_TABS[$tab])) {
    $tab = 'general';
}
$errors = [];

/** Current value: saved setting, else the built-in default shown in the form. */
$cur = static fn(string $k, string $d = ''): string => site_setting($k, $d);

$validUrl = static fn(string $u): bool => $u === '' || (bool)preg_match('~^https?://[^\s]+$~i', $u);
$validPhone = static fn(string $p): bool => $p === '' || (bool)preg_match('/^[0-9+()\-.\s]{5,30}$/', $p);

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (admin_post_too_large()) {
        $errors[] = 'The file is larger than the server allows (max 2 MB). Compress the image and try again.';
    } elseif (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Your session expired. Please try again.';
    } else {
        $save = [];
        $removed = [];
        $stored = [];
        $warnings = [];
        $done = '';

        switch ($tab) {
            case 'general':
                $name = admin_str('site_name', 120);
                if ($name === '') { $errors[] = 'Website name is required.'; }
                $save = ['site_name' => $name, 'site_tagline' => admin_str('site_tagline', 255), 'seo_description' => admin_str('seo_description', 500)];
                foreach (['logo' => ['logo_path', 'Logo'], 'favicon' => ['favicon_path', 'Favicon']] as $field => [$key, $label]) {
                    $old = site_setting_exists($key) ? site_setting($key) : '';
                    if (admin_flag('remove_' . $field) && $old !== '') { $removed[] = $old; $save[$key] = null; }
                    if (!empty($_FILES[$field]['name'])) {
                        $r = admin_save_image($_FILES[$field], 'site', $label);
                        if ($r['ok']) { if ($old !== '') { $removed[] = $old; } $save[$key] = $r['path']; $stored[] = $r['path']; }
                        elseif ($r['error'] !== '') { $errors[] = $r['error']; }
                    }
                }
                $done = 'General settings saved.';
                break;

            case 'contact':
                $phone = admin_str('contact_phone', 30);
                $email = admin_str('contact_email', 190);
                $support = admin_str('contact_support_email', 190);
                if ($phone === '' || !$validPhone($phone)) { $errors[] = 'Enter a valid contact phone number.'; }
                if (!$validPhone(admin_str('contact_phone_alt', 30)) || !$validPhone(admin_str('contact_whatsapp', 30))) { $errors[] = 'Phone numbers may only contain digits, spaces, + ( ) - and dots.'; }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Enter a valid contact email address.'; }
                if ($support !== '' && !filter_var($support, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Enter a valid support email address, or leave it empty.'; }
                if (!$validUrl(admin_str('contact_map_url', 1000))) { $errors[] = 'The Google Maps link must start with http:// or https://'; }
                $save = ['contact_phone' => $phone, 'contact_phone_alt' => admin_str('contact_phone_alt', 30), 'contact_whatsapp' => admin_str('contact_whatsapp', 30),
                    'contact_email' => $email, 'contact_support_email' => $support, 'contact_address' => admin_str('contact_address', 500), 'contact_map_url' => admin_str('contact_map_url', 1000)];
                $done = 'Contact details saved.';
                break;

            case 'social':
                foreach (['facebook', 'instagram', 'youtube', 'linkedin', 'twitter'] as $n) {
                    $u = admin_str('social_' . $n, 500);
                    if (!$validUrl($u)) { $errors[] = ucfirst($n) . ' link must start with http:// or https://'; }
                    $save['social_' . $n] = $u;
                }
                $done = 'Social media links saved.';
                break;

            case 'seo':
                $save = ['seo_title' => admin_str('seo_title', 255)];
                $old = site_setting_exists('seo_og_image') ? site_setting('seo_og_image') : '';
                if (admin_flag('remove_og') && $old !== '') { $removed[] = $old; $save['seo_og_image'] = null; }
                if (!empty($_FILES['og_image']['name'])) {
                    $r = admin_save_image($_FILES['og_image'], 'site', 'Share image');
                    if ($r['ok']) { if ($old !== '' && !preg_match('~^https?://~i', $old)) { $removed[] = $old; } $save['seo_og_image'] = $r['path']; $stored[] = $r['path']; }
                    elseif ($r['error'] !== '') { $errors[] = $r['error']; }
                }
                $done = 'SEO settings saved.';
                break;

            case 'email':
                $port = admin_str('smtp_port', 5);
                $from = admin_str('mail_from_email', 190);
                $enc = admin_str('smtp_encryption', 5);
                if ($port !== '' && (!ctype_digit($port) || (int)$port < 1 || (int)$port > 65535)) { $errors[] = 'The SMTP port must be a number between 1 and 65535.'; }
                if ($from !== '' && !filter_var($from, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Enter a valid sender email address.'; }
                $save = ['smtp_host' => admin_str('smtp_host', 190), 'smtp_port' => $port, 'smtp_user' => admin_str('smtp_user', 190), 'smtp_encryption' => in_array($enc, ['tls', 'ssl', 'none'], true) ? $enc : 'tls',
                    'mail_from_email' => $from, 'mail_from_name' => admin_str('mail_from_name', 120)];
                $newPass = (string)($_POST['smtp_pass'] ?? '');
                if (admin_flag('smtp_pass_clear')) {
                    $save['smtp_pass'] = null;
                } elseif ($newPass !== '') {
                    $save['smtp_pass'] = site_secret_encrypt($newPass);   // encrypted at rest, never shown again
                }
                $done = 'Email settings saved.';
                break;

            case 'account':
                $name = admin_str('admin_name', 120);
                $email = strtolower(admin_str('admin_email', 190));
                $current = (string)($_POST['current_password'] ?? '');
                $new = (string)($_POST['new_password'] ?? '');
                $confirm = (string)($_POST['confirm_password'] ?? '');
                if ($name === '') { $errors[] = 'Your name is required.'; }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Enter a valid email address.'; }
                $st = $pdo->prepare('SELECT * FROM admin_users WHERE id = ?');
                $st->execute([$admin['id']]);
                $row = $st->fetch();
                if (!$row) { $errors[] = 'Your account could not be found.'; }
                if ($row && $email !== strtolower($row['email'])) {
                    $dup = $pdo->prepare('SELECT COUNT(*) FROM admin_users WHERE email = ? AND id <> ?');
                    $dup->execute([$email, $admin['id']]);
                    if ((int)$dup->fetchColumn()) { $errors[] = 'Another admin already uses that email address.'; }
                }
                $changePw = $new !== '' || $confirm !== '';
                if ($row && ($changePw || $email !== strtolower($row['email'])) && !password_verify($current, $row['password_hash'])) {
                    $errors[] = 'Your current password is incorrect.';
                }
                if ($changePw) {
                    if ($new !== $confirm) { $errors[] = 'The new passwords do not match.'; }
                    elseif (strlen($new) < 10 || !preg_match('/[a-z]/', $new) || !preg_match('/[A-Z]/', $new) || !preg_match('/\d/', $new) || !preg_match('/[^A-Za-z0-9]/', $new)) {
                        $errors[] = 'The new password needs at least 10 characters with an uppercase letter, a lowercase letter, a number and a symbol.';
                    } elseif ($row && password_verify($new, $row['password_hash'])) {
                        $errors[] = 'Choose a password you have not used before.';
                    }
                }
                if (!$errors && $row) {
                    $pdo->prepare('UPDATE admin_users SET name = ?, email = ? WHERE id = ?')->execute([$name, $email, $admin['id']]);
                    if ($changePw) {
                        $pdo->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?')->execute([password_hash($new, PASSWORD_DEFAULT), $admin['id']]);
                        session_regenerate_id(true);
                    }
                    $_SESSION['admin_name'] = $name;
                    $_SESSION['admin_email'] = $email;
                    admin_flash('success', $changePw ? 'Account updated and password changed.' : 'Account updated.');
                    admin_redirect('settings/index.php?tab=account');
                }
                break;
        }

        if (!$errors && $tab !== 'account') {
            try {
                site_settings_save($pdo, $save);
                foreach ($removed as $p) { admin_delete_uploaded($pdo, $p); }
                admin_flash('success', $done);
                foreach ($warnings as $w) { admin_flash('warning', $w); }
                admin_redirect('settings/index.php?tab=' . $tab);
            } catch (PDOException $e) {
                error_log('Settings save failed: ' . $e->getMessage());
                foreach ($stored as $p) { admin_delete_uploaded($pdo, $p); }
                $errors[] = 'The settings could not be saved. Please try again.';
            }
        } elseif ($errors) {
            foreach ($stored as $p) { admin_delete_uploaded($pdo, $p); }
        }
    }
}

// values for the form: posted values after an error, else the saved ones
$post = $errors ? $_POST : null;
$f = static fn(string $k, string $d = ''): string => e($post !== null && isset($post[$k]) ? (string)$post[$k] : $cur($k, $d));

foreach ($errors as $err) {
    admin_flash('error', $err);
}
admin_header('Settings', 'settings', [['Settings', null]], 'Website details, contact information, social links, SEO, email and your account.');
?>
<ul class="nav nav-tabs mb-3">
  <?php foreach (SETTINGS_TABS as $k => [$label, $icon]): ?>
    <li class="nav-item"><a class="nav-link<?php echo $k === $tab ? ' active fw-bold' : ''; ?>" href="?tab=<?php echo e($k); ?>"><i class="bi <?php echo e($icon); ?>"></i> <?php echo e($label); ?></a></li>
  <?php endforeach; ?>
</ul>

<form method="post" action="?tab=<?php echo e($tab); ?>" enctype="multipart/form-data" novalidate autocomplete="off">
  <?php echo admin_csrf_field(); ?>
  <input type="hidden" name="tab" value="<?php echo e($tab); ?>">
  <div class="row g-3"><div class="col-xl-8">

<?php if ($tab === 'general'): ?>
  <div class="cy-card mb-3"><div class="cy-card-head"><h2>General</h2></div><div class="cy-card-body"><div class="row g-3">
    <div class="col-md-6"><label class="form-label fw-bold" for="site_name">Website name *</label><input class="form-control" id="site_name" name="site_name" maxlength="120" value="<?php echo $f('site_name', 'Club Yatri'); ?>"></div>
    <div class="col-md-6"><label class="form-label fw-bold" for="site_tagline">Tagline</label><input class="form-control" id="site_tagline" name="site_tagline" maxlength="255" value="<?php echo $f('site_tagline', 'Vacation Ownership & Bespoke Timeshare Holidays'); ?>"></div>
    <div class="col-12"><label class="form-label fw-bold" for="seo_description">Default website description</label><textarea class="form-control" id="seo_description" name="seo_description" rows="3" maxlength="500"><?php echo $f('seo_description', (string)($GLOBALS['defaultSeo']['description'] ?? '')); ?></textarea><div class="form-text">Used by search engines for pages that do not have their own description.</div></div>
    <?php foreach (['logo' => ['logo_path', 'Logo', site_logo_url()], 'favicon' => ['favicon_path', 'Favicon (browser tab icon)', site_favicon_url()]] as $field => [$key, $label, $curUrl]): ?>
      <div class="col-md-6"><label class="form-label fw-bold" for="<?php echo $field; ?>"><?php echo e($label); ?></label>
        <div class="d-flex align-items-center gap-3 mb-2"><img src="<?php echo e($curUrl); ?>" alt="" style="width:56px;height:56px;object-fit:contain;border-radius:12px;background:#F6F4F0;padding:4px;">
          <?php if (site_setting_exists($key) && site_setting($key) !== ''): ?><label class="form-check small text-danger mb-0"><input class="form-check-input" type="checkbox" name="remove_<?php echo $field; ?>" value="1"> Go back to the original</label><?php endif; ?></div>
        <input class="form-control" type="file" id="<?php echo $field; ?>" name="<?php echo $field; ?>" accept="image/jpeg,image/png,image/webp"><div class="form-text">JPG, PNG or WebP, max 2 MB. A square image works best.<?php echo $field === 'favicon' ? ' Without one, the logo is used.' : ''; ?></div></div>
    <?php endforeach; ?>
  </div></div></div>

<?php elseif ($tab === 'contact'): ?>
  <div class="cy-card mb-3"><div class="cy-card-head"><h2>Contact details</h2><span class="text-muted small">Shown in the header, footer and Contact page</span></div><div class="cy-card-body"><div class="row g-3">
    <div class="col-md-6"><label class="form-label fw-bold" for="contact_phone">Contact phone *</label><input class="form-control" id="contact_phone" name="contact_phone" maxlength="30" value="<?php echo $f('contact_phone', '1800-209-9287'); ?>"></div>
    <div class="col-md-6"><label class="form-label fw-bold" for="contact_phone_alt">Alternate phone</label><input class="form-control" id="contact_phone_alt" name="contact_phone_alt" maxlength="30" value="<?php echo $f('contact_phone_alt'); ?>"></div>
    <div class="col-md-6"><label class="form-label fw-bold" for="contact_whatsapp">WhatsApp number</label><input class="form-control" id="contact_whatsapp" name="contact_whatsapp" maxlength="30" value="<?php echo $f('contact_whatsapp', '+91 98765 43210'); ?>"></div>
    <div class="col-md-6"><label class="form-label fw-bold" for="contact_email">Email *</label><input class="form-control" type="email" id="contact_email" name="contact_email" maxlength="190" value="<?php echo $f('contact_email', 'concierge@clubyatri.com'); ?>"></div>
    <div class="col-md-6"><label class="form-label fw-bold" for="contact_support_email">Support email</label><input class="form-control" type="email" id="contact_support_email" name="contact_support_email" maxlength="190" value="<?php echo $f('contact_support_email', 'support@clubyatri.com'); ?>"></div>
    <div class="col-12"><label class="form-label fw-bold" for="contact_address">Address</label><textarea class="form-control" id="contact_address" name="contact_address" rows="2" maxlength="500"><?php echo $f('contact_address', 'Level 14, The Imperial Tower, Bandra Kurla Complex (BKC), Mumbai - 400051, Maharashtra, India'); ?></textarea></div>
    <div class="col-12"><label class="form-label fw-bold" for="contact_map_url">Google Maps link</label><input class="form-control" id="contact_map_url" name="contact_map_url" maxlength="1000" value="<?php echo $f('contact_map_url'); ?>" placeholder="https://maps.google.com/…"><div class="form-text">Adds a "View on Google Maps" link on the Contact page.</div></div>
  </div></div></div>

<?php elseif ($tab === 'social'): ?>
  <div class="cy-card mb-3"><div class="cy-card-head"><h2>Social media</h2><span class="text-muted small">Icons in the footer</span></div><div class="cy-card-body"><div class="row g-3">
    <?php foreach (['facebook' => ['Facebook', 'https://facebook.com/clubyatri'], 'instagram' => ['Instagram', 'https://instagram.com/clubyatri'], 'youtube' => ['YouTube', 'https://youtube.com/@clubyatri'], 'linkedin' => ['LinkedIn', 'https://linkedin.com/company/clubyatri'], 'twitter' => ['X (Twitter)', '']] as $n => [$label, $def]): ?>
      <div class="col-md-6"><label class="form-label fw-bold" for="social_<?php echo $n; ?>"><?php echo e($label); ?></label><input class="form-control" id="social_<?php echo $n; ?>" name="social_<?php echo $n; ?>" maxlength="500" value="<?php echo $f('social_' . $n, $def); ?>" placeholder="https://…"></div>
    <?php endforeach; ?>
    <div class="col-12 form-text mt-0">X (Twitter) is optional: its icon only appears when a link is entered. Clearing another network brings back its original link.</div>
  </div></div></div>

<?php elseif ($tab === 'seo'): ?>
  <div class="cy-card mb-3"><div class="cy-card-head"><h2>Default SEO</h2></div><div class="cy-card-body"><div class="row g-3">
    <div class="col-12"><label class="form-label fw-bold" for="seo_title">Default meta title</label><input class="form-control" id="seo_title" name="seo_title" maxlength="255" value="<?php echo $f('seo_title', (string)($GLOBALS['defaultSeo']['title'] ?? '')); ?>"><div class="form-text">Used on pages that do not set their own title. The default description is on the General tab.</div></div>
    <div class="col-12"><label class="form-label fw-bold" for="og_image">Default share image</label>
      <?php $og = site_setting('seo_og_image'); if ($og !== ''): ?><div class="mb-2"><img src="<?php echo e(preg_match('~^https?://~i', $og) ? $og : url($og)); ?>" alt="" style="max-width:260px;border-radius:12px;"><label class="form-check small text-danger mt-1"><input class="form-check-input" type="checkbox" name="remove_og" value="1"> Go back to the original</label></div><?php endif; ?>
      <input class="form-control" type="file" id="og_image" name="og_image" accept="image/jpeg,image/png,image/webp"><div class="form-text">Shown when a page is shared on social media. 1200 × 630 px works best. JPG, PNG or WebP, max 2 MB.</div></div>
  </div></div></div>

<?php elseif ($tab === 'email'): ?>
  <div class="cy-card mb-3"><div class="cy-card-head"><h2>Email (SMTP)</h2></div><div class="cy-card-body"><div class="row g-3">
    <div class="col-12"><div class="alert alert-warning py-2 mb-0 small"><i class="bi bi-info-circle"></i> These details are stored securely for when the website sends email. The website does not send any email yet, so changing them has no visible effect today.</div></div>
    <div class="col-md-8"><label class="form-label fw-bold" for="smtp_host">SMTP host</label><input class="form-control" id="smtp_host" name="smtp_host" maxlength="190" value="<?php echo $f('smtp_host'); ?>" placeholder="mail.example.com"></div>
    <div class="col-md-4"><label class="form-label fw-bold" for="smtp_port">Port</label><input class="form-control" inputmode="numeric" id="smtp_port" name="smtp_port" maxlength="5" value="<?php echo $f('smtp_port', '587'); ?>"></div>
    <div class="col-md-4"><label class="form-label fw-bold" for="smtp_encryption">Encryption</label><select class="form-select" id="smtp_encryption" name="smtp_encryption">
      <?php $encv = $post !== null ? (string)($post['smtp_encryption'] ?? 'tls') : $cur('smtp_encryption', 'tls'); foreach (['tls' => 'STARTTLS (587)', 'ssl' => 'SSL/TLS (465)', 'none' => 'None'] as $k => $l): ?><option value="<?php echo $k; ?>" <?php echo $encv === $k ? 'selected' : ''; ?>><?php echo e($l); ?></option><?php endforeach; ?></select></div>
    <div class="col-md-8"><label class="form-label fw-bold" for="smtp_user">SMTP username</label><input class="form-control" id="smtp_user" name="smtp_user" maxlength="190" value="<?php echo $f('smtp_user'); ?>" autocomplete="off"></div>
    <div class="col-12"><label class="form-label fw-bold" for="smtp_pass">SMTP password</label><input class="form-control" type="password" id="smtp_pass" name="smtp_pass" autocomplete="new-password" placeholder="<?php echo site_setting('smtp_pass') !== '' ? '•••••••• (saved. Type to replace)' : 'Not set'; ?>">
      <?php if (site_setting('smtp_pass') !== ''): ?><label class="form-check small text-danger mt-1"><input class="form-check-input" type="checkbox" name="smtp_pass_clear" value="1"> Remove the saved password</label><?php endif; ?>
      <div class="form-text">Stored encrypted and never shown again.</div></div>
    <div class="col-md-6"><label class="form-label fw-bold" for="mail_from_email">Sender email</label><input class="form-control" type="email" id="mail_from_email" name="mail_from_email" maxlength="190" value="<?php echo $f('mail_from_email'); ?>"></div>
    <div class="col-md-6"><label class="form-label fw-bold" for="mail_from_name">Sender name</label><input class="form-control" id="mail_from_name" name="mail_from_name" maxlength="120" value="<?php echo $f('mail_from_name', 'Club Yatri'); ?>"></div>
  </div></div></div>

<?php else: ?>
  <?php
  $st = $pdo->prepare('SELECT name, email, last_login_at, created_at FROM admin_users WHERE id = ?');
  $st->execute([$admin['id']]);
  $me = $st->fetch() ?: ['name' => '', 'email' => '', 'last_login_at' => null, 'created_at' => null];
  ?>
  <div class="cy-card mb-3"><div class="cy-card-head"><h2>My account</h2><span class="text-muted small">Role: Super Admin</span></div><div class="cy-card-body"><div class="row g-3">
    <div class="col-md-6"><label class="form-label fw-bold" for="admin_name">Name *</label><input class="form-control" id="admin_name" name="admin_name" maxlength="120" value="<?php echo $f('admin_name', (string)$me['name']); ?>"></div>
    <div class="col-md-6"><label class="form-label fw-bold" for="admin_email">Email (your login) *</label><input class="form-control" type="email" id="admin_email" name="admin_email" maxlength="190" value="<?php echo $f('admin_email', (string)$me['email']); ?>"></div>
    <div class="col-12"><hr class="my-1"><h3 class="h6 fw-bold mb-0">Change password</h3><div class="form-text">Leave the password boxes empty to keep your current password.</div></div>
    <div class="col-md-4"><label class="form-label fw-bold" for="current_password">Current password</label><input class="form-control" type="password" id="current_password" name="current_password" autocomplete="current-password"><div class="form-text">Needed to change your email or password.</div></div>
    <div class="col-md-4"><label class="form-label fw-bold" for="new_password">New password</label><input class="form-control" type="password" id="new_password" name="new_password" autocomplete="new-password"></div>
    <div class="col-md-4"><label class="form-label fw-bold" for="confirm_password">Repeat new password</label><input class="form-control" type="password" id="confirm_password" name="confirm_password" autocomplete="new-password"></div>
    <div class="col-12 form-text mt-0">At least 10 characters, with an uppercase letter, a lowercase letter, a number and a symbol.</div>
  </div></div></div>
<?php endif; ?>

  </div>
  <div class="col-xl-4">
    <div class="cy-card mb-3" style="position: sticky; top: 76px;"><div class="cy-card-body">
      <button class="btn btn-cy btn-lg w-100 mb-2" type="submit"><i class="bi bi-check2-circle"></i> Save <?php echo e(strtolower(SETTINGS_TABS[$tab][0])); ?></button>
      <p class="small text-muted mb-0">Changes appear on the website as soon as you save.</p>
      <?php if ($tab === 'account'): ?>
        <hr><p class="small text-muted mb-1">Last sign-in: <?php echo !empty($me['last_login_at']) ? e(date('j M Y, g:i a', strtotime((string)$me['last_login_at']))) : '—'; ?></p>
      <?php endif; ?>
    </div></div>
    <?php if ($tab === 'account'): ?>
      <div class="cy-card mb-3"><div class="cy-card-body"><h3 class="h6 fw-bold">Sign out</h3><p class="small text-muted">End your admin session on this device.</p>
        <button class="btn btn-outline-danger w-100" type="submit" formaction="<?php echo e(admin_url('logout.php')); ?>" formnovalidate><i class="bi bi-box-arrow-right"></i> Sign out</button></div></div>
    <?php endif; ?>
  </div></div>
</form>
<?php admin_footer(); ?>
