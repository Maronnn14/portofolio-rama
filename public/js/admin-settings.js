/* ============================================
   ADMIN SETTINGS — Site config & data mgmt (API-backed)
   ============================================ */

async function renderSettings(container) {
  container.innerHTML = '<div class="admin-loading"><span class="spinner"></span> Loading settings...</div>';
  let settings = {};
  try { settings = await API.settings.get(); } catch { settings = {}; }

  container.innerHTML = `
    <div class="admin-section-card"><h3 class="admin-section-card__title" style="margin-bottom:var(--space-xl);">Site Settings</h3><div class="admin-form">
        <div class="form-group"><label class="form-label">Site Title</label><input type="text" class="form-input" id="settings-title" value="${AdminUI.escapeHtml(settings.site_title || 'Rama Adin — Portfolio')}" /></div>
        <div class="form-group"><label class="form-label">Meta Description (SEO)</label><textarea class="form-textarea" id="settings-meta" rows="2">${AdminUI.escapeHtml(settings.site_description || '')}</textarea></div>
        <button class="btn btn--primary" onclick="saveSiteSettings()">Save Site Settings</button>
      </div></div>
    <div class="admin-section-card"><h3 class="admin-section-card__title" style="margin-bottom:var(--space-xl);">Admin Account</h3><div class="admin-form">
        <p style="font-size:var(--fs-sm);color:var(--text-secondary);margin-bottom:var(--space-md);">Logged in as <strong>${AdminUI.escapeHtml(AdminAuth.getEmail())}</strong></p>
        <div class="form-group"><label class="form-label">Current Password</label><input type="password" class="form-input" id="settings-current-pw" placeholder="Enter current password" /></div>
        <div class="form-group"><label class="form-label">New Password</label><input type="password" class="form-input" id="settings-new-pw" placeholder="Enter new password (min 8 chars)" /></div>
        <button class="btn btn--primary" onclick="saveAdminAccount()">Change Password</button>
      </div></div>
    <div class="admin-section-card"><h3 class="admin-section-card__title" style="margin-bottom:var(--space-xl);">Data Management</h3>
      <div style="display:flex;flex-wrap:wrap;gap:var(--space-md);">
        <button class="btn btn--secondary" onclick="exportAllData()">📦 Export All Data</button>
        <button class="btn btn--secondary" style="border-color:var(--error);color:var(--error);" onclick="resetAllData()">🔄 Reset to Defaults</button>
      </div></div>
    <div class="admin-section-card"><h3 class="admin-section-card__title" style="margin-bottom:var(--space-xl);">About Dashboard</h3>
      <p style="font-size:var(--fs-sm);color:var(--text-secondary);margin-bottom:var(--space-sm);"><strong>Version:</strong> 2.1.0 (Sanctum Auth)</p>
      <p style="font-size:var(--fs-sm);color:var(--text-secondary);"><strong>Stack:</strong> Laravel Sanctum REST API + Vanilla JS Frontend</p>
    </div>`;
}

async function saveSiteSettings() {
  const data = {
    siteTitle: document.getElementById('settings-title').value.trim(),
    metaDesc: document.getElementById('settings-meta').value.trim(),
  };
  try { await API.settings.update(data); AdminAuth.logAction('Saved', 'Site settings'); AdminUI.toast('Site settings saved!'); }
  catch (err) { AdminUI.toast(err.message || 'Failed to save', 'error'); }
}

async function saveAdminAccount() {
  const currentPw = document.getElementById('settings-current-pw').value;
  const newPw = document.getElementById('settings-new-pw').value;
  if (!currentPw) { AdminUI.toast('Enter current password to confirm changes', 'error'); return; }
  if (!newPw) { AdminUI.toast('Enter a new password', 'error'); return; }
  if (newPw.length < 8) { AdminUI.toast('Password must be at least 8 characters', 'error'); return; }
  try {
    const res = await apiFetch('/api/auth/change-password', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({ current_password: currentPw, new_password: newPw }),
    });
    if (!res.ok) {
      const data = await res.json();
      throw new Error(data.message || data.errors?.current_password?.[0] || 'Failed to change password');
    }
    const data = await res.json();
    // Update stored token
    sessionStorage.setItem('portfolio_sanctum_token', data.token);
    AdminUI.toast('Password changed! Other sessions logged out.');
  } catch (err) {
    AdminUI.toast(err.message || 'Failed to change password', 'error');
  }
  document.getElementById('settings-current-pw').value = '';
  document.getElementById('settings-new-pw').value = '';
}

async function exportAllData() {
  try {
    const [projects, skills, experiences, socials, interests, gallery, messages, personalInfo, settings] = await Promise.all([
      API.projects.list(), API.skills.list(), API.experiences.list(), API.socials.list(),
      API.interests.list(), API.gallery.list(), API.messages.list(), API.personalInfo.get(), API.settings.get()
    ]);
    const data = { projects, skills, experiences, socials, interests, gallery, messages, personalInfo, settings, exportedAt: new Date().toISOString() };
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a'); a.href = url; a.download = `portfolio-backup-${new Date().toISOString().slice(0,10)}.json`; a.click();
    URL.revokeObjectURL(url);
    AdminAuth.logAction('Exported', 'All data');
    AdminUI.toast('Data exported!');
  } catch (err) { AdminUI.toast(err.message || 'Export failed', 'error'); }
}

function resetAllData() {
  AdminUI.confirm('Reset All Data', 'This will erase ALL customizations and restore defaults. This CANNOT be undone!', () => {
    AdminUI.confirm('Are you sure?', 'All data will be lost.', async () => {
      AdminAuth.logAction('Reset', 'All data to defaults');
      AdminUI.toast('To reset, please re-run the database seeder on the server.');
    }, 'Yes, Reset Everything');
  });
}
