/**
 * Club Yatri - Redesign layer: fade-in on scroll.
 * Content stays visible if JS or IntersectionObserver is unavailable
 * (the .js class that hides .reveal is only added by the inline script in header.php).
 */
(function () {
  'use strict';

  var selectors = [
    '.section-title-wrap',
    '.bento-tile',
    '.flagship-card-25y',
    '.today-office-card',
    '.card-luxury',
    '.process-visual',
    '.process-step',
    '.transparency-notice-box',
    '.trust-badge-card',
    '.faq-card'
  ];

  var nodes = document.querySelectorAll(selectors.join(','));
  if (!nodes.length) return;

  if (!('IntersectionObserver' in window)) {
    document.documentElement.classList.remove('js');
    return;
  }

  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        var el = entry.target;
        el.classList.add('in');
        io.unobserve(el);
        // Drop the stagger delay afterwards so hover transitions stay instant
        setTimeout(function () { el.style.transitionDelay = ''; }, 1000);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

  nodes.forEach(function (el, i) {
    el.classList.add('reveal');
    el.style.transitionDelay = (i % 4) * 60 + 'ms';
    io.observe(el);
  });
})();

/**
 * Hero background slider: auto-rotates, with arrows and dots.
 * Pauses on hover/focus and when the tab is hidden; no autoplay when the
 * visitor prefers reduced motion (arrows and dots still work).
 */
(function () {
  'use strict';

  var hero = document.getElementById('homeHero');
  var wrap = document.getElementById('heroSlides');
  if (!hero || !wrap) return;

  var slides = Array.prototype.slice.call(wrap.querySelectorAll('.hero-slide'));
  var dotsWrap = document.getElementById('heroDots');
  var prevBtn = document.getElementById('heroPrev');
  var nextBtn = document.getElementById('heroNext');
  var controls = hero.querySelector('.hero-controls');

  if (slides.length < 2) {
    if (controls) controls.style.display = 'none';
    return;
  }

  var INTERVAL = 5500;
  var current = 0;
  var timer = null;
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  var dots = slides.map(function (_, i) {
    var b = document.createElement('button');
    b.type = 'button';
    b.className = 'hero-dot';
    b.setAttribute('aria-label', 'Show background image ' + (i + 1));
    b.addEventListener('click', function () { go(i, true); });
    if (dotsWrap) dotsWrap.appendChild(b);
    return b;
  });

  function render() {
    slides.forEach(function (s, i) { s.classList.toggle('is-active', i === current); });
    dots.forEach(function (d, i) {
      d.classList.toggle('is-active', i === current);
      d.setAttribute('aria-current', i === current ? 'true' : 'false');
    });
  }

  function go(index, userAction) {
    current = (index + slides.length) % slides.length;
    render();
    if (userAction) restart();
  }

  function start() {
    if (reduceMotion || timer) return;
    timer = setInterval(function () { go(current + 1); }, INTERVAL);
  }
  function stop() { clearInterval(timer); timer = null; }
  function restart() { stop(); start(); }

  if (prevBtn) prevBtn.addEventListener('click', function () { go(current - 1, true); });
  if (nextBtn) nextBtn.addEventListener('click', function () { go(current + 1, true); });

  hero.addEventListener('mouseenter', stop);
  hero.addEventListener('mouseleave', start);
  hero.addEventListener('focusin', stop);
  hero.addEventListener('focusout', start);
  document.addEventListener('visibilitychange', function () { document.hidden ? stop() : start(); });

  render();
  start();
})();
