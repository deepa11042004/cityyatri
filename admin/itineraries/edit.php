<?php
/**
 * Club Yatri Admin - Itineraries: add / edit with day-by-day builder, linked destinations and hotels, gallery.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/crud.php';

$pdo = admin_pdo();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$it = null;
if ($id > 0) {
    $st = $pdo->prepare('SELECT * FROM itineraries WHERE id = ?');
    $st->execute([$id]);
    $it = $st->fetch() ?: null;
    if (!$it) {
        admin_flash('error', 'That itinerary could not be found.');
        admin_redirect('itineraries/index.php');
    }
}

$destinations = $pdo->query('SELECT id, name, country, slug, location_type FROM destinations WHERE is_archived = 0 ORDER BY name')->fetchAll();
$destById = array_column($destinations, null, 'id');
$categories = $pdo->query("SELECT DISTINCT category FROM itineraries WHERE category <> '' ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

/** textarea (one per line) -> "; "-joined column value (semicolons inside an item would split it, so they become commas) */
$joinList = static function (string $key): ?string {
    $items = array_filter(array_map(static fn($l) => trim(str_replace(';', ',', $l)), preg_split('/[\r\n]+/', (string)($_POST[$key] ?? '')) ?: []));
    return $items ? implode('; ', $items) : null;
};
$listText = static fn(?string $raw): string => implode("\n", array_filter(array_map('trim', explode(';', (string)$raw))));

$errors = [];
$old = null;

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (admin_post_too_large()) {
        $errors[] = 'The files you selected add up to more than the server allows in one go. Upload fewer or smaller photos (max 2 MB each) and try again.';
    } elseif (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Your session expired. Please try again.';
    } else {
        $old = $_POST;
        $title = admin_str('title', 255);
        $days = admin_int('duration_days', 0);
        $nights = admin_int('duration_nights', 0);
        $type = admin_str('location_type', 16) === 'INTERNATIONAL' ? 'INTERNATIONAL' : 'DOMESTIC';
        $destIds = array_values(array_unique(array_filter(array_map('intval', (array)($_POST['dest_ids'] ?? [])), static fn($d) => isset($destById[$d]))));
        $mainDest = (int)($_POST['main_dest'] ?? 0);
        if ($mainDest && in_array($mainDest, $destIds, true)) {   // the chosen main destination goes first
            $destIds = array_values(array_unique(array_merge([$mainDest], $destIds)));
        }
        $hotelIds = array_values(array_unique(array_map('intval', (array)($_POST['hotel_ids'] ?? []))));
        if ($hotelIds) {
            $in = implode(',', array_fill(0, count($hotelIds), '?'));
            $chk = $pdo->prepare("SELECT id FROM hotels WHERE id IN ($in)");
            $chk->execute($hotelIds);
            $hotelIds = array_map('intval', $chk->fetchAll(PDO::FETCH_COLUMN));
        }

        // day-by-day rows (all per-row arrays line up by position)
        $dTitles = (array)($_POST['day_title'] ?? []);
        $dDescs = (array)($_POST['day_desc'] ?? []);
        $dKeep = (array)($_POST['day_keep'] ?? []);
        $dDrop = (array)($_POST['day_drop'] ?? []);
        $dayRows = [];
        foreach ($dTitles as $i => $dt) {
            $t = mb_substr(trim((string)$dt), 0, 255);
            $d = trim((string)($dDescs[$i] ?? ''));
            if ($t === '' && $d === '') {
                continue;
            }
            $dayRows[] = ['i' => (int)$i, 'title' => $t, 'desc' => $d, 'keep' => trim((string)($dKeep[$i] ?? '')), 'drop' => !empty($dDrop[$i])];
        }

        if ($title === '') { $errors[] = 'Itinerary title is required.'; }
        if ($days < 1 || $days > 60) { $errors[] = 'Duration in days must be between 1 and 60.'; }
        if ($nights < 0 || $nights > 60) { $errors[] = 'Duration in nights must be between 0 and 60.'; }
        if (!trim((string)($_POST['summary'] ?? ''))) { $errors[] = 'A short description is required (it is shown on the itinerary cards).'; }

        if (!$errors) {
            $slug = admin_unique_slug($pdo, 'itineraries', admin_slugify(admin_str('slug', 160)) ?: admin_slugify($title), $id);
            $primary = $destIds ? $destById[$destIds[0]] : null;
            $label = admin_str('location_label', 255) ?: ($primary ? $primary['name'] . ', ' . $primary['country'] : null);
            $data = [
                'title' => $title, 'slug' => $slug, 'badge_text' => admin_str('badge_text', 120) ?: null, 'location_label' => $label,
                'destination_slug' => $primary['slug'] ?? null, 'duration_days' => $days, 'duration_nights' => $nights,
                'category' => admin_str('category', 80) ?: null, 'location_type' => $primary['location_type'] ?? $type,
                'region' => admin_str('region', 120) ?: null, 'best_time' => admin_str('best_time', 255) ?: null,
                'summary' => trim((string)$_POST['summary']), 'overview' => trim((string)($_POST['overview'] ?? '')) ?: null,
                'highlights' => $joinList('highlights'), 'inclusions' => $joinList('inclusions'), 'exclusions' => $joinList('exclusions'),
                'activities' => $joinList('activities'), 'important_info' => $joinList('important_info'),
                'featured' => admin_flag('featured'), 'display_order' => max(0, admin_int('display_order')),
                'meta_title' => admin_str('meta_title', 255) ?: null, 'meta_description' => admin_str('meta_description', 500) ?: null,
                'is_published' => admin_flag('is_published'),
            ];

            $removed = [];
            $stored = [];
            $warnings = [];
            $saved = false;
            try {
                $pdo->beginTransaction();
                if ($it) {
                    $set = implode(', ', array_map(static fn($c) => "`$c` = ?", array_keys($data)));
                    $pdo->prepare("UPDATE itineraries SET $set WHERE id = ?")->execute([...array_values($data), $id]);
                } else {
                    $cols = implode(', ', array_map(static fn($c) => "`$c`", array_keys($data)));
                    $pdo->prepare("INSERT INTO itineraries ($cols) VALUES (" . implode(',', array_fill(0, count($data), '?')) . ')')->execute(array_values($data));
                    $id = (int)$pdo->lastInsertId();
                }

                // linked destinations and hotels (replace the sets)
                $pdo->prepare('DELETE FROM itinerary_destinations WHERE itinerary_id = ?')->execute([$id]);
                foreach ($destIds as $n => $did) {
                    $pdo->prepare('INSERT INTO itinerary_destinations (itinerary_id, destination_id, sort_order) VALUES (?,?,?)')->execute([$id, $did, $n]);
                }
                $pdo->prepare('DELETE FROM itinerary_hotels WHERE itinerary_id = ?')->execute([$id]);
                foreach ($hotelIds as $n => $hid) {
                    $pdo->prepare('INSERT INTO itinerary_hotels (itinerary_id, hotel_id, sort_order) VALUES (?,?,?)')->execute([$id, $hid, $n]);
                }

                // days: rebuild in the order shown, numbered 1..n
                $oldDayImgs = $pdo->prepare('SELECT image_path FROM itinerary_days WHERE itinerary_id = ? AND image_path IS NOT NULL');
                $oldDayImgs->execute([$id]);
                $previous = $oldDayImgs->fetchAll(PDO::FETCH_COLUMN);
                $pdo->prepare('DELETE FROM itinerary_days WHERE itinerary_id = ?')->execute([$id]);
                $kept = [];
                foreach ($dayRows as $n => $r) {
                    $img = in_array($r['keep'], $previous, true) && !$r['drop'] ? $r['keep'] : null;
                    if (!empty($_FILES['day_image']['name'][$r['i']])) {
                        $one = [];
                        foreach (['name', 'type', 'tmp_name', 'error', 'size'] as $k) {
                            $one[$k] = $_FILES['day_image'][$k][$r['i']];
                        }
                        $up = admin_save_image($one, 'itineraries/' . $slug, 'Day ' . ($n + 1) . ' photo');
                        if ($up['ok']) {
                            $img = $up['path'];
                            $stored[] = $up['path'];
                        } elseif ($up['error'] !== '') {
                            $warnings[] = $up['error'];
                        }
                    }
                    if ($img) {
                        $kept[] = $img;
                    }
                    $pdo->prepare('INSERT INTO itinerary_days (itinerary_id, day_number, title, description, image_path) VALUES (?,?,?,?,?)')
                        ->execute([$id, $n + 1, $r['title'] !== '' ? $r['title'] : 'Day ' . ($n + 1), $r['desc'], $img]);
                }
                foreach ($previous as $p) {
                    if (!in_array($p, $kept, true)) {
                        $removed[] = (string)$p;
                    }
                }

                // gallery; the first photo is the cover
                admin_gallery_apply($pdo, 'itinerary_images', 'itinerary_id', $id, 'itineraries/' . $slug, $removed, $stored, $warnings);
                $cover = $pdo->prepare('SELECT path FROM itinerary_images WHERE itinerary_id = ? ORDER BY sort_order, id LIMIT 1');
                $cover->execute([$id]);
                $pdo->prepare('UPDATE itineraries SET hero_image = ? WHERE id = ?')->execute([$cover->fetchColumn() ?: null, $id]);

                $pdo->commit();
                $saved = true;
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                foreach ($stored as $p) {
                    admin_delete_uploaded($pdo, $p);
                }
                if (!$it) {
                    $id = 0;
                }
                error_log('Itinerary save failed: ' . $e->getMessage());
                $errors[] = 'The itinerary could not be saved. Please check the details and try again.';
            }

            if ($saved) {
                foreach ($removed as $p) {
                    admin_delete_uploaded($pdo, $p);
                }
                admin_flash('success', $it ? 'Itinerary updated.' : 'Itinerary added.');
                foreach ($warnings as $w) {
                    admin_flash('warning', $w);
                }
                admin_redirect('itineraries/edit.php?id=' . $id);
            }
        }
    }
}

// ---- values for the form -----------------------------------------------------------------
$v = $old ?? ($it ?: ['is_published' => 1, 'location_type' => 'DOMESTIC', 'duration_days' => 5, 'duration_nights' => 4, 'display_order' => 0]);
$val = static fn(string $k, string $d = ''): string => e((string)($v[$k] ?? $d));
$txt = static fn(string $k): string => e($old ? (string)($old[$k] ?? '') : $listText($it[$k] ?? ''));

$selDest = [];
$selHotels = [];
$dayList = [];
$gallery = [];
if ($old) {
    $selDest = array_map('intval', (array)($old['dest_ids'] ?? []));
    foreach (array_map('intval', (array)($old['hotel_ids'] ?? [])) as $hid) {
        $st = $pdo->prepare('SELECT id, name, location_name FROM hotels WHERE id = ?');
        $st->execute([$hid]);
        if ($h = $st->fetch()) { $selHotels[] = $h; }
    }
    foreach ((array)($old['day_title'] ?? []) as $i => $dt) {
        $dayList[] = ['title' => (string)$dt, 'description' => (string)($old['day_desc'][$i] ?? ''), 'image_path' => (string)($old['day_keep'][$i] ?? '')];
    }
}
if ($it) {
    $st = $pdo->prepare('SELECT * FROM itinerary_images WHERE itinerary_id = ? ORDER BY sort_order, id');
    $st->execute([$id]);
    $gallery = $st->fetchAll();
    if (!$old) {
        $st = $pdo->prepare('SELECT destination_id FROM itinerary_destinations WHERE itinerary_id = ? ORDER BY sort_order');
        $st->execute([$id]);
        $selDest = array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));
        $st = $pdo->prepare('SELECT h.id, h.name, h.location_name FROM itinerary_hotels x JOIN hotels h ON h.id = x.hotel_id WHERE x.itinerary_id = ? ORDER BY x.sort_order');
        $st->execute([$id]);
        $selHotels = $st->fetchAll();
        $st = $pdo->prepare('SELECT title, description, image_path FROM itinerary_days WHERE itinerary_id = ? ORDER BY day_number');
        $st->execute([$id]);
        $dayList = $st->fetchAll();
    }
}
if (!$dayList) {
    $dayList = [['title' => '', 'description' => '', 'image_path' => '']];
}

/** One day row (also used as the JS template with $i = '__I__'). */
$dayRow = static function (array $d, int $n): string {
    $img = (string)($d['image_path'] ?? '');
    $h = '<div class="day-row border rounded-3 p-3 mb-3 bg-white" data-day>'
        . '<div class="d-flex align-items-center gap-2 mb-2"><span class="day-badge">Day <b data-day-num>' . $n . '</b></span>'
        . '<div class="ms-auto d-flex gap-1">'
        . '<button class="btn btn-sm btn-light border" type="button" data-day-up title="Move up"><i class="bi bi-arrow-up"></i></button>'
        . '<button class="btn btn-sm btn-light border" type="button" data-day-down title="Move down"><i class="bi bi-arrow-down"></i></button>'
        . '<button class="btn btn-sm btn-outline-danger" type="button" data-day-remove title="Remove this day"><i class="bi bi-trash"></i></button></div></div>'
        . '<input class="form-control mb-2" name="day_title[]" maxlength="255" placeholder="Day title, e.g. Arrival in Srinagar" value="' . e((string)($d['title'] ?? '')) . '" aria-label="Day title">'
        . '<textarea class="form-control mb-2" name="day_desc[]" rows="3" placeholder="What happens on this day" aria-label="Day description">' . e((string)($d['description'] ?? '')) . '</textarea>'
        . '<input type="hidden" name="day_keep[]" value="' . e($img) . '"><input type="hidden" name="day_drop[]" value="0" data-day-drop>'
        . '<div class="d-flex flex-wrap align-items-center gap-3">';
    if ($img !== '') {
        $h .= '<img src="' . e(url($img)) . '" alt="" style="width:90px;height:60px;object-fit:cover;border-radius:8px;">'
            . '<label class="form-check small text-danger mb-0"><input class="form-check-input" type="checkbox" data-day-dropbox> Remove photo</label>';
    }
    return $h . '<div class="flex-grow-1" style="min-width:200px"><input class="form-control form-control-sm" type="file" name="day_image[]" accept="image/jpeg,image/png,image/webp" aria-label="Day photo">'
        . '<div class="form-text mt-0">Optional photo for this day (max 2 MB).</div></div></div></div>';
};

foreach ($errors as $err) {
    admin_flash('error', $err);
}
admin_header($it ? 'Edit itinerary' : 'Add itinerary', 'itineraries', [['Itineraries', 'itineraries/index.php'], [$it ? (string)$it['title'] : 'New itinerary', null]],
    $it ? 'Changes appear on the website as soon as you save.' : 'Create a day-by-day itinerary for the website.');
?>
<form method="post" action="" enctype="multipart/form-data" novalidate id="itinForm">
  <?php echo admin_csrf_field(); ?>
  <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
  <div class="row g-3">
    <div class="col-xl-8">
      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Basic information</h2></div><div class="cy-card-body"><div class="row g-3">
        <div class="col-12"><label class="form-label fw-bold" for="title">Itinerary title *</label><input class="form-control" id="title" name="title" maxlength="255" required value="<?php echo $val('title'); ?>"></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="slug">URL slug</label><input class="form-control" id="slug" name="slug" maxlength="160" value="<?php echo $val('slug'); ?>" placeholder="auto-generated from the title"></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="badge_text">Badge text</label><input class="form-control" id="badge_text" name="badge_text" maxlength="120" value="<?php echo $val('badge_text'); ?>" placeholder="e.g. Heaven On Earth"></div>
        <div class="col-md-3"><label class="form-label fw-bold" for="duration_days">Days *</label><input class="form-control" type="number" min="1" max="60" id="duration_days" name="duration_days" value="<?php echo $val('duration_days', '5'); ?>"></div>
        <div class="col-md-3"><label class="form-label fw-bold" for="duration_nights">Nights *</label><input class="form-control" type="number" min="0" max="60" id="duration_nights" name="duration_nights" value="<?php echo $val('duration_nights', '4'); ?>"></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="category">Category</label><input class="form-control" id="category" name="category" maxlength="80" list="catList" value="<?php echo $val('category'); ?>" placeholder="e.g. Beach &amp; Coastal">
          <datalist id="catList"><?php foreach ($categories as $c): ?><option value="<?php echo e($c); ?>"><?php endforeach; ?></datalist></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="location_label">Location shown on the page</label><input class="form-control" id="location_label" name="location_label" maxlength="255" value="<?php echo $val('location_label'); ?>" placeholder="e.g. Srinagar, Gulmarg &amp; Pahalgam, India"><div class="form-text">Leave empty to use the first destination's name.</div></div>
        <div class="col-md-3"><label class="form-label fw-bold" for="region">Region label</label><input class="form-control" id="region" name="region" maxlength="120" value="<?php echo $val('region'); ?>" placeholder="e.g. India: Northern Himalayas"></div>
        <div class="col-md-3"><label class="form-label fw-bold" for="location_type">Classification</label><select class="form-select" id="location_type" name="location_type">
          <option value="DOMESTIC" <?php echo ($v['location_type'] ?? '') === 'DOMESTIC' ? 'selected' : ''; ?>>India</option><option value="INTERNATIONAL" <?php echo ($v['location_type'] ?? '') === 'INTERNATIONAL' ? 'selected' : ''; ?>>International</option></select>
          <div class="form-text">Follows the first destination when one is chosen.</div></div>
        <div class="col-12"><label class="form-label fw-bold" for="best_time">Best time to travel</label><input class="form-control" id="best_time" name="best_time" maxlength="255" value="<?php echo $val('best_time'); ?>"></div>
        <div class="col-12"><label class="form-label fw-bold" for="summary">Short description *</label><textarea class="form-control" id="summary" name="summary" rows="3"><?php echo $val('summary'); ?></textarea><div class="form-text">Shown on cards and at the top of the itinerary page.</div></div>
        <div class="col-12"><label class="form-label fw-bold" for="overview">Full description</label><textarea class="form-control" id="overview" name="overview" rows="6"><?php echo $val('overview'); ?></textarea><div class="form-text">Plain text.</div></div>
      </div></div></div>

      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Destinations</h2><span class="text-muted small">Tick the places visited; the radio marks the main one</span></div><div class="cy-card-body">
        <input class="form-control mb-2" type="search" id="destFilter" placeholder="Filter destinations…" aria-label="Filter destinations">
        <div id="destList" class="border rounded-3 p-2" style="max-height:230px;overflow:auto;">
          <?php foreach ($destinations as $d): ?>
            <div class="d-flex align-items-center justify-content-between gap-2 py-1" data-name="<?php echo e(strtolower($d['name'] . ' ' . $d['country'])); ?>">
              <label class="form-check mb-0 flex-grow-1"><input class="form-check-input" type="checkbox" name="dest_ids[]" value="<?php echo (int)$d['id']; ?>" <?php echo in_array((int)$d['id'], $selDest, true) ? 'checked' : ''; ?>> <?php echo e($d['name']); ?> <span class="text-muted small">&middot; <?php echo e($d['country']); ?></span></label>
              <label class="small text-muted mb-0" title="Main destination"><input type="radio" name="main_dest" value="<?php echo (int)$d['id']; ?>" <?php echo ($selDest[0] ?? 0) === (int)$d['id'] ? 'checked' : ''; ?>> main</label>
            </div>
          <?php endforeach; ?>
        </div>
      </div></div>

      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Hotels on this itinerary</h2></div><div class="cy-card-body">
        <div class="position-relative"><input class="form-control" type="search" id="hotelSearch" autocomplete="off" placeholder="Type a hotel name or place (2+ letters) to add it…" aria-label="Search hotels">
          <div id="hotelResults" class="list-group position-absolute w-100 shadow-sm" style="z-index:20;display:none;"></div></div>
        <div id="hotelChips" class="d-flex flex-wrap gap-2 mt-3">
          <?php foreach ($selHotels as $h): ?>
            <span class="badge text-bg-light border fs-6 fw-normal d-flex align-items-center gap-2" data-hotel="<?php echo (int)$h['id']; ?>"><?php echo e($h['name']); ?> <small class="text-muted"><?php echo e($h['location_name']); ?></small>
              <input type="hidden" name="hotel_ids[]" value="<?php echo (int)$h['id']; ?>"><button type="button" class="btn-close btn-close-sm" aria-label="Remove <?php echo e($h['name']); ?>"></button></span>
          <?php endforeach; ?>
        </div>
        <div class="form-text">Shown on the itinerary page as "Where you'll stay".</div>
      </div></div>

      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Highlights, inclusions and more</h2></div><div class="cy-card-body"><div class="row g-3">
        <div class="col-md-6"><label class="form-label fw-bold" for="highlights">Journey highlights</label><textarea class="form-control" id="highlights" name="highlights" rows="5" placeholder="One per line"><?php echo $txt('highlights'); ?></textarea></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="activities">Activities</label><textarea class="form-control" id="activities" name="activities" rows="5" placeholder="One per line"><?php echo $txt('activities'); ?></textarea></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="inclusions">Inclusions</label><textarea class="form-control" id="inclusions" name="inclusions" rows="5" placeholder="One per line"><?php echo $txt('inclusions'); ?></textarea></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="exclusions">Exclusions</label><textarea class="form-control" id="exclusions" name="exclusions" rows="5" placeholder="One per line"><?php echo $txt('exclusions'); ?></textarea></div>
        <div class="col-12"><label class="form-label fw-bold" for="important_info">Important information</label><textarea class="form-control" id="important_info" name="important_info" rows="4" placeholder="One per line, e.g. passport validity, dress code"><?php echo $txt('important_info'); ?></textarea></div>
      </div></div></div>

      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Day-by-day plan</h2><button class="btn btn-sm btn-cy" type="button" id="addDay"><i class="bi bi-plus-lg"></i> Add a day</button></div><div class="cy-card-body">
        <div id="days"><?php foreach ($dayList as $n => $d) { echo $dayRow($d, $n + 1); } ?></div>
        <template id="dayTpl"><?php echo $dayRow([], 0); ?></template>
        <div class="form-text">Days are numbered automatically in the order shown. Use the arrows to reorder. Empty rows are ignored.</div>
      </div></div>

      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Photo gallery</h2><span class="text-muted small">The first photo is the cover</span></div><div class="cy-card-body">
        <?php echo admin_gallery_fields($gallery); ?>
      </div></div>
    </div>

    <div class="col-xl-4">
      <div class="cy-card mb-3" style="position: sticky; top: 76px;">
        <div class="cy-card-head"><h2>Publishing</h2></div>
        <div class="cy-card-body">
          <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" role="switch" id="is_published" name="is_published" value="1" <?php echo !empty($v['is_published']) ? 'checked' : ''; ?>><label class="form-check-label fw-bold" for="is_published">Published on website</label></div>
          <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" role="switch" id="featured" name="featured" value="1" <?php echo !empty($v['featured']) ? 'checked' : ''; ?>><label class="form-check-label fw-bold" for="featured">Featured on the homepage</label></div>
          <div class="mb-3"><label class="form-label fw-bold" for="display_order">Display order</label><input class="form-control" type="number" min="0" id="display_order" name="display_order" value="<?php echo (int)($v['display_order'] ?? 0); ?>"><div class="form-text">1 shows first. 0 = default order.</div></div>
          <hr>
          <h3 class="h6 fw-bold">Search engines</h3>
          <div class="mb-3"><label class="form-label small fw-bold" for="meta_title">Meta title</label><input class="form-control" id="meta_title" name="meta_title" maxlength="255" value="<?php echo $val('meta_title'); ?>"></div>
          <div class="mb-3"><label class="form-label small fw-bold" for="meta_description">Meta description</label><textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="500"><?php echo $val('meta_description'); ?></textarea></div>
          <div class="d-grid gap-2">
            <button class="btn btn-cy btn-lg" type="submit"><i class="bi bi-check2-circle"></i> <?php echo $it ? 'Save changes' : 'Create itinerary'; ?></button>
            <a class="btn btn-light border" href="index.php">Cancel</a>
            <?php if ($it && $it['is_published'] && !$it['is_archived']): ?><a class="btn btn-link" target="_blank" rel="noopener" href="<?php echo e(url('itinerary-details.php?slug=' . urlencode((string)$it['slug']))); ?>"><i class="bi bi-box-arrow-up-right"></i> View on website</a><?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<?php
$js = admin_gallery_script() . '<script>
(function () {
  // ---- day builder ----
  var days = document.getElementById("days"), tpl = document.getElementById("dayTpl");
  function renumber() { days.querySelectorAll("[data-day]").forEach(function (r, i) { r.querySelector("[data-day-num]").textContent = i + 1; }); }
  document.getElementById("addDay").addEventListener("click", function () {
    var n = tpl.content.firstElementChild.cloneNode(true); days.appendChild(n); renumber(); n.querySelector("input").focus();
  });
  days.addEventListener("click", function (e) {
    var row = e.target.closest("[data-day]"); if (!row) { return; }
    if (e.target.closest("[data-day-remove]")) { if (days.querySelectorAll("[data-day]").length > 1) { row.remove(); } else { row.querySelectorAll("input[type=text],input:not([type]),textarea").forEach(function (i) { i.value = ""; }); } renumber(); }
    else if (e.target.closest("[data-day-up]") && row.previousElementSibling) { days.insertBefore(row, row.previousElementSibling); renumber(); }
    else if (e.target.closest("[data-day-down]") && row.nextElementSibling) { days.insertBefore(row.nextElementSibling, row); renumber(); }
  });
  days.addEventListener("change", function (e) {
    if (e.target.matches("[data-day-dropbox]")) { e.target.closest("[data-day]").querySelector("[data-day-drop]").value = e.target.checked ? "1" : "0"; }
  });

  // ---- destination filter ----
  document.getElementById("destFilter").addEventListener("input", function () {
    var q = this.value.toLowerCase();
    document.querySelectorAll("#destList [data-name]").forEach(function (l) { l.style.display = l.getAttribute("data-name").indexOf(q) > -1 ? "" : "none"; });
  });

  // choosing a main destination also ticks it
  document.getElementById("destList").addEventListener("change", function (e) {
    if (e.target.name === "main_dest") { e.target.closest("[data-name]").querySelector("input[type=checkbox]").checked = true; }
  });

  // ---- hotel picker ----
  var input = document.getElementById("hotelSearch"), box = document.getElementById("hotelResults"), chips = document.getElementById("hotelChips"), timer = null;
  function picked(id) { return chips.querySelector("[data-hotel=\"" + id + "\"]"); }
  function addChip(h) {
    if (picked(h.id)) { return; }
    var s = document.createElement("span"); s.className = "badge text-bg-light border fs-6 fw-normal d-flex align-items-center gap-2"; s.setAttribute("data-hotel", h.id);
    s.appendChild(document.createTextNode(h.name + " "));
    var sm = document.createElement("small"); sm.className = "text-muted"; sm.textContent = h.location_name; s.appendChild(sm);
    var hid = document.createElement("input"); hid.type = "hidden"; hid.name = "hotel_ids[]"; hid.value = h.id; s.appendChild(hid);
    var x = document.createElement("button"); x.type = "button"; x.className = "btn-close btn-close-sm"; x.setAttribute("aria-label", "Remove " + h.name); s.appendChild(x);
    chips.appendChild(s);
  }
  chips.addEventListener("click", function (e) { if (e.target.matches(".btn-close")) { e.target.closest("[data-hotel]").remove(); } });
  input.addEventListener("input", function () {
    clearTimeout(timer); var q = input.value.trim();
    if (q.length < 2) { box.style.display = "none"; return; }
    timer = setTimeout(function () {
      fetch("hotel-search.php?q=" + encodeURIComponent(q), { headers: { "X-Requested-With": "fetch" } }).then(function (r) { return r.json(); }).then(function (rows) {
        box.innerHTML = "";
        rows.forEach(function (h) {
          var b = document.createElement("button"); b.type = "button"; b.className = "list-group-item list-group-item-action";
          b.textContent = h.name + " — " + h.location_name + ", " + h.country;
          b.addEventListener("click", function () { addChip(h); input.value = ""; box.style.display = "none"; });
          box.appendChild(b);
        });
        if (!rows.length) { box.innerHTML = "<div class=\"list-group-item text-muted\">No hotels found</div>"; }
        box.style.display = "block";
      }).catch(function () { box.style.display = "none"; });
    }, 250);
  });
  document.addEventListener("click", function (e) { if (!box.contains(e.target) && e.target !== input) { box.style.display = "none"; } });
})();
</script>';
admin_footer($js);
