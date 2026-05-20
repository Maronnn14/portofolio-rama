/* ============================================
   MAIN.JS — Shared Navigation, Scroll Reveal
   ============================================ */

document.addEventListener('DOMContentLoaded', () => {
  applyAppearanceSettings();
  initScrollReveal();
  initSmoothScroll();
});

/* ---- Apply Admin Appearance Settings ---- */
function applyAppearanceSettings() {
  try {
    const appearance = JSON.parse(localStorage.getItem('portfolio_appearance') || '{}');
    if (appearance.accentColor) {
      document.documentElement.style.setProperty('--accent', appearance.accentColor);
      document.documentElement.style.setProperty('--accent-gold', appearance.accentColor);
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
  const navbar = document.querySelector('.navbar');
  const hamburger = document.querySelector('.navbar__hamburger');
  const mobileMenu = document.querySelector('.navbar__mobile-menu');
  const mobileLinks = document.querySelectorAll('.navbar__mobile-link');

  // Scroll effect
  let lastScroll = 0;
  window.addEventListener('scroll', () => {
    const currentScroll = window.scrollY;
    if (currentScroll > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
    lastScroll = currentScroll;
  }, { passive: true });

  // Hamburger toggle
  if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', () => {
      hamburger.classList.toggle('open');
      mobileMenu.classList.toggle('open');
      document.body.style.overflow = mobileMenu.classList.contains('open') ? 'hidden' : '';
    });

    mobileLinks.forEach(link => {
      link.addEventListener('click', () => {
        hamburger.classList.remove('open');
        mobileMenu.classList.remove('open');
        document.body.style.overflow = '';
      });
    });
  }

  // Set active nav link based on current page
  setActiveNavLink();
}

function setActiveNavLink() {
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';
  const navLinks = document.querySelectorAll('.navbar__link, .navbar__mobile-link');

  navLinks.forEach(link => {
    const href = link.getAttribute('href');
    if (!href) return;
    const linkPage = href.split('/').pop();

    // Match current page
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

/* ---- Scroll Reveal via Intersection Observer ---- */
function initScrollReveal() {
  const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');

  if (revealElements.length === 0) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('revealed');
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  });

  revealElements.forEach(el => observer.observe(el));
}

/* ---- Smooth Scroll for Anchor Links ---- */
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

/* ---- Utility: Relative Time ---- */
function getRelativeTime(timestamp) {
  const now = Date.now();
  const diff = now - timestamp;
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

/* ---- Utility: Get Initials ---- */
function getInitials(name) {
  return name
    .split(' ')
    .map(w => w[0])
    .join('')
    .toUpperCase()
    .substring(0, 2);
}

/* ---- Utility: Generate Session Token ---- */
function getSessionToken() {
  let token = localStorage.getItem('portfolio_session_token');
  if (!token) {
    token = 'session_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);
    localStorage.setItem('portfolio_session_token', token);
  }
  return token;
}

/* ---- Utility: Render Stars ---- */
function renderStars(rating, readonly = true) {
  let html = '<div class="star-rating">';
  for (let i = 1; i <= 5; i++) {
    const activeClass = i <= rating ? 'active' : '';
    const readonlyClass = readonly ? 'readonly' : '';
    html += `<span class="star-rating__star ${activeClass} ${readonlyClass}" data-rating="${i}">★</span>`;
  }
  html += '</div>';
  return html;
}

/* ---- Render Navbar HTML ---- */
function renderNavbar() {
  return `
  <nav class="navbar" role="navigation" aria-label="Main navigation">
    <div class="navbar__inner">
      <a href="index.html" class="navbar__logo" aria-label="Home">
        R<span>.</span>
      </a>
      <div class="navbar__links">
        <a href="index.html" class="navbar__link">Home</a>
        <a href="about.html" class="navbar__link">About</a>
        <a href="skills.html" class="navbar__link">Skills</a>
        <a href="projects.html" class="navbar__link">Projects</a>
        <a href="contact.html" class="navbar__link">Contact</a>
      </div>
      <div style="display:flex;align-items:center;">
        <button class="navbar__admin-lock" id="navbar-admin-lock" onclick="openLoginModal()" aria-label="Admin login">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </button>
        <div class="navbar__admin-badge" id="navbar-admin-badge" onclick="toggleAdminDropdown(event)">
          A
          <div class="admin-dropdown" id="admin-dropdown">
            <a href="admin.html" class="admin-dropdown__item">🧩 Dashboard</a>
            <a href="index.html" class="admin-dropdown__item">👁 View Site</a>
            <div class="admin-dropdown__divider"></div>
            <button class="admin-dropdown__item admin-dropdown__item--danger" onclick="adminLogout()">🚪 Logout</button>
          </div>
        </div>
        <button class="navbar__hamburger" aria-label="Toggle menu" aria-expanded="false">
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>
    </div>
    <div class="navbar__mobile-menu" role="dialog" aria-label="Mobile navigation">
      <a href="index.html" class="navbar__mobile-link">Home</a>
      <a href="about.html" class="navbar__mobile-link">About</a>
      <a href="skills.html" class="navbar__mobile-link">Skills</a>
      <a href="projects.html" class="navbar__mobile-link">Projects</a>
      <a href="contact.html" class="navbar__mobile-link">Contact</a>
      <button class="navbar__mobile-link" style="opacity:0.4;border:none;background:none;cursor:pointer;" onclick="openLoginModal()" id="navbar-mobile-admin">🔒 Admin</button>
      <a href="admin.html" class="navbar__mobile-link" id="navbar-mobile-dashboard" style="display:none;">🧩 Dashboard</a>
      <button class="navbar__mobile-link" style="display:none;border:none;background:none;cursor:pointer;color:var(--error);" onclick="adminLogout()" id="navbar-mobile-logout">🚪 Logout</button>
    </div>
  </nav>`;
}

/* ---- Render Footer HTML ---- */
function renderFooter() {
  const d = PORTFOLIO_DATA;
  const socialIcons = {
    github: '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>',
    linkedin: '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
    instagram: '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',
    twitter: '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
    youtube: '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
  };

  const socialsHtml = d.socials.map(s => `
    <a href="${s.url}" class="footer__social-icon" target="_blank" rel="noopener noreferrer" aria-label="${s.name}">
      ${socialIcons[s.icon] || s.name[0]}
    </a>
  `).join('');

  return `
  <footer class="footer">
    <div class="container">
      <div class="footer__inner">
        <div class="footer__brand">
          <div class="footer__brand-name">R<span>.</span></div>
          <p class="footer__brand-tagline">${d.personal.tagline}</p>
          <div class="footer__socials" style="margin-top: var(--space-lg);">
            ${socialsHtml}
          </div>
        </div>
        <div class="footer__nav">
          <h4 class="footer__heading">Navigation</h4>
          <a href="index.html" class="footer__link">Home</a>
          <a href="about.html" class="footer__link">About</a>
          <a href="skills.html" class="footer__link">Skills</a>
          <a href="projects.html" class="footer__link">Projects</a>
          <a href="contact.html" class="footer__link">Contact</a>
        </div>
        <div class="footer__contact">
          <h4 class="footer__heading">Get in Touch</h4>
          <a href="mailto:${d.personal.email}" class="footer__link">${d.personal.email}</a>
          <p class="footer__link">${d.personal.location}</p>
        </div>
      </div>
      <div class="footer__bottom">
        <p class="footer__copyright">&copy; ${new Date().getFullYear()} ${d.personal.fullName}. All rights reserved.</p>
        <div class="footer__bottom-links">
          <span class="footer__bottom-link">Built with passion & code</span>
        </div>
      </div>
    </div>
  </footer>`;
}

/* ---- Insert Navbar & Footer into page ---- */
function initLayout() {
  // Insert navbar at top of body
  const navPlaceholder = document.getElementById('navbar-placeholder');
  if (navPlaceholder) {
    navPlaceholder.innerHTML = renderNavbar();
    initNavbar();
  }

  // Insert footer at bottom
  const footerPlaceholder = document.getElementById('footer-placeholder');
  if (footerPlaceholder) {
    footerPlaceholder.innerHTML = renderFooter();
  }

  // Inject login modal + init admin auth (only if admin-auth.js is loaded)
  if (typeof renderLoginModal === 'function') {
    document.body.insertAdjacentHTML('beforeend', renderLoginModal());
    initLoginModal();
    updateNavbarAuthState();
    if (typeof AdminAuth !== 'undefined' && AdminAuth.isAuthenticated()) {
      showAdminBanner();
      AdminAuth.startInactivityTimer();
    }
  }
}

// Auto-init layout
document.addEventListener('DOMContentLoaded', initLayout);
