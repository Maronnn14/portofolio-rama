/* ============================================
   MAIN.JS — Scroll Reveal, Utilities, Admin
   ============================================ */

document.addEventListener('DOMContentLoaded', () => {
  applyAppearanceSettings();
  initNavbar();
  initScrollReveal();
  initSmoothScroll();
  // Admin auth modal (rendered once on any page)
  if (typeof renderLoginModal === 'function') {
    document.body.insertAdjacentHTML('beforeend', renderLoginModal());
    initLoginModal();
    (async () => {
      if (typeof AdminAuth !== 'undefined') {
        await AdminAuth.init();
      }
      updateNavbarAuthState();
      if (typeof AdminAuth !== 'undefined' && AdminAuth.isAuthenticated()) {
        showAdminBanner();
      }
    })();
  }
});

/* ---- Apply Admin Appearance Settings ---- */
function applyAppearanceSettings() {
  try {
    const appearance = JSON.parse(localStorage.getItem('portfolio_appearance') || '{}');
    if (appearance.accentColor) {
      if (typeof setAllAccentVars === 'function') {
        setAllAccentVars(appearance.accentColor);
      } else {
        const hex = appearance.accentColor;
        const _hexToRgb = (h) => { h = h.replace('#',''); const n = parseInt(h,16); return { r:(n>>16)&255, g:(n>>8)&255, b:n&255 }; };
        const _lighten = (h, p) => { const {r,g,b} = _hexToRgb(h); const t=p/100; return '#'+[r+(255-r)*t,g+(255-g)*t,b+(255-b)*t].map(v=>Math.max(0,Math.min(255,Math.round(v))).toString(16).padStart(2,'0')).join(''); };
        const _darken  = (h, p) => { const {r,g,b} = _hexToRgb(h); const t=1-p/100; return '#'+[r*t,g*t,b*t].map(v=>Math.max(0,Math.min(255,Math.round(v))).toString(16).padStart(2,'0')).join(''); };
        const { r, g, b } = _hexToRgb(hex);
        const light = _lighten(hex, 15);
        const dark  = _darken(hex, 15);
        const root = document.documentElement.style;
        root.setProperty('--accent',             hex);
        root.setProperty('--accent-light',       light);
        root.setProperty('--accent-dark',        dark);
        root.setProperty('--accent-glow',        `rgba(${r}, ${g}, ${b}, 0.15)`);
        root.setProperty('--accent-glow-strong', `rgba(${r}, ${g}, ${b}, 0.3)`);
        root.setProperty('--border-accent',      `rgba(${r}, ${g}, ${b}, 0.3)`);
        root.setProperty('--shadow-glow',        `0 0 30px rgba(${r}, ${g}, ${b}, 0.15)`);
        root.setProperty('--shadow-glow-strong', `0 0 50px rgba(${r}, ${g}, ${b}, 0.25)`);
        root.setProperty('--color-accent',       hex);
        root.setProperty('--color-accent-light', light);
        root.setProperty('--color-accent-dark',  dark);
      }
    }
    if (appearance.borderRadius !== undefined) {
      document.documentElement.style.setProperty('--radius-lg', appearance.borderRadius + 'px');
      document.documentElement.style.setProperty('--radius-xl', Math.min(appearance.borderRadius + 4, 28) + 'px');
      document.documentElement.style.setProperty('--radius-md', Math.max(appearance.borderRadius - 4, 2) + 'px');
    }
  } catch { /* ignore */ }
}

/* ---- Navbar ---- */
function initNavbar() {
  // Existing navbar logic (scroll effect, active link, etc. are handled in initLayout/renderNavbar and dynamically bound if needed)
  window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (navbar) {
      if (window.scrollY > 50) navbar.classList.add('scrolled');
      else navbar.classList.remove('scrolled');
    }
  }, { passive: true });
}

function setActiveNavLink() {
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';
  const navLinks = document.querySelectorAll('.navbar__link, .navbar__mobile-link');

  navLinks.forEach(link => {
    const href = link.getAttribute('href');
    if (!href) return;
    const linkPage = href.split('/').pop();

    if (linkPage === currentPage ||
        (currentPage === '' && linkPage === 'index.html') ||
        (currentPage === 'index.html' && linkPage === 'index.html') ||
        (currentPage.startsWith('project-detail') && linkPage === 'projects.html')) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });
}

/* ---- Scroll Reveal ---- */
function initScrollReveal() {
  const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');
  if (revealElements.length === 0) return;

  // Immediately reveal elements in the viewport (no observer delay)
  const viewportH = window.innerHeight;
  revealElements.forEach(el => {
    const rect = el.getBoundingClientRect();
    if (rect.top < viewportH && rect.bottom > 0) {
      el.style.transition = 'none';
      el.classList.add('revealed');
    }
  });

  // Force reflow so the no-transition reveal takes effect
  void document.body.offsetHeight;

  // Restore transitions for elements not yet revealed
  revealElements.forEach(el => {
    if (!el.classList.contains('revealed')) {
      el.style.transition = '';
    }
  });

  // Observe remaining elements for scroll-based reveal
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('revealed');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
  revealElements.forEach(el => {
    if (!el.classList.contains('revealed')) {
      observer.observe(el);
    }
  });
}

/* ---- Smooth Scroll ---- */
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', (e) => {
      const targetId = anchor.getAttribute('href');
      if (targetId === '#') return;
      const target = document.querySelector(targetId);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
}

/* ---- Utilities ---- */
function getRelativeTime(timestamp) {
  const diff = Date.now() - timestamp;
  const seconds = Math.floor(diff / 1000);
  const minutes = Math.floor(seconds / 60);
  const hours = Math.floor(minutes / 60);
  const days = Math.floor(hours / 24);
  const weeks = Math.floor(days / 7);
  const months = Math.floor(days / 30);

  if (seconds < 60) return 'Just now';
  if (minutes < 60) return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
  if (hours < 24) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
  if (days < 7) return `${days} day${days > 1 ? 's' : ''} ago`;
  if (weeks < 5) return `${weeks} week${weeks > 1 ? 's' : ''} ago`;
  return `${months} month${months > 1 ? 's' : ''} ago`;
}

function getInitials(name) {
  return name.split(' ').map(w => w[0]).join('').toUpperCase().substring(0, 2);
}

function getSessionToken() {
  let token = localStorage.getItem('portfolio_session_token');
  if (!token) {
    token = 'session_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);
    localStorage.setItem('portfolio_session_token', token);
  }
  return token;
}

function renderStars(rating, readonly = true) {
  let html = '<div class="star-rating">';
  for (let i = 1; i <= 5; i++) {
    html += `<span class="star-rating__star ${i <= rating ? 'active' : ''} ${readonly ? 'readonly' : ''}" data-rating="${i}">★</span>`;
  }
  html += '</div>';
  return html;
}

/* ---- Hamburger Menu Binding ---- */
(function bindHamburger() {
  document.addEventListener('DOMContentLoaded', () => {
    const hamburger = document.querySelector('.navbar__hamburger');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileOverlay = document.getElementById('mobile-overlay');
    const closeBtn = document.getElementById('mobile-close-btn');

    function openMobile() {
      hamburger?.classList.add('open');
      mobileMenu?.classList.add('open');
      mobileOverlay?.classList.add('open');
      document.body.style.overflow = 'hidden';
    }

    function closeMobile() {
      hamburger?.classList.remove('open');
      mobileMenu?.classList.remove('open');
      mobileOverlay?.classList.remove('open');
      document.body.style.overflow = '';
    }

    if (hamburger && mobileMenu) {
      hamburger.addEventListener('click', () => {
        if (mobileMenu.classList.contains('open')) {
          closeMobile();
        } else {
          openMobile();
        }
      });

      if (closeBtn) {
        closeBtn.addEventListener('click', closeMobile);
      }

      if (mobileOverlay) {
        mobileOverlay.addEventListener('click', closeMobile);
      }

      document.querySelectorAll('.navbar__mobile-link').forEach(link => {
        link.addEventListener('click', closeMobile);
      });

      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && mobileMenu?.classList.contains('open')) {
          closeMobile();
        }
      });
    }
    setActiveNavLink();
  });
})();


