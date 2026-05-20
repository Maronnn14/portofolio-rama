/* ============================================
   API CLIENT — Centralized REST API Helper
   ============================================ */

const API = {
  base: '/api',

  /**
   * Core request method.
   * @param {string} method - HTTP method
   * @param {string} endpoint - API endpoint (e.g., '/projects')
   * @param {object|null} data - Request body
   * @returns {Promise<any>} Parsed JSON response
   */
  async request(method, endpoint, data = null) {
    const cacheBust = method === 'GET'
      ? `${endpoint.includes('?') ? '&' : '?'}_=${Date.now()}`
      : '';
    const url = this.base + endpoint + cacheBust;
    const options = {
      method,
      cache: 'no-store',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    };

    // Add CSRF token if available
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta) {
      options.headers['X-CSRF-TOKEN'] = csrfMeta.content;
    }

    if (data && (method === 'POST' || method === 'PUT' || method === 'DELETE')) {
      options.body = JSON.stringify(data);
    }

    const response = await fetch(url, options);

    if (!response.ok) {
      const errorBody = await response.json().catch(() => ({}));
      const error = new Error(errorBody.message || `API Error: ${response.status}`);
      error.status = response.status;
      error.errors = errorBody.errors || {};
      throw error;
    }

    return response.json();
  },

  async upload(endpoint, formData) {
    const url = this.base + endpoint;
    const options = {
      method: 'POST',
      cache: 'no-store',
      headers: {
        'Accept': 'application/json',
      },
      body: formData,
    };

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta) {
      options.headers['X-CSRF-TOKEN'] = csrfMeta.content;
    }

    const response = await fetch(url, options);

    if (!response.ok) {
      const errorBody = await response.json().catch(() => ({}));
      const error = new Error(errorBody.message || `API Error: ${response.status}`);
      error.status = response.status;
      error.errors = errorBody.errors || {};
      throw error;
    }

    return response.json();
  },

  get(endpoint) { return this.request('GET', endpoint); },
  post(endpoint, data) { return this.request('POST', endpoint, data); },
  put(endpoint, data) { return this.request('PUT', endpoint, data); },
  del(endpoint, data) { return this.request('DELETE', endpoint, data); },

  /* ---- Resource helpers ---- */

  projects: {
    list()          { return API.get('/projects'); },
    create(data)    { return API.post('/projects', data); },
    update(id, data){ return API.put(`/projects/${id}`, data); },
    delete(id)      { return API.del(`/projects/${id}`); },
  },

  skills: {
    list()          { return API.get('/skills'); },
    create(data)    { return API.post('/skills', data); },
    update(id, data){ return API.put(`/skills/${id}`, data); },
    delete(id)      { return API.del(`/skills/${id}`); },
  },

  experiences: {
    list()          { return API.get('/experiences'); },
    create(data)    { return API.post('/experiences', data); },
    update(id, data){ return API.put(`/experiences/${id}`, data); },
    delete(id)      { return API.del(`/experiences/${id}`); },
  },

  messages: {
    list()            { return API.get('/messages'); },
    create(data)      { return API.post('/messages', data); },
    update(id, data)  { return API.put(`/messages/${id}`, data); },
    delete(id)        { return API.del(`/messages/${id}`); },
    bulkDelete(ids)   { return API.del('/messages/bulk', { ids }); },
  },

  socials: {
    list()          { return API.get('/socials'); },
    create(data)    { return API.post('/socials', data); },
    update(id, data){ return API.put(`/socials/${id}`, data); },
    delete(id)      { return API.del(`/socials/${id}`); },
  },

  gallery: {
    list()          { return API.get('/gallery'); },
    create(data)    { return API.post('/gallery', data); },
    update(id, data){ return API.put(`/gallery/${id}`, data); },
    delete(id)      { return API.del(`/gallery/${id}`); },
    bulkDelete(ids) { return API.del('/gallery-bulk', { ids }); },
  },

  interests: {
    list()              { return API.get('/interests'); },
    create(data)        { return API.post('/interests', data); },
    update(id, data)    { return API.put(`/interests/${id}`, data); },
    delete(id)          { return API.del(`/interests/${id}`); },
    bulkUpdate(items)   { return API.put('/interests-bulk', { interests: items }); },
  },

  personalInfo: {
    get()       { return API.get('/personal-info'); },
    update(data){ return API.put('/personal-info', data); },
  },

  settings: {
    get()       { return API.get('/settings'); },
    update(data){ return API.put('/settings', data); },
  },

  media: {
    uploadImage(file, folder = 'profile') {
      const formData = new FormData();
      formData.append('image', file);
      formData.append('folder', folder);
      return API.upload('/media', formData);
    },
  },
};

/* Make available globally */
if (typeof window !== 'undefined') {
  window.API = API;
}
