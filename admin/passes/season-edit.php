<?php
/**
 * Club Yatri Admin - add / edit a seasonal pass.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/crud.php';
require_once __DIR__ . '/../../includes/content-db.php';
require_once __DIR__ . '/_tabs.php';

$pdo = admin_pdo();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$s = null;
if ($id > 0) {
    $st = $pdo->prepare('SELECT * FROM pass_seasons WHERE id = ?');
    $st->execute([$id]);
    $s = $st->fetch() ?: null;
    if (!$s) {
        admin_flash('error', 'That seasonal pass could not be found.');
        admin_redirect('passes/seasons.php');
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
        $name = admin_str('name', 120);
        $color = strtoupper(admin_str('color', 9));
        if ($name === '') { $errors[] = 'Name is required.'; }
        if (!preg_match('/^#[0-9A-F]{6}$/', $color)) { $color = '#EA2224'; }

        if (!$errors) {
            $slug = admin_slugify(admin_str('slug', 40)) ?: admin_slugify($name);
            $slug = substr($slug, 0, 40);
            // slug must be unique in pass_seasons
            $base = $slug;
            for ($i = 2; ; $i++) {
                $c = $pdo->prepare('SELECT COUNT(*) FROM pass_seasons WHERE slug = ? AND id <> ?');
                $c->execute([$slug, $id]);
                if (!(int)$c->fetchColumn()) { break; }
                $slug = substr($base, 0, 36) . '-' . $i;
            }
            $data = [
                'slug' => $slug, 'name' => $name, 'season_label' => admin_str('season_label', 120), 'price_display' => admin_str('price_display', 60),
                'access_text' => trim((string)($_POST['access_text'] ?? '')) ?: null, 'experience_text' => trim((string)($_POST['experience_text'] ?? '')) ?: null,
                'blackout_text' => admin_str('blackout_text', 255) ?: null, 'color' => $color, 'image_alt' => admin_str('image_alt', 190) ?: null,
                'is_published' => admin_flag('is_published'),
            ];
            $removed = [];
            $stored = [];
            $warnings = [];
            $saved = false;
            try {
                $pdo->beginTransaction();
                $image = $s['image_path'] ?? null;
                if (admin_flag('remove_image') && $image) {
                    $removed[] = (string)$image;
                    $image = null;
                }
                if (!empty($_FILES['image']['name'])) {
                    $r = admin_save_image($_FILES['image'], 'passes', 'Image');
                    if ($r['ok']) {
                        if ($image) { $removed[] = (string)$image; }
                        $image = $r['path'];
                        $stored[] = $r['path'];
                    } elseif ($r['error'] !== '') {
                        $warnings[] = $r['error'];
                    }
                }
                $data['image_path'] = $image;
                if ($s) {
                    $set = implode(', ', array_map(static fn($c) => "`$c` = ?", array_keys($data)));
                    $pdo->prepare("UPDATE pass_seasons SET $set WHERE id = ?")->execute([...array_values($data), $id]);
                } else {
                    $data['sort_order'] = 1 + (int)$pdo->query('SELECT COALESCE(MAX(sort_order), 0) FROM pass_seasons')->fetchColumn();
                    $cols = implode(', ', array_map(static fn($c) => "`$c`", array_keys($data)));
                    $pdo->prepare("INSERT INTO pass_seasons ($cols) VALUES (" . implode(',', array_fill(0, count($data), '?')) . ')')->execute(array_values($data));
                    $id = (int)$pdo->lastInsertId();
                }
                $pdo->commit();
                $saved = true;
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) { $pdo->rollBack(); }
                foreach ($stored as $p) { admin_delete_uploaded($pdo, $p); }
                if (!$s) { $id = 0; }
                error_log('Season save failed: ' . $e->getMessage());
                $errors[] = 'The seasonal pass could not be saved. Please try again.';
            }
            if ($saved) {
                foreach ($removed as $p) { admin_delete_uploaded($pdo, $p); }
                admin_flash('success', $s ? 'Seasonal pass updated.' : 'Seasonal pass added.');
                foreach ($warnings as $w) { admin_flash('warning', $w); }
                admin_redirect('passes/season-edit.php?id=' . $id);
            }
        }
    }
}

$v = $old ?? ($s ?: ['is_published' => 1, 'color' => '#EA2224']);
$val = static fn(string $k, string $d = ''): string => e((string)($v[$k] ?? $d));
foreach ($errors as $err) {
    admin_flash('error', $err);
}
admin_header($s ? 'Edit seasonal pass' : 'Add seasonal pass', 'passes', [['Passes & Subscriptions', 'passes/index.php'], ['Seasonal passes', 'passes/seasons.php'], [$s ? (string)$s['name'] : 'New', null]],
    'One card of the Exclusive Pass Collection.');
?>
<form method="post" action="" enctype="multipart/form-data" novalidate>
  <?php echo admin_csrf_field(); ?>
  <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
  <div class="row g-3">
    <div class="col-xl-8">
      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Season pass</h2></div><div class="cy-card-body"><div class="row g-3">
        <div class="col-md-6"><label class="form-label fw-bold" for="name">Name *</label><input class="form-control" id="name" name="name" maxlength="120" required value="<?php echo $val('name'); ?>" placeholder="e.g. Yatri Green Pass"></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="season_label">Season</label><input class="form-control" id="season_label" name="season_label" maxlength="120" value="<?php echo $val('season_label'); ?>" placeholder="e.g. Off-Peak / Monsoon Season"></div>
        <div class="col-md-4"><label class="form-label fw-bold" for="price_display">Price shown</label><input class="form-control" id="price_display" name="price_display" maxlength="60" value="<?php echo $val('price_display'); ?>" placeholder="e.g. ₹6,66,450"></div>
        <div class="col-md-4"><label class="form-label fw-bold" for="color">Colour</label><input class="form-control form-control-color w-100" type="color" id="color" name="color" value="<?php echo $val('color', '#EA2224'); ?>"></div>
        <div class="col-md-4"><label class="form-label fw-bold" for="slug">URL slug</label><input class="form-control" id="slug" name="slug" maxlength="40" value="<?php echo $val('slug'); ?>" placeholder="auto"></div>
        <div class="col-12"><label class="form-label fw-bold" for="access_text">Season access</label><textarea class="form-control" id="access_text" name="access_text" rows="2"><?php echo $val('access_text'); ?></textarea></div>
        <div class="col-12"><label class="form-label fw-bold" for="experience_text">Access &amp; experience</label><textarea class="form-control" id="experience_text" name="experience_text" rows="2"><?php echo $val('experience_text'); ?></textarea></div>
        <div class="col-12"><label class="form-label fw-bold" for="blackout_text">Blackout dates</label><input class="form-control" id="blackout_text" name="blackout_text" maxlength="255" value="<?php echo $val('blackout_text'); ?>"></div>
        <div class="col-12 form-text mt-0">These details are the written version of the card; the website shows the card image below.</div>
      </div></div></div>
    </div>
    <div class="col-xl-4">
      <div class="cy-card mb-3" style="position: sticky; top: 76px;">
        <div class="cy-card-head"><h2>Card image</h2></div>
        <div class="cy-card-body">
          <?php if (!empty($s['image_path'])): ?>
            <img src="<?php echo e(content_image_url($s['image_path'])); ?>" alt="" style="width:100%;border-radius:12px;" class="mb-1">
            <label class="form-check small text-danger mb-2"><input class="form-check-input" type="checkbox" name="remove_image" value="1"> Remove image</label>
          <?php endif; ?>
          <input class="form-control mb-1" type="file" name="image" accept="image/jpeg,image/png,image/webp" aria-label="Card image">
          <div class="form-text mb-2">JPG, PNG or WebP, max 2 MB. A tall poster works best.</div>
          <input class="form-control form-control-sm mb-3" name="image_alt" maxlength="190" placeholder="Image alt text" value="<?php echo $val('image_alt'); ?>" aria-label="Image alt text">
          <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" role="switch" id="is_published" name="is_published" value="1" <?php echo !empty($v['is_published']) ? 'checked' : ''; ?>><label class="form-check-label fw-bold" for="is_published">Published on website</label></div>
          <div class="d-grid gap-2">
            <button class="btn btn-cy btn-lg" type="submit"><i class="bi bi-check2-circle"></i> <?php echo $s ? 'Save changes' : 'Create'; ?></button>
            <a class="btn btn-light border" href="seasons.php">Cancel</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<?php admin_footer(); ?>
