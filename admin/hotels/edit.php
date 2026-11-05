<?php
/**
 * Club Yatri Admin - Hotels: add / edit form with photo manager.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/crud.php';

$pdo = admin_pdo();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$hotel = null;
if ($id > 0) {
    $st = $pdo->prepare('SELECT * FROM hotels WHERE id = ?');
    $st->execute([$id]);
    $hotel = $st->fetch() ?: null;
    if (!$hotel) {
        admin_flash('error', 'That hotel could not be found.');
        admin_redirect('hotels/index.php');
    }
}

$destinations = $pdo->query('SELECT id, name, country, location_type FROM destinations WHERE is_archived = 0 ORDER BY name')->fetchAll();
$destById = array_column($destinations, null, 'id');
$hotelTypes = array_values(array_unique(array_merge(['HOTEL', 'RESORT', 'VILLA', 'APARTMENT'], $pdo->query('SELECT DISTINCT hotel_type FROM hotels')->fetchAll(PDO::FETCH_COLUMN))));
$stayOptions = [];
foreach ($pdo->query("SELECT DISTINCT stay_types FROM hotels WHERE stay_types <> ''")->fetchAll(PDO::FETCH_COLUMN) as $raw) {
    foreach (explode(';', (string)$raw) as $t) {
        if (trim($t) !== '') {
            $stayOptions[trim($t)] = true;
        }
    }
}
$stayOptions = array_keys($stayOptions + array_flip(['hotels', 'resorts', 'villas', 'wellness-retreats', 'heritage-homes', 'castles', 'beach-houses']));
sort($stayOptions);

$errors = [];
$old = null;

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (admin_post_too_large()) {
        $errors[] = 'The files you selected add up to more than the server allows in one go. Upload fewer or smaller photos (max 2 MB each) and try again.';
    } elseif (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Your session expired. Please try again.';
    } else {
        $old = $_POST;
        $name = admin_str('name', 255);
        $destId = admin_int('destination_id');
        $dest = $destById[$destId] ?? null;
        $city = admin_str('city', 80);
        $state = admin_str('state', 80);
        $country = admin_str('country', 64) ?: ($dest['country'] ?? '');
        $locationType = admin_str('location_type', 16);
        if (!in_array($locationType, ['DOMESTIC', 'INTERNATIONAL'], true)) {
            $locationType = $dest['location_type'] ?? ($country === 'India' ? 'DOMESTIC' : 'INTERNATIONAL');
        }
        $hotelType = strtoupper(admin_str('hotel_type', 32)) ?: 'HOTEL';
        $category = admin_str('category', 16) === 'INTERNAL' ? 'INTERNAL' : 'ASSOCIATED';
        $mapLink = admin_str('map_link', 2000);
        $slugIn = admin_slugify(admin_str('slug', 190));
        $amen = array_filter(array_map('trim', preg_split('/[\r\n]+/', (string)($_POST['amenities'] ?? '')) ?: []));
        $stay = array_values(array_intersect($stayOptions, array_map('strval', (array)($_POST['stay_types'] ?? []))));
        $lat = admin_coord('latitude', -90, 90);
        $lng = admin_coord('longitude', -180, 180);

        if ($name === '') { $errors[] = 'Hotel name is required.'; }
        if ($country === '') { $errors[] = 'Country is required (or choose a destination).'; }
        if ($mapLink !== '' && !preg_match('~^https://~i', $mapLink)) { $errors[] = 'The Google Maps URL must start with https://'; }
        if (trim((string)($_POST['latitude'] ?? '')) !== '' && $lat === null) { $errors[] = 'Latitude must be a number between -90 and 90.'; }
        if (trim((string)($_POST['longitude'] ?? '')) !== '' && $lng === null) { $errors[] = 'Longitude must be a number between -180 and 180.'; }

        if (!$errors) {
            $slug = admin_unique_slug($pdo, 'hotels', $slugIn !== '' ? $slugIn : admin_slugify($name), $id);
            $locationName = $dest['name'] ?? ($city !== '' ? $city : $country);
            $data = [
                'name' => $name, 'slug' => $slug, 'destination_id' => $dest ? $destId : null, 'location_name' => $locationName,
                'country' => $country, 'location_type' => $locationType, 'state' => $state ?: null, 'city' => $city ?: null,
                'hotel_type' => $hotelType, 'category' => $category, 'stay_types' => implode('; ', $stay) ?: null,
                'short_description' => admin_str('short_description', 500) ?: null,
                'description' => trim((string)($_POST['description'] ?? '')) ?: null,
                'address' => trim((string)($_POST['address'] ?? '')) ?: null, 'map_link' => $mapLink ?: null,
                'latitude' => $lat, 'longitude' => $lng,
                'amenities' => implode(', ', $amen) ?: null,
                'resort_classification' => admin_str('resort_classification', 128) ?: null,
                'hospitality_rating' => admin_str('hospitality_rating', 64) ?: null,
                'geographic_location' => admin_str('geographic_location', 255) ?: null,
                'airport_proximity' => admin_str('airport_proximity', 255) ?: null,
                'transit_access' => admin_str('transit_access', 255) ?: null,
                'featured' => admin_flag('featured'), 'display_order' => max(0, admin_int('display_order')),
                'meta_title' => admin_str('meta_title', 255) ?: null, 'meta_description' => admin_str('meta_description', 500) ?: null,
                'is_published' => admin_flag('is_published'),
            ];

            $removed = [];
            $saved = false;
            $photoWarnings = [];
            try {
                $pdo->beginTransaction();
                if ($hotel) {
                    $set = implode(', ', array_map(static fn($c) => "`$c` = ?", array_keys($data)));
                    $pdo->prepare("UPDATE hotels SET $set WHERE id = ?")->execute([...array_values($data), $id]);
                } else {
                    $data['hotel_code'] = 'CY' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
                    $cols = implode(', ', array_map(static fn($c) => "`$c`", array_keys($data)));
                    $pdo->prepare("INSERT INTO hotels ($cols) VALUES (" . implode(',', array_fill(0, count($data), '?')) . ')')->execute(array_values($data));
                    $id = (int)$pdo->lastInsertId();
                }

                // --- Photos: edit existing, delete ticked, add new, then renumber (cover first) ---
                $imgs = $pdo->prepare('SELECT id, path FROM hotel_images WHERE hotel_id = ?');
                $imgs->execute([$id]);
                $existing = $imgs->fetchAll();
                $delete = array_map('intval', (array)($_POST['delete_image'] ?? []));
                foreach ($existing as $im) {
                    $iid = (int)$im['id'];
                    if (in_array($iid, $delete, true)) {
                        $pdo->prepare('DELETE FROM hotel_images WHERE id = ? AND hotel_id = ?')->execute([$iid, $id]);
                        $removed[] = $im['path'];
                    } else {
                        $pdo->prepare('UPDATE hotel_images SET alt_text = ?, sort_order = ? WHERE id = ? AND hotel_id = ?')
                            ->execute([mb_substr(trim((string)($_POST['alt'][$iid] ?? '')), 0, 190) ?: null, max(0, (int)($_POST['sort'][$iid] ?? 0)), $iid, $id]);
                    }
                }
                $up = admin_save_images('new_images', 'hotels/' . $slug);
                $photoWarnings = $up['errors'];
                $max = (int)$pdo->query('SELECT COALESCE(MAX(sort_order), 0) FROM hotel_images WHERE hotel_id = ' . $id)->fetchColumn();
                $newIds = [];
                foreach ($up['paths'] as $i => $p) {
                    $pdo->prepare('INSERT INTO hotel_images (hotel_id, path, sort_order) VALUES (?,?,?)')->execute([$id, $p, $max + 1000 + $i]);
                    $newIds[$i] = (int)$pdo->lastInsertId();
                }
                // cover choice: "img:<id>" for an existing photo or "new:<n>" for an uploaded one
                $cover = (string)($_POST['cover'] ?? '');
                $coverId = 0;
                if (preg_match('/^img:(\d+)$/', $cover, $m) && !in_array((int)$m[1], $delete, true)) {
                    $coverId = (int)$m[1];
                } elseif (preg_match('/^new:(\d+)$/', $cover, $m) && isset($newIds[(int)$m[1]])) {
                    $coverId = $newIds[(int)$m[1]];
                }
                $all = $pdo->prepare('SELECT id, path FROM hotel_images WHERE hotel_id = ? ORDER BY sort_order, id');
                $all->execute([$id]);
                $ordered = $all->fetchAll();
                if ($coverId) {
                    usort($ordered, static fn($a, $b) => ((int)$b['id'] === $coverId) <=> ((int)$a['id'] === $coverId));
                }
                foreach ($ordered as $n => $im) {
                    $pdo->prepare('UPDATE hotel_images SET sort_order = ? WHERE id = ?')->execute([$n + 1, $im['id']]);
                }
                $pdo->prepare('UPDATE hotels SET image_path = ? WHERE id = ?')->execute([$ordered[0]['path'] ?? null, $id]);
                $pdo->commit();
                $saved = true;
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                foreach (($up['paths'] ?? []) as $p) {   // photos stored for a save that was rolled back
                    admin_delete_uploaded($pdo, (string)$p);
                }
                if (!$hotel) {
                    $id = 0;
                }
                error_log('Hotel save failed: ' . $e->getMessage());
                $errors[] = 'The hotel could not be saved. Please check the details and try again.';
            }

            if ($saved) {
                foreach ($removed as $p) {
                    admin_delete_uploaded($pdo, (string)$p);
                }
                admin_flash('success', $hotel ? 'Hotel updated.' : 'Hotel added.');
                foreach ($photoWarnings as $w) {
                    admin_flash('warning', $w);
                }
                admin_redirect('hotels/edit.php?id=' . $id);
            }
        }
    }
}

// Values shown in the form: posted values after an error, else the saved hotel, else defaults
$v = $old ?? ($hotel ?: [
    'is_published' => 1, 'category' => 'ASSOCIATED', 'hotel_type' => 'HOTEL', 'location_type' => 'DOMESTIC', 'country' => 'India', 'display_order' => 0,
]);
$val = static fn(string $k, string $d = ''): string => e((string)($v[$k] ?? $d));
$selStay = $old ? array_map('strval', (array)($old['stay_types'] ?? [])) : array_filter(array_map('trim', explode(';', (string)($v['stay_types'] ?? ''))));
$amenText = $old ? (string)($old['amenities'] ?? '') : implode("\n", array_filter(array_map('trim', explode(',', (string)($v['amenities'] ?? '')))));
$images = [];
if ($hotel) {
    $st = $pdo->prepare('SELECT * FROM hotel_images WHERE hotel_id = ? ORDER BY sort_order, id');
    $st->execute([$id]);
    $images = $st->fetchAll();
}

foreach ($errors as $err) {
    admin_flash('error', $err);
}
$title = $hotel ? 'Edit hotel' : 'Add hotel';
admin_header($title, 'hotels', [['Hotels', 'hotels/index.php'], [$hotel ? (string)$hotel['name'] : 'New hotel', null]],
    $hotel ? 'Changes appear on the website as soon as you save.' : 'Create a hotel or resort for the Resort Network.');
?>
<form method="post" action="" enctype="multipart/form-data" novalidate>
  <?php echo admin_csrf_field(); ?>
  <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
  <div class="row g-3">
    <div class="col-xl-8">
      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Basic information</h2></div><div class="cy-card-body"><div class="row g-3">
        <div class="col-12"><label class="form-label fw-bold" for="name">Hotel name *</label>
          <input class="form-control" id="name" name="name" maxlength="255" required value="<?php echo $val('name'); ?>"></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="slug">URL slug</label>
          <input class="form-control" id="slug" name="slug" maxlength="190" value="<?php echo $val('slug'); ?>" placeholder="auto-generated from the name">
          <div class="form-text">Used in the page address. Changing it changes the hotel's web address.</div></div>
        <div class="col-md-3"><label class="form-label fw-bold" for="hotel_type">Property type</label>
          <select class="form-select" id="hotel_type" name="hotel_type"><?php foreach ($hotelTypes as $t): ?><option value="<?php echo e($t); ?>" <?php echo strtoupper((string)($v['hotel_type'] ?? '')) === $t ? 'selected' : ''; ?>><?php echo e(ucfirst(strtolower($t))); ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><label class="form-label fw-bold" for="category">Partner type</label>
          <select class="form-select" id="category" name="category">
            <option value="ASSOCIATED" <?php echo ($v['category'] ?? '') === 'ASSOCIATED' ? 'selected' : ''; ?>>Associated partner</option>
            <option value="INTERNAL" <?php echo ($v['category'] ?? '') === 'INTERNAL' ? 'selected' : ''; ?>>Club Yatri property</option></select></div>
        <div class="col-12"><label class="form-label fw-bold d-block">Stay styles</label>
          <div class="d-flex flex-wrap gap-3"><?php foreach ($stayOptions as $s): ?>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="stay_types[]" id="st_<?php echo e($s); ?>" value="<?php echo e($s); ?>" <?php echo in_array($s, $selStay, true) ? 'checked' : ''; ?>>
              <label class="form-check-label" for="st_<?php echo e($s); ?>"><?php echo e(ucwords(str_replace('-', ' ', $s))); ?></label></div><?php endforeach; ?></div></div>
        <div class="col-12"><label class="form-label fw-bold" for="short_description">Short description</label>
          <input class="form-control" id="short_description" name="short_description" maxlength="500" value="<?php echo $val('short_description'); ?>"><div class="form-text">One or two lines for cards and search results.</div></div>
        <div class="col-12"><label class="form-label fw-bold" for="description">Full description</label>
          <textarea class="form-control" id="description" name="description" rows="8"><?php echo $val('description'); ?></textarea>
          <div class="form-text">Plain text. Leave a blank line between paragraphs.</div></div>
      </div></div></div>

      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Location</h2></div><div class="cy-card-body"><div class="row g-3">
        <div class="col-md-6"><label class="form-label fw-bold" for="destination_id">Destination</label>
          <select class="form-select" id="destination_id" name="destination_id">
            <option value="">— none —</option>
            <?php foreach ($destinations as $d): ?><option value="<?php echo (int)$d['id']; ?>" data-country="<?php echo e($d['country']); ?>" data-type="<?php echo e($d['location_type']); ?>" <?php echo (int)($v['destination_id'] ?? 0) === (int)$d['id'] ? 'selected' : ''; ?>><?php echo e($d['name']); ?></option><?php endforeach; ?>
          </select><div class="form-text">The hotel is listed under this destination on the website.</div></div>
        <div class="col-md-3"><label class="form-label fw-bold" for="country">Country *</label>
          <input class="form-control" id="country" name="country" maxlength="64" value="<?php echo $val('country'); ?>"></div>
        <div class="col-md-3"><label class="form-label fw-bold" for="location_type">Region</label>
          <select class="form-select" id="location_type" name="location_type">
            <option value="DOMESTIC" <?php echo ($v['location_type'] ?? '') === 'DOMESTIC' ? 'selected' : ''; ?>>India</option>
            <option value="INTERNATIONAL" <?php echo ($v['location_type'] ?? '') === 'INTERNATIONAL' ? 'selected' : ''; ?>>International</option></select></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="state">State / region</label><input class="form-control" id="state" name="state" maxlength="80" value="<?php echo $val('state'); ?>"></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="city">City</label><input class="form-control" id="city" name="city" maxlength="80" value="<?php echo $val('city'); ?>"></div>
        <div class="col-12"><label class="form-label fw-bold" for="address">Complete address</label><textarea class="form-control" id="address" name="address" rows="2"><?php echo $val('address'); ?></textarea></div>
        <div class="col-12"><label class="form-label fw-bold" for="map_link">Google Maps URL</label>
          <input class="form-control" id="map_link" name="map_link" maxlength="2000" value="<?php echo $val('map_link'); ?>" placeholder="https://www.google.com/maps/embed?pb=…">
          <div class="form-text">The map on the hotel page only appears for a Google Maps <em>embed</em> link (Share → Embed a map → copy the src address).</div></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="latitude">Latitude</label><input class="form-control" id="latitude" name="latitude" inputmode="decimal" value="<?php echo $val('latitude'); ?>" placeholder="e.g. 15.5000"></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="longitude">Longitude</label><input class="form-control" id="longitude" name="longitude" inputmode="decimal" value="<?php echo $val('longitude'); ?>" placeholder="e.g. 73.8300"></div>
      </div></div></div>

      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Property details</h2></div><div class="cy-card-body"><div class="row g-3">
        <div class="col-md-6"><label class="form-label fw-bold" for="resort_classification">Classification</label><input class="form-control" id="resort_classification" name="resort_classification" maxlength="128" value="<?php echo $val('resort_classification'); ?>" placeholder="e.g. Heritage Palace &amp; City Hotel"></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="hospitality_rating">Rating</label><input class="form-control" id="hospitality_rating" name="hospitality_rating" maxlength="64" value="<?php echo $val('hospitality_rating'); ?>" placeholder="e.g. ★★★★★ (5-Star Luxury)"></div>
        <div class="col-12"><label class="form-label fw-bold" for="amenities">Amenities</label>
          <textarea class="form-control" id="amenities" name="amenities" rows="5" placeholder="One amenity per line"><?php echo e($amenText); ?></textarea><div class="form-text">One per line, e.g. Swimming Pool.</div></div>
        <div class="col-12"><label class="form-label fw-bold" for="geographic_location">Geographic location</label><input class="form-control" id="geographic_location" name="geographic_location" maxlength="255" value="<?php echo $val('geographic_location'); ?>"></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="airport_proximity">Airport proximity</label><input class="form-control" id="airport_proximity" name="airport_proximity" maxlength="255" value="<?php echo $val('airport_proximity'); ?>"></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="transit_access">Railway &amp; transit access</label><input class="form-control" id="transit_access" name="transit_access" maxlength="255" value="<?php echo $val('transit_access'); ?>"></div>
        <div class="col-12 form-text mt-0">Anything left empty falls back to the website's standard wording.</div>
      </div></div></div>

      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Photos</h2><span class="text-muted small">JPG, PNG or WebP &middot; max 2 MB each</span></div><div class="cy-card-body">
        <?php if ($images): ?>
          <div class="row g-3 mb-3">
            <?php foreach ($images as $n => $im): ?>
              <div class="col-sm-6 col-lg-4"><div class="border rounded-3 p-2 h-100">
                <img src="<?php echo e(url($im['path'])); ?>" alt="" loading="lazy" style="width:100%;height:130px;object-fit:cover;border-radius:10px;">
                <input class="form-control form-control-sm mt-2" name="alt[<?php echo (int)$im['id']; ?>]" value="<?php echo e((string)($im['alt_text'] ?? '')); ?>" placeholder="Alt text (describe the photo)" maxlength="190" aria-label="Alt text">
                <div class="d-flex align-items-center justify-content-between mt-2 small">
                  <label class="form-check mb-0"><input class="form-check-input" type="radio" name="cover" value="img:<?php echo (int)$im['id']; ?>" <?php echo $n === 0 ? 'checked' : ''; ?>> Cover</label>
                  <label class="d-flex align-items-center gap-1 mb-0">Order <input class="form-control form-control-sm" style="width:64px" type="number" min="0" name="sort[<?php echo (int)$im['id']; ?>]" value="<?php echo (int)$im['sort_order']; ?>"></label>
                  <label class="form-check mb-0 text-danger"><input class="form-check-input" type="checkbox" name="delete_image[]" value="<?php echo (int)$im['id']; ?>"> Remove</label>
                </div></div></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <label class="form-label fw-bold" for="new_images"><?php echo $images ? 'Add more photos' : 'Upload photos'; ?></label>
        <input class="form-control" type="file" id="new_images" name="new_images[]" accept="image/jpeg,image/png,image/webp" multiple>
        <div class="form-text">Pick several files at once. The Cover photo is the one shown on cards and listings; lower Order numbers show first.</div>
        <div id="newPreview" class="d-flex flex-wrap gap-2 mt-2"></div>
      </div></div>
    </div>

    <div class="col-xl-4">
      <div class="cy-card mb-3" style="position: sticky; top: 76px;">
        <div class="cy-card-head"><h2>Publishing</h2></div>
        <div class="cy-card-body">
          <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" role="switch" id="is_published" name="is_published" value="1" <?php echo !empty($v['is_published']) ? 'checked' : ''; ?>><label class="form-check-label fw-bold" for="is_published">Published on website</label></div>
          <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" role="switch" id="featured" name="featured" value="1" <?php echo !empty($v['featured']) ? 'checked' : ''; ?>><label class="form-check-label fw-bold" for="featured">Featured on the homepage</label></div>
          <div class="mb-3"><label class="form-label fw-bold" for="display_order">Display order</label>
            <input class="form-control" type="number" min="0" id="display_order" name="display_order" value="<?php echo (int)($v['display_order'] ?? 0); ?>">
            <div class="form-text">1 shows first. 0 = default alphabetical order.</div></div>
          <hr>
          <h3 class="h6 fw-bold">Search engines</h3>
          <div class="mb-3"><label class="form-label small fw-bold" for="meta_title">Meta title</label><input class="form-control" id="meta_title" name="meta_title" maxlength="255" value="<?php echo $val('meta_title'); ?>"></div>
          <div class="mb-3"><label class="form-label small fw-bold" for="meta_description">Meta description</label><textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="500"><?php echo $val('meta_description'); ?></textarea></div>
          <div class="d-grid gap-2">
            <button class="btn btn-cy btn-lg" type="submit"><i class="bi bi-check2-circle"></i> <?php echo $hotel ? 'Save changes' : 'Create hotel'; ?></button>
            <a class="btn btn-light border" href="index.php">Cancel</a>
            <?php if ($hotel && $hotel['is_published'] && !$hotel['is_archived']): ?><a class="btn btn-link" target="_blank" rel="noopener" href="<?php echo e(url('resort-details.php?id=' . urlencode((string)$hotel['slug']))); ?>"><i class="bi bi-box-arrow-up-right"></i> View on website</a><?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<?php
$js = '<script>
(function () {
  var dest = document.getElementById("destination_id"), country = document.getElementById("country"), type = document.getElementById("location_type");
  dest.addEventListener("change", function () {
    var o = dest.options[dest.selectedIndex];
    if (o && o.dataset.country) { country.value = o.dataset.country; type.value = o.dataset.type || type.value; }
  });
  var input = document.getElementById("new_images"), box = document.getElementById("newPreview");
  input.addEventListener("change", function () {
    box.innerHTML = "";
    Array.prototype.forEach.call(input.files, function (f, i) {
      var tooBig = f.size > 2 * 1024 * 1024;
      var d = document.createElement("div"); d.className = "small text-center"; d.style.width = "92px";
      var img = document.createElement("img"); img.style.cssText = "width:92px;height:70px;object-fit:cover;border-radius:8px;" + (tooBig ? "outline:3px solid #dc3545" : "");
      img.src = URL.createObjectURL(f);
      var cap = document.createElement("div"); cap.className = tooBig ? "text-danger" : "text-muted"; cap.textContent = tooBig ? "Over 2 MB" : Math.round(f.size / 1024) + " KB";
      var r = document.createElement("label"); r.className = "form-check small mb-0 d-block"; r.innerHTML = \'<input class="form-check-input" type="radio" name="cover" value="new:\' + i + \'"> Cover\';
      d.appendChild(img); d.appendChild(cap); d.appendChild(r); box.appendChild(d);
    });
  });
})();
</script>';
admin_footer($js);
