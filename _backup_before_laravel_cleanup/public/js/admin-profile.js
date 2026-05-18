/* ============================================
   ADMIN PROFILE — Edit About / Personal info
   ============================================ */

function renderProfile(container) {
  const data = DataStore.getData();
  const p = data.personal || {};
  const interests = data.interests || [];

  let uploadedPhoto = p.photo || '';

  container.innerHTML = `
    <div class="admin-form">
      <div class="admin-section-card">
        <h3 class="admin-section-card__title" style="margin-bottom:var(--space-xl);">Profile Photo</h3>
        <div id="profile-photo-upload">
          ${AdminUI.createImageUpload(uploadedPhoto, null, { maxSize: 5*1024*1024 })}
        </div>
      </div>

      <div class="admin-section-card">
        <h3 class="admin-section-card__title" style="margin-bottom:var(--space-xl);">Personal Info</h3>
        <div class="admin-form__row">
          <div class="form-group">
            <label class="form-label">Display Name</label>
            <input type="text" class="form-input" id="profile-name" value="${AdminUI.escapeHtml(p.fullName || '')}" />
          </div>
          <div class="form-group">
            <label class="form-label">First Name</label>
            <input type="text" class="form-input" id="profile-firstname" value="${AdminUI.escapeHtml(p.name || '')}" />
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Role / Title</label>
          <input type="text" class="form-input" id="profile-role" value="${AdminUI.escapeHtml(p.role || '')}" />
        </div>
        <div class="form-group">
          <label class="form-label">Tagline</label>
          <input type="text" class="form-input" id="profile-tagline" value="${AdminUI.escapeHtml(p.tagline || '')}" />
        </div>
        <div class="form-group">
          <label class="form-label">Short Bio <small style="color:var(--text-muted);">(homepage preview, max 300 chars)</small></label>
          <textarea class="form-textarea" id="profile-bio" maxlength="300" rows="3">${AdminUI.escapeHtml(p.bio || '')}</textarea>
          <p class="form-hint"><span id="profile-bio-count">${(p.bio || '').length}</span> / 300</p>
        </div>
        <div class="form-group">
          <label class="form-label">Full Background Story <small style="color:var(--text-muted);">(About page)</small></label>
          <textarea class="form-textarea" id="profile-story" rows="6">${AdminUI.escapeHtml(p.story || '')}</textarea>
        </div>
        <div class="admin-form__row">
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" class="form-input" id="profile-email" value="${AdminUI.escapeHtml(p.email || '')}" />
          </div>
          <div class="form-group">
            <label class="form-label">Location</label>
            <input type="text" class="form-input" id="profile-location" value="${AdminUI.escapeHtml(p.location || '')}" />
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">GitHub Username</label>
          <input type="text" class="form-input" id="profile-github" value="${AdminUI.escapeHtml(p.github || '')}" />
        </div>
      </div>

      <!-- Interests -->
      <div class="admin-section-card">
        <div class="admin-section-card__header">
          <h3 class="admin-section-card__title">Interests / Hobbies</h3>
          <button class="btn btn--secondary btn--sm" onclick="addInterest()">+ Add Interest</button>
        </div>
        <div id="interests-list">
          ${interests.map((int, i) => renderInterestRow(int, i)).join('')}
          ${interests.length === 0 ? '<p class="admin-empty__text">No interests added yet.</p>' : ''}
        </div>
      </div>

      <!-- Actions -->
      <div class="admin-form__actions">
        <button class="btn btn--primary" onclick="saveProfile()">Save Changes</button>
        <a href="about.html" target="_blank" class="btn btn--secondary">Preview →</a>
      </div>
    </div>
  `;

  // Bind image upload
  const uploadZone = container.querySelector('.admin-upload-zone');
  if (uploadZone) {
    AdminUI.bindImageUpload(uploadZone.id, (base64) => { uploadedPhoto = base64; });
  }

  // Bind bio counter
  const bioEl = document.getElementById('profile-bio');
  const bioCount = document.getElementById('profile-bio-count');
  if (bioEl && bioCount) {
    bioEl.addEventListener('input', () => { bioCount.textContent = bioEl.value.length; });
  }

  // Store photo ref globally for save
  window._profilePhoto = uploadedPhoto;
}

function renderInterestRow(interest, index) {
  return `
    <div style="display:flex;align-items:center;gap:var(--space-md);padding:var(--space-md) 0;border-bottom:1px solid var(--border-subtle);" data-interest-idx="${index}">
      <input type="text" class="form-input" style="width:60px;text-align:center;" value="${interest.icon || '🎯'}" data-field="icon" />
      <input type="text" class="form-input" style="flex:1;" placeholder="Interest name" value="${AdminUI.escapeHtml(interest.name || '')}" data-field="name" />
      <input type="text" class="form-input" style="flex:2;" placeholder="Short description" value="${AdminUI.escapeHtml(interest.desc || '')}" data-field="desc" />
      <button class="admin-table__btn admin-table__btn--danger" onclick="removeInterest(${index})">✕</button>
    </div>`;
}

function addInterest() {
  const list = document.getElementById('interests-list');
  const data = DataStore.getData();
  if (!data.interests) data.interests = [];
  data.interests.push({ icon: '🎯', name: '', desc: '' });
  list.innerHTML = data.interests.map((int, i) => renderInterestRow(int, i)).join('');
}

function removeInterest(idx) {
  const data = DataStore.getData();
  data.interests.splice(idx, 1);
  const list = document.getElementById('interests-list');
  list.innerHTML = data.interests.map((int, i) => renderInterestRow(int, i)).join('');
  if (data.interests.length === 0) list.innerHTML = '<p class="admin-empty__text">No interests added yet.</p>';
}

function saveProfile() {
  const data = DataStore.getData();

  data.personal.name = document.getElementById('profile-firstname').value.trim();
  data.personal.fullName = document.getElementById('profile-name').value.trim();
  data.personal.role = document.getElementById('profile-role').value.trim();
  data.personal.tagline = document.getElementById('profile-tagline').value.trim();
  data.personal.bio = document.getElementById('profile-bio').value.trim();
  data.personal.story = document.getElementById('profile-story').value.trim();
  data.personal.email = document.getElementById('profile-email').value.trim();
  data.personal.location = document.getElementById('profile-location').value.trim();
  data.personal.github = document.getElementById('profile-github').value.trim();

  if (window._profilePhoto) data.personal.photo = window._profilePhoto;

  // Collect interests from DOM
  const interestRows = document.querySelectorAll('[data-interest-idx]');
  data.interests = Array.from(interestRows).map(row => ({
    icon: row.querySelector('[data-field="icon"]').value,
    name: row.querySelector('[data-field="name"]').value.trim(),
    desc: row.querySelector('[data-field="desc"]').value.trim(),
  })).filter(i => i.name);

  DataStore.saveAll(data);
  AdminAuth.logAction('Updated', 'Profile');
  AdminUI.toast('Profile saved successfully!');
}
