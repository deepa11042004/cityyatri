<?php
/**
 * Club Yatri Admin - Rooms & Suites: add / edit a room category (photos, amenities).
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/crud.php';
require_once __DIR__ . '/../../includes/content-db.php';

$pdo = admin_pdo();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$room = null;
if ($id > 0) {
    $st = $pdo->prepare('SELECT * FROM room_categories WHERE id = ?');
    $st->execute([$id]);
    $room = $st->fetch() ?: null;
    if (!$room) {
        admin_flash('error', 'That room category could not be found.');
        admin_redirect('rooms/index.php');
    }
}

$errors = [];
$old = null;

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (admin_post_too_large()) {
        $errors[] = 'The files you selected add up to more than the server allows in one go. Upload fewer or smaller photos (max 2 MB each) and try again.';
    } elseif (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Your session expired. Please try again.';
    } else {
        $old = $_POST;
        $title = admin_str('title', 190);
        $maxAdults = max(0, min(30, admin_int('max_adults')));
        $maxChildren = max(0, min(30, admin_int('max_children')));
        $points = trim((string)($_POST['points_night'] ?? ''));
        $amen = array_values(array_filter(array_map(static fn($l) => mb_substr(trim($l), 0, 120), preg_split('/[\r\n]+/', (string)($_POST['amenities'] ?? '')) ?: [])));

        if ($title === '') { $errors[] = 'Room name is required.'; }
        if ($points !== '' && (!ctype_digit($points) || (int)$points > 1000000)) { $errors[] = 'Points per night must be a whole number.'; }
        if (!$room && empty($_FILES['image']['name'])) { $errors[] = 'Please upload a main image for the new room category.'; }

        if (!$errors) {
            $slug = admin_unique_slug($pdo, 'room_categories', admin_slugify(admin_str('slug', 120)) ?: admin_slugify($title), $id);
            $data = [
                'slug' => $slug, 'title' => $title, 'short_description' => admin_str('short_description', 500) ?: null,
                'description' => trim((string)($_POST['description'] ?? '')) ?: null, 'area' => admin_str('area', 80) ?: null,
                'bed_type' => admin_str('bed_type', 120) ?: null, 'capacity' => admin_str('capacity', 120), 'max_adults' => $maxAdults,
                'max_children' => $maxChildren, 'points_night' => $points !== '' ? (int)$points : null, 'featured' => admin_flag('featured'),
                'display_order' => max(0, admin_int('display_order')), 'is_published' => admin_flag('is_published'),
            ];
            $removed = [];
            $stored = [];
            $warnings = [];
            $saved = false;
            try {
                $pdo->beginTransaction();
                $image = $room['image_path'] ?? null;
                if (!empty($_FILES['image']['name'])) {
                    $r = admin_save_image($_FILES['image'], 'rooms/' . $slug, 'Main image');
                    if ($r['ok']) {
                        if ($image && !preg_match('~^https?://~i', (string)$image)) { $removed[] = (string)$image; }
                        $image = $r['path'];
                        $stored[] = $r['path'];
                    } elseif ($r['error'] !== '') {
                        if (!$room) { throw new RuntimeException($r['error']); }
                        $warnings[] = $r['error'];
                    }
                }
                $data['image_path'] = $image;
                if ($room) {
                    $set = implode(', ', array_map(static fn($c) => "`$c` = ?", array_keys($data)));
                    $pdo->prepare("UPDATE room_categories SET $set WHERE id = ?")->execute([...array_values($data), $id]);
                } else {
                    $cols = implode(', ', array_map(static fn($c) => "`$c`", array_keys($data)));
                    $pdo->prepare("INSERT INTO room_categories ($cols) VALUES (" . implode(',', array_fill(0, count($data), '?')) . ')')->execute(array_values($data));
                    $id = (int)$pdo->lastInsertId();
                }
                $pdo->prepare('DELETE FROM room_amenities WHERE room_id = ?')->execute([$id]);
                foreach ($amen as $n => $a) {
                    $pdo->prepare('INSERT INTO room_amenities (room_id, name, sort_order) VALUES (?,?,?)')->execute([$id, $a, $n + 1]);
                }
                admin_gallery_apply($pdo, 'room_images', 'room_id', $id, 'rooms/' . $slug, $removed, $stored, $warnings);
                $pdo->commit();
                $saved = true;
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) { $pdo->rollBack(); }
                foreach ($stored as $p) { admin_delete_uploaded($pdo, $p); }
                if (!$room) { $id = 0; }
                if ($e instanceof RuntimeException) {
                    $errors[] = $e->getMessage();
                } else {
                    error_log('Room save failed: ' . $e->getMessage());
                    $errors[] = 'The room category could not be saved. Please check the details and try again.';
                }
            }
            if ($saved) {
                foreach ($removed as $p) { admin_delete_uploaded($pdo, $p); }
                admin_flash('success', $room ? 'Room category updated.' : 'Room category added.');
                foreach ($warnings as $w) { admin_flash('warning', $w); }
                admin_redirect('rooms/edit.php?id=' . $id);
            }
        }
    }
}

$v = $old ?? ($room ?: ['is_published' => 1, 'display_order' => 0]);
$val = static fn(string $k, string $d = ''): string => e((string)($v[$k] ?? $d));
$amenText = '';
$gallery = [];
if ($old) {
    $amenText = (string)($old['amenities'] ?? '');
} elseif ($room) {
    $st = $pdo->prepare('SELECT name FROM room_amenities WHERE room_id = ? ORDER BY sort_order, id');
    $st->execute([$id]);
    $amenText = implode("\n", $st->fetchAll(PDO::FETCH_COLUMN));
}
if ($room) {
    $st = $pdo->prepare('SELECT * FROM room_images WHERE room_id = ? ORDER BY sort_order, id');
    $st->execute([$id]);
    $gallery = $st->fetchAll();
}

foreach ($errors as $err) {
    admin_flash('error', $err);
}
admin_header($room ? 'Edit room category' : 'Add room category', 'rooms', [['Rooms & Suites', 'rooms/index.php'], [$room ? (string)$room['title'] : 'New room category', null]],
    $room ? 'Changes appear on the website as soon as you save.' : 'Create an accommodation category for the website.');
?>
<form method="post" action="" enctype="multipart/form-data" novalidate>
  <?php echo admin_csrf_field(); ?>
  <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
  <div class="row g-3">
    <div class="col-xl-8">
      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Room details</h2></div><div class="cy-card-body"><div class="row g-3">
        <div class="col-md-8"><label class="form-label fw-bold" for="title">Room name *</label><input class="form-control" id="title" name="title" maxlength="190" required value="<?php echo $val('title'); ?>"></div>
        <div class="col-md-4"><label class="form-label fw-bold" for="slug">URL slug</label><input class="form-control" id="slug" name="slug" maxlength="120" value="<?php echo $val('slug'); ?>" placeholder="auto"></div>
        <div class="col-12"><label class="form-label fw-bold" for="short_description">Short description</label><input class="form-control" id="short_description" name="short_description" maxlength="500" value="<?php echo $val('short_description'); ?>"><div class="form-text">One line shown under the room name.</div></div>
        <div class="col-12"><label class="form-label fw-bold" for="description">Full description</label><textarea class="form-control" id="description" name="description" rows="6"><?php echo $val('description'); ?></textarea><div class="form-text">Plain text.</div></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="area">Room size</label><input class="form-control" id="area" name="area" maxlength="80" value="<?php echo $val('area'); ?>" placeholder="e.g. 450 - 580 sq.ft."></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="bed_type">Bed type</label><input class="form-control" id="bed_type" name="bed_type" maxlength="120" value="<?php echo $val('bed_type'); ?>" placeholder="e.g. Plush King Bed / Twin Options"></div>
        <div class="col-md-3"><label class="form-label fw-bold" for="max_adults">Max adults</label><input class="form-control" type="number" min="0" max="30" id="max_adults" name="max_adults" value="<?php echo $val('max_adults', '0'); ?>"></div>
        <div class="col-md-3"><label class="form-label fw-bold" for="max_children">Max children</label><input class="form-control" type="number" min="0" max="30" id="max_children" name="max_children" value="<?php echo $val('max_children', '0'); ?>"></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="capacity">Capacity label</label><input class="form-control" id="capacity" name="capacity" maxlength="120" value="<?php echo $val('capacity'); ?>" placeholder="e.g. 2 Adults + 1 Child"><div class="form-text">Leave empty to build it from the adult and child numbers.</div></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="points_night">Points per night</label><input class="form-control" inputmode="numeric" id="points_night" name="points_night" value="<?php echo $val('points_night'); ?>"></div>
        <div class="col-12"><label class="form-label fw-bold" for="amenities">Amenities</label><textarea class="form-control" id="amenities" name="amenities" rows="6" placeholder="One per line"><?php echo e($amenText); ?></textarea></div>
      </div></div></div>

      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Photos</h2><span class="text-muted small">JPG, PNG or WebP &middot; max 2 MB each</span></div><div class="cy-card-body">
        <h3 class="h6 fw-bold">Main image <?php echo $room ? '' : '*'; ?></h3>
        <div class="d-flex flex-wrap gap-3 align-items-start mb-4">
          <?php if (!empty($room['image_path'])): ?><img src="<?php echo e(content_image_url($room['image_path'])); ?>" alt="" style="width:200px;height:130px;object-fit:cover;border-radius:12px;"><?php endif; ?>
          <div class="flex-grow-1" style="min-width:220px"><label class="form-label small fw-bold" for="image"><?php echo $room ? 'Replace main image' : 'Upload main image'; ?></label>
            <input class="form-control" type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"><div class="form-text">Shown on room cards and at the top of the room page.</div></div>
        </div>
        <h3 class="h6 fw-bold">Gallery</h3>
        <?php echo admin_gallery_fields($gallery); ?>
      </div></div>
    </div>
    <div class="col-xl-4">
      <div class="cy-card mb-3" style="position: sticky; top: 76px;">
        <div class="cy-card-head"><h2>Publishing</h2></div>
        <div class="cy-card-body">
          <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" role="switch" id="is_published" name="is_published" value="1" <?php echo !empty($v['is_published']) ? 'checked' : ''; ?>><label class="form-check-label fw-bold" for="is_published">Published on website</label></div>
          <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" role="switch" id="featured" name="featured" value="1" <?php echo !empty($v['featured']) ? 'checked' : ''; ?>><label class="form-check-label fw-bold" for="featured">Featured</label></div>
          <div class="mb-3"><label class="form-label fw-bold" for="display_order">Display order</label><input class="form-control" type="number" min="0" id="display_order" name="display_order" value="<?php echo (int)($v['display_order'] ?? 0); ?>"><div class="form-text">1 shows first.</div></div>
          <div class="d-grid gap-2">
            <button class="btn btn-cy btn-lg" type="submit"><i class="bi bi-check2-circle"></i> <?php echo $room ? 'Save changes' : 'Create room category'; ?></button>
            <a class="btn btn-light border" href="index.php">Cancel</a>
            <?php if ($room && $room['is_published'] && !$room['is_archived']): ?><a class="btn btn-link" target="_blank" rel="noopener" href="<?php echo e(url('room-details.php?id=' . urlencode((string)$room['slug']))); ?>"><i class="bi bi-box-arrow-up-right"></i> View on website</a><?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<?php admin_footer(admin_gallery_script()); ?>
