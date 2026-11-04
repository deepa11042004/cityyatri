<div class="pass-lightbox" id="passLightbox" role="dialog" aria-modal="true" aria-label="Pass image" hidden>
  <button type="button" class="pass-lightbox-close" aria-label="Close">&times;</button>
  <img id="passLightboxImg" src="" alt="">
</div>
<script>
(function () {
  var box = document.getElementById('passLightbox');
  var big = document.getElementById('passLightboxImg');
  if (!box) { return; }
  function close() { box.hidden = true; big.src = ''; document.body.style.overflow = ''; }
  document.querySelectorAll('.pass-zoom').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var img = btn.querySelector('img');
      big.src = img.currentSrc || img.src; big.alt = img.alt;
      box.hidden = false; document.body.style.overflow = 'hidden';
      box.querySelector('.pass-lightbox-close').focus();
    });
  });
  box.addEventListener('click', function (e) { if (e.target !== big) { close(); } });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !box.hidden) { close(); } });
})();
</script>

<script>
/* Stacked pass cards (scroll-linked): every card shrinks a little as it pins to the top, and cards further
   back in the stack shrink more as later cards slide up over them. */
(function () {
  var stack = document.getElementById('passStack');
  if (!stack) { return; }
  var cards = Array.prototype.slice.call(stack.querySelectorAll('.pass-card'));
  var anchors = Array.prototype.slice.call(stack.querySelectorAll('.pass-anchor'));
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)');
  var ticking = false;
  var OWN = 0.05, DEPTH = 0.04;

  function clamp(v) { return Math.min(1, Math.max(0, v)); }

  function update() {
    ticking = false;
    var sticky = getComputedStyle(cards[0]).position === 'sticky';
    if (!sticky || reduce.matches) {
      cards.forEach(function (c) { c.style.transform = ''; });
      return;
    }
    var vh = window.innerHeight;
    var pins = cards.map(function (c) { return parseFloat(getComputedStyle(c).top) || 0; });
    var nats = anchors.map(function (a) { return a.getBoundingClientRect().top; });   // where each card would be without sticky
    cards.forEach(function (card, i) {
      var own = clamp((pins[i] - nats[i]) / (vh * 0.55));            // shrink while pinned
      var deep = 0;                                                    // shrink as later cards arrive
      for (var j = i + 1; j < cards.length; j++) { deep += clamp(1 - (nats[j] - pins[j]) / (vh * 0.85)); }
      var scale = 1 - OWN * own - DEPTH * deep;
      card.style.transform = 'scale(' + scale.toFixed(4) + ')';
    });
  }
  function onScroll() { if (!ticking) { ticking = true; requestAnimationFrame(update); } }

  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll);
  update();
})();
</script>
