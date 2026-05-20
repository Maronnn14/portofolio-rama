/* ============================================
   ADMIN AUTH — Login, Session, Rate Limiting
   ============================================ */

const AdminAuth = {

  /* ---- Check if authenticated ---- */
  isAuthenticated() {
    const session = sessionStorage.getItem(ADMIN_CONFIG.sessionKey);
    if (!session) return false;
    try {
      const parsed = JSON.parse(session);
      const now = Date.now();
      // Check timeout
      const lastActivity = parseInt(localStorage.getItem(ADMIN_CONFIG.lastActivityKey) || '0');
      if (lastActivity && (now - lastActivity) > ADMIN_CONFIG.sessionTimeout) {
        this.logout();
        return false;
      }
      return parsed.authenticated === true;
    } catch {
      return false;
    }
  },

  /* ---- Login ---- */
  async login(username, password) {
    // Rate limit check
    const lockout = this.getLockoutRemaining();
    if (lockout > 0) {
      return { success: false, error: `Too many attempts. Try again in ${Math.ceil(lockout / 1000)}s`, locked: true, lockoutMs: lockout };
    }

    // Get stored credentials or use defaults
    const stored = this.getStoredAccount();
    const targetUsername = stored.username || ADMIN_CONFIG.defaultUsername;
    const targetHash = stored.passwordHash || ADMIN_CONFIG.defaultPasswordHash;

    // Hash the provided password
    const inputHash = await sha256(password);

    if (username === targetUsername && inputHash === targetHash) {
      // Success
      const session = { authenticated: true, username, loginTime: Date.now() };
      sessionStorage.setItem(ADMIN_CONFIG.sessionKey, JSON.stringify(session));
      localStorage.setItem(ADMIN_CONFIG.lastActivityKey, Date.now().toString());
      localStorage.setItem(ADMIN_CONFIG.lastLoginKey, Date.now().toString());
      this.clearLoginAttempts();
      return { success: true };
    } else {
      // Failed
      this.recordFailedAttempt();
      const attempts = this.getFailedAttempts();
      const remaining = ADMIN_CONFIG.maxLoginAttempts - attempts;
      if (remaining <= 0) {
        this.setLockout();
        const lockoutMs = ADMIN_CONFIG.lockoutDuration;
        return { success: false, error: `Too many attempts. Locked for ${lockoutMs / 1000}s`, locked: true, lockoutMs };
      }
      return { success: false, error: `Invalid username or password. ${remaining} attempt${remaining > 1 ? 's' : ''} remaining.` };
    }
  },

  /* ---- Logout ---- */
  logout() {
    sessionStorage.removeItem(ADMIN_CONFIG.sessionKey);
    localStorage.removeItem(ADMIN_CONFIG.lastActivityKey);
  },

  /* ---- Get Current Username ---- */
  getUsername() {
    try {
      const session = JSON.parse(sessionStorage.getItem(ADMIN_CONFIG.sessionKey) || '{}');
      return session.username || 'Admin';
    } catch { return 'Admin'; }
  },

  /* ---- Get Last Login ---- */
  getLastLogin() {
    const ts = localStorage.getItem(ADMIN_CONFIG.lastLoginKey);
    return ts ? parseInt(ts) : null;
  },

  /* ---- Update Activity (reset timeout) ---- */
  touchActivity() {
    if (this.isAuthenticated()) {
      localStorage.setItem(ADMIN_CONFIG.lastActivityKey, Date.now().toString());
    }
  },

  /* ---- Rate Limiting ---- */
  getFailedAttempts() {
    return parseInt(localStorage.getItem(ADMIN_CONFIG.loginAttemptsKey) || '0');
  },

  recordFailedAttempt() {
    const current = this.getFailedAttempts();
    localStorage.setItem(ADMIN_CONFIG.loginAttemptsKey, (current + 1).toString());
  },

  clearLoginAttempts() {
    localStorage.removeItem(ADMIN_CONFIG.loginAttemptsKey);
    localStorage.removeItem(ADMIN_CONFIG.lockoutUntilKey);
  },

  setLockout() {
    const until = Date.now() + ADMIN_CONFIG.lockoutDuration;
    localStorage.setItem(ADMIN_CONFIG.lockoutUntilKey, until.toString());
  },

  getLockoutRemaining() {
    const until = parseInt(localStorage.getItem(ADMIN_CONFIG.lockoutUntilKey) || '0');
    if (!until) return 0;
    const remaining = until - Date.now();
    if (remaining <= 0) {
      this.clearLoginAttempts();
      return 0;
    }
    return remaining;
  },

  /* ---- Stored Admin Account ---- */
  getStoredAccount() {
    try {
      return JSON.parse(localStorage.getItem(ADMIN_CONFIG.adminAccountKey) || '{}');
    } catch { return {}; }
  },

  async updateAccount(username, newPassword) {
    const account = this.getStoredAccount();
    if (username) account.username = username;
    if (newPassword) account.passwordHash = await sha256(newPassword);
    localStorage.setItem(ADMIN_CONFIG.adminAccountKey, JSON.stringify(account));
    // Update session
    if (username) {
      const session = JSON.parse(sessionStorage.getItem(ADMIN_CONFIG.sessionKey) || '{}');
      session.username = username;
      sessionStorage.setItem(ADMIN_CONFIG.sessionKey, JSON.stringify(session));
    }
  },

  /* ---- Auto-Logout Timer ---- */
  startInactivityTimer() {
    // Reset timer on user interaction
    const events = ['mousedown', 'keydown', 'scroll', 'touchstart'];
    const handler = () => this.touchActivity();
    events.forEach(e => document.addEventListener(e, handler, { passive: true }));

    // Check every minute
    setInterval(() => {
      if (this.isAuthenticated()) {
        const lastActivity = parseInt(localStorage.getItem(ADMIN_CONFIG.lastActivityKey) || '0');
        if (Date.now() - lastActivity > ADMIN_CONFIG.sessionTimeout) {
          this.logout();
          window.location.href = 'index.html';
        }
      }
    }, 60000);
  },

  /* ---- Activity Log ---- */
  logAction(type, itemName) {
    try {
      const log = JSON.parse(localStorage.getItem(ADMIN_CONFIG.activityLogKey) || '[]');
      log.unshift({
        type,
        item: itemName,
        timestamp: Date.now(),
        user: this.getUsername()
      });
      // Keep only last N entries
      const trimmed = log.slice(0, ADMIN_CONFIG.maxActivityLogEntries);
      localStorage.setItem(ADMIN_CONFIG.activityLogKey, JSON.stringify(trimmed));
    } catch { /* ignore */ }
  },

  getActivityLog() {
    try {
      return JSON.parse(localStorage.getItem(ADMIN_CONFIG.activityLogKey) || '[]');
    } catch { return []; }
  }
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
          <label for="login-username" class="form-label">Username</label>
          <input type="text" id="login-username" class="form-input" placeholder="Enter username" required autocomplete="username" />
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
      <div class="login-modal__lockout" id="login-lockout" style="display:none;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span>Too many attempts. Try again in <strong id="lockout-countdown">30</strong>s</span>
      </div>
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
  const lockoutEl = document.getElementById('login-lockout');

  // Close modal
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

  // Toggle password visibility
  togglePw.addEventListener('click', () => {
    const isPassword = pwInput.type === 'password';
    pwInput.type = isPassword ? 'text' : 'password';
    togglePw.innerHTML = isPassword
      ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
      : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
  });

  // Check initial lockout
  checkLockoutState();

  // Submit
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    errorEl.style.display = 'none';

    const username = document.getElementById('login-username').value.trim();
    const password = document.getElementById('login-password').value;

    if (!username || !password) return;

    // Loading state
    submitBtn.disabled = true;
    submitText.textContent = 'Signing in...';

    const result = await AdminAuth.login(username, password);

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

      if (result.locked) {
        form.style.display = 'none';
        lockoutEl.style.display = 'flex';
        startLockoutCountdown(result.lockoutMs);
      } else {
        // Shake animation
        const card = document.getElementById('login-modal-card');
        card.classList.add('shake');
        setTimeout(() => card.classList.remove('shake'), 500);

        errorEl.textContent = result.error;
        errorEl.style.display = 'block';
      }
    }
  });
}

function checkLockoutState() {
  const remaining = AdminAuth.getLockoutRemaining();
  if (remaining > 0) {
    const form = document.getElementById('login-form');
    const lockoutEl = document.getElementById('login-lockout');
    if (form) form.style.display = 'none';
    if (lockoutEl) lockoutEl.style.display = 'flex';
    startLockoutCountdown(remaining);
  }
}

function startLockoutCountdown(ms) {
  const countdownEl = document.getElementById('lockout-countdown');
  const form = document.getElementById('login-form');
  const lockoutEl = document.getElementById('login-lockout');
  let remaining = Math.ceil(ms / 1000);

  const interval = setInterval(() => {
    remaining--;
    if (countdownEl) countdownEl.textContent = remaining;
    if (remaining <= 0) {
      clearInterval(interval);
      AdminAuth.clearLoginAttempts();
      if (form) form.style.display = '';
      if (lockoutEl) lockoutEl.style.display = 'none';
    }
  }, 1000);
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

  if (AdminAuth.isAuthenticated()) {
    if (lockIcon) lockIcon.style.display = 'none';
    if (adminBadge) adminBadge.style.display = 'flex';
  } else {
    if (lockIcon) lockIcon.style.display = 'flex';
    if (adminBadge) adminBadge.style.display = 'none';
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
    document.getElementById('login-username').focus();
  }
}

/* ---- Admin Dropdown Toggle ---- */
function toggleAdminDropdown() {
  const dropdown = document.getElementById('admin-dropdown');
  if (dropdown) dropdown.classList.toggle('open');
}

function adminLogout() {
  AdminAuth.logout();
  updateNavbarAuthState();
  const banner = document.getElementById('admin-banner');
  if (banner) banner.remove();
  const dropdown = document.getElementById('admin-dropdown');
  if (dropdown) dropdown.classList.remove('open');
}

/* Make available globally */
if (typeof window !== 'undefined') {
  window.AdminAuth = AdminAuth;
  window.openLoginModal = openLoginModal;
  window.toggleAdminDropdown = toggleAdminDropdown;
  window.adminLogout = adminLogout;
}
