/**
 * Jest tests for public/js/admin-auth.js
 *
 * Mocks:
 * - fetch: never make real HTTP requests
 * - sessionStorage: delete (configurable) then replace with jest.fn()
 * - localStorage: delete (configurable) then replace with jest.fn()
 * - document.*: jest.spyOn() (document is non-configurable in jsdom)
 */

const fs = require('fs');
const path = require('path');

const sourcePath = path.resolve(__dirname, '../../public/js/admin-auth.js');
const sourceCode = fs.readFileSync(sourcePath, 'utf-8');

const loadSource = () => {
  global.Response = class Response {
    constructor(body, init = {}) {
      this._body = typeof body === 'string' ? body : JSON.stringify(body);
      this.ok = init.status >= 200 && init.status < 300;
      this.status = init.status || 200;
    }
    async json() { return JSON.parse(this._body); }
  };

  // jsdom defines sessionStorage/localStorage as getters with no setter.
  // Delete first (configurable: true), then replace.
  try { delete global.sessionStorage; } catch (e) { /* noop */ }
  try { delete global.localStorage; } catch (e) { /* noop */ }

  // Replace sessionStorage with mock
  const ss = {};
  global.sessionStorage = {
    getItem: jest.fn((k) => ss[k] ?? null),
    setItem: jest.fn((k, v) => { ss[k] = String(v); }),
    removeItem: jest.fn((k) => { delete ss[k]; }),
    clear: jest.fn(() => { Object.keys(ss).forEach(k => delete ss[k]); }),
    key: jest.fn((i) => Object.keys(ss)[i] ?? null),
    get length() { return Object.keys(ss).length; },
  };

  // Replace localStorage with mock
  const ls = {};
  global.localStorage = {
    getItem: jest.fn((k) => ls[k] ?? null),
    setItem: jest.fn((k, v) => { ls[k] = String(v); }),
    removeItem: jest.fn((k) => { delete ls[k]; }),
    clear: jest.fn(() => { Object.keys(ls).forEach(k => delete ls[k]); }),
    key: jest.fn((i) => Object.keys(ls)[i] ?? null),
    get length() { return Object.keys(ls).length; },
  };

  global.window = global;

  // Execute the source and collect its exports
  const wrap = new Function(`
    ${sourceCode}
    return {
      AdminAuth, renderLoginModal, initLoginModal, openLoginModal,
      toggleAdminDropdown, adminLogout, apiFetch, showAdminBanner,
      updateNavbarAuthState, onLoginSuccess
    };
  `);
  const exports = wrap();
  Object.assign(global, exports);
};

beforeEach(() => {
  loadSource();
});

afterEach(() => {
  jest.restoreAllMocks();
});

// ====== AdminAuth ======

test('init returns false when no stored token', async () => {
  sessionStorage.getItem.mockReturnValue(null);

  const result = await AdminAuth.init();

  expect(result).toBe(false);
  expect(sessionStorage.getItem).toHaveBeenCalledWith('portfolio_sanctum_token');
});

test('init calls check() when token exists', async () => {
  sessionStorage.getItem.mockReturnValue('valid-token');
  global.fetch = jest.fn().mockResolvedValue(new Response({ user: { name: 'Admin' } }, { status: 200 }));

  const result = await AdminAuth.init();

  expect(result).toBe(true);
  expect(AdminAuth._token).toBe('valid-token');
});

test('check returns true on valid token and stores user', async () => {
  AdminAuth._token = 'valid-token';
  global.fetch = jest.fn().mockResolvedValue(new Response(
    { user: { name: 'Rama', email: 'rama@test.com' } }, { status: 200 },
  ));

  const result = await AdminAuth.check();

  expect(result).toBe(true);
  expect(sessionStorage.setItem).toHaveBeenCalledWith(
    'portfolio_admin_user',
    JSON.stringify({ name: 'Rama', email: 'rama@test.com' }),
  );
});

test('check returns false on 401 and calls logout', async () => {
  AdminAuth._token = 'expired-token';
  global.fetch = jest.fn().mockResolvedValue(new Response({ message: 'Unauthenticated' }, { status: 401 }));

  const result = await AdminAuth.check();

  expect(result).toBe(false);
  expect(AdminAuth._token).toBeNull();
});

test('check returns false on network error', async () => {
  AdminAuth._token = 'valid-token';
  global.fetch = jest.fn().mockRejectedValue(new Error('Network error'));

  const result = await AdminAuth.check();

  expect(result).toBe(false);
  expect(AdminAuth._token).toBeNull();
});

test('check returns false immediately when _token is null', async () => {
  AdminAuth._token = null;

  const result = await AdminAuth.check();

  expect(result).toBe(false);
  expect(global.fetch).not.toHaveBeenCalled();
});

test('login succeeds and stores token and user', async () => {
  global.fetch = jest.fn().mockResolvedValue(new Response(
    { token: 'new-token', user: { name: 'Admin', email: 'admin@test.com' } }, { status: 200 },
  ));

  const result = await AdminAuth.login('admin@test.com', 'password');

  expect(result).toEqual({ success: true });
  expect(AdminAuth._token).toBe('new-token');
  expect(sessionStorage.setItem).toHaveBeenCalledWith('portfolio_sanctum_token', 'new-token');
  expect(localStorage.setItem).toHaveBeenCalledWith('portfolio_last_login', expect.any(String));
});

test('login returns error on invalid credentials', async () => {
  global.fetch = jest.fn().mockResolvedValue(new Response({ message: 'Invalid credentials' }, { status: 422 }));

  const result = await AdminAuth.login('wrong@test.com', 'wrong');

  expect(result.success).toBe(false);
  expect(result.error).toBe('Invalid credentials');
  expect(AdminAuth._token).toBeNull();
});

test('login returns field-specific error when email field fails', async () => {
  global.fetch = jest.fn().mockResolvedValue(new Response(
    { errors: { email: ['These credentials do not match our records.'] } }, { status: 422 },
  ));

  const result = await AdminAuth.login('bad@test.com', 'wrong');

  expect(result.success).toBe(false);
  expect(result.error).toBe('These credentials do not match our records.');
});

test('login returns network error on fetch failure', async () => {
  global.fetch = jest.fn().mockRejectedValue(new Error('Network error'));

  const result = await AdminAuth.login('admin@test.com', 'password');

  expect(result.success).toBe(false);
  expect(result.error).toBe('Network error. Please try again.');
});

test('login returns default error when no message in response', async () => {
  global.fetch = jest.fn().mockResolvedValue(new Response({}, { status: 401 }));

  const result = await AdminAuth.login('admin@test.com', 'wrong');

  expect(result.success).toBe(false);
  expect(result.error).toBe('Invalid credentials');
});

test('logout clears token and storage', async () => {
  AdminAuth._token = 'some-token';
  global.fetch = jest.fn().mockResolvedValue(new Response({}, { status: 200 }));

  await AdminAuth.logout();

  expect(AdminAuth._token).toBeNull();
  expect(sessionStorage.removeItem).toHaveBeenCalledWith('portfolio_sanctum_token');
  expect(sessionStorage.removeItem).toHaveBeenCalledWith('portfolio_admin_user');
});

test('logout skips fetch when already logged out', async () => {
  AdminAuth._token = null;

  await AdminAuth.logout();

  expect(global.fetch).not.toHaveBeenCalled();
});

test('logout does not throw on fetch failure', async () => {
  AdminAuth._token = 'some-token';
  global.fetch = jest.fn().mockRejectedValue(new Error('Network error'));

  await expect(AdminAuth.logout()).resolves.toBeUndefined();
  expect(AdminAuth._token).toBeNull();
});

test('isAuthenticated returns true when _token is set', () => {
  AdminAuth._token = 'valid-token';
  expect(AdminAuth.isAuthenticated()).toBe(true);
});

test('isAuthenticated falls back to sessionStorage when _token is null', () => {
  AdminAuth._token = null;
  sessionStorage.getItem.mockReturnValue('stored-token');
  expect(AdminAuth.isAuthenticated()).toBe(true);
  expect(AdminAuth._token).toBe('stored-token');
});

test('isAuthenticated returns false when no token', () => {
  AdminAuth._token = null;
  sessionStorage.getItem.mockReturnValue(null);
  expect(AdminAuth.isAuthenticated()).toBe(false);
});

test('getUser returns parsed user from sessionStorage', () => {
  sessionStorage.getItem.mockReturnValue(JSON.stringify({ name: 'Rama', email: 'rama@test.com' }));
  expect(AdminAuth.getUser()).toEqual({ name: 'Rama', email: 'rama@test.com' });
});

test('getUser returns empty object on null or corrupted JSON', () => {
  sessionStorage.getItem.mockReturnValue(null);
  expect(AdminAuth.getUser()).toEqual({});
  sessionStorage.getItem.mockReturnValue('not-json');
  expect(AdminAuth.getUser()).toEqual({});
});

test('getUsername returns name or default', () => {
  sessionStorage.getItem.mockReturnValue(JSON.stringify({ name: 'Rama' }));
  expect(AdminAuth.getUsername()).toBe('Rama');
  sessionStorage.getItem.mockReturnValue(JSON.stringify({}));
  expect(AdminAuth.getUsername()).toBe('Admin');
});

test('getEmail returns email or default', () => {
  sessionStorage.getItem.mockReturnValue(JSON.stringify({ email: 'rama@test.com' }));
  expect(AdminAuth.getEmail()).toBe('rama@test.com');
  sessionStorage.getItem.mockReturnValue(JSON.stringify({}));
  expect(AdminAuth.getEmail()).toBe('');
});

test('getLastLogin returns parsed timestamp', () => {
  localStorage.getItem.mockReturnValue('1700000000000');
  expect(AdminAuth.getLastLogin()).toBe(1700000000000);
});

test('getLastLogin returns null when never stored', () => {
  localStorage.getItem.mockReturnValue(null);
  expect(AdminAuth.getLastLogin()).toBeNull();
});

test('logAction appends entry to activity log', () => {
  AdminAuth.getUsername = jest.fn(() => 'Rama');
  const captured = [];
  localStorage.setItem.mockImplementation((k, v) => captured.push(JSON.parse(v)));

  AdminAuth.logAction('update', 'Project X');

  expect(captured[0]).toHaveLength(1);
  expect(captured[0][0].type).toBe('update');
  expect(captured[0][0].item).toBe('Project X');
  expect(captured[0][0].user).toBe('Rama');
});

test('logAction trims to 50 entries', () => {
  const existing = Array.from({ length: 55 }, (_, i) => ({ type: 'old', item: `Item ${i}`, timestamp: i }));
  localStorage.getItem.mockReturnValue(JSON.stringify(existing));
  const captured = [];
  localStorage.setItem.mockImplementation((k, v) => captured.push(JSON.parse(v)));

  AdminAuth.logAction('create', 'New Item');

  expect(captured[0]).toHaveLength(50);
  expect(captured[0][0].item).toBe('New Item');
});

test('logAction does not throw on storage error', () => {
  localStorage.getItem.mockImplementation(() => { throw new Error('Error'); });
  expect(() => AdminAuth.logAction('test', 'x')).not.toThrow();
});

test('getActivityLog returns parsed log', () => {
  const log = [{ type: 'login', item: 'Session', timestamp: 1000 }];
  localStorage.getItem.mockReturnValue(JSON.stringify(log));
  expect(AdminAuth.getActivityLog()).toEqual(log);
});

test('getActivityLog returns empty array on error', () => {
  localStorage.getItem.mockReturnValue('not-json');
  expect(AdminAuth.getActivityLog()).toEqual([]);
});

test('getAuthHeaders returns Bearer header or empty', () => {
  AdminAuth._token = 'my-token';
  expect(AdminAuth.getAuthHeaders()).toEqual({ Authorization: 'Bearer my-token' });
  AdminAuth._token = null;
  expect(AdminAuth.getAuthHeaders()).toEqual({});
});

// ====== UI FUNCTIONS (use jest.spyOn for non-configurable document) ======

test('renderLoginModal returns correct HTML', () => {
  const html = renderLoginModal();
  expect(html).toContain('login-modal');
  expect(html).toContain('Admin Login');
  expect(html).toContain('login-email');
  expect(html).toContain('login-password');
  expect(html).toContain('Sign In');
});

test('openLoginModal adds class and focuses email', () => {
  const email = { focus: jest.fn() };
  const modal = { classList: { add: jest.fn(), contains: jest.fn() } };

  jest.spyOn(document, 'getElementById').mockImplementation((id) => {
    if (id === 'login-modal') return modal;
    if (id === 'login-email') return email;
    return null;
  });

  openLoginModal();

  expect(modal.classList.add).toHaveBeenCalledWith('open');
  expect(document.body.style.overflow).toBe('hidden');
  expect(email.focus).toHaveBeenCalled();
});

test('openLoginModal no-op when modal missing', () => {
  jest.spyOn(document, 'getElementById').mockReturnValue(null);
  expect(() => openLoginModal()).not.toThrow();
});

test('initLoginModal returns early when modal not found', () => {
  jest.spyOn(document, 'getElementById').mockReturnValue(null);
  expect(() => initLoginModal()).not.toThrow();
});

test('toggleAdminDropdown opens when closed', () => {
  document.body.innerHTML = '<div id="navbar-admin-badge"><div id="admin-dropdown"></div></div>';
  var badge = document.getElementById('navbar-admin-badge');
  var dropdown = document.getElementById('admin-dropdown');

  jest.spyOn(badge, 'getBoundingClientRect').mockReturnValue({
    top: 10, bottom: 44, left: 100, right: 134, width: 34, height: 34,
  });

  toggleAdminDropdown({ stopPropagation: jest.fn() });

  expect(dropdown.classList.contains('open')).toBe(true);
  expect(dropdown.style.position).toBe('fixed');
});

test('toggleAdminDropdown closes when open', () => {
  document.body.innerHTML = '<div id="navbar-admin-badge"></div>';
  var badge = document.getElementById('navbar-admin-badge');
  var dropdown = document.createElement('div');
  dropdown.id = 'admin-dropdown';
  dropdown.className = 'admin-dropdown open';
  dropdown.style.position = 'fixed';
  document.body.appendChild(dropdown);

  toggleAdminDropdown({ stopPropagation: jest.fn() });

  expect(dropdown.classList.contains('open')).toBe(false);
  expect(badge.contains(dropdown)).toBe(true);
});

test('toggleAdminDropdown no-op when dropdown or badge missing', () => {
  document.body.innerHTML = '';
  expect(function() { toggleAdminDropdown({ stopPropagation: jest.fn() }); }).not.toThrow();
});

test('updateNavbarAuthState shows badge when authenticated', () => {
  AdminAuth.isAuthenticated = jest.fn(() => true);
  AdminAuth.getUsername = jest.fn(() => 'Rama');
  const els = {
    'navbar-admin-lock': { style: { display: '' } },
    'navbar-admin-badge': { style: { display: '' } },
    'mobile-admin-login-btn': { style: { display: '' } },
    'mobile-admin-controls': { style: { display: '' } },
    'mobile-admin-name': { textContent: '' },
  };
  jest.spyOn(document, 'getElementById').mockImplementation((id) => els[id]);

  updateNavbarAuthState();

  expect(els['navbar-admin-lock'].style.display).toBe('none');
  expect(els['navbar-admin-badge'].style.display).toBe('flex');
  expect(els['mobile-admin-login-btn'].style.display).toBe('none');
  expect(els['mobile-admin-controls'].style.display).toBe('block');
  expect(els['mobile-admin-name'].textContent).toBe('Rama');
});

test('updateNavbarAuthState shows lock when not authenticated', () => {
  AdminAuth.isAuthenticated = jest.fn(() => false);
  const els = {
    'navbar-admin-lock': { style: { display: '' } },
    'navbar-admin-badge': { style: { display: '' } },
    'mobile-admin-login-btn': { style: { display: '' } },
    'mobile-admin-controls': { style: { display: '' } },
  };
  jest.spyOn(document, 'getElementById').mockImplementation((id) => els[id]);

  updateNavbarAuthState();

  expect(els['navbar-admin-lock'].style.display).toBe('flex');
  expect(els['navbar-admin-badge'].style.display).toBe('none');
  expect(els['mobile-admin-login-btn'].style.display).toBe('flex');
  expect(els['mobile-admin-controls'].style.display).toBe('none');
});

test('showAdminBanner no-op when not authenticated', () => {
  AdminAuth.isAuthenticated = jest.fn(() => false);
  jest.spyOn(document.body, 'prepend');
  showAdminBanner();
  expect(document.body.prepend).not.toHaveBeenCalled();
});

test('showAdminBanner does not duplicate', () => {
  AdminAuth.isAuthenticated = jest.fn(() => true);
  jest.spyOn(document, 'getElementById').mockReturnValue({});
  jest.spyOn(document, 'createElement');
  showAdminBanner();
  expect(document.createElement).not.toHaveBeenCalled();
});

test('showAdminBanner creates and prepends banner', () => {
  AdminAuth.isAuthenticated = jest.fn(() => true);
  jest.spyOn(document, 'getElementById').mockReturnValue(null);
  jest.spyOn(document, 'createElement').mockReturnValue({ id: '', className: '', innerHTML: '' });
  jest.spyOn(document.body, 'prepend');

  showAdminBanner();

  expect(document.createElement).toHaveBeenCalledWith('div');
  const banner = document.createElement.mock.results[0].value;
  expect(banner.id).toBe('admin-banner');
  expect(banner.className).toBe('admin-banner');
  expect(banner.innerHTML).toContain('Admin Mode Active');
  expect(document.body.prepend).toHaveBeenCalledWith(banner);
});

test('adminLogout calls logout and cleans up', async () => {
  AdminAuth.logout = jest.fn(() => Promise.resolve());
  const banner = { remove: jest.fn() };
  const badge = { appendChild: jest.fn(), style: { display: '' } };
  const dropdown = {
    classList: { contains: jest.fn(() => true), remove: jest.fn(), add: jest.fn() },
    style: { position: '', top: '', bottom: '', left: '', right: '', transition: '' },
    parentNode: null,
    _closeHandler: null,
    offsetHeight: 180,
  };
  jest.spyOn(document, 'getElementById').mockImplementation((id) => ({
    'admin-banner': banner,
    'admin-dropdown': dropdown,
    'navbar-admin-badge': badge,
  })[id]);

  await adminLogout();

  expect(AdminAuth.logout).toHaveBeenCalled();
  expect(banner.remove).toHaveBeenCalled();
  expect(dropdown.classList.remove).toHaveBeenCalledWith('open');
});

test('apiFetch includes auth headers', async () => {
  AdminAuth.getAuthHeaders = jest.fn(() => ({ Authorization: 'Bearer test-token' }));
  global.fetch = jest.fn().mockResolvedValue(new Response({}, { status: 200 }));

  await apiFetch('/api/projects');

  const [, opts] = global.fetch.mock.calls[0];
  expect(opts.headers.Authorization).toBe('Bearer test-token');
});

test('apiFetch merges custom headers', async () => {
  AdminAuth.getAuthHeaders = jest.fn(() => ({ Authorization: 'Bearer test-token' }));
  global.fetch = jest.fn().mockResolvedValue(new Response({}, { status: 200 }));

  await apiFetch('/api/projects', { headers: { 'X-Custom': 'value' } });

  const [, opts] = global.fetch.mock.calls[0];
  expect(opts.headers.Authorization).toBe('Bearer test-token');
  expect(opts.headers['X-Custom']).toBe('value');
});

test('exposes functions on window', () => {
  expect(window.AdminAuth).toBe(AdminAuth);
  expect(window.openLoginModal).toBe(openLoginModal);
  expect(window.toggleAdminDropdown).toBe(toggleAdminDropdown);
  expect(window.adminLogout).toBe(adminLogout);
  expect(window.apiFetch).toBe(apiFetch);
});


/*
 * ====================================================================
 * TESTABILITY ANALYSIS
 * ====================================================================
 *
 * 1. DOM queries mixed with auth logic — LINES 176-298
 *    Problem: initLoginModal(), updateNavbarAuthState(),
 *    showAdminBanner(), and openLoginModal() all call
 *    document.getElementById() directly, coupling auth state (business
 *    logic) with DOM (UI). Every test must mock the DOM.
 *    Suggestion: Emit events on auth change; let separate UI code listen:
 *    Before:
 *        this.logout().then(() => updateNavbarAuthState());
 *    After:
 *        this.logout().then(() => document.dispatchEvent(new Event('admin:logout')));
 *        // UI listens: document.addEventListener('admin:logout', () => { ... });
 *
 * 2. 9 sequential getElementById calls in initLoginModal — LINES 177-187
 *    Problem: If ANY element is missing, later code silently fails on
 *    undefined variable access. No early return on failure.
 *    Suggestion: Add null-guard at the top:
 *        const $ = (id) => document.getElementById(id);
 *        if (!modal || !overlay || !form || !submitBtn) return;
 *
 * 3. AdminAuth._token is a mutable public property — LINE 8
 *    Problem: Any code can overwrite _token with arbitrary values.
 *    Suggestion: Use a getter/setter pair:
 *        set token(v) { this._token = v; sessionStorage.setItem('...', v); }
 *        get token() { return this._token || sessionStorage.getItem('...'); }
 *
 * ====================================================================
 */
