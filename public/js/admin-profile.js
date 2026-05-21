/* ============================================
   ADMIN PROFILE — Edit About / Personal info (API-backed)
   ============================================ */

async function renderProfile(container) {
  container.innerHTML = '<div class="admin-loading"><span class="spinner"></span> Loading profile...</div>';
  let p = {}, interests = [];
  try {
    [p, interests] = await Promise.all([API.personalInfo.get(), API.interests.list()]);
  } catch (err) {
    container.innerHTML = '<div class="admin-empty"><div class="admin-empty__icon">⚠️</div><h3 class="admin-empty__title">Failed to load</h3></div>'; return;
  }
  let homeProfileImage = p.homeProfileImage || p.photo || p.profileImage || '';
  let aboutProfileImage = p.aboutProfileImage || p.photo || p.profileImage || '';
  container.innerHTML = `<div class="admin-form">
      <div class="admin-section-card">
        <h3 class="admin-section-card__title" style="margin-bottom:var(--space-xl);">Profile Photos</h3>
        <div class="admin-form__row">
          <div class="form-group">
            <label class="form-label">Home Page Photo</label>
            <div id="profile-home-photo-upload">${AdminUI.createImageUpload(homeProfileImage, null, { maxSize: 5*1024*1024 })}</div>
          </div>
          <div class="form-group">
            <label class="form-label">About Me Photo</label>
            <div id="profile-about-photo-upload">${AdminUI.createImageUpload(aboutProfileImage, null, { maxSize: 5*1024*1024 })}</div>
          </div>
        </div>
      </div>
      <div class="admin-section-card"><h3 class="admin-section-card__title" style="margin-bottom:var(--space-xl);">Personal Info</h3>
        <div class="admin-form__row"><div class="form-group"><label class="form-label">Display Name</label><input type="text" class="form-input" id="profile-name" value="${AdminUI.escapeHtml(p.fullName || '')}" /></div><div class="form-group"><label class="form-label">Nickname / Panggilan</label><input type="text" class="form-input" id="profile-firstname" value="${AdminUI.escapeHtml(p.nickname || p.name || '')}" /></div></div>
        <div class="form-group"><label class="form-label">Role / Title</label><input type="text" class="form-input" id="profile-role" value="${AdminUI.escapeHtml(p.role || '')}" /></div>
        <div class="form-group"><label class="form-label">Tagline</label><input type="text" class="form-input" id="profile-tagline" value="${AdminUI.escapeHtml(p.tagline || '')}" /></div>
        <div class="form-group"><label class="form-label">Short Bio</label><textarea class="form-textarea" id="profile-bio" maxlength="300" rows="3">${AdminUI.escapeHtml(p.shortBio || p.bio || '')}</textarea><p class="form-hint"><span id="profile-bio-count">${(p.shortBio || p.bio || '').length}</span> / 300</p></div>
        <div class="form-group"><label class="form-label">Full Background Story</label><textarea class="form-textarea" id="profile-story" rows="6">${AdminUI.escapeHtml(p.fullBio || p.story || '')}</textarea></div>
        <div class="admin-form__row"><div class="form-group"><label class="form-label">Email</label><input type="email" class="form-input" id="profile-email" value="${AdminUI.escapeHtml(p.email || '')}" /></div><div class="form-group"><label class="form-label">Location</label><input type="text" class="form-input" id="profile-location" value="${AdminUI.escapeHtml(p.location || '')}" /></div></div>
        <div class="form-group"><label class="form-label">GitHub Username</label><input type="text" class="form-input" id="profile-github" value="${AdminUI.escapeHtml(p.github || '')}" /></div>
      </div>
      <div class="admin-section-card"><div class="admin-section-card__header"><h3 class="admin-section-card__title">Interests / Hobbies</h3><button class="btn btn--secondary btn--sm" onclick="addInterest()">+ Add Interest</button></div><div id="interests-list">${interests.map((int, i) => renderInterestRow(int, i)).join('')}${interests.length === 0 ? '<p class="admin-empty__text">No interests added yet.</p>' : ''}</div></div>
      <div class="admin-form__actions"><button class="btn btn--primary" onclick="saveProfile()">Save Changes</button><a href="about.html" target="_blank" class="btn btn--secondary">Preview →</a></div>
    </div>`;

  AdminUI.bindStoredImageUpload(
    document.querySelector('#profile-home-photo-upload .admin-upload-zone')?.id,
    (url) => { homeProfileImage = url; window._homeProfileImage = url; },
    { folder: 'profile', maxSize: 5*1024*1024 }
  );
  AdminUI.bindStoredImageUpload(
    document.querySelector('#profile-about-photo-upload .admin-upload-zone')?.id,
    (url) => { aboutProfileImage = url; window._aboutProfileImage = url; },
    { folder: 'profile', maxSize: 5*1024*1024 }
  );
  const bioEl = document.getElementById('profile-bio');
  const bioCount = document.getElementById('profile-bio-count');
  if (bioEl && bioCount) { bioEl.addEventListener('input', () => { bioCount.textContent = bioEl.value.length; }); }
  window._homeProfileImage = homeProfileImage;
  window._aboutProfileImage = aboutProfileImage;
  window._profileInterests = interests;
}

function renderInterestRow(interest, index) {
  return `<div style="display:flex;align-items:center;gap:var(--space-md);padding:var(--space-md) 0;border-bottom:1px solid var(--border-subtle);" data-interest-idx="${index}" data-interest-id="${interest.id || ''}"><input type="text" class="form-input" style="width:60px;text-align:center;" value="${interest.icon || '🎯'}" data-field="icon" /><input type="text" class="form-input" style="flex:1;" placeholder="Interest name" value="${AdminUI.escapeHtml(interest.name || '')}" data-field="name" /><input type="text" class="form-input" style="flex:2;" placeholder="Short description" value="${AdminUI.escapeHtml(interest.description || interest.desc || '')}" data-field="desc" /><button class="admin-table__btn admin-table__btn--danger" onclick="removeInterest(${index})">✕</button></div>`;
}

function addInterest() {
  if (!window._profileInterests) window._profileInterests = [];
  window._profileInterests.push({ icon: '🎯', name: '', description: '' });
  const list = document.getElementById('interests-list');
  list.innerHTML = window._profileInterests.map((int, i) => renderInterestRow(int, i)).join('');
}

function removeInterest(idx) {
  window._profileInterests.splice(idx, 1);
  const list = document.getElementById('interests-list');
  list.innerHTML = window._profileInterests.map((int, i) => renderInterestRow(int, i)).join('');
  if (window._profileInterests.length === 0) list.innerHTML = '<p class="admin-empty__text">No interests added yet.</p>';
}

async function saveProfile() {
  const personalData = {
    nickname: document.getElementById('profile-firstname').value.trim(),
    name: document.getElementById('profile-firstname').value.trim(),
    fullName: document.getElementById('profile-name').value.trim(),
    role: document.getElementById('profile-role').value.trim(),
    tagline: document.getElementById('profile-tagline').value.trim(),
    shortBio: document.getElementById('profile-bio').value.trim(),
    fullBio: document.getElementById('profile-story').value.trim(),
    email: document.getElementById('profile-email').value.trim(),
    location: document.getElementById('profile-location').value.trim(),
    github: document.getElementById('profile-github').value.trim(),
    homeProfileImage: window._homeProfileImage || '',
    aboutProfileImage: window._aboutProfileImage || '',
  };

  const interestRows = document.querySelectorAll('[data-interest-idx]');
  const interestsData = Array.from(interestRows).map(row => ({
    id: row.dataset.interestId ? parseInt(row.dataset.interestId) : null,
    icon: row.querySelector('[data-field="icon"]').value,
    name: row.querySelector('[data-field="name"]').value.trim(),
    description: row.querySelector('[data-field="desc"]').value.trim(),
  })).filter(i => i.name);

  const saveBtn = document.querySelector('.admin-form__actions .btn--primary');
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
  } catch (err) {
    AdminUI.toast(err.message || 'Failed to save profile', 'error');
  } finally {
    if (saveBtn) {
      saveBtn.disabled = false;
      saveBtn.textContent = originalLabel;
    }
  }
}
