/**
 * Club Yatri - Interactive Client Logic & Utilities
 */

document.addEventListener('DOMContentLoaded', () => {
  initNavbar();
  initMobileDrawer();
  initHeroSearch();
  initTabsFilter();
  initRoiCalculator();
  initFaqAccordion();
  initModals();
  initNewsletter();
});

/* 1. Navbar Sticky Effect */
function initNavbar() {
  const navbar = document.querySelector('.navbar');
  if (!navbar) return;

  window.addEventListener('scroll', () => {
    if (window.scrollY > 40) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });
}

/* 2. Mobile Drawer Navigation */
function initMobileDrawer() {
  const toggleBtn = document.querySelector('.mobile-toggle');
  const drawer = document.querySelector('.mobile-nav-drawer');
  const backdrop = document.querySelector('.drawer-backdrop');
  const closeBtn = document.querySelector('.drawer-close');

  if (!toggleBtn || !drawer || !backdrop) return;

  function openDrawer() {
    drawer.classList.add('open');
    backdrop.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer() {
    drawer.classList.remove('open');
    backdrop.classList.remove('active');
    document.body.style.overflow = '';
  }

  toggleBtn.addEventListener('click', openDrawer);
  if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
  backdrop.addEventListener('click', closeDrawer);

  const links = drawer.querySelectorAll('.drawer-link');
  links.forEach(link => {
    link.addEventListener('click', closeDrawer);
  });
}

/* 3. Hero Search & Preview */
function initHeroSearch() {
  const searchForm = document.getElementById('heroSearchForm');
  if (!searchForm) return;

  searchForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const dest = document.getElementById('searchDestination')?.value || 'All Destinations';
    const dates = document.getElementById('searchDates')?.value || 'Flexible';
    const guests = document.getElementById('searchGuests')?.value || '2 Guests';
    
    // Open Booking/Inquiry Modal pre-filled
    openInquiryModal({
      destination: dest,
      dates: dates,
      guests: guests,
      source: 'Hero Search'
    });
  });
}

/* 4. Tab Category Filtering */
function initTabsFilter() {
  const tabs = document.querySelectorAll('.tab-btn[data-category]');
  const cards = document.querySelectorAll('.card-luxury[data-category]');

  if (!tabs.length || !cards.length) return;

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      const targetCategory = tab.getAttribute('data-category');

      cards.forEach(card => {
        const categories = card.getAttribute('data-category') || '';
        if (targetCategory === 'all' || categories.includes(targetCategory)) {
          card.style.display = 'flex';
          card.style.animation = 'fadeInUp 0.4s ease';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });
}

/* 5. 10-Year Vacation ROI & Savings Calculator */
function initRoiCalculator() {
  const nightsSlider = document.getElementById('calcNightsSlider');
  const nightsValBadge = document.getElementById('calcNightsVal');
  const hotelRateSlider = document.getElementById('calcRateSlider');
  const rateValBadge = document.getElementById('calcRateVal');
  const savingsDisplay = document.getElementById('calcSavingsDisplay');
  const hotelSpendDisplay = document.getElementById('calcHotelSpendDisplay');
  const clubSpendDisplay = document.getElementById('calcClubSpendDisplay');

  if (!nightsSlider || !hotelRateSlider || !savingsDisplay) return;

  function recalculate() {
    const nights = parseInt(nightsSlider.value, 10);
    const ratePerNight = parseInt(hotelRateSlider.value, 10);

    if (nightsValBadge) nightsValBadge.textContent = `${nights} Nights / Year`;
    if (rateValBadge) rateValBadge.textContent = `₹${ratePerNight.toLocaleString('en-IN')}`;

    // 10-year calculations with 7% annual hotel inflation
    let totalHotelSpend = 0;
    let currentRate = ratePerNight;
    for (let year = 1; year <= 10; year++) {
      totalHotelSpend += nights * currentRate;
      currentRate *= 1.07; // 7% annual hotel rate escalation
    }

    // Club Yatri vacation ownership estimation (Membership fee amortized + minimal maintenance)
    // Average Club Yatri 10-year cost is ~42% of volatile retail 5-star hotel spending
    const clubOwnershipCost = Math.round(totalHotelSpend * 0.42);
    const totalSavings = Math.max(0, Math.round(totalHotelSpend - clubOwnershipCost));

    if (hotelSpendDisplay) {
      hotelSpendDisplay.textContent = `₹${Math.round(totalHotelSpend).toLocaleString('en-IN')}`;
    }
    if (clubSpendDisplay) {
      clubSpendDisplay.textContent = `₹${clubOwnershipCost.toLocaleString('en-IN')}`;
    }
    if (savingsDisplay) {
      savingsDisplay.textContent = `₹${totalSavings.toLocaleString('en-IN')}`;
    }
  }

  nightsSlider.addEventListener('input', recalculate);
  hotelRateSlider.addEventListener('input', recalculate);
  recalculate();
}

/* 6. FAQ Accordion */
function initFaqAccordion() {
  const faqHeaders = document.querySelectorAll('.faq-header');
  faqHeaders.forEach(header => {
    header.addEventListener('click', () => {
      const parentCard = header.closest('.faq-card');
      const isOpen = parentCard.classList.contains('open');

      // Close all other open cards
      document.querySelectorAll('.faq-card.open').forEach(card => {
        if (card !== parentCard) card.classList.remove('open');
      });

      if (isOpen) {
        parentCard.classList.remove('open');
      } else {
        parentCard.classList.add('open');
      }
    });
  });
}

/* 7. Modals (Inquiry / Booking / Resort Details) */
function initModals() {
  const modalBackdrop = document.getElementById('inquiryModal');
  const closeBtns = document.querySelectorAll('[data-close-modal]');

  if (modalBackdrop) {
    closeBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        modalBackdrop.classList.remove('active');
        document.body.style.overflow = '';
      });
    });

    modalBackdrop.addEventListener('click', (e) => {
      if (e.target === modalBackdrop) {
        modalBackdrop.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  }

  // Handle Form Submission
  const inquiryForm = document.getElementById('clubInquiryForm');
  if (inquiryForm) {
    inquiryForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const submitBtn = inquiryForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;

      submitBtn.disabled = true;
      submitBtn.innerHTML = `<span>Securing Concierge...</span>`;

      setTimeout(() => {
        inquiryForm.innerHTML = `
          <div style="text-align: center; padding: 30px 10px;">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(0, 201, 167, 0.15); color: #00c9a7; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; font-size: 28px;">✓</div>
            <h3 class="heading-display" style="font-size: 1.6rem; margin-bottom: 12px; color: #fff;">Inquiry Received with Priority</h3>
            <p style="color: var(--text-secondary); max-width: 440px; margin: 0 auto 24px auto; font-size: 0.95rem; line-height: 1.6;">
              Thank you for choosing <strong>Club Yatri</strong>. Your dedicated Senior Lifestyle Concierge has been assigned and will connect with you within 30 minutes.
            </p>
            <button class="btn btn-outline-gold" onclick="document.getElementById('inquiryModal').classList.remove('active'); document.body.style.overflow='';">Close Window</button>
          </div>
        `;
      }, 1000);
    });
  }

  // Trigger buttons with data-open-modal
  document.querySelectorAll('[data-open-modal]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const plan = btn.getAttribute('data-plan') || '';
      openInquiryModal({ plan: plan });
    });
  });
}

function openInquiryModal(data = {}) {
  const modalBackdrop = document.getElementById('inquiryModal');
  if (!modalBackdrop) return;

  const notesField = document.getElementById('modalNotes');
  if (notesField && data.destination) {
    notesField.value = `Destination: ${data.destination} | Dates: ${data.dates} | Travelers: ${data.guests}`;
  } else if (notesField && data.plan) {
    notesField.value = `Interested in Tier: ${data.plan}`;
  }

  modalBackdrop.classList.add('active');
  document.body.style.overflow = 'hidden';
}

/* 8. Newsletter Subscription Toast */
function initNewsletter() {
  const form = document.getElementById('newsletterForm');
  if (!form) return;

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const input = form.querySelector('input[type="email"]');
    if (!input || !input.value) return;

    const email = input.value;
    input.value = '';
    showToast(`Welcome to the Club Yatri Journal! An exclusive invitation has been dispatched to ${email}.`);
  });
}

function showToast(msg) {
  let toast = document.getElementById('clubToast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'clubToast';
    toast.style.position = 'fixed';
    toast.style.bottom = '28px';
    toast.style.right = '28px';
    toast.style.background = 'linear-gradient(135deg, #101c2a 0%, #162436 100%)';
    toast.style.color = '#fff';
    toast.style.border = '1px solid #d4af37';
    toast.style.padding = '16px 24px';
    toast.style.borderRadius = '12px';
    toast.style.boxShadow = '0 10px 30px rgba(0,0,0,0.6), 0 0 20px rgba(212,175,55,0.3)';
    toast.style.zIndex = '9999';
    toast.style.fontSize = '0.92rem';
    toast.style.maxWidth = '380px';
    toast.style.lineHeight = '1.5';
    toast.style.transition = 'all 0.3s ease';
    document.body.appendChild(toast);
  }

  toast.textContent = msg;
  toast.style.opacity = '1';
  toast.style.transform = 'translateY(0)';

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(15px)';
  }, 4500);
}
