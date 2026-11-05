<?php
/**
 * Club Yatri Admin - Passes & Subscriptions: add / edit a holiday pass with its benefits.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/crud.php';
require_once __DIR__ . '/../../includes/content-db.php';
require_once __DIR__ . '/_tabs.php';

$pdo = admin_pdo();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$pass = null;
if ($id > 0) {
    $st = $pdo->prepare('SELECT * FROM passes WHERE id = ?');
    $st->execute([$id]);
    $pass = $st->fetch() ?: null;
    if (!$pass) {
        admin_flash('error', 'That pass could not be found.');
        admin_redirect('passes/index.php');
    }
}

$errors = [];
$old = null;

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (admin_post_too_large()) {
        $errors[] = 'The file is larger than the server allows (max 2 MB). Compress the image and try again.';
    } elseif (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Your session expired. Please try again.';
    } else {
        $old = $_POST;
        $name = admin_str('name', 190);
        $years = admin_int('duration_years', 0);
        $nights = admin_int('annual_nights', 0);
        $points = trim((string)($_POST['annual_points'] ?? ''));
        $bTitles = (array)($_POST['benefit_title'] ?? []);
        $bDescs = (array)($_POST['benefit_desc'] ?? []);
        $bIcons = (array)($_POST['benefit_icon'] ?? []);

        if ($name === '') { $errors[] = 'Pass name is required.'; }
        if ($years < 1 || $years > 99) { $errors[] = 'Duration must be between 1 and 99 years.'; }
        if ($nights < 0 || $nights > 365) { $errors[] = 'Annual nights must be between 0 and 365.'; }
        if ($points !== '' && (!ctype_digit($points) || (int)$points > 4000000)) { $errors[] = 'Annual points must be a whole number.'; }

        if (!$errors) {
            $slug = admin_unique_slug($pdo, 'passes', admin_slugify(admin_str('slug', 80)) ?: admin_slugify($name), $id);
            $data = [
                'slug' => $slug, 'name' => $name, 'short_name' => admin_str('short_name', 120), 'category' => admin_str('category', 80),
                'duration_years' => $years, 'tenure_label' => admin_str('tenure_label', 80), 'display_price' => admin_str('display_price', 80) ?: null,
                'short_description' => trim((string)($_POST['short_description'] ?? '')) ?: null,
                'full_description' => trim((string)($_POST['full_description'] ?? '')) ?: null,
                'annual_nights' => $nights, 'annual_points' => $points !== '' ? (int)$points : null,
                'banking_years' => admin_str('banking_years', 40) ?: null, 'resorts_access' => admin_str('resorts_access', 190) ?: null,
                'dining_discount' => admin_str('dining_discount', 60) ?: null, 'concierge' => admin_str('concierge', 120) ?: null,
                'advance_booking' => admin_str('advance_booking', 190) ?: null, 'image_alt' => admin_str('image_alt', 190) ?: null,
                'terms' => trim((string)($_POST['terms'] ?? '')) ?: null, 'featured' => admin_flag('featured'),
                'display_order' => max(0, admin_int('display_order')), 'meta_title' => admin_str('meta_title', 255) ?: null,
                'meta_description' => admin_str('meta_description', 500) ?: null, 'is_published' => admin_flag('is_published'),
            ];

            $removed = [];
            $stored = [];
            $warnings = [];
            $saved = false;
            try {
                $pdo->beginTransaction();
                $image = $pass['image_path'] ?? null;
                if (admin_flag('remove_image') && $image) {
                    $removed[] = (string)$image;
                    $image = null;
                }
                if (!empty($_FILES['image']['name'])) {
                    $r = admin_save_image($_FILES['image'], 'passes', 'Pass image');
                    if ($r['ok']) {
                        if ($image) { $removed[] = (string)$image; }
                        $image = $r['path'];
                        $stored[] = $r['path'];
                    } elseif ($r['error'] !== '') {
                        $warnings[] = $r['error'];
                    }
                }
                $data['image_path'] = $image;

                if ($pass) {
                    $set = implode(', ', array_map(static fn($c) => "`$c` = ?", array_keys($data)));
                    $pdo->prepare("UPDATE passes SET $set WHERE id = ?")->execute([...array_values($data), $id]);
                } else {
                    $cols = implode(', ', array_map(static fn($c) => "`$c`", array_keys($data)));
                    $pdo->prepare("INSERT INTO passes ($cols) VALUES (" . implode(',', array_fill(0, count($data), '?')) . ')')->execute(array_values($data));
                    $id = (int)$pdo->lastInsertId();
                }

                // benefits: rebuilt in the order shown
                $pdo->prepare('DELETE FROM pass_benefits WHERE pass_id = ?')->execute([$id]);
                $n = 0;
                foreach ($bTitles as $i => $bt) {
                    $bt = mb_substr(trim((string)$bt), 0, 190);
                    if ($bt === '') {
                        continue;
                    }
                    $icon = isset(CONTENT_ICONS[(string)($bIcons[$i] ?? '')]) ? (string)$bIcons[$i] : 'check-circle';
                    $pdo->prepare('INSERT INTO pass_benefits (pass_id, title, description, icon, sort_order) VALUES (?,?,?,?,?)')
                        ->execute([$id, $bt, mb_substr(trim((string)($bDescs[$i] ?? '')), 0, 1000) ?: null, $icon, ++$n]);
                }
                $pdo->commit();
                $saved = true;
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                foreach ($stored as $p) {
                    admin_delete_uploaded($pdo, $p);
                }
                if (!$pass) { $id = 0; }
                error_log('Pass save failed: ' . $e->getMessage());
                $errors[] = 'The pass could not be saved. Please check the details and try again.';
            }
            if ($saved) {
                foreach ($removed as $p) {
                    admin_delete_uploaded($pdo, $p);
                }
                admin_flash('success', $pass ? 'Pass updated.' : 'Pass added.');
                foreach ($warnings as $w) { admin_flash('warning', $w); }
                admin_redirect('passes/edit.php?id=' . $id);
            }
        }
    }
}

$v = $old ?? ($pass ?: ['is_published' => 1, 'duration_years' => 3, 'annual_nights' => 7, 'display_order' => 0]);
$val = static fn(string $k, string $d = ''): string => e((string)($v[$k] ?? $d));
$benefits = [];
if ($old) {
    foreach ((array)($old['benefit_title'] ?? []) as $i => $bt) {
        $benefits[] = ['title' => (string)$bt, 'description' => (string)($old['benefit_desc'][$i] ?? ''), 'icon' => (string)($old['benefit_icon'][$i] ?? 'check-circle')];
    }
} elseif ($pass) {
    $st = $pdo->prepare('SELECT title, description, icon FROM pass_benefits WHERE pass_id = ? ORDER BY sort_order, id');
    $st->execute([$id]);
    $benefits = $st->fetchAll();
}
if (!$benefits) {
    $benefits = [['title' => '', 'description' => '', 'icon' => 'check-circle']];
}

$benefitRow = static function (array $b): string {
    $opts = '';
    foreach (CONTENT_ICONS as $key => $sym) {
        $opts .= '<option value="' . e($key) . '"' . (($b['icon'] ?? '') === $key ? ' selected' : '') . '>' . e($sym . ' ' . ucwords(str_replace('-', ' ', $key))) . '</option>';
    }
    return '<div class="border rounded-3 p-3 mb-2 bg-white" data-benefit><div class="row g-2 align-items-start">'
        . '<div class="col-md-3"><select class="form-select" name="benefit_icon[]" aria-label="Icon">' . $opts . '</select></div>'
        . '<div class="col-md-9"><input class="form-control mb-2" name="benefit_title[]" maxlength="190" placeholder="Benefit, e.g. 7 vacation nights every year" value="' . e((string)($b['title'] ?? '')) . '" aria-label="Benefit title">'
        . '<input class="form-control form-control-sm" name="benefit_desc[]" maxlength="1000" placeholder="Optional detail" value="' . e((string)($b['description'] ?? '')) . '" aria-label="Benefit description"></div>'
        . '</div><div class="d-flex gap-1 justify-content-end mt-2">'
        . '<button class="btn btn-sm btn-light border" type="button" data-up title="Move up"><i class="bi bi-arrow-up"></i></button>'
        . '<button class="btn btn-sm btn-light border" type="button" data-down title="Move down"><i class="bi bi-arrow-down"></i></button>'
        . '<button class="btn btn-sm btn-outline-danger" type="button" data-remove title="Remove"><i class="bi bi-trash"></i></button></div></div>';
};

foreach ($errors as $err) {
    admin_flash('error', $err);
}
admin_header($pass ? 'Edit pass' : 'Add pass', 'passes', [['Passes & Subscriptions', 'passes/index.php'], [$pass ? (string)$pass['name'] : 'New pass', null]],
    $pass ? 'Changes appear on the website as soon as you save.' : 'Create a holiday pass for the website.');
?>
<form method="post" action="" enctype="multipart/form-data" novalidate>
  <?php echo admin_csrf_field(); ?>
  <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
  <div class="row g-3">
    <div class="col-xl-8">
      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Pass details</h2></div><div class="cy-card-body"><div class="row g-3">
        <div class="col-12"><label class="form-label fw-bold" for="name">Pass name *</label><input class="form-control" id="name" name="name" maxlength="190" required value="<?php echo $val('name'); ?>" placeholder="e.g. 10-Year — Yatri Imperial World Pass"></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="short_name">Short name</label><input class="form-control" id="short_name" name="short_name" maxlength="120" value="<?php echo $val('short_name'); ?>" placeholder="e.g. Yatri Imperial World Pass"></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="slug">URL slug</label><input class="form-control" id="slug" name="slug" maxlength="80" value="<?php echo $val('slug'); ?>" placeholder="auto-generated from the name"><div class="form-text">Used in page and booking links.<?php echo $pass ? ' Changing it breaks old links to this pass.' : ''; ?></div></div>
        <div class="col-md-4"><label class="form-label fw-bold" for="category">Category / badge</label><input class="form-control" id="category" name="category" maxlength="80" value="<?php echo $val('category'); ?>" placeholder="e.g. Family Favorite"></div>
        <div class="col-md-4"><label class="form-label fw-bold" for="duration_years">Duration (years) *</label><input class="form-control" type="number" min="1" max="99" id="duration_years" name="duration_years" value="<?php echo $val('duration_years', '3'); ?>"></div>
        <div class="col-md-4"><label class="form-label fw-bold" for="tenure_label">Duration label</label><input class="form-control" id="tenure_label" name="tenure_label" maxlength="80" value="<?php echo $val('tenure_label'); ?>" placeholder="e.g. 10 Years"></div>
        <div class="col-md-4"><label class="form-label fw-bold" for="display_price">Price shown</label><input class="form-control" id="display_price" name="display_price" maxlength="80" value="<?php echo $val('display_price'); ?>" placeholder="e.g. ₹14,999 / Year"><div class="form-text">Shown as text on the pass page.</div></div>
        <div class="col-md-4"><label class="form-label fw-bold" for="annual_nights">Annual nights</label><input class="form-control" type="number" min="0" max="365" id="annual_nights" name="annual_nights" value="<?php echo $val('annual_nights', '0'); ?>"></div>
        <div class="col-md-4"><label class="form-label fw-bold" for="annual_points">Annual points</label><input class="form-control" inputmode="numeric" id="annual_points" name="annual_points" value="<?php echo $val('annual_points'); ?>"></div>
        <div class="col-12"><label class="form-label fw-bold" for="short_description">Short description</label><textarea class="form-control" id="short_description" name="short_description" rows="3"><?php echo $val('short_description'); ?></textarea></div>
        <div class="col-12"><label class="form-label fw-bold" for="full_description">Full description</label><textarea class="form-control" id="full_description" name="full_description" rows="5"><?php echo $val('full_description'); ?></textarea><div class="form-text">Plain text, shown on the pass page when filled in.</div></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="resorts_access">Resort access network</label><input class="form-control" id="resorts_access" name="resorts_access" maxlength="190" value="<?php echo $val('resorts_access'); ?>" placeholder="e.g. 200+ Resorts Worldwide"></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="advance_booking">Advance booking requirement</label><input class="form-control" id="advance_booking" name="advance_booking" maxlength="190" value="<?php echo $val('advance_booking'); ?>" placeholder="e.g. 25 to 45 days prior notice"></div>
        <div class="col-md-4"><label class="form-label fw-bold" for="banking_years">Points banking (years)</label><input class="form-control" id="banking_years" name="banking_years" maxlength="40" value="<?php echo $val('banking_years'); ?>" placeholder="e.g. 3 or Lifetime Rolling"></div>
        <div class="col-md-4"><label class="form-label fw-bold" for="dining_discount">Dining &amp; spa discount</label><input class="form-control" id="dining_discount" name="dining_discount" maxlength="60" value="<?php echo $val('dining_discount'); ?>" placeholder="e.g. 20%"></div>
        <div class="col-md-4"><label class="form-label fw-bold" for="concierge">Concierge</label><input class="form-control" id="concierge" name="concierge" maxlength="120" value="<?php echo $val('concierge'); ?>" placeholder="e.g. 24/7 Senior Lifestyle Butler"></div>
        <div class="col-12"><label class="form-label fw-bold" for="terms">Terms &amp; conditions for this pass</label><textarea class="form-control" id="terms" name="terms" rows="4"><?php echo $val('terms'); ?></textarea><div class="form-text">Optional. The terms that apply to every pass are under "Core terms".</div></div>
      </div></div></div>

      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Benefits</h2><button class="btn btn-sm btn-cy" type="button" id="addBenefit"><i class="bi bi-plus-lg"></i> Add benefit</button></div><div class="cy-card-body">
        <div id="benefits"><?php foreach ($benefits as $b) { echo $benefitRow($b); } ?></div>
        <template id="benefitTpl"><?php echo $benefitRow([]); ?></template>
        <div class="form-text">Listed on the pass page in this order. Empty rows are ignored.</div>
      </div></div>
    </div>

    <div class="col-xl-4">
      <div class="cy-card mb-3" style="position: sticky; top: 76px;">
        <div class="cy-card-head"><h2>Publishing</h2></div>
        <div class="cy-card-body">
          <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" role="switch" id="is_published" name="is_published" value="1" <?php echo !empty($v['is_published']) ? 'checked' : ''; ?>><label class="form-check-label fw-bold" for="is_published">Published on website</label></div>
          <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" role="switch" id="featured" name="featured" value="1" <?php echo !empty($v['featured']) ? 'checked' : ''; ?>><label class="form-check-label fw-bold" for="featured">Featured ("Most popular")</label></div>
          <div class="mb-3"><label class="form-label fw-bold" for="display_order">Display order</label><input class="form-control" type="number" min="0" id="display_order" name="display_order" value="<?php echo (int)($v['display_order'] ?? 0); ?>"><div class="form-text">1 shows first.</div></div>
          <hr>
          <h3 class="h6 fw-bold">Pass banner image</h3>
          <?php if (!empty($pass['image_path'])): ?>
            <img src="<?php echo e(content_image_url($pass['image_path'])); ?>" alt="" style="width:100%;border-radius:12px;" class="mb-1">
            <label class="form-check small text-danger mb-2"><input class="form-check-input" type="checkbox" name="remove_image" value="1"> Remove image</label>
          <?php endif; ?>
          <input class="form-control mb-1" type="file" name="image" accept="image/jpeg,image/png,image/webp" aria-label="Pass image">
          <div class="form-text mb-2">JPG, PNG or WebP, max 2 MB. Shown in the pass banners on the homepage and Passes page.</div>
          <input class="form-control form-control-sm mb-3" name="image_alt" maxlength="190" placeholder="Image alt text" value="<?php echo $val('image_alt'); ?>" aria-label="Image alt text">
          <hr>
          <h3 class="h6 fw-bold">Search engines</h3>
          <div class="mb-3"><label class="form-label small fw-bold" for="meta_title">Meta title</label><input class="form-control" id="meta_title" name="meta_title" maxlength="255" value="<?php echo $val('meta_title'); ?>"></div>
          <div class="mb-3"><label class="form-label small fw-bold" for="meta_description">Meta description</label><textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="500"><?php echo $val('meta_description'); ?></textarea></div>
          <div class="d-grid gap-2">
            <button class="btn btn-cy btn-lg" type="submit"><i class="bi bi-check2-circle"></i> <?php echo $pass ? 'Save changes' : 'Create pass'; ?></button>
            <a class="btn btn-light border" href="index.php">Cancel</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<?php
$js = '<script>
(function () {
  var list = document.getElementById("benefits"), tpl = document.getElementById("benefitTpl");
  document.getElementById("addBenefit").addEventListener("click", function () {
    var n = tpl.content.firstElementChild.cloneNode(true); list.appendChild(n); n.querySelector("input").focus();
  });
  list.addEventListener("click", function (e) {
    var row = e.target.closest("[data-benefit]"); if (!row) { return; }
    if (e.target.closest("[data-remove]")) {
      if (list.querySelectorAll("[data-benefit]").length > 1) { row.remove(); } else { row.querySelectorAll("input").forEach(function (i) { i.value = ""; }); }
    } else if (e.target.closest("[data-up]") && row.previousElementSibling) { list.insertBefore(row, row.previousElementSibling); }
    else if (e.target.closest("[data-down]") && row.nextElementSibling) { list.insertBefore(row.nextElementSibling, row); }
  });
})();
</script>';
admin_footer($js);
