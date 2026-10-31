<?php
/**
 * Club Yatri - Video Testimonials block (click-to-play; nothing loads from YouTube until a video is played).
 * Reads $videoTestimonials from data/video-testimonials.php; shows placeholders while it is empty.
 */

declare(strict_types=1);

require_once __DIR__ . '/../data/video-testimonials.php';

$vtItems = [];
foreach ($videoTestimonials as $v) {
    $yt = (string)($v['youtube'] ?? '');
    $mp4 = (string)($v['mp4'] ?? '');
    $ok = ($yt !== '' && preg_match('/^[A-Za-z0-9_-]{11}$/', $yt))
        || ($mp4 !== '' && preg_match('#^assets/[A-Za-z0-9_./-]+\.mp4$#', $mp4) && !str_contains($mp4, '..'));
    if (!empty($v['name']) && $ok) {
        $vtItems[] = $v;
    }
}
$vtPlaceholder = !$vtItems;
if ($vtPlaceholder) {
    $vtItems = [[], [], []];
}
?>
<div class="video-testimonials" id="videoTestimonials" style="margin-top: 52px;">
  <h3 class="heading-serif" style="text-align: center; color: var(--color-text); font-size: 1.8rem; margin-bottom: 12px;">
    Video <span style="color: var(--color-primary);">Testimonials</span>
  </h3>
  <p class="section-desc" style="text-align: center; max-width: 640px; margin: 0 auto 28px;">
    Hear directly from Club Yatri members about their holidays.
  </p>

  <div class="grid-3 video-grid">
    <?php foreach ($vtItems as $v): ?>
      <?php
        $yt = (string)($v['youtube'] ?? '');
        $mp4 = (string)($v['mp4'] ?? '');
        $poster = $yt !== '' ? 'https://i.ytimg.com/vi/' . $yt . '/hqdefault.jpg' : (!empty($v['poster']) ? url((string)$v['poster']) : '');
      ?>
      <article class="video-card<?php echo $vtPlaceholder ? ' is-placeholder' : ''; ?>">
        <div class="video-frame"<?php echo (!$vtPlaceholder && $poster !== '') ? ' style="background-image: url(\'' . e($poster) . '\');"' : ''; ?>>
          <?php if ($vtPlaceholder): ?>
            <span class="video-play" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="30" height="30" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
            </span>
            <span class="video-soon">Video coming soon</span>
          <?php else: ?>
            <button type="button" class="video-play"
                    <?php echo $yt !== '' ? 'data-youtube="' . e($yt) . '"' : 'data-mp4="' . e(url($mp4)) . '"'; ?>
                    data-title="<?php echo e('Video testimonial from ' . $v['name']); ?>"
                    aria-label="Play video testimonial from <?php echo e($v['name']); ?>">
              <svg viewBox="0 0 24 24" width="30" height="30" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
            </button>
          <?php endif; ?>
        </div>
        <div class="video-meta">
          <?php if ($vtPlaceholder): ?>
            <strong>Member story</strong>
            <span>Coming soon</span>
          <?php else: ?>
            <strong><?php echo e($v['name']); ?></strong>
            <?php if (!empty($v['detail'])): ?><span><?php echo e($v['detail']); ?></span><?php endif; ?>
            <?php if (!empty($v['quote'])): ?><p>&ldquo;<?php echo e($v['quote']); ?>&rdquo;</p><?php endif; ?>
          <?php endif; ?>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</div>

<script>
document.querySelectorAll('#videoTestimonials .video-play[data-youtube], #videoTestimonials .video-play[data-mp4]').forEach(function (btn) {
  btn.addEventListener('click', function () {
    var frame = btn.parentNode, title = btn.getAttribute('data-title') || 'Video testimonial', el;
    if (btn.hasAttribute('data-youtube')) {
      el = document.createElement('iframe');
      el.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(btn.getAttribute('data-youtube')) + '?autoplay=1&rel=0';
      el.title = title;
      el.allow = 'accelerometer; autoplay; encrypted-media; picture-in-picture; fullscreen';
      el.allowFullscreen = true;
      el.referrerPolicy = 'strict-origin-when-cross-origin';
    } else {
      el = document.createElement('video');
      el.src = btn.getAttribute('data-mp4');
      el.controls = true; el.autoplay = true; el.playsInline = true;
      el.setAttribute('aria-label', title);
    }
    frame.innerHTML = '';
    frame.style.backgroundImage = 'none';
    frame.appendChild(el);
  });
});
</script>
