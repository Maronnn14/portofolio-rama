/* ============================================
   ADMIN SETTINGS — Site config & data mgmt
   ============================================ */

const SITE_SETTINGS_KEY = 'portfolio_site_settings';

function getSiteSettings() {
  try { return JSON.parse(localStorage.getItem(SITE_SETTINGS_KEY) || '{}'); } catch { return {}; }
}

function renderSettings(container) {
  const settings = getSiteSettings();
  const account = AdminAuth.getStoredAccount();

  container.innerHTML = `
    <!-- Site Settings -->
    <div class="admin-section-card">
      <h3 class="admin-section-card__title" style="margin-bottom:var(--space-xl);">Site Settings</h3>
      <div class="admin-form">
        <div class="form-group">
          <label class="form-label">Site Title</label>
          <input type="text" class="form-input" id="settings-title" value="${AdminUI.escapeHtml(settings.siteTitle || 'Rama Adin — Portfolio')}" />
        </div>
        <div class="form-group">
          <label class="form-label">Meta Description (SEO)</label>
          <textarea class="form-textarea" id="settings-meta" rows="2">${AdminUI.escapeHtml(settings.metaDesc || '')}</textarea>
        </div>
        <button class="btn btn--primary" onclick="saveSiteSettings()">Save Site Settings</button>
      </div>
    </div>

    <!-- Admin Account -->
    <div class="admin-section-card">
      <h3 class="admin-section-card__title" style="margin-bottom:var(--space-xl);">Admin Account</h3>
      <div class="admin-form">
        <div class="form-group">
          <label class="form-label">Username</label>
          <input type="text" class="form-input" id="settings-username" value="${AdminUI.escapeHtml(account.username || 'admin')}" />
        </div>
        <div class="form-group">
          <label class="form-label">Current Password <small style="color:var(--text-muted);">(required to change)</small></label>
          <input type="password" class="form-input" id="settings-current-pw" placeholder="Enter current password" />
        </div>
        <div class="form-group">
          <label class="form-label">New Password</label>
          <input type="password" class="form-input" id="settings-new-pw" placeholder="Enter new password" />
        </div>
        <button class="btn btn--primary" onclick="saveAdminAccount()">Save Account</button>
      </div>
    </div>

    <!-- Data Management -->
    <div class="admin-section-card">
      <h3 class="admin-section-card__title" style="margin-bottom:var(--space-xl);">Data Management</h3>
      <div style="display:flex;flex-wrap:wrap;gap:var(--space-md);">
        <button class="btn btn--secondary" onclick="exportAllData()">📦 Export All Data</button>
        <button class="btn btn--secondary" onclick="document.getElementById('import-file').click()">📥 Import Data</button>
        <input type="file" id="import-file" accept=".json" style="display:none;" onchange="importData(this)" />
        <button class="btn btn--secondary" style="border-color:var(--error);color:var(--error);" onclick="resetAllData()">🔄 Reset to Defaults</button>
      </div>
    </div>

    <!-- About -->
    <div class="admin-section-card">
      <h3 class="admin-section-card__title" style="margin-bottom:var(--space-xl);">About Dashboard</h3>
      <p style="font-size:var(--fs-sm);color:var(--text-secondary);margin-bottom:var(--space-sm);">
        <strong>Version:</strong> 1.0.0
      </p>
      <p style="font-size:var(--fs-sm);color:var(--text-secondary);">
        <strong>Stack:</strong> Vanilla HTML5, CSS3, JavaScript — No frameworks
      </p>
    </div>
  `;
}

function saveSiteSettings() {
  const settings = getSiteSettings();
  settings.siteTitle = document.getElementById('settings-title').value.trim();
  settings.metaDesc = document.getElementById('settings-meta').value.trim();
  localStorage.setItem(SITE_SETTINGS_KEY, JSON.stringify(settings));
  AdminAuth.logAction('Saved', 'Site settings');
  AdminUI.toast('Site settings saved!');
}

async function saveAdminAccount() {
  const currentPw = document.getElementById('settings-current-pw').value;
  const newUsername = document.getElementById('settings-username').value.trim();
  const newPw = document.getElementById('settings-new-pw').value;

  if (!currentPw) { AdminUI.toast('Enter current password to confirm changes', 'error'); return; }

  // Verify current password
  const stored = AdminAuth.getStoredAccount();
  const targetHash = stored.passwordHash || ADMIN_CONFIG.defaultPasswordHash;
  const inputHash = await sha256(currentPw);

  if (inputHash !== targetHash) { AdminUI.toast('Current password is incorrect', 'error'); return; }

  if (newUsername) await AdminAuth.updateAccount(newUsername, null);
  if (newPw) await AdminAuth.updateAccount(null, newPw);

  AdminAuth.logAction('Updated', 'Admin account');
  AdminUI.toast('Account updated!');
  document.getElementById('settings-current-pw').value = '';
  document.getElementById('settings-new-pw').value = '';
}

function exportAllData() {
  const json = DataStore.export();
  const blob = new Blob([json], { type: 'application/json' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `portfolio-backup-${new Date().toISOString().slice(0,10)}.json`;
  a.click();
  URL.revokeObjectURL(url);
  AdminAuth.logAction('Exported', 'All data');
  AdminUI.toast('Data exported!');
}

function importData(input) {
  const file = input.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = (e) => {
    AdminUI.confirm('Import Data', 'This will overwrite ALL current data. Continue?', () => {
      const success = DataStore.import(e.target.result);
      if (success) {
        AdminAuth.logAction('Imported', 'Data from file');
        AdminUI.toast('Data imported successfully!');
        renderSettings(document.getElementById('admin-content'));
      } else {
        AdminUI.toast('Invalid JSON file', 'error');
      }
    }, 'Import', false);
  };
  reader.readAsText(file);
  input.value = '';
}

function resetAllData() {
  AdminUI.confirm('Reset All Data', 'This will erase ALL customizations and restore defaults. This CANNOT be undone!', () => {
    AdminUI.confirm('Are you sure?', 'Type "RESET" mentally and click confirm. All data will be lost.', () => {
      DataStore.reset();
      localStorage.removeItem('portfolio_messages');
      localStorage.removeItem(APPEARANCE_KEY);
      localStorage.removeItem(SITE_SETTINGS_KEY);
      AdminAuth.logAction('Reset', 'All data to defaults');
      AdminUI.toast('All data reset to defaults');
      renderSettings(document.getElementById('admin-content'));
    }, 'Yes, Reset Everything');
  });
}
