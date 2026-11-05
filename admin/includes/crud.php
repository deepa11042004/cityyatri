<?php
/**
 * Club Yatri Admin - shared helpers for the content modules (slugs, uploads, pagination, form input).
 */

declare(strict_types=1);

const ADMIN_MAX_UPLOAD_BYTES = 2 * 1024 * 1024;   // hosting limit: 2 MB per file
const ADMIN_IMAGE_TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

function admin_slugify(string $text): string
{
    $t = trim($text);
    if (function_exists('transliterator_transliterate')) {
        $t = (string)transliterator_transliterate('Any-Latin; Latin-ASCII', $t);
    } else {
        $t = (string)iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $t);
    }
    $t = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $t) ?? '');
    return trim($t, '-');
}

/** A slug that is unique in $table (ignoring the row being edited). */
function admin_unique_slug(PDO $pdo, string $table, string $wanted, int $excludeId = 0): string
{
    $base = $wanted !== '' ? $wanted : 'item';
    $slug = $base;
    $i = 2;
    while (true) {
        $st = $pdo->prepare("SELECT COUNT(*) FROM `$table` WHERE slug = ? AND id <> ?");
        $st->execute([$slug, $excludeId]);
        if ((int)$st->fetchColumn() === 0) {
            return $slug;
        }
        $slug = $base . '-' . $i++;
    }
}

function admin_str(string $key, int $max = 255, ?array $src = null): string
{
    $src ??= $_POST;
    return mb_substr(trim((string)($src[$key] ?? '')), 0, $max);
}

function admin_int(string $key, int $default = 0, ?array $src = null): int
{
    $src ??= $_POST;
    return isset($src[$key]) && $src[$key] !== '' ? (int)$src[$key] : $default;
}

function admin_flag(string $key): int
{
    return empty($_POST[$key]) ? 0 : 1;
}

/** Decimal coordinate or null. */
function admin_coord(string $key, float $min, float $max): ?string
{
    $v = trim((string)($_POST[$key] ?? ''));
    if ($v === '' || !is_numeric($v)) {
        return null;
    }
    $f = (float)$v;
    return ($f < $min || $f > $max) ? null : number_format($f, 6, '.', '');
}

/** True when the request was larger than PHP's post_max_size (PHP then silently empties $_POST/$_FILES). */
function admin_post_too_large(): bool
{
    $len = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
    $ini = ini_get('post_max_size');
    $max = (int)$ini;
    $unit = strtolower(substr(trim((string)$ini), -1));
    $max *= match ($unit) { 'g' => 1073741824, 'm' => 1048576, 'k' => 1024, default => 1 };
    return $max > 0 && $len > $max;
}

/**
 * Validate and store one uploaded image under assets/images/<dir>/.
 *
 * @return array{ok:bool,path?:string,error?:string}
 */
function admin_save_image(array $file, string $dir, string $label = 'Image'): array
{
    $err = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($err === UPLOAD_ERR_NO_FILE) {
        return ['ok' => false, 'error' => ''];
    }
    $name = (string)($file['name'] ?? 'file');
    if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
        return ['ok' => false, 'error' => "$label \"$name\" is larger than 2 MB."];
    }
    if ($err !== UPLOAD_ERR_OK || !is_uploaded_file((string)$file['tmp_name'])) {
        return ['ok' => false, 'error' => "$label \"$name\" could not be uploaded (error $err)."];
    }
    if ((int)$file['size'] > ADMIN_MAX_UPLOAD_BYTES) {
        return ['ok' => false, 'error' => "$label \"$name\" is larger than 2 MB. Please compress it and try again."];
    }
    $mime = (string)(new finfo(FILEINFO_MIME_TYPE))->file((string)$file['tmp_name']);
    if (!isset(ADMIN_IMAGE_TYPES[$mime])) {
        return ['ok' => false, 'error' => "$label \"$name\" must be a JPG, PNG or WebP image."];
    }
    $info = @getimagesize((string)$file['tmp_name']);
    if (!$info || $info[0] < 1 || $info[1] < 1 || $info[0] > 8000 || $info[1] > 8000) {
        return ['ok' => false, 'error' => "$label \"$name\" is not a valid image (max 8000 px)."];
    }

    $dir = trim(str_replace('\\', '/', $dir), '/');
    $root = dirname(__DIR__, 2);
    $abs = $root . '/assets/images/' . $dir;
    if (!is_dir($abs) && !@mkdir($abs, 0755, true) && !is_dir($abs)) {
        return ['ok' => false, 'error' => 'The upload folder could not be created. Check folder permissions.'];
    }
    $fname = 'u-' . date('ymd-His') . '-' . bin2hex(random_bytes(3)) . '.' . ADMIN_IMAGE_TYPES[$mime];
    if (!move_uploaded_file((string)$file['tmp_name'], $abs . '/' . $fname)) {
        return ['ok' => false, 'error' => "$label \"$name\" could not be saved. Check folder permissions."];
    }
    @chmod($abs . '/' . $fname, 0644);
    return ['ok' => true, 'path' => 'assets/images/' . $dir . '/' . $fname];
}

/**
 * Save every file in a multi-file input (name="images[]").
 *
 * @return array{paths:string[],errors:string[]}
 */
function admin_save_images(string $field, string $dir, int $maxFiles = 12): array
{
    $paths = [];
    $errors = [];
    if (empty($_FILES[$field]['name'])) {
        return ['paths' => $paths, 'errors' => $errors];
    }
    $f = $_FILES[$field];
    $names = is_array($f['name']) ? $f['name'] : [$f['name']];
    foreach ($names as $i => $n) {
        if (count($paths) >= $maxFiles) {
            $errors[] = "Only $maxFiles images can be uploaded at once.";
            break;
        }
        $one = [];
        foreach (['name', 'type', 'tmp_name', 'error', 'size'] as $k) {
            $one[$k] = is_array($f[$k]) ? $f[$k][$i] : $f[$k];
        }
        $r = admin_save_image($one, $dir);
        if ($r['ok']) {
            $paths[] = $r['path'];
        } elseif ($r['error'] !== '') {
            $errors[] = $r['error'];
        }
    }
    return ['paths' => $paths, 'errors' => $errors];
}

/**
 * Delete an image file only if it was uploaded through the admin ("u-" prefix) and nothing else still uses it.
 * Photos that came with the site (imported) are never removed from disk, only unlinked from the record.
 */
function admin_delete_uploaded(PDO $pdo, string $path): bool
{
    $path = ltrim(str_replace('\\', '/', $path), '/');
    if (!preg_match('~^assets/(images|videos)/[a-z0-9_\-/]+/u-[0-9\-]+-[a-f0-9]{6}\.(jpg|png|webp|mp4)$~', $path)) {
        return false;
    }
    $uses = 0;
    foreach ([['hotel_images', 'path'], ['destination_images', 'path'], ['itinerary_images', 'path'], ['itinerary_days', 'image_path'],
              ['hotels', 'image_path'], ['destinations', 'hero_image'], ['itineraries', 'hero_image'], ['passes', 'image_path'], ['pass_seasons', 'image_path'],
              ['room_categories', 'image_path'], ['room_images', 'path'], ['testimonials', 'profile_image'], ['testimonials', 'thumbnail_path'],
              ['testimonials', 'video_path']] as [$t, $c]) {
        try {
            $st = $pdo->prepare("SELECT COUNT(*) FROM `$t` WHERE `$c` = ?");
            $st->execute([$path]);
            $uses += (int)$st->fetchColumn();
        } catch (PDOException $e) {
            // table not present: ignore
        }
    }
    $abs = dirname(__DIR__, 2) . '/' . $path;
    return $uses === 0 && is_file($abs) && @unlink($abs);
}

/** Bootstrap pagination (keeps the caller's query string via $href(page)). */
function admin_pagination(int $page, int $pages, callable $href): string
{
    if ($pages <= 1) {
        return '';
    }
    $win = array_values(array_unique(array_filter([1, $page - 1, $page, $page + 1, $pages], static fn($p) => $p >= 1 && $p <= $pages)));
    sort($win);
    $h = '<nav aria-label="Pages"><ul class="pagination pagination-sm mb-0">';
    $h .= '<li class="page-item' . ($page <= 1 ? ' disabled' : '') . '"><a class="page-link" href="' . e($href($page - 1)) . '">&laquo;</a></li>';
    $prev = 0;
    foreach ($win as $p) {
        if ($p - $prev > 1) {
            $h .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
        }
        $prev = $p;
        $h .= '<li class="page-item' . ($p === $page ? ' active' : '') . '"><a class="page-link" href="' . e($href($p)) . '">' . $p . '</a></li>';
    }
    $h .= '<li class="page-item' . ($page >= $pages ? ' disabled' : '') . '"><a class="page-link" href="' . e($href($page + 1)) . '">&raquo;</a></li></ul></nav>';
    return $h;
}

/** One-line POST form button (CSRF included) used for row actions. */
function admin_action_button(string $action, array $fields, string $label, string $class = 'btn-light border', string $icon = '', string $confirm = '', string $title = ''): string
{
    $attrs = $confirm !== '' ? ' data-confirm="' . e($confirm) . '" data-confirm-title="' . e($label) . '" data-confirm-ok="' . e($label) . '"' : '';
    $h = '<form method="post" action="' . e($action) . '" class="d-inline">' . admin_csrf_field();
    foreach ($fields as $k => $v) {
        $h .= '<input type="hidden" name="' . e((string)$k) . '" value="' . e((string)$v) . '">';
    }
    return $h . '<button class="btn btn-sm ' . $class . '" type="submit"' . $attrs . ($title !== '' ? ' title="' . e($title) . '"' : '') . '>'
        . ($icon !== '' ? '<i class="bi ' . $icon . '"></i>' : '') . ($label !== '' ? ' ' . e($label) : '') . '</button></form>';
}

function admin_status_pill(array $row): string
{
    if (!empty($row['is_archived'])) {
        return '<span class="pill pill-lost">Archived</span>';
    }
    return !empty($row['is_published']) ? '<span class="pill pill-converted">Published</span>' : '<span class="pill pill-follow_up">Draft</span>';
}

/**
 * Render the photo manager for a gallery (existing photos with alt text / order / remove + an upload input).
 *
 * @param array<int,array> $images rows with id, path, alt_text, sort_order
 */
function admin_gallery_fields(array $images, string $inputName = 'new_images'): string
{
    $h = '';
    if ($images) {
        $h .= '<div class="row g-3 mb-3">';
        foreach ($images as $im) {
            $iid = (int)$im['id'];
            $h .= '<div class="col-sm-6 col-lg-4"><div class="border rounded-3 p-2 h-100">'
                . '<img src="' . e(url((string)$im['path'])) . '" alt="" loading="lazy" style="width:100%;height:130px;object-fit:cover;border-radius:10px;">'
                . '<input class="form-control form-control-sm mt-2" name="alt[' . $iid . ']" value="' . e((string)($im['alt_text'] ?? '')) . '" placeholder="Alt text (describe the photo)" maxlength="190" aria-label="Alt text">'
                . '<div class="d-flex align-items-center justify-content-between mt-2 small">'
                . '<label class="d-flex align-items-center gap-1 mb-0">Order <input class="form-control form-control-sm" style="width:64px" type="number" min="0" name="sort[' . $iid . ']" value="' . (int)$im['sort_order'] . '"></label>'
                . '<label class="form-check mb-0 text-danger"><input class="form-check-input" type="checkbox" name="delete_image[]" value="' . $iid . '"> Remove</label>'
                . '</div></div></div>';
        }
        $h .= '</div>';
    }
    $h .= '<label class="form-label fw-bold" for="' . e($inputName) . '">' . ($images ? 'Add more photos' : 'Upload photos') . '</label>'
        . '<input class="form-control" type="file" id="' . e($inputName) . '" name="' . e($inputName) . '[]" accept="image/jpeg,image/png,image/webp" multiple data-preview="' . e($inputName) . 'Preview">'
        . '<div class="form-text">JPG, PNG or WebP, max 2 MB each. Pick several at once. Lower Order numbers show first.</div>'
        . '<div id="' . e($inputName) . 'Preview" class="d-flex flex-wrap gap-2 mt-2"></div>';
    return $h;
}

/** Script that previews chosen files and flags any over 2 MB. Output once per page. */
function admin_gallery_script(): string
{
    return '<script>
document.querySelectorAll("input[data-preview]").forEach(function (input) {
  var box = document.getElementById(input.getAttribute("data-preview"));
  input.addEventListener("change", function () {
    box.innerHTML = "";
    Array.prototype.forEach.call(input.files, function (f) {
      var big = f.size > 2 * 1024 * 1024, d = document.createElement("div");
      d.className = "small text-center"; d.style.width = "92px";
      var img = document.createElement("img"); img.src = URL.createObjectURL(f);
      img.style.cssText = "width:92px;height:70px;object-fit:cover;border-radius:8px;" + (big ? "outline:3px solid #dc3545" : "");
      var cap = document.createElement("div"); cap.className = big ? "text-danger" : "text-muted"; cap.textContent = big ? "Over 2 MB" : Math.round(f.size / 1024) + " KB";
      d.appendChild(img); d.appendChild(cap); box.appendChild(d);
    });
  });
});
</script>';
}

/**
 * Apply the gallery part of a form to a table of (id, <fk>, path, alt_text, sort_order) rows:
 * update alt/order, delete ticked photos, add uploads, renumber 1..n. Call inside a transaction.
 *
 * @param string[] $removed  paths unlinked from the record (delete the files after commit with admin_delete_uploaded)
 * @param string[] $stored   paths of files newly written to disk (delete them if the transaction rolls back)
 * @param string[] $warnings upload problems to show to the admin
 */
function admin_gallery_apply(PDO $pdo, string $table, string $fk, int $parentId, string $dir, array &$removed, array &$stored, array &$warnings, string $field = 'new_images'): void
{
    $st = $pdo->prepare("SELECT id, path FROM `$table` WHERE `$fk` = ?");
    $st->execute([$parentId]);
    $delete = array_map('intval', (array)($_POST['delete_image'] ?? []));
    foreach ($st->fetchAll() as $im) {
        $iid = (int)$im['id'];
        if (in_array($iid, $delete, true)) {
            $pdo->prepare("DELETE FROM `$table` WHERE id = ? AND `$fk` = ?")->execute([$iid, $parentId]);
            $removed[] = (string)$im['path'];
        } else {
            $pdo->prepare("UPDATE `$table` SET alt_text = ?, sort_order = ? WHERE id = ? AND `$fk` = ?")
                ->execute([mb_substr(trim((string)($_POST['alt'][$iid] ?? '')), 0, 190) ?: null, max(0, (int)($_POST['sort'][$iid] ?? 0)), $iid, $parentId]);
        }
    }
    $up = admin_save_images($field, $dir);
    $warnings = array_merge($warnings, $up['errors']);
    $stored = array_merge($stored, $up['paths']);
    $max = (int)$pdo->query("SELECT COALESCE(MAX(sort_order), 0) FROM `$table` WHERE `$fk` = " . $parentId)->fetchColumn();
    foreach ($up['paths'] as $i => $p) {
        $pdo->prepare("INSERT INTO `$table` (`$fk`, path, sort_order) VALUES (?,?,?)")->execute([$parentId, $p, $max + 1000 + $i]);
    }
    $all = $pdo->prepare("SELECT id FROM `$table` WHERE `$fk` = ? ORDER BY sort_order, id");
    $all->execute([$parentId]);
    foreach ($all->fetchAll(PDO::FETCH_COLUMN) as $n => $iid) {
        $pdo->prepare("UPDATE `$table` SET sort_order = ? WHERE id = ?")->execute([$n + 1, $iid]);
    }
}

/**
 * Pixel position of a place on the globe artwork (727 x 479), same polynomial warp the importer used.
 * Returns [x, y] or null when the place is off the visible part of the artwork.
 *
 * @return array{0:float,1:float}|null
 */
function admin_globe_pin(float $lat, float $lon): ?array
{
    static $warp = null;
    if ($warp === null) {
        $file = dirname(__DIR__, 2) . '/database/tools/globe_warp.json';
        $warp = is_file($file) ? json_decode((string)file_get_contents($file), true) : false;
    }
    if (!$warp) {
        return null;
    }
    $u = ($lon - $warp['lon0']) / 50.0;
    $v = ($lat - $warp['lat0']) / 50.0;
    $t = [1, $u, $v, $u * $u, $u * $v, $v * $v, $u ** 3, $u * $u * $v, $u * $v * $v, $v ** 3];
    $n = count($t);
    $x = $y = 0.0;
    foreach ($t as $i => $term) {
        $x += $term * $warp['p'][$i];
        $y += $term * $warp['p'][$n + $i];
    }
    $visible = $lon > -25 && $x > 8 && $x < 727 - 8 && $y > 255 && $y < 479 - 4;
    return $visible ? [round($x, 2), round($y, 2)] : null;
}

require_once __DIR__ . '/video.php';
