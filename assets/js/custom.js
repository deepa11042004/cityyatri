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
