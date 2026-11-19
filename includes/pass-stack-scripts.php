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
/**
 * Club Yatri - 3D Rotating & Flipping Pass Showcase Controller
 * 1. Initial entrance: Card 1 drops down from top and starts rotating continuously in 3D.
 * 2. As user scrolls cursor: Active card flips away, next card flips in and starts rotating.
 * 3. Reverse scroll flips backwards.
 * 4. Direct tab selection and arrow buttons switch cards with 3D flip.
 */
(function () {
  'use strict';

  var track = document.getElementById('passShowcaseTrack');
  var stage = document.getElementById('passShowcaseStage');
  if (!track || !stage) { return; }

  var cards = Array.prototype.slice.call(track.querySelectorAll('.pass-3d-card-unit'));
  var tabs = Array.prototype.slice.call(stage.querySelectorAll('.pass-stage-tab'));
  var prevBtn = document.getElementById('passArrowPrev');
  var nextBtn = document.getElementById('passArrowNext');
  
  var badgeEl = document.getElementById('passActiveBadge');
  var titleEl = document.getElementById('passActiveTitle');
  var subEl = document.getElementById('passActiveSub');
  var ctaEl = document.getElementById('passActiveCta');

  if (!cards.length) { return; }

  var currentIndex = 0;
  var isAnimating = false;
  var hasEntered = false;
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Initialize first card
  cards.forEach(function (c, idx) {
    if (idx === 0) {
      c.classList.add('is-active');
    } else {
      c.classList.remove('is-active', 'is-rotating', 'animate-come-down');
    }
  });

  // Entrance: First card drops down and starts rotating
  function triggerEntrance() {
    if (hasEntered) { return; }
    hasEntered = true;
    var firstCard = cards[0];
    if (!firstCard) { return; }

    if (reduceMotion) {
      firstCard.classList.add('is-active');
      return;
    }

    firstCard.classList.add('animate-come-down');
    setTimeout(function () {
      firstCard.classList.remove('animate-come-down');
      firstCard.classList.add('is-rotating');
    }, 1150);
  }

  // IntersectionObserver to trigger drop down when section appears
  if ('IntersectionObserver' in window) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting && !hasEntered) {
          triggerEntrance();
          observer.disconnect();
        }
      });
    }, { threshold: 0.15 });
    observer.observe(track);
  } else {
    triggerEntrance();
  }

  // Update dynamic footer info
  function updateFooter(card) {
    if (!card) { return; }
    var title = card.getAttribute('data-title') || '';
    var badge = card.getAttribute('data-badge') || '';
    var sub = card.getAttribute('data-subtitle') || '';
    var years = card.getAttribute('data-years') || '3';
    var plan = card.getAttribute('data-plan') || '';

    if (badgeEl) {
      badgeEl.style.opacity = '0';
      setTimeout(function () {
        badgeEl.textContent = badge;
        badgeEl.style.opacity = '1';
      }, 150);
    }
    if (titleEl) {
      titleEl.style.opacity = '0';
      setTimeout(function () {
        titleEl.textContent = title;
        titleEl.style.opacity = '1';
      }, 150);
    }
    if (subEl) {
      subEl.style.opacity = '0';
      setTimeout(function () {
        subEl.textContent = sub;
        subEl.style.opacity = '1';
      }, 150);
    }
    if (ctaEl) {
      ctaEl.textContent = 'Enquire ' + years + '-Year Pass';
      ctaEl.href = 'booking-request.php?plan=' + encodeURIComponent(plan);
    }
  }

  // Update tabs active state
  function updateTabs(idx) {
    tabs.forEach(function (tab, i) {
      if (i === idx) {
        tab.classList.add('is-active');
        tab.setAttribute('aria-selected', 'true');
      } else {
        tab.classList.remove('is-active');
        tab.setAttribute('aria-selected', 'false');
      }
    });
  }

  // 3D Flip to target card
  function flipToCard(targetIdx, direction) {
    if (targetIdx === currentIndex || isAnimating || targetIdx < 0 || targetIdx >= cards.length) {
      return;
    }

    isAnimating = true;
    var dir = direction || (targetIdx > currentIndex ? 'forward' : 'reverse');
    var oldCard = cards[currentIndex];
    var newCard = cards[targetIdx];

    updateTabs(targetIdx);
    updateFooter(newCard);

    if (reduceMotion) {
      oldCard.classList.remove('is-active', 'is-rotating');
      newCard.classList.add('is-active');
      currentIndex = targetIdx;
      isAnimating = false;
      return;
    }

    // Stop rotation on old card and flip out
    oldCard.classList.remove('is-rotating', 'flip-in-forward', 'flip-in-reverse', 'animate-come-down');
    oldCard.classList.add(dir === 'forward' ? 'flip-out-forward' : 'flip-out-reverse');

    // Prepare new card to flip in
    newCard.classList.remove('is-rotating', 'flip-out-forward', 'flip-out-reverse', 'animate-come-down');
    newCard.classList.add('is-active');
    newCard.classList.add(dir === 'forward' ? 'flip-in-forward' : 'flip-in-reverse');

    setTimeout(function () {
      // Clean up old card
      oldCard.classList.remove('is-active', 'flip-out-forward', 'flip-out-reverse');

      // Finish flip in on new card & start continuous 360 rotation!
      newCard.classList.remove('flip-in-forward', 'flip-in-reverse');
      newCard.classList.add('is-rotating');

      currentIndex = targetIdx;
      isAnimating = false;
    }, 720);
  }

  // Tab clicks
  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      var targetIdx = parseInt(tab.getAttribute('data-index'), 10);
      if (targetIdx !== currentIndex && !isAnimating) {
        var dir = targetIdx > currentIndex ? 'forward' : 'reverse';
        flipToCard(targetIdx, dir);

        // Sync scroll position with tab
        var rect = track.getBoundingClientRect();
        var scrollable = track.offsetHeight - window.innerHeight;
        if (scrollable > 0) {
          var targetY = window.pageYOffset + rect.top + (targetIdx / (cards.length - 1)) * scrollable;
          window.scrollTo({ top: targetY, behavior: 'smooth' });
        }
      }
    });
  });

  // Prev / Next arrow buttons
  if (prevBtn) {
    prevBtn.addEventListener('click', function () {
      if (isAnimating) { return; }
      var targetIdx = (currentIndex - 1 + cards.length) % cards.length;
      flipToCard(targetIdx, 'reverse');

      var rect = track.getBoundingClientRect();
      var scrollable = track.offsetHeight - window.innerHeight;
      if (scrollable > 0) {
        var targetY = window.pageYOffset + rect.top + (targetIdx / (cards.length - 1)) * scrollable;
        window.scrollTo({ top: targetY, behavior: 'smooth' });
      }
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', function () {
      if (isAnimating) { return; }
      var targetIdx = (currentIndex + 1) % cards.length;
      flipToCard(targetIdx, 'forward');

      var rect = track.getBoundingClientRect();
      var scrollable = track.offsetHeight - window.innerHeight;
      if (scrollable > 0) {
        var targetY = window.pageYOffset + rect.top + (targetIdx / (cards.length - 1)) * scrollable;
        window.scrollTo({ top: targetY, behavior: 'smooth' });
      }
    });
  }

  // Scroll listener: Drive card flips as cursor scrolls through track
  var ticking = false;
  function onScroll() {
    ticking = false;
    if (!hasEntered) {
      triggerEntrance();
    }
    if (isAnimating) { return; }

    var rect = track.getBoundingClientRect();
    var windowH = window.innerHeight;
    var scrollable = track.offsetHeight - windowH;

    if (scrollable <= 0) { return; }

    // Check if stage is within active sticky zone
    if (rect.top <= 100 && rect.bottom >= windowH) {
      var progress = Math.min(1, Math.max(0, -rect.top / scrollable));
      // Slices for each card: 0: [0, 0.25), 1: [0.25, 0.50), 2: [0.50, 0.75), 3: [0.75, 1.0]
      var targetIdx = Math.min(cards.length - 1, Math.floor(progress * cards.length));
      if (targetIdx !== currentIndex) {
        flipToCard(targetIdx, targetIdx > currentIndex ? 'forward' : 'reverse');
      }
    }
  }

  window.addEventListener('scroll', function () {
    if (!ticking) {
      ticking = true;
      requestAnimationFrame(onScroll);
    }
  }, { passive: true });

  // Optional: Gentle mouse wheel nudge directly over stage for instant tactile feel
  stage.addEventListener('wheel', function (e) {
    if (Math.abs(e.deltaY) < 18 || isAnimating) { return; }
    var rect = track.getBoundingClientRect();
    var windowH = window.innerHeight;
    if (rect.top <= 100 && rect.bottom >= windowH) {
      if (e.deltaY > 0 && currentIndex < cards.length - 1) {
        // Scrolling down -> flip to next
        flipToCard(currentIndex + 1, 'forward');
      } else if (e.deltaY < 0 && currentIndex > 0) {
        // Scrolling up -> flip to prev
        flipToCard(currentIndex - 1, 'reverse');
      }
    }
  }, { passive: true });

})();
</script>
