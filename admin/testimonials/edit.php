<?php
/**
 * Club Yatri Admin - Testimonials: add / edit a written or video testimonial.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/crud.php';
require_once __DIR__ . '/../../includes/content-db.php';

$pdo = admin_pdo();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$t = null;
if ($id > 0) {
    $st = $pdo->prepare('SELECT * FROM testimonials WHERE id = ?');
    $st->execute([$id]);
    $t = $st->fetch() ?: null;
    if (!$t) {
        admin_flash('error', 'That testimonial could not be found.');
        admin_redirect('testimonials/index.php');
    }
}
$type = $t['type'] ?? (($_GET['type'] ?? $_POST['type'] ?? '') === 'video' ? 'video' : 'text');
$isVideo = $type === 'video';

$errors = [];
$old = null;

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (admin_post_too_large()) {
        $errors[] = 'The files you selected are larger than the server allows (max 2 MB each). Compress them and try again.';
    } elseif (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Your session expired. Please try again.';
    } else {
        $old = $_POST;
        $name = admin_str('name', 160);
        $rating = admin_int('rating', 0);
        $body = trim((string)($_POST['body'] ?? ''));
        $source = admin_str('video_source', 10);
        $url = admin_str('video_url', 500);
        $ref = null;

        if ($name === '') { $errors[] = 'Member name is required.'; }
        if (!$isVideo && $body === '') { $errors[] = 'The testimonial text is required.'; }
        if ($rating < 0 || $rating > 5) { $errors[] = 'Rating must be between 1 and 5 stars.'; }
        if ($isVideo) {
            if (!in_array($source, ['youtube', 'vimeo', 'upload'], true)) {
                $errors[] = 'Choose where the video comes from.';
            } elseif ($source !== 'upload') {
                $ref = content_video_ref($source, $url);
                if ($ref === '') { $errors[] = 'That does not look like a valid ' . ucfirst($source) . ' link. Paste the full video address.'; }
            } elseif (empty($_FILES['video_file']['name']) && empty($t['video_path'])) {
                $errors[] = 'Upload an MP4 video (max 2 MB) or choose YouTube / Vimeo.';
            }
        }

        if (!$errors) {
            $data = [
                'type' => $type, 'name' => $name, 'location' => admin_str('location', 120) ?: null, 'detail' => admin_str('detail', 190) ?: null,
                'rating' => (!$isVideo && $rating > 0) ? $rating : null, 'body' => $isVideo ? null : $body,
                'video_title' => $isVideo ? (admin_str('video_title', 190) ?: null) : null, 'video_source' => $isVideo ? $source : null,
                'video_url' => ($isVideo && $source !== 'upload') ? $url : null, 'video_ref' => $isVideo ? ($ref ?: null) : null,
                'description' => $isVideo ? (trim((string)($_POST['description'] ?? '')) ?: null) : null,
                'featured' => admin_flag('featured'), 'display_order' => max(0, admin_int('display_order')), 'is_published' => admin_flag('is_published'),
            ];
            $removed = [];
            $stored = [];
            $warnings = [];
            $saved = false;
            try {
                $pdo->beginTransaction();
                $slugDir = 'testimonials';
                // profile photo (written testimonials) / thumbnail (videos)
                $img = $t['profile_image'] ?? null;
                if (admin_flag('remove_profile') && $img) { $removed[] = (string)$img; $img = null; }
                if (!empty($_FILES['profile_image']['name'])) {
                    $r = admin_save_image($_FILES['profile_image'], $slugDir, 'Profile photo');
                    if ($r['ok']) { if ($img) { $removed[] = (string)$img; } $img = $r['path']; $stored[] = $r['path']; }
                    elseif ($r['error'] !== '') { $warnings[] = $r['error']; }
                }
                $data['profile_image'] = $img;

                $thumb = $t['thumbnail_path'] ?? null;
                if (!$isVideo) {
                    if ($thumb) { $removed[] = (string)$thumb; }
                    $thumb = null;
                } else {
                    if (admin_flag('remove_thumbnail') && $thumb) { $removed[] = (string)$thumb; $thumb = null; }
                    if (!empty($_FILES['thumbnail']['name'])) {
                        $r = admin_save_image($_FILES['thumbnail'], $slugDir, 'Thumbnail');
                        if ($r['ok']) { if ($thumb) { $removed[] = (string)$thumb; } $thumb = $r['path']; $stored[] = $r['path']; }
                        elseif ($r['error'] !== '') { $warnings[] = $r['error']; }
                    }
                }
                $data['thumbnail_path'] = $thumb;

                $vpath = $t['video_path'] ?? null;
                if ($isVideo && $source === 'upload') {
                    if (!empty($_FILES['video_file']['name'])) {
                        $r = admin_save_video($_FILES['video_file'], $slugDir, 'Video');
                        if ($r['ok']) { if ($vpath) { $removed[] = (string)$vpath; } $vpath = $r['path']; $stored[] = $r['path']; }
                        else { throw new RuntimeException($r['error'] ?: 'The video could not be uploaded.'); }
                    }
                } else {
                    if ($vpath) { $removed[] = (string)$vpath; }
                    $vpath = null;
                }
                $data['video_path'] = $vpath;

                if ($t) {
                    $set = implode(', ', array_map(static fn($c) => "`$c` = ?", array_keys($data)));
                    $pdo->prepare("UPDATE testimonials SET $set WHERE id = ?")->execute([...array_values($data), $id]);
                } else {
                    $cols = implode(', ', array_map(static fn($c) => "`$c`", array_keys($data)));
                    $pdo->prepare("INSERT INTO testimonials ($cols) VALUES (" . implode(',', array_fill(0, count($data), '?')) . ')')->execute(array_values($data));
                    $id = (int)$pdo->lastInsertId();
                }
                $pdo->commit();
                $saved = true;
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) { $pdo->rollBack(); }
                foreach ($stored as $p) { admin_delete_uploaded($pdo, $p); }
                if (!$t) { $id = 0; }
                if ($e instanceof RuntimeException) {
                    $errors[] = $e->getMessage();
                } else {
                    error_log('Testimonial save failed: ' . $e->getMessage());
                    $errors[] = 'The testimonial could not be saved. Please check the details and try again.';
                }
            }
            if ($saved) {
                foreach ($removed as $p) { admin_delete_uploaded($pdo, $p); }
                admin_flash('success', $t ? 'Testimonial updated.' : 'Testimonial added.');
                foreach ($warnings as $w) { admin_flash('warning', $w); }
                admin_redirect('testimonials/edit.php?id=' . $id);
            }
        }
    }
}

$v = $old ?? ($t ?: ['is_published' => 1, 'rating' => 5, 'video_source' => 'youtube', 'display_order' => 0]);
$val = static fn(string $k, string $d = ''): string => e((string)($v[$k] ?? $d));
$src = (string)($v['video_source'] ?? 'youtube');
foreach ($errors as $err) {
    admin_flash('error', $err);
}
$pageTitle = ($t ? 'Edit ' : 'Add ') . ($isVideo ? 'video testimonial' : 'written testimonial');
admin_header($pageTitle, 'testimonials', [['Testimonials', 'testimonials/index.php'], [$t ? (string)$t['name'] : 'New', null]], 'Shown on the homepage as soon as you save and publish.');
?>
<form method="post" action="" enctype="multipart/form-data" novalidate>
  <?php echo admin_csrf_field(); ?>
  <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
  <input type="hidden" name="type" value="<?php echo e($type); ?>">
  <div class="row g-3">
    <div class="col-xl-8">
      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Member</h2></div><div class="cy-card-body"><div class="row g-3">
        <div class="col-md-6"><label class="form-label fw-bold" for="name">Member name *</label><input class="form-control" id="name" name="name" maxlength="160" required value="<?php echo $val('name'); ?>"></div>
        <div class="col-md-6"><label class="form-label fw-bold" for="location">Location</label><input class="form-control" id="location" name="location" maxlength="120" value="<?php echo $val('location'); ?>" placeholder="e.g. Mumbai"></div>
        <div class="col-12"><label class="form-label fw-bold" for="detail">Membership line</label><input class="form-control" id="detail" name="detail" maxlength="190" value="<?php echo $val('detail'); ?>" placeholder="e.g. Platinum Crest Member since 2022"></div>
      </div></div></div>

      <?php if (!$isVideo): ?>
      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Testimonial</h2></div><div class="cy-card-body"><div class="row g-3">
        <div class="col-12"><label class="form-label fw-bold" for="body">What they said *</label><textarea class="form-control" id="body" name="body" rows="6"><?php echo $val('body'); ?></textarea></div>
        <div class="col-md-4"><label class="form-label fw-bold" for="rating">Rating</label><select class="form-select" id="rating" name="rating">
          <option value="0">No rating shown</option><?php for ($i = 5; $i >= 1; $i--): ?><option value="<?php echo $i; ?>" <?php echo (int)($v['rating'] ?? 0) === $i ? 'selected' : ''; ?>><?php echo str_repeat('★', $i) . ' (' . $i . ')'; ?></option><?php endfor; ?></select>
          <div class="form-text">The website shows five stars when no rating is set.</div></div>
      </div></div></div>
      <?php else: ?>
      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Video</h2></div><div class="cy-card-body"><div class="row g-3">
        <div class="col-12"><label class="form-label fw-bold" for="video_title">Video title</label><input class="form-control" id="video_title" name="video_title" maxlength="190" value="<?php echo $val('video_title'); ?>"></div>
        <div class="col-md-4"><label class="form-label fw-bold" for="video_source">Video source *</label><select class="form-select" id="video_source" name="video_source">
          <option value="youtube" <?php echo $src === 'youtube' ? 'selected' : ''; ?>>YouTube link</option>
          <option value="vimeo" <?php echo $src === 'vimeo' ? 'selected' : ''; ?>>Vimeo link</option>
          <option value="upload" <?php echo $src === 'upload' ? 'selected' : ''; ?>>Upload MP4</option></select></div>
        <div class="col-md-8" data-src="link"><label class="form-label fw-bold" for="video_url">Video link *</label><input class="form-control" id="video_url" name="video_url" maxlength="500" value="<?php echo $val('video_url'); ?>" placeholder="https://www.youtube.com/watch?v=…"><div class="form-text">Paste the normal address from the browser.</div></div>
        <div class="col-md-8" data-src="upload"><label class="form-label fw-bold" for="video_file">MP4 file *</label><input class="form-control" type="file" id="video_file" name="video_file" accept="video/mp4">
          <div class="form-text">Max 2 MB on this hosting, so only very short clips fit. For anything longer, use YouTube or Vimeo.<?php echo !empty($t['video_path']) ? ' <strong>A video is already uploaded; choose a file only to replace it.</strong>' : ''; ?></div></div>
        <div class="col-12"><label class="form-label fw-bold" for="description">Short quote / description</label><textarea class="form-control" id="description" name="description" rows="3"><?php echo $val('description'); ?></textarea></div>
        <div class="col-12"><h3 class="h6 fw-bold mb-1">Thumbnail</h3>
          <?php if (!empty($t['thumbnail_path'])): ?><img src="<?php echo e(content_image_url($t['thumbnail_path'])); ?>" alt="" style="width:160px;height:100px;object-fit:cover;border-radius:10px;" class="d-block mb-1"><label class="form-check small text-danger mb-2"><input class="form-check-input" type="checkbox" name="remove_thumbnail" value="1"> Remove thumbnail</label><?php endif; ?>
          <input class="form-control" type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp" aria-label="Thumbnail"><div class="form-text">YouTube videos show their own thumbnail unless you upload one. Vimeo and uploaded videos need one.</div></div>
      </div></div></div>
      <?php endif; ?>
    </div>

    <div class="col-xl-4">
      <div class="cy-card mb-3" style="position: sticky; top: 76px;">
        <div class="cy-card-head"><h2>Publishing</h2></div>
        <div class="cy-card-body">
          <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" role="switch" id="is_published" name="is_published" value="1" <?php echo !empty($v['is_published']) ? 'checked' : ''; ?>><label class="form-check-label fw-bold" for="is_published">Published on website</label></div>
          <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" role="switch" id="featured" name="featured" value="1" <?php echo !empty($v['featured']) ? 'checked' : ''; ?>><label class="form-check-label fw-bold" for="featured">Featured (shown first)</label></div>
          <div class="mb-3"><label class="form-label fw-bold" for="display_order">Display order</label><input class="form-control" type="number" min="0" id="display_order" name="display_order" value="<?php echo (int)($v['display_order'] ?? 0); ?>"><div class="form-text">1 shows first.</div></div>
          <hr>
          <h3 class="h6 fw-bold">Profile photo</h3>
          <?php if (!empty($t['profile_image'])): ?><img src="<?php echo e(content_image_url($t['profile_image'])); ?>" alt="" style="width:72px;height:72px;object-fit:cover;border-radius:50%;" class="d-block mb-1"><label class="form-check small text-danger mb-2"><input class="form-check-input" type="checkbox" name="remove_profile" value="1"> Remove photo</label><?php endif; ?>
          <input class="form-control mb-1" type="file" name="profile_image" accept="image/jpeg,image/png,image/webp" aria-label="Profile photo">
          <div class="form-text mb-3">Optional. Without one, the member's initials are shown. JPG, PNG or WebP, max 2 MB.</div>
          <div class="d-grid gap-2">
            <button class="btn btn-cy btn-lg" type="submit"><i class="bi bi-check2-circle"></i> <?php echo $t ? 'Save changes' : 'Create testimonial'; ?></button>
            <a class="btn btn-light border" href="index.php">Cancel</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<?php
admin_footer($isVideo ? '<script>
(function () {
  var sel = document.getElementById("video_source");
  function sync() {
    document.querySelectorAll("[data-src]").forEach(function (el) {
      var link = el.getAttribute("data-src") === "link";
      el.style.display = (sel.value === "upload") === link ? "none" : "";
    });
  }
  sel.addEventListener("change", sync); sync();
})();
</script>' : '');
