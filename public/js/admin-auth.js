/* ============================================
   ADMIN AUTH — Server-side Sanctum Token Auth
   ============================================ */

const AUTH_API_BASE = '/api/auth';

const AdminAuth = {
  _token: null,

  /* ---- Initialize from stored token ---- */
  init() {
    this._token = sessionStorage.getItem('portfolio_sanctum_token');
    if (this._token) {
      return this.check();
    }
    return Promise.resolve(false);
  },

  /* ---- Check if token is valid ---- */
  async check() {
    if (!this._token) return false;
    try {
      const res = await fetch(`${AUTH_API_BASE}/check`, {
        headers: { Authorization: `Bearer ${this._token}` },
      });
      if (!res.ok) {
        this.logout();
        return false;
      }
      const data = await res.json();
      this._storeSession(data.user);
      return true;
    } catch {
      this.logout();
      return false;
    }
  },

  /* ---- Login via Sanctum ---- */
  async login(email, password) {
    try {
      const res = await fetch(`${AUTH_API_BASE}/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({ email, password }),
      });

      const data = await res.json();

      if (!res.ok) {
        return {
          success: false,
          error: data.message || data.errors?.email?.[0] || 'Invalid credentials',
        };
      }

      this._token = data.token;
      sessionStorage.setItem('portfolio_sanctum_token', data.token);
      localStorage.setItem('portfolio_last_login', Date.now().toString());
      this._storeSession(data.user);
      return { success: true };
    } catch (err) {
      return { success: false, error: 'Network error. Please try again.' };
    }
  },

  /* ---- Logout (revoke token on server) ---- */
  async logout() {
    if (this._token) {
      try {
        await fetch(`${AUTH_API_BASE}/logout`, {
          method: 'POST',
          headers: { Authorization: `Bearer ${this._token}`, Accept: 'application/json' },
        });
      } catch { /* ignore */ }
    }
    this._token = null;
    sessionStorage.removeItem('portfolio_sanctum_token');
    sessionStorage.removeItem('portfolio_admin_user');
  },

  /* ---- Check if authenticated ---- */
  isAuthenticated() {
    if (this._token) return true;
    this._token = sessionStorage.getItem('portfolio_sanctum_token');
    return !!this._token;
  },

  /* ---- Get User Info ---- */
  getUser() {
    try {
      return JSON.parse(sessionStorage.getItem('portfolio_admin_user') || '{}');
    } catch { return {}; }
  },

  getUsername() {
    return this.getUser().name || 'Admin';
  },

  getEmail() {
    return this.getUser().email || '';
  },

  /* ---- Private helpers ---- */
  _storeSession(user) {
    sessionStorage.setItem('portfolio_admin_user', JSON.stringify(user));
  },

  /* ---- Last Login (from localStorage for display) ---- */
  getLastLogin() {
    const ts = localStorage.getItem('portfolio_last_login');
    return ts ? parseInt(ts) : null;
  },

  /* ---- Activity Log (local for display only) ---- */
  logAction(type, itemName) {
    try {
      const log = JSON.parse(localStorage.getItem('portfolio_activity_log') || '[]');
      log.unshift({ type, item: itemName, timestamp: Date.now(), user: this.getUsername() });
      const trimmed = log.slice(0, 50);
      localStorage.setItem('portfolio_activity_log', JSON.stringify(trimmed));
    } catch { /* ignore */ }
  },

  getActivityLog() {
    try {
      return JSON.parse(localStorage.getItem('portfolio_activity_log') || '[]');
    } catch { return []; }
  },

  /* ---- Auth header helper for fetch calls ---- */
  getAuthHeaders() {
    if (!this._token) return {};
    return { Authorization: `Bearer ${this._token}` };
  },
};

/* ---- Login Modal Rendering ---- */
function renderLoginModal() {
  return `
  <div class="login-modal" id="login-modal">
    <div class="login-modal__overlay" id="login-modal-overlay"></div>
    <div class="login-modal__card" id="login-modal-card">
      <button class="login-modal__close" id="login-modal-close" aria-label="Close">&times;</button>
      <div class="login-modal__header">
        <div class="login-modal__icon">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <h2 class="login-modal__title">Admin Login</h2>
        <p class="login-modal__subtitle">Enter your credentials to access the dashboard</p>
      </div>
      <form class="login-modal__form" id="login-form">
        <div class="login-modal__error" id="login-error" style="display:none;"></div>
        <div class="form-group">
          <label for="login-email" class="form-label">Email</label>
          <input type="email" id="login-email" class="form-input" placeholder="Enter admin email" required autocomplete="email" />
        </div>
        <div class="form-group">
          <label for="login-password" class="form-label">Password</label>
          <div class="login-modal__password-wrap">
            <input type="password" id="login-password" class="form-input" placeholder="Enter password" required autocomplete="current-password" />
            <button type="button" class="login-modal__toggle-pw" id="toggle-password" aria-label="Show password">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>
        <button type="submit" class="btn btn--primary btn--lg btn--full login-modal__submit" id="login-submit">
          <span class="login-modal__submit-text">Sign In</span>
        </button>
      </form>
    </div>
  </div>`;
}

/* ---- Bind Login Modal Events ---- */
function initLoginModal() {
  const modal = document.getElementById('login-modal');
  if (!modal) return;

  const overlay = document.getElementById('login-modal-overlay');
  const closeBtn = document.getElementById('login-modal-close');
  const form = document.getElementById('login-form');
  const togglePw = document.getElementById('toggle-password');
  const pwInput = document.getElementById('login-password');
  const errorEl = document.getElementById('login-error');
  const submitBtn = document.getElementById('login-submit');
  const submitText = submitBtn.querySelector('.login-modal__submit-text');

  const closeModal = () => {
    modal.classList.remove('open');
    document.body.style.overflow = '';
    form.reset();
    errorEl.style.display = 'none';
  };

  overlay.addEventListener('click', closeModal);
  closeBtn.addEventListener('click', closeModal);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.classList.contains('open')) closeModal();
  });

  togglePw.addEventListener('click', () => {
    const isPassword = pwInput.type === 'password';
    pwInput.type = isPassword ? 'text' : 'password';
    togglePw.innerHTML = isPassword
      ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
      : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    errorEl.style.display = 'none';

    const email = document.getElementById('login-email').value.trim();
    const password = document.getElementById('login-password').value;
    if (!email || !password) return;

    submitBtn.disabled = true;
    submitText.textContent = 'Signing in...';

    const result = await AdminAuth.login(email, password);

    if (result.success) {
      submitText.textContent = '✓ Success';
      submitBtn.style.background = 'var(--success)';
      setTimeout(() => {
        closeModal();
        submitBtn.disabled = false;
        submitText.textContent = 'Sign In';
        submitBtn.style.background = '';
        onLoginSuccess();
      }, 600);
    } else {
      submitBtn.disabled = false;
      submitText.textContent = 'Sign In';

      const card = document.getElementById('login-modal-card');
      card.classList.add('shake');
      setTimeout(() => card.classList.remove('shake'), 500);

      errorEl.textContent = result.error;
      errorEl.style.display = 'block';
    }
  });
}

/* ---- Post-Login Success Handler ---- */
function onLoginSuccess() {
  updateNavbarAuthState();
  showAdminBanner();
}

/* ---- Update Navbar for Auth State ---- */
function updateNavbarAuthState() {
  const lockIcon = document.getElementById('navbar-admin-lock');
  const adminBadge = document.getElementById('navbar-admin-badge');
  const mobileLoginBtn = document.getElementById('mobile-admin-login-btn');
  const mobileAdminControls = document.getElementById('mobile-admin-controls');
  const mobileAdminName = document.getElementById('mobile-admin-name');

  if (AdminAuth.isAuthenticated()) {
    if (lockIcon) lockIcon.style.display = 'none';
    if (adminBadge) adminBadge.style.display = 'flex';
    if (mobileLoginBtn) mobileLoginBtn.style.display = 'none';
    if (mobileAdminControls) mobileAdminControls.style.display = 'block';
    if (mobileAdminName) mobileAdminName.textContent = AdminAuth.getUsername();
  } else {
    if (lockIcon) lockIcon.style.display = 'flex';
    if (adminBadge) adminBadge.style.display = 'none';
    if (mobileLoginBtn) mobileLoginBtn.style.display = 'flex';
    if (mobileAdminControls) mobileAdminControls.style.display = 'none';
  }
}

/* ---- Admin Banner ---- */
function showAdminBanner() {
  if (!AdminAuth.isAuthenticated()) return;
  if (document.getElementById('admin-banner')) return;

  const banner = document.createElement('div');
  banner.id = 'admin-banner';
  banner.className = 'admin-banner';
  banner.innerHTML = `
    <span>🛡️ Admin Mode Active</span>
    <button class="admin-banner__close" onclick="this.parentElement.remove()">&times;</button>
  `;
  document.body.prepend(banner);
}

/* ---- Open Login Modal ---- */
function openLoginModal() {
  const modal = document.getElementById('login-modal');
  if (modal) {
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
    document.getElementById('login-email').focus();
  }
}

/* ---- Admin Dropdown Toggle ---- */
function toggleAdminDropdown(event) {
  if (event) {
    event.stopPropagation();
  }
  const dropdown = document.getElementById('admin-dropdown');
  const badge = document.getElementById('navbar-admin-badge');
  if (!dropdown) return;

  const isOpen = dropdown.classList.contains('open');

  if (isOpen) {
    dropdown.classList.remove('open');
  } else {
    dropdown.classList.add('open');

    // Smooth click outside listener
    const closeOnOutsideClick = (e) => {
      if (badge && dropdown && !badge.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove('open');
        document.removeEventListener('click', closeOnOutsideClick);
      }
    };
    document.addEventListener('click', closeOnOutsideClick);
  }
}

function adminLogout() {
  AdminAuth.logout().then(() => {
    updateNavbarAuthState();
    const banner = document.getElementById('admin-banner');
    if (banner) banner.remove();
    const dropdown = document.getElementById('admin-dropdown');
    if (dropdown) dropdown.classList.remove('open');
  });
}

/* ---- API Helper with Auth ---- */
async function apiFetch(url, options = {}) {
  const headers = { ...AdminAuth.getAuthHeaders(), ...options.headers };
  return fetch(url, { ...options, headers });
}

/* Make available globally */
if (typeof window !== 'undefined') {
  window.AdminAuth = AdminAuth;
  window.openLoginModal = openLoginModal;
  window.toggleAdminDropdown = toggleAdminDropdown;
  window.adminLogout = adminLogout;
  window.apiFetch = apiFetch;
}
