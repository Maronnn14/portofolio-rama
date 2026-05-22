/* ============================================
   ADMIN PROFILE — Edit About / Personal info (API-backed)
   With draft state layer to preserve unsaved changes
   ============================================ */

/*
 * ===== DRAFT STATE LAYER =====
 * _original*  — frozen copy of data as loaded from server (for comparison / reset)
 * _draft*     — editable working copy; all reads/writes go here
 * The render function always reads from _draft*, never from _original* or a fresh API call.
 * Add/delete operations modify _draft* only and do NOT trigger a server re-fetch.
 */
let _draftProfile   = {};
let _draftInterests = [];
let _originalProfile   = {};
let _originalInterests = [];

async function renderProfile(container) {
  container.innerHTML = '<div class="admin-loading"><span class="spinner"></span> Loading profile...</div>';
  let p = {}, interests = [], about = { quote: '', stats: [] };
  try {
    [p, interests, about] = await Promise.all([API.personalInfo.get(), API.interests.list(), API.about.get()]);
  } catch (err) {
    container.innerHTML = '<div class="admin-empty"><div class="admin-empty__icon">⚠️</div><h3 class="admin-empty__title">Failed to load</h3></div>'; return;
  }

  /* Store original server data (read-only reference for comparison/cancel) */
  _originalProfile = {
    homeProfileImage: p.homeProfileImage || p.photo || p.profileImage || '',
    aboutProfileImage: p.aboutProfileImage || p.photo || p.profileImage || '',
    nickname: p.nickname || p.name || '',
    fullName: p.fullName || 'Rama Adin',
    role: p.role || 'Full Stack Developer',
    tagline: p.tagline || '',
    shortBio: p.shortBio || p.bio || '',
    fullBio: p.fullBio || p.story || '',
    email: p.email || '',
    location: p.location || '',
    github: p.github || '',
    aboutQuote: about.quote || '',
    aboutStats: (about.stats || []).map(s => ({ value: s.value || '', label: s.label || '' })),
  };
  _originalInterests = interests.map(i => ({
    id: i.id, name: i.name, icon: i.icon || '', description: i.description || '',
  }));

  /* Initialize draft state — editable copy of the server data */
  _draftProfile = { ..._originalProfile };
  _draftInterests = _originalInterests.map(i => ({ ...i }));
  _draftProfile.aboutStats = _originalProfile.aboutStats.map(s => ({ ...s }));

  const homeImg = _draftProfile.homeProfileImage;
  const aboutImg = _draftProfile.aboutProfileImage;

  container.innerHTML = `<div class="admin-form">
      <div class="admin-section-card">
        <h3 class="admin-section-card__title" style="margin-bottom:var(--space-xl);">Profile Photos</h3>
        <div class="admin-form__row">
          <div class="form-group">
            <label class="form-label">Home Page Photo</label>
            <div id="profile-home-photo-upload">${AdminUI.createImageUpload(homeImg, null, { maxSize: 5*1024*1024 })}</div>
          </div>
          <div class="form-group">
            <label class="form-label">About Me Photo</label>
            <div id="profile-about-photo-upload">${AdminUI.createImageUpload(aboutImg, null, { maxSize: 5*1024*1024 })}</div>
          </div>
        </div>
      </div>
      <div class="admin-section-card"><h3 class="admin-section-card__title" style="margin-bottom:var(--space-xl);">Personal Info</h3>
        <div class="admin-form__row"><div class="form-group"><label class="form-label">Display Name</label><input type="text" class="form-input" id="profile-name" value="${AdminUI.escapeHtml(_draftProfile.fullName)}" /></div><div class="form-group"><label class="form-label">Nickname / Panggilan</label><input type="text" class="form-input" id="profile-firstname" value="${AdminUI.escapeHtml(_draftProfile.nickname)}" /></div></div>
        <div class="form-group"><label class="form-label">Role / Title</label><input type="text" class="form-input" id="profile-role" value="${AdminUI.escapeHtml(_draftProfile.role)}" /></div>
        <div class="form-group"><label class="form-label">Tagline</label><input type="text" class="form-input" id="profile-tagline" value="${AdminUI.escapeHtml(_draftProfile.tagline)}" /></div>
        <div class="form-group"><label class="form-label">Short Bio</label><textarea class="form-textarea" id="profile-bio" maxlength="300" rows="3">${AdminUI.escapeHtml(_draftProfile.shortBio)}</textarea><p class="form-hint"><span id="profile-bio-count">${_draftProfile.shortBio.length}</span> / 300</p></div>
        <div class="form-group"><label class="form-label">Full Background Story</label><textarea class="form-textarea" id="profile-story" rows="6">${AdminUI.escapeHtml(_draftProfile.fullBio)}</textarea></div>
        <div class="admin-form__row"><div class="form-group"><label class="form-label">Email</label><input type="email" class="form-input" id="profile-email" value="${AdminUI.escapeHtml(_draftProfile.email)}" /></div><div class="form-group"><label class="form-label">Location</label><input type="text" class="form-input" id="profile-location" value="${AdminUI.escapeHtml(_draftProfile.location)}" /></div></div>
        <div class="form-group"><label class="form-label">GitHub Username</label><input type="text" class="form-input" id="profile-github" value="${AdminUI.escapeHtml(_draftProfile.github)}" /></div>
      </div>
      <div class="admin-section-card">
        <h3 class="admin-section-card__title" style="margin-bottom:var(--space-xl);">About Section</h3>
        <div class="form-group">
          <label class="form-label">Quote</label>
          <textarea class="form-textarea" id="about-quote" maxlength="300" rows="3">${AdminUI.escapeHtml(_draftProfile.aboutQuote)}</textarea>
          <p class="form-hint"><span id="about-quote-count">${_draftProfile.aboutQuote.length}</span> / 300</p>
        </div>
        <div style="display:grid;gap:var(--space-lg);margin-top:var(--space-lg);" id="about-stats-editor">
          ${[1,2,3].map(i => `
            <div style="display:flex;gap:var(--space-md);align-items:flex-end;padding:var(--space-md);background:var(--bg-tertiary);border-radius:var(--radius-md);">
              <div class="form-group" style="flex:1;margin:0;">
                <label class="form-label" style="font-size:0.7rem;">Stat ${i} — Value</label>
                <input type="text" class="form-input" id="stat-${i}-value" maxlength="20" value="${AdminUI.escapeHtml((_draftProfile.aboutStats[i-1]||{}).value || '')}" placeholder="e.g. 5+" />
              </div>
              <div class="form-group" style="flex:2;margin:0;">
                <label class="form-label" style="font-size:0.7rem;">Stat ${i} — Label</label>
                <input type="text" class="form-input" id="stat-${i}-label" maxlength="50" value="${AdminUI.escapeHtml((_draftProfile.aboutStats[i-1]||{}).label || '')}" placeholder="e.g. Years Experience" />
              </div>
            </div>
          `).join('')}
        </div>
        <button class="btn btn--primary" style="margin-top:var(--space-lg);" onclick="saveAbout()" id="about-save-btn">Save About Section</button>
      </div>
      <div class="admin-section-card"><div class="admin-section-card__header"><h3 class="admin-section-card__title">Interests / Hobbies</h3><button class="btn btn--secondary btn--sm" onclick="addInterest()">+ Add Interest</button></div><div id="interests-list">${_draftInterests.map((int, i) => renderInterestRow(int, i)).join('')}${_draftInterests.length === 0 ? '<p class="admin-empty__text">No interests added yet.</p>' : ''}</div></div>
      <div class="admin-form__actions">
        <button class="btn btn--primary" onclick="saveProfile()" id="profile-save-btn">Save Changes</button>
        <a href="about.html" target="_blank" class="btn btn--secondary">Preview →</a>
      </div>
    </div>`;

  AdminUI.bindStoredImageUpload(
    document.querySelector('#profile-home-photo-upload .admin-upload-zone')?.id,
    (url) => { _draftProfile.homeProfileImage = url; updateUnsavedIndicator(); },
    { folder: 'profile', maxSize: 5*1024*1024 }
  );
  AdminUI.bindStoredImageUpload(
    document.querySelector('#profile-about-photo-upload .admin-upload-zone')?.id,
    (url) => { _draftProfile.aboutProfileImage = url; updateUnsavedIndicator(); },
    { folder: 'profile', maxSize: 5*1024*1024 }
  );
  const bioEl = document.getElementById('profile-bio');
  const bioCount = document.getElementById('profile-bio-count');
  if (bioEl && bioCount) { bioEl.addEventListener('input', () => { bioCount.textContent = bioEl.value.length; }); }
  const quoteEl = document.getElementById('about-quote');
  const quoteCount = document.getElementById('about-quote-count');
  if (quoteEl && quoteCount) { quoteEl.addEventListener('input', () => { quoteCount.textContent = quoteEl.value.length; }); }
  bindInterestUploads();
  bindProfileInputs();
  bindInterestInputs();
  bindAboutInputs();
  updateUnsavedIndicator();
}

function createInterestThumb(iconUrl) {
  const id = 'iu_' + Math.random().toString(36).substring(2, 7);
  const hasImage = !!iconUrl;
  return `<div class="interest-thumb" id="${id}" title="Click to upload icon">
    <div class="interest-thumb__preview">
      ${hasImage
        ? `<img src="${AdminUI.escapeHtml(iconUrl)}" alt="" />`
        : `<span class="interest-thumb__placeholder">+</span>`
      }
    </div>
    <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" />
  </div>`;
}

function renderInterestRow(interest, index) {
  const iconUrl = interest.icon || '';
  return `<div class="interest-row" data-interest-idx="${index}" data-interest-id="${interest.id || ''}">
    <button class="interest-row__remove" onclick="removeInterest(${index})" title="Remove interest">✕</button>
    <div class="interest-row__icon-container">
      <div data-field="icon" data-url="${AdminUI.escapeHtml(iconUrl)}" id="interest-upload-${index}">
        ${createInterestThumb(iconUrl)}
      </div>
    </div>
    <div class="interest-row__body">
      <div style="width:100%;">
        <label class="form-hint" style="margin-bottom:0.35rem; display:block; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-muted); font-weight:600; text-align:center;">Hobby Name</label>
        <input type="text" class="form-input" placeholder="e.g. Photography" value="${AdminUI.escapeHtml(interest.name || '')}" data-field="name" />
      </div>
      <div style="width:100%;">
        <label class="form-hint" style="margin-bottom:0.35rem; display:block; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-muted); font-weight:600; text-align:center;">Short Description</label>
        <input type="text" class="form-input" placeholder="e.g. Capturing light and shadow" value="${AdminUI.escapeHtml(interest.description || '')}" data-field="desc" />
      </div>
    </div>
  </div>`;
}

/*
 * Before re-rendering the interests list, sync every interest's current
 * DOM values back into _draftInterests so that in-progress edits are
 * never lost when an element is added or removed.
 */
function syncInterestDOMToDraft() {
  document.querySelectorAll('[data-interest-idx]').forEach(row => {
    const idx = parseInt(row.dataset.interestIdx);
    if (idx >= 0 && idx < _draftInterests.length) {
      const iconEl = row.querySelector('[data-field="icon"]');
      if (iconEl) _draftInterests[idx].icon = iconEl.dataset.url || '';
      const nameEl = row.querySelector('[data-field="name"]');
      if (nameEl) _draftInterests[idx].name = nameEl.value;
      const descEl = row.querySelector('[data-field="desc"]');
      if (descEl) _draftInterests[idx].description = descEl.value;
    }
  });
}

/*
 * Fixed add element logic:
 * 1. Sync all current interest values from DOM into draft state
 * 2. Add the new item to draft state only (no server call, no re-fetch)
 * 3. Re-render only the interests list (not the entire page)
 * 4. Preserve all other unsaved field values untouched
 */
function addInterest() {
  syncInterestDOMToDraft();
  _draftInterests.push({ icon: '', name: '', description: '' });
  renderInterestList();
}

/*
 * Fixed delete element logic:
 * 1. Sync all current interest values from DOM into draft state
 * 2. Remove the item from draft state only (no server call, no re-fetch)
 * 3. Re-render only the interests list
 * 4. Preserve all other unsaved field values untouched
 */
function removeInterest(idx) {
  syncInterestDOMToDraft();
  _draftInterests.splice(idx, 1);
  renderInterestList();
}

/* Re-render only the interests list from _draftInterests (partial re-render) */
function renderInterestList() {
  const list = document.getElementById('interests-list');
  list.innerHTML = _draftInterests.map((int, i) => renderInterestRow(int, i)).join('');
  if (_draftInterests.length === 0) list.innerHTML = '<p class="admin-empty__text">No interests added yet.</p>';
  bindInterestUploads();
  bindInterestInputs();
  updateUnsavedIndicator();
}

/*
 * Bind input/change listeners on profile fields to keep _draftProfile
 * in sync as the user types — no more relying on DOM reads at save time.
 */
function bindProfileInputs() {
  const fieldMap = {
    'profile-name': 'fullName',
    'profile-firstname': 'nickname',
    'profile-role': 'role',
    'profile-tagline': 'tagline',
    'profile-bio': 'shortBio',
    'profile-story': 'fullBio',
    'profile-email': 'email',
    'profile-location': 'location',
    'profile-github': 'github',
  };
  Object.entries(fieldMap).forEach(([id, key]) => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('input', () => {
      _draftProfile[key] = el.value;
      updateUnsavedIndicator();
    });
  });
}

/*
 * Bind input/change listeners on interest name/description fields to
 * keep _draftInterests in sync as the user types.
 */
function bindInterestInputs() {
  document.querySelectorAll('[data-interest-idx]').forEach(row => {
    const idx = parseInt(row.dataset.interestIdx);
    const nameInput = row.querySelector('[data-field="name"]');
    const descInput = row.querySelector('[data-field="desc"]');
    if (nameInput) {
      nameInput.addEventListener('input', () => {
        if (idx >= 0 && idx < _draftInterests.length) {
          _draftInterests[idx].name = nameInput.value;
          updateUnsavedIndicator();
        }
      });
    }
    if (descInput) {
      descInput.addEventListener('input', () => {
        if (idx >= 0 && idx < _draftInterests.length) {
          _draftInterests[idx].description = descInput.value;
          updateUnsavedIndicator();
        }
      });
    }
  });
}

function bindInterestUploads() {
  document.querySelectorAll('[id^="interest-upload-"]').forEach(el => {
    if (el._bound) return;
    el._bound = true;
    const thumb = el.querySelector('.interest-thumb');
    const input = thumb?.querySelector('input[type="file"]');
    if (!thumb || !input) return;

    thumb.addEventListener('click', () => input.click());

    input.addEventListener('change', async (e) => {
      const file = e.target.files[0];
      if (!file) return;
      if (file.size > 5 * 1024 * 1024) {
        AdminUI.toast('Image too large. Max 5MB', 'error');
        return;
      }
      thumb.classList.add('interest-thumb--loading');
      try {
        const result = await API.media.uploadImage(file, 'interests');
        const url = result.url;
        const preview = thumb.querySelector('.interest-thumb__preview');
        preview.innerHTML = `<img src="${url}" alt="" />`;
        thumb.classList.remove('interest-thumb--loading');
        el.dataset.url = url;
        /* sync to draft state so it is not lost on re-render */
        const row = el.closest('[data-interest-idx]');
        if (row) {
          const idx = parseInt(row.dataset.interestIdx);
          if (idx >= 0 && idx < _draftInterests.length) {
            _draftInterests[idx].icon = url;
            updateUnsavedIndicator();
          }
        }
      } catch (err) {
        thumb.classList.remove('interest-thumb--loading');
        AdminUI.toast(err.message || 'Upload failed', 'error');
      }
    });
  });
}

/*
 * Bind input/change listeners on about quote and stat fields to keep
 * _draftProfile in sync as the user types.
 */
function bindAboutInputs() {
  const quoteEl = document.getElementById('about-quote');
  if (quoteEl) {
    quoteEl.addEventListener('input', () => {
      _draftProfile.aboutQuote = quoteEl.value;
      updateUnsavedIndicator();
    });
  }
  [1, 2, 3].forEach(i => {
    const valueEl = document.getElementById(`stat-${i}-value`);
    const labelEl = document.getElementById(`stat-${i}-label`);
    if (valueEl) {
      valueEl.addEventListener('input', () => {
        if (_draftProfile.aboutStats[i - 1]) {
          _draftProfile.aboutStats[i - 1].value = valueEl.value;
          updateUnsavedIndicator();
        }
      });
    }
    if (labelEl) {
      labelEl.addEventListener('input', () => {
        if (_draftProfile.aboutStats[i - 1]) {
          _draftProfile.aboutStats[i - 1].label = labelEl.value;
          updateUnsavedIndicator();
        }
      });
    }
  });
}

/*
 * saveAbout sends the quote + 3 stats to PUT /api/about.
 * It reads from the draft state layer so unsaved edits are never lost.
 */
async function saveAbout() {
  const quote = (document.getElementById('about-quote')?.value || _draftProfile.aboutQuote || '').trim();
  const stats = [1, 2, 3].map(i => ({
    value: (document.getElementById(`stat-${i}-value`)?.value || (_draftProfile.aboutStats[i - 1]?.value || '')).trim(),
    label: (document.getElementById(`stat-${i}-label`)?.value || (_draftProfile.aboutStats[i - 1]?.label || '')).trim(),
  }));

  const saveBtn = document.getElementById('about-save-btn');
  const originalLabel = saveBtn ? saveBtn.textContent : '';
  if (saveBtn) { saveBtn.disabled = true; saveBtn.textContent = 'Saving...'; }

  try {
    const result = await API.about.update({ quote, stats });
    _draftProfile.aboutQuote = result.quote || '';
    _draftProfile.aboutStats = (result.stats || []).map(s => ({ value: s.value || '', label: s.label || '' }));
    _originalProfile.aboutQuote = _draftProfile.aboutQuote;
    _originalProfile.aboutStats = _draftProfile.aboutStats.map(s => ({ ...s }));
    AdminAuth.logAction('Updated', 'About section');
    AdminUI.toast('About section saved!');
    updateUnsavedIndicator();
  } catch (err) {
    AdminUI.toast(err.message || 'Failed to save about section', 'error');
  } finally {
    if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = originalLabel; }
  }
}

/* Compare draft vs original to show an unsaved changes indicator */
function hasUnsavedChanges() {
  /* Check profile fields (including aboutQuote which is a string) */
  for (const key of Object.keys(_originalProfile)) {
    if (key === 'aboutStats') continue;
    if (_draftProfile[key] !== _originalProfile[key]) return true;
  }
  /* Check aboutStats array */
  const ds = _draftProfile.aboutStats || [];
  const os = _originalProfile.aboutStats || [];
  if (ds.length !== os.length) return true;
  for (let i = 0; i < ds.length; i++) {
    if (ds[i].value !== os[i].value || ds[i].label !== os[i].label) return true;
  }
  /* Check interests (length, then each field) */
  if (_draftInterests.length !== _originalInterests.length) return true;
  for (let i = 0; i < _draftInterests.length; i++) {
    const d = _draftInterests[i];
    const o = _originalInterests[i] || {};
    if (d.name !== (o.name || '') ||
        d.description !== (o.description || '') ||
        d.icon !== (o.icon || '')) return true;
  }
  return false;
}

function updateUnsavedIndicator() {
  const btn = document.getElementById('profile-save-btn');
  if (!btn) return;
  const changed = hasUnsavedChanges();
  btn.textContent = changed ? 'Save Changes ●' : 'Save Changes';
  btn.classList.toggle('btn--unsaved', changed);
}

/*
 * saveProfile reads from the draft state layer instead of querying the DOM.
 * This guarantees that whatever the user last typed is included, even if the
 * DOM was partially re-rendered by an add/delete operation.
 */
async function saveProfile() {
  /* Collect any last-moment interest edits from the DOM into draft */
  syncInterestDOMToDraft();

  const personalData = {
    nickname: _draftProfile.nickname.trim(),
    name: _draftProfile.nickname.trim(),
    fullName: _draftProfile.fullName.trim(),
    role: _draftProfile.role.trim(),
    tagline: _draftProfile.tagline.trim(),
    shortBio: _draftProfile.shortBio.trim(),
    fullBio: _draftProfile.fullBio.trim(),
    email: _draftProfile.email.trim(),
    location: _draftProfile.location.trim(),
    github: _draftProfile.github.trim(),
    homeProfileImage: _draftProfile.homeProfileImage || '',
    aboutProfileImage: _draftProfile.aboutProfileImage || '',
  };

  const interestsData = _draftInterests
    .filter(i => i.name.trim())
    .map(i => ({
      id: i.id || null,
      icon: i.icon || '',
      name: i.name.trim(),
      description: i.description.trim(),
    }));

  const saveBtn = document.getElementById('profile-save-btn');
  const originalLabel = saveBtn ? saveBtn.textContent : '';
  if (saveBtn) {
    saveBtn.disabled = true;
    saveBtn.textContent = 'Saving...';
  }

  try {
    await Promise.all([
      API.personalInfo.update(personalData),
      API.interests.bulkUpdate(interestsData),
    ]);
    if (window.PortfolioData) {
      window.PortfolioData.invalidate();
    }
    AdminAuth.logAction('Updated', 'Profile');
    AdminUI.toast('Profile saved successfully!');
    /* After a successful save, update only the personal + interest fields as original.
       About fields (aboutQuote, aboutStats) are managed by saveAbout() separately. */
    const savedKeys = ['homeProfileImage','aboutProfileImage','nickname','fullName','role','tagline','shortBio','fullBio','email','location','github'];
    savedKeys.forEach(k => { _originalProfile[k] = _draftProfile[k]; });
    _originalInterests = _draftInterests.map(i => ({ ...i }));
    updateUnsavedIndicator();
  } catch (err) {
    AdminUI.toast(err.message || 'Failed to save profile', 'error');
  } finally {
    if (saveBtn) {
      saveBtn.disabled = false;
      saveBtn.textContent = originalLabel;
    }
  }
}

/* Warn the user if they try to leave with unsaved changes */
window.addEventListener('beforeunload', (e) => {
  if (hasUnsavedChanges()) {
    e.preventDefault();
    e.returnValue = '';
  }
});
