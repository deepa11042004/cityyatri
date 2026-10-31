<?php
/**
 * Club Yatri - "Recent activity" popup (bottom-left). Shows real, anonymised booking activity from
 * recent-activity.php, one item at a time. Nothing is shown when there is no recent activity.
 * Add ?activity_preview=1 to any page URL to preview the design with clearly labelled sample items.
 */

declare(strict_types=1);

$activityPreview = (($_GET['activity_preview'] ?? '') === '1');
$activityEndpoint = url('recent-activity.php') . ($activityPreview ? '?preview=1' : '');
?>
<aside class="activity-toast" id="activityToast" role="status" aria-live="polite" aria-atomic="true" hidden
       data-endpoint="<?php echo e($activityEndpoint); ?>">
  <span class="activity-icon" aria-hidden="true">
    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
  </span>
  <div class="activity-body">
    <p class="activity-text" id="activityText"></p>
    <p class="activity-meta"><span id="activityTime"></span><span class="activity-sample" id="activitySample" hidden>Sample preview</span></p>
  </div>
  <button type="button" class="activity-close" id="activityClose" aria-label="Dismiss recent activity notifications">&times;</button>
</aside>

<script>
(function () {
  var toast = document.getElementById('activityToast');
  if (!toast || !window.fetch) { return; }
  var KEY = 'cyActivityDismissed';
  try { if (sessionStorage.getItem(KEY) === '1') { return; } } catch (e) {}

  var textEl = document.getElementById('activityText');
  var timeEl = document.getElementById('activityTime');
  var sampleEl = document.getElementById('activitySample');
  var closeBtn = document.getElementById('activityClose');
  var items = [], idx = 0, timer = null, paused = false, stopped = false;
  var SHOW_MS = 6500, GAP_MS = 9000, FIRST_MS = 5000;

  function hide() { toast.classList.remove('is-visible'); }
  function schedule(fn, ms) { clearTimeout(timer); timer = setTimeout(fn, ms); }

  function show() {
    if (stopped) { return; }
    if (paused) { return schedule(show, 1000); }
    var it = items[idx % items.length]; idx++;
    textEl.textContent = it.text;
    timeEl.textContent = it.when;
    sampleEl.hidden = !it.sample;
    toast.hidden = false;
    requestAnimationFrame(function () { toast.classList.add('is-visible'); });
    schedule(function () { hide(); schedule(show, GAP_MS); }, SHOW_MS);
  }

  toast.addEventListener('mouseenter', function () { paused = true; });
  toast.addEventListener('mouseleave', function () { paused = false; });
  toast.addEventListener('focusin', function () { paused = true; });
  toast.addEventListener('focusout', function () { paused = false; });

  closeBtn.addEventListener('click', function () {
    stopped = true; clearTimeout(timer); hide();
    try { sessionStorage.setItem(KEY, '1'); } catch (e) {}
  });

  fetch(toast.getAttribute('data-endpoint'), { headers: { 'Accept': 'application/json' } })
    .then(function (r) { return r.ok ? r.json() : { items: [] }; })
    .then(function (data) {
      items = (data && data.items) || [];
      if (items.length) { schedule(show, FIRST_MS); }
    })
    .catch(function () { /* no activity popup if the request fails */ });
})();
</script>
