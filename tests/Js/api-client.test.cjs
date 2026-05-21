/**
 * Jest tests for public/js/api-client.js
 *
 * Mocks:
 * - fetch: never make real HTTP requests
 * - document.querySelector: CSRF token meta tag
 * - sessionStorage: Sanctum bearer token persistence
 * - AdminAuth: authentication status check
 * - FormData: multipart uploads
 */

const fs = require('fs');
const path = require('path');

const sourcePath = path.resolve(__dirname, '../../public/js/api-client.js');
const sourceCode = fs.readFileSync(sourcePath, 'utf-8');

const loadSource = () => {
  global.fetch = jest.fn();
  global.AdminAuth = { isAuthenticated: jest.fn(() => false) };
  try { delete global.sessionStorage; } catch (e) { /* not configurable */ }
  try { delete global.window.sessionStorage; } catch (e) { /* not configurable */ }
  const mockStorage = {};
  global.sessionStorage = {
    getItem: jest.fn((key) => mockStorage[key] ?? null),
    setItem: jest.fn((key, val) => { mockStorage[key] = String(val); }),
    removeItem: jest.fn((key) => { delete mockStorage[key]; }),
    clear: jest.fn(() => { Object.keys(mockStorage).forEach(k => delete mockStorage[k]); }),
    key: jest.fn((i) => Object.keys(mockStorage)[i] ?? null),
    get length() { return Object.keys(mockStorage).length; },
  };
  global.FormData = class FormData {
    constructor() { this._data = {}; }
    append(key, value) { this._data[key] = value; }
  };
  global.window = global;

  global.Response = class Response {
    constructor(body, init = {}) {
      this._body = typeof body === 'string' ? body : JSON.stringify(body);
      this.ok = init.status >= 200 && init.status < 300;
      this.status = init.status || 200;
    }
    async json() { return JSON.parse(this._body); }
  };

  const evalFn = new Function(sourceCode);
  evalFn.call(global);
};

beforeEach(() => {
  loadSource();
});

// ====== HAPPY PATH ======

test('GET request builds correct URL and method', async () => {
  global.fetch.mockResolvedValue(new global.Response({ data: [] }, { status: 200 }));

  const data = await API.get('/projects');

  expect(global.fetch).toHaveBeenCalledTimes(1);
  const [url, options] = global.fetch.mock.calls[0];
  expect(url).toMatch(/^\/api\/projects\?_=\d+$/);
  expect(options.method).toBe('GET');
  expect(data).toEqual({ data: [] });
});

test('POST request sends JSON body', async () => {
  global.fetch.mockResolvedValue(new global.Response({ id: 1 }, { status: 201 }));

  const payload = { name: 'Test', category: 'web' };
  const data = await API.post('/projects', payload);

  const [, options] = global.fetch.mock.calls[0];
  expect(options.method).toBe('POST');
  expect(options.headers['Content-Type']).toBe('application/json');
  expect(JSON.parse(options.body)).toEqual(payload);
  expect(data).toEqual({ id: 1 });
});

test('PUT request sends JSON body', async () => {
  global.fetch.mockResolvedValue(new global.Response({ id: 1 }, { status: 200 }));

  await API.put('/projects/1', { name: 'Updated' });

  const [, options] = global.fetch.mock.calls[0];
  expect(options.method).toBe('PUT');
  expect(JSON.parse(options.body)).toEqual({ name: 'Updated' });
});

test('DELETE request sends JSON body and correct method', async () => {
  global.fetch.mockResolvedValue(new global.Response({ ok: true }, { status: 200 }));

  await API.del('/messages/bulk', { ids: ['a', 'b'] });

  const [, options] = global.fetch.mock.calls[0];
  expect(options.method).toBe('DELETE');
  expect(JSON.parse(options.body)).toEqual({ ids: ['a', 'b'] });
});

test('GET request does not send body', async () => {
  global.fetch.mockResolvedValue(new global.Response([], { status: 200 }));

  await API.get('/skills');

  const [, options] = global.fetch.mock.calls[0];
  expect(options.body).toBeUndefined();
});

test('sets cache: no-store and Accept header', async () => {
  global.fetch.mockResolvedValue(new global.Response({}, { status: 200 }));

  await API.get('/test');

  const [, options] = global.fetch.mock.calls[0];
  expect(options.cache).toBe('no-store');
  expect(options.headers['Accept']).toBe('application/json');
});

test('upload sends FormData without Content-Type header', async () => {
  global.fetch.mockResolvedValue(new global.Response({ url: '/storage/test.jpg' }, { status: 201 }));

  const formData = new global.FormData();
  formData.append('image', 'fake-file');
  formData.append('folder', 'profile');

  const data = await API.upload('/media', formData);

  const [, options] = global.fetch.mock.calls[0];
  expect(options.method).toBe('POST');
  expect(options.headers['Content-Type']).toBeUndefined();
  expect(options.body).toBe(formData);
  expect(data).toEqual({ url: '/storage/test.jpg' });
});

// ====== CSRF TOKEN ======

test('includes CSRF token when meta tag exists', async () => {
  global.fetch.mockResolvedValue(new global.Response({}, { status: 200 }));
  jest.spyOn(global.document, 'querySelector').mockReturnValue({ content: 'csrf-token-value' });

  await API.post('/projects', { name: 'X' });

  const [, options] = global.fetch.mock.calls[0];
  expect(options.headers['X-CSRF-TOKEN']).toBe('csrf-token-value');
});

test('does not fail when CSRF meta tag is missing', async () => {
  global.fetch.mockResolvedValue(new global.Response({}, { status: 200 }));
  jest.spyOn(global.document, 'querySelector').mockReturnValue(null);

  await API.post('/projects', { name: 'X' });

  const [, options] = global.fetch.mock.calls[0];
  expect(options.headers['X-CSRF-TOKEN']).toBeUndefined();
});

// ====== BEARER TOKEN ======

test('includes Bearer token when authenticated', async () => {
  global.fetch.mockResolvedValue(new global.Response({}, { status: 200 }));
  global.AdminAuth.isAuthenticated.mockReturnValue(true);
  global.sessionStorage.getItem.mockReturnValue('sanctum-token-abc');

  await API.get('/projects');

  const [, options] = global.fetch.mock.calls[0];
  expect(options.headers['Authorization']).toBe('Bearer sanctum-token-abc');
  expect(global.sessionStorage.getItem).toHaveBeenCalledWith('portfolio_sanctum_token');
});

test('omits Bearer token when not authenticated', async () => {
  global.fetch.mockResolvedValue(new global.Response({}, { status: 200 }));
  global.AdminAuth.isAuthenticated.mockReturnValue(false);

  await API.get('/projects');

  const [, options] = global.fetch.mock.calls[0];
  expect(options.headers['Authorization']).toBeUndefined();
});

test('does not fail when AdminAuth is absent', async () => {
  global.fetch.mockResolvedValue(new global.Response({}, { status: 200 }));
  delete global.AdminAuth;
  const standaloneEval = new Function(sourceCode);
  standaloneEval.call(global);

  await API.get('/projects');

  const [, options] = global.fetch.mock.calls[0];
  expect(options.headers['Authorization']).toBeUndefined();
});

// ====== FAILURE CASES ======

test('throws on 422 with error message and field errors', async () => {
  global.fetch.mockResolvedValue(new global.Response(
    { message: 'Validation failed', errors: { name: ['Required'] } },
    { status: 422 },
  ));

  await expect(API.post('/projects', {})).rejects.toThrow('Validation failed');

  try {
    await API.post('/projects', {});
  } catch (e) {
    expect(e.status).toBe(422);
    expect(e.errors).toEqual({ name: ['Required'] });
  }
});

test('throws generic error when JSON parse fails on error', async () => {
  global.fetch.mockResolvedValue({
    ok: false,
    status: 500,
    json: jest.fn().mockRejectedValue(new Error('Invalid JSON')),
  });

  await expect(API.get('/projects')).rejects.toThrow('API Error: 500');
});

test('throws on 401 unauthorized', async () => {
  global.fetch.mockResolvedValue(new global.Response(
    { message: 'Unauthenticated' }, { status: 401 },
  ));

  await expect(API.get('/settings')).rejects.toThrow('Unauthenticated');
});

test('throws on 403 forbidden', async () => {
  global.fetch.mockResolvedValue(new global.Response(
    { message: 'Forbidden' }, { status: 403 },
  ));

  await expect(API.del('/projects/1')).rejects.toThrow('Forbidden');
});

test('throws on 404', async () => {
  global.fetch.mockResolvedValue(new global.Response(
    { message: 'Not found' }, { status: 404 },
  ));

  await expect(API.get('/projects/999')).rejects.toThrow('Not found');
});

// ====== EDGE CASES ======

test('handles empty JSON object response', async () => {
  global.fetch.mockResolvedValue(new global.Response({}, { status: 200 }));

  const data = await API.get('/projects');
  expect(data).toEqual({});
});

test('GET request adds cache-busting parameter', async () => {
  global.fetch.mockResolvedValue(new global.Response([], { status: 200 }));

  await API.get('/projects');

  const [url] = global.fetch.mock.calls[0];
  expect(url).toContain('_=');
});

test('GET appends cache-busting when query params already present', async () => {
  global.fetch.mockResolvedValue(new global.Response([], { status: 200 }));

  await API.get('/projects?page=1');

  const [url] = global.fetch.mock.calls[0];
  expect(url).toContain('?page=1&_=');
});

test('all resource helpers call correct endpoint', async () => {
  global.fetch.mockResolvedValue(new global.Response([], { status: 200 }));

  await API.projects.list();
  expect(global.fetch.mock.calls[0][0]).toMatch(/\/api\/projects\?/);

  global.fetch.mockResolvedValue(new global.Response({}, { status: 200 }));
  await API.skills.list();
  expect(global.fetch.mock.calls[1][0]).toMatch(/\/api\/skills\?/);

  await API.experiences.list();
  expect(global.fetch.mock.calls[2][0]).toMatch(/\/api\/experiences\?/);

  await API.socials.list();
  expect(global.fetch.mock.calls[3][0]).toMatch(/\/api\/socials\?/);

  await API.gallery.list();
  expect(global.fetch.mock.calls[4][0]).toMatch(/\/api\/gallery\?/);

  await API.interests.list();
  expect(global.fetch.mock.calls[5][0]).toMatch(/\/api\/interests\?/);

  await API.messages.list();
  expect(global.fetch.mock.calls[6][0]).toMatch(/\/api\/messages\?/);

  await API.personalInfo.get();
  expect(global.fetch.mock.calls[7][0]).toMatch(/\/api\/personal-info\?/);

  await API.settings.get();
  expect(global.fetch.mock.calls[8][0]).toMatch(/\/api\/settings\?/);
});

test('media.uploadImage creates FormData with file and folder', async () => {
  global.fetch.mockResolvedValue(new global.Response({ url: '/storage/test.jpg' }, { status: 201 }));

  const file = { name: 'test.jpg', size: 1024 };
  await API.media.uploadImage(file, 'gallery');

  const [, options] = global.fetch.mock.calls[0];
  expect(options.body._data.image).toBe(file);
  expect(options.body._data.folder).toBe('gallery');
});

test('media.uploadImage defaults folder to profile', async () => {
  global.fetch.mockResolvedValue(new global.Response({ url: '/storage/test.jpg' }, { status: 201 }));

  const file = { name: 'test.jpg', size: 1024 };
  await API.media.uploadImage(file);

  expect(global.fetch.mock.calls[0][1].body._data.folder).toBe('profile');
});

test('exposes API on window object', () => {
  expect(global.window.API).toBeDefined();
  expect(global.window.API).toBe(API);
});

test('handles concurrent requests independently', async () => {
  let callCount = 0;
  global.fetch.mockImplementation(async () => {
    callCount++;
    return new global.Response({ count: callCount }, { status: 200 });
  });

  const [r1, r2, r3] = await Promise.all([
    API.get('/a'),
    API.get('/b'),
    API.get('/c'),
  ]);

  expect(r1).toEqual({ count: 1 });
  expect(r2).toEqual({ count: 2 });
  expect(r3).toEqual({ count: 3 });
  expect(global.fetch).toHaveBeenCalledTimes(3);
});

test('rejects when network fails', async () => {
  global.fetch.mockRejectedValue(new TypeError('Failed to fetch'));

  await expect(API.get('/projects')).rejects.toThrow('Failed to fetch');
});


/*
 * ====================================================================
 * TESTABILITY ANALYSIS
 * ====================================================================
 *
 * 1. API object is hard-coupled to AdminAuth global — LINE 36
 *    Problem: API.request() and API.upload() reference
 *    `typeof AdminAuth !== 'undefined'` directly. If AdminAuth is
 *    removed or renamed, all resource calls break silently.
 *    Suggestion: Accept a token source function via setTokenProvider():
 *    Before:
 *        if (typeof AdminAuth !== 'undefined' && AdminAuth.isAuthenticated()) {
 *            const token = sessionStorage.getItem('portfolio_sanctum_token');
 *    After:
 *        API.setTokenProvider(() => sessionStorage.getItem('portfolio_sanctum_token'));
 *        // Then internally:
 *        const token = this._tokenProvider && this._tokenProvider();
 *
 * 2. CSRF token read from DOM on every request — LINE 30-33
 *    Problem: Each request calls document.querySelector(), coupling
 *    every test to the DOM. Tests must mock document for every test.
 *    Suggestion: Cache CSRF token at init time, re-fetch only on 419:
 *    Before:
 *        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
 *        if (csrfMeta) { options.headers['X-CSRF-TOKEN'] = csrfMeta.content; }
 *    After:
 *        API._csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
 *        // Use API._csrfToken in request()
 *
 * 3. Resource helpers are static object literals — LINES 103-172
 *    Problem: API.projects, API.skills, etc. are baked in at definition
 *    time. The base URL / endpoint structure is not overridable.
 *    Suggestion: Make base URL configurable:
 *        API.setBaseUrl(url) { this.base = url; }
 *
 * ====================================================================
 */
