<?php
/**
 * Club Yatri Admin - Destinations: add / edit form (cover photo, gallery, map pin).
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/crud.php';

$pdo = admin_pdo();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$dest = null;
if ($id > 0) {
    $st = $pdo->prepare('SELECT * FROM destinations WHERE id = ?');
    $st->execute([$id]);
    $dest = $st->fetch() ?: null;
    if (!$dest) {
        admin_flash('error', 'That destination could not be found.');
        admin_redirect('destinations/index.php');
    }
}
$pin = null;
if ($dest) {
    $st = $pdo->prepare('SELECT latitude, longitude, map_x FROM destination_map WHERE slug = ?');
    $st->execute([$dest['slug']]);
    $pin = $st->fetch() ?: null;
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
        $name = admin_str('name', 160);
        $country = admin_str('country', 80);
        $type = admin_str('location_type', 16);
        $type = in_array($type, ['DOMESTIC', 'INTERNATIONAL'], true) ? $type : ($country === 'India' ? 'DOMESTIC' : 'INTERNATIONAL');
        $lat = admin_coord('latitude', -90, 90);
        $lng = admin_coord('longitude', -180, 180);
        $latRaw = trim((string)($_POST['latitude'] ?? ''));
        $lngRaw = trim((string)($_POST['longitude'] ?? ''));
        $source = admin_str('source_url', 500);
        $highlights = array_filter(array_map('trim', preg_split('/[\r\n;]+/', (string)($_POST['highlights'] ?? '')) ?: []));

        if ($name === '') { $errors[] = 'Destination name is required.'; }
        if ($country === '') { $errors[] = 'Country is required.'; }
        if (($latRaw !== '') !== ($lngRaw !== '')) { $errors[] = 'Enter both latitude and longitude for the map pin, or leave both empty.'; }
        elseif ($latRaw !== '' && ($lat === null || $lng === null)) { $errors[] = 'Latitude must be -90 to 90 and longitude -180 to 180.'; }
        if ($source !== '' && !preg_match('~^https://~i', $source)) { $errors[] = 'The photo credit link must start with https://'; }

        if (!$errors) {
            $slug = admin_unique_slug($pdo, 'destinations', admin_slugify(admin_str('slug', 160)) ?: admin_slugify($name), $id);
            $data = [
                'name' => $name, 'slug' => $slug, 'country' => $country, 'region' => admin_str('region', 80) ?: null, 'location_type' => $type,
                'tagline' => admin_str('tagline', 255) ?: null, 'description' => trim((string)($_POST['description'] ?? '')) ?: null,
                'highlights' => implode('; ', $highlights) ?: null, 'best_time' => admin_str('best_time', 255) ?: null,
                'meta_title' => admin_str('meta_title', 255) ?: null, 'meta_description' => admin_str('meta_description', 500) ?: null,
                'source_url' => $source ?: null, 'featured' => admin_flag('featured'), 'display_order' => max(0, admin_int('display_order')),
                'is_published' => admin_flag('is_published'),
            ];

            $removed = [];
            $stored = [];
            $warnings = [];
            $saved = false;
            try {
                $pdo->beginTransaction();
                $oldName = $dest['name'] ?? null;
                $oldSlug = $dest['slug'] ?? null;

                // cover photo
                $hero = $dest['hero_image'] ?? null;
                if (admin_flag('remove_hero') && $hero) {
                    $removed[] = (string)$hero;
                    $hero = null;
                }
                if (!empty($_FILES['hero']['name'])) {
                    $r = admin_save_image($_FILES['hero'], 'places', 'Cover photo');
                    if ($r['ok']) {
                        if ($hero) { $removed[] = (string)$hero; }
                        $hero = $r['path'];
                        $stored[] = $r['path'];
                    } elseif ($r['error'] !== '') {
                        $warnings[] = $r['error'];
                    }
                }
                $data['hero_image'] = $hero;

                if ($dest) {
                    $set = implode(', ', array_map(static fn($c) => "`$c` = ?", array_keys($data)));
                    $pdo->prepare("UPDATE destinations SET $set WHERE id = ?")->execute([...array_values($data), $id]);
                    // keep everything that refers to this destination by name or slug in step
                    if ($oldName !== $name) {
                        $pdo->prepare('UPDATE hotels SET location_name = ? WHERE destination_id = ?')->execute([$name, $id]);
                    }
                    if ($oldSlug !== $slug) {
                        $pdo->prepare('UPDATE itineraries SET destination_slug = ? WHERE destination_slug = ?')->execute([$slug, $oldSlug]);
                        $pdo->prepare('UPDATE destination_map SET slug = ? WHERE slug = ?')->execute([$slug, $oldSlug]);
                    }
                } else {
                    $cols = implode(', ', array_map(static fn($c) => "`$c`", array_keys($data)));
                    $pdo->prepare("INSERT INTO destinations ($cols) VALUES (" . implode(',', array_fill(0, count($data), '?')) . ')')->execute(array_values($data));
                    $id = (int)$pdo->lastInsertId();
                }

                // map pin
                if ($lat !== null && $lng !== null) {
                    $xy = admin_globe_pin((float)$lat, (float)$lng);
                    $pdo->prepare('INSERT INTO destination_map (slug, latitude, longitude, map_x, map_y) VALUES (?,?,?,?,?)
                                   ON DUPLICATE KEY UPDATE latitude = VALUES(latitude), longitude = VALUES(longitude), map_x = VALUES(map_x), map_y = VALUES(map_y)')
                        ->execute([$slug, $lat, $lng, $xy[0] ?? null, $xy[1] ?? null]);
                    if (!$xy) {
                        $warnings[] = 'Saved the coordinates, but that place is outside the part of the world drawn on the globe, so it gets no pin.';
                    }
                } else {
                    $pdo->prepare('DELETE FROM destination_map WHERE slug = ?')->execute([$slug]);
                }

                admin_gallery_apply($pdo, 'destination_images', 'destination_id', $id, 'places/' . $slug, $removed, $stored, $warnings);
                $pdo->commit();
                $saved = true;
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                foreach ($stored as $p) {
                    admin_delete_uploaded($pdo, $p);
                }
                if (!$dest) {
                    $id = 0;
                }
                error_log('Destination save failed: ' . $e->getMessage());
                $errors[] = 'The destination could not be saved. Please check the details and try again.';
            }

            if ($saved) {
                foreach ($removed as $p) {
                    admin_delete_uploaded($pdo, $p);
                }
                admin_flash('success', $dest ? 'Destination updated.' : 'Destination added.');
                foreach ($warnings as $w) {
                    admin_flash('warning', $w);
                }
                admin_redirect('destinations/edit.php?id=' . $id);
            }
        }
    }
}

$v = $old ?? ($dest ?: ['is_published' => 1, 'location_type' => 'DOMESTIC', 'country' => 'India', 'display_order' => 0]);
$val = static fn(string $k, string $d = ''): string => e((string)($v[$k] ?? $d));
$hlText = $old ? (string)($old['highlights'] ?? '') : implode("\n", array_filter(array_map('trim', explode(';', (string)($v['highlights'] ?? '')))));
$lat = $old ? (string)($old['latitude'] ?? '') : (string)($pin['latitude'] ?? '');
$lng = $old ? (string)($old['longitude'] ?? '') : (string)($pin['longitude'] ?? '');
$gallery = [];
$hotelCount = 0;
if ($dest) {
    $st = $pdo->prepare('SELECT * FROM destination_images WHERE destination_id = ? ORDER BY sort_order, id');
    $st->execute([$id]);
    $gallery = $st->fetchAll();
    $hotelCount = admin_count($pdo, 'SELECT COUNT(*) FROM hotels WHERE destination_id = ? AND is_archived = 0', [$id]);
}

foreach ($errors as $err) {
    admin_flash('error', $err);
}
admin_header($dest ? 'Edit destination' : 'Add destination', 'destinations', [['Destinations', 'destinations/index.php'], [$dest ? (string)$dest['name'] : 'New destination', null]],
    $dest ? 'Changes appear on the website as soon as you save.' : 'Create a destination for the Destinations pages and map.');
?>
<form method="post" action="" enctype="multipart/form-data" novalidate>
  <?php echo admin_csrf_field(); ?>
  <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
  <div class="row g-3">
    <div class="col-xl-8">
      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Basic information</h2></div><div class="cy-card-body"><div class="row g-3">
        <div class="col-md-8"><label class="form-label fw-bold" for="name">Destination name *</label><input class="form-control" id="name" name="name" maxlength="160" required value="<?php echo $val('name'); ?>"></div>
        <div class="col-md-4"><label class="form-label fw-bold" for="location_type">Classification</label><select class="form-select" id="location_type" name="location_type">
          <option value="DOMESTIC" <?php echo ($v['location_type'] ?? '') === 'DOMESTIC' ? 'selected' : ''; ?>>India (domestic)</option>
          <option value="INTERNATIONAL" <?php echo ($v['location_type'] ?? '') === 'INTERNATIONAL' ? 'selected' : ''; ?>>International</option></select></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="slug">URL slug</label><input class="form-control" id="slug" name="slug" maxlength="160" value="<?php echo $val('slug'); ?>" placeholder="auto-generated from the name">
          <div class="form-text">Used in the page address.<?php echo $dest ? ' Changing it also updates the map pin and any itineraries that use it.' : ''; ?></div></div>
        <div class="col-md-3"><label class="form-label fw-bold" for="country">Country *</label><input class="form-control" id="country" name="country" maxlength="80" value="<?php echo $val('country'); ?>"></div>
        <div class="col-md-3"><label class="form-label fw-bold" for="region">State / region</label><input class="form-control" id="region" name="region" maxlength="80" value="<?php echo $val('region'); ?>"></div>
        <div class="col-12"><label class="form-label fw-bold" for="tagline">Short description</label><input class="form-control" id="tagline" name="tagline" maxlength="255" value="<?php echo $val('tagline'); ?>"><div class="form-text">One line shown under the destination name.</div></div>
        <div class="col-12"><label class="form-label fw-bold" for="description">Full description</label><textarea class="form-control" id="description" name="description" rows="8"><?php echo $val('description'); ?></textarea><div class="form-text">Plain text.</div></div>
        <div class="col-12"><label class="form-label fw-bold" for="highlights">Top attractions</label><textarea class="form-control" id="highlights" name="highlights" rows="5" placeholder="One attraction per line"><?php echo e($hlText); ?></textarea></div>
        <div class="col-12"><label class="form-label fw-bold" for="best_time">Best time to visit</label><input class="form-control" id="best_time" name="best_time" maxlength="255" value="<?php echo $val('best_time'); ?>" placeholder="e.g. October to March"></div>
      </div></div></div>

      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Map pin</h2><span class="text-muted small">Shown on the globe map</span></div><div class="cy-card-body"><div class="row g-3">
        <div class="col-md-6"><label class="form-label fw-bold" for="latitude">Latitude</label><input class="form-control" id="latitude" name="latitude" inputmode="decimal" value="<?php echo e($lat); ?>" placeholder="e.g. 15.5000"></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="longitude">Longitude</label><input class="form-control" id="longitude" name="longitude" inputmode="decimal" value="<?php echo e($lng); ?>" placeholder="e.g. 73.8300"></div>
        <div class="col-12 form-text mt-0">Find these in Google Maps: right-click the place and click the numbers at the top of the menu. The pin position on the globe is worked out automatically. Leave both empty for no pin.<?php echo ($pin && $pin['map_x'] === null) ? ' <strong>This place is currently outside the drawn part of the globe, so it has no pin.</strong>' : ''; ?></div>
      </div></div></div>

      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Photos</h2><span class="text-muted small">JPG, PNG or WebP &middot; max 2 MB each</span></div><div class="cy-card-body">
        <h3 class="h6 fw-bold">Cover photo</h3>
        <div class="d-flex flex-wrap gap-3 align-items-start mb-4">
          <?php if (!empty($dest['hero_image'])): ?>
            <div><img src="<?php echo e(url((string)$dest['hero_image'])); ?>" alt="" style="width:200px;height:130px;object-fit:cover;border-radius:12px;">
              <label class="form-check mt-1 text-danger small"><input class="form-check-input" type="checkbox" name="remove_hero" value="1"> Remove cover</label></div>
          <?php endif; ?>
          <div class="flex-grow-1" style="min-width:220px"><label class="form-label small fw-bold" for="hero"><?php echo !empty($dest['hero_image']) ? 'Replace cover photo' : 'Upload cover photo'; ?></label>
            <input class="form-control" type="file" id="hero" name="hero" accept="image/jpeg,image/png,image/webp">
            <div class="form-text">Wide photos work best. It appears in the banner and on destination cards.</div></div>
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
          <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" role="switch" id="featured" name="featured" value="1" <?php echo !empty($v['featured']) ? 'checked' : ''; ?>><label class="form-check-label fw-bold" for="featured">Featured (listed first)</label></div>
          <div class="mb-3"><label class="form-label fw-bold" for="display_order">Display order</label><input class="form-control" type="number" min="0" id="display_order" name="display_order" value="<?php echo (int)($v['display_order'] ?? 0); ?>"><div class="form-text">1 shows first. 0 = default order (busiest first).</div></div>
          <?php if ($dest): ?><p class="small text-muted mb-3"><i class="bi bi-buildings"></i> <?php echo (int)$hotelCount; ?> hotel<?php echo $hotelCount === 1 ? '' : 's'; ?> in this destination. <a href="<?php echo e(admin_url('hotels/index.php?destination=' . $id)); ?>">View</a></p><?php endif; ?>
          <div class="mb-3"><label class="form-label small fw-bold" for="source_url">Photo credit link</label><input class="form-control" id="source_url" name="source_url" maxlength="500" value="<?php echo $val('source_url'); ?>" placeholder="https://commons.wikimedia.org/…"><div class="form-text">Only Wikimedia Commons links are shown as "Photo: Wikimedia Commons". Leave empty for your own photos.</div></div>
          <hr>
          <h3 class="h6 fw-bold">Search engines</h3>
          <div class="mb-3"><label class="form-label small fw-bold" for="meta_title">Meta title</label><input class="form-control" id="meta_title" name="meta_title" maxlength="255" value="<?php echo $val('meta_title'); ?>"></div>
          <div class="mb-3"><label class="form-label small fw-bold" for="meta_description">Meta description</label><textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="500"><?php echo $val('meta_description'); ?></textarea></div>
          <div class="d-grid gap-2">
            <button class="btn btn-cy btn-lg" type="submit"><i class="bi bi-check2-circle"></i> <?php echo $dest ? 'Save changes' : 'Create destination'; ?></button>
            <a class="btn btn-light border" href="index.php">Cancel</a>
            <?php if ($dest && $dest['is_published'] && !$dest['is_archived']): ?><a class="btn btn-link" target="_blank" rel="noopener" href="<?php echo e(url('destination-details.php?slug=' . urlencode((string)$dest['slug']))); ?>"><i class="bi bi-box-arrow-up-right"></i> View on website</a><?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<?php admin_footer(admin_gallery_script()); ?>
