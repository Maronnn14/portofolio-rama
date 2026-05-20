/* ============================================
   ADMIN SOCIALS — CRUD Manager
   ============================================ */

function renderSocials(container) {
  const data = DataStore.getData();
  const socials = data.socials || [];

  container.innerHTML = `
    <div class="admin-section-card">
      <div class="admin-section-card__header">
        <h3 class="admin-section-card__title">Social Media Links</h3>
        <button class="btn btn--primary btn--sm" onclick="openSocialModal()">+ Add Link</button>
      </div>
      ${socials.length ? `
        <div class="admin-table-responsive">
          <table class="admin-table">
            <thead><tr><th>Platform</th><th>URL</th><th>Label</th><th>Visible</th><th>Actions</th></tr></thead>
            <tbody>
              ${socials.map((s, i) => `
                <tr>
                  <td style="color:var(--text-primary);font-weight:var(--fw-medium);">${getSocialIcon(s.platform)} ${AdminUI.escapeHtml(s.platform)}</td>
                  <td><a href="${s.url}" target="_blank" style="color:var(--accent);font-size:var(--fs-xs);">${AdminUI.escapeHtml(s.url).substring(0, 35)}...</a></td>
                  <td>${AdminUI.escapeHtml(s.label || '—')}</td>
                  <td>${s.visible !== false ? AdminUI.badge('Visible', 'success') : AdminUI.badge('Hidden', 'default')}</td>
                  <td>
                    <div class="admin-table__actions">
                      <button class="admin-table__btn" onclick="toggleSocialVisibility(${i})">${s.visible !== false ? '👁' : '👁‍🗨'}</button>
                      <button class="admin-table__btn" onclick="openSocialModal(${i})">✏️</button>
                      <button class="admin-table__btn admin-table__btn--danger" onclick="deleteSocial(${i})">🗑️</button>
                    </div>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      ` : `
        <div class="admin-empty">
          <div class="admin-empty__icon">🔗</div>
          <h3 class="admin-empty__title">No social links</h3>
          <p class="admin-empty__text">Add your social media profiles.</p>
        </div>
      `}
    </div>
  `;
}

function getSocialIcon(platform) {
  const icons = {
    GitHub: '🐙', LinkedIn: '💼', Instagram: '📸', Twitter: '🐦', YouTube: '📺',
    Dribbble: '🏀', TikTok: '🎵', Facebook: '📘', Medium: '📝', Discord: '💬',
    Telegram: '✈️', WhatsApp: '📱', Behance: '🅱️', CodePen: '💻',
  };
  return icons[platform] || '🔗';
}

function openSocialModal(editIndex = null) {
  const data = DataStore.getData();
  const isEdit = editIndex !== null;
  const social = isEdit ? data.socials[editIndex] : {};

  const platforms = ['GitHub', 'LinkedIn', 'Instagram', 'Twitter', 'YouTube', 'Dribbble', 'TikTok', 'Facebook', 'Medium', 'Discord', 'Telegram', 'WhatsApp', 'Behance', 'CodePen', 'Other'];

  const formHTML = `
    <div class="admin-form">
      <div class="form-group">
        <label class="form-label">Platform</label>
        <select class="form-input" id="social-platform">
          ${platforms.map(p => `<option value="${p}" ${social.platform === p ? 'selected' : ''}>${getSocialIcon(p)} ${p}</option>`).join('')}
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Profile URL *</label>
        <input type="url" class="form-input" id="social-url" value="${AdminUI.escapeHtml(social.url || '')}" placeholder="https://..." />
      </div>
      <div class="form-group">
        <label class="form-label">Display Label</label>
        <input type="text" class="form-input" id="social-label" value="${AdminUI.escapeHtml(social.label || '')}" placeholder="e.g., @yourhandle" />
      </div>
      <div class="form-group">
        <label style="display:flex;align-items:center;gap:var(--space-sm);cursor:pointer;">
          <input type="checkbox" id="social-visible" ${social.visible !== false ? 'checked' : ''} />
          <span class="form-label" style="margin:0;">Visible on site</span>
        </label>
      </div>
      <div class="admin-form__actions">
        <button class="btn btn--primary" id="social-save">${isEdit ? 'Update' : 'Add'}</button>
        <button class="btn btn--secondary" id="social-cancel">Cancel</button>
      </div>
    </div>`;

  const { modal, close } = AdminUI.openModal(isEdit ? 'Edit Social Link' : 'Add Social Link', formHTML, { maxWidth: '480px' });

  document.getElementById('social-save').addEventListener('click', () => {
    const url = document.getElementById('social-url').value.trim();
    if (!url) { AdminUI.toast('URL is required', 'error'); return; }

    const newSocial = {
      platform: document.getElementById('social-platform').value,
      url,
      label: document.getElementById('social-label').value.trim(),
      visible: document.getElementById('social-visible').checked,
    };

    const d = DataStore.getData();
    if (!d.socials) d.socials = [];
    if (isEdit) { d.socials[editIndex] = newSocial; } else { d.socials.push(newSocial); }
    DataStore.saveAll(d);
    AdminAuth.logAction(isEdit ? 'Updated' : 'Created', `Social: ${newSocial.platform}`);
    AdminUI.toast(`Social link ${isEdit ? 'updated' : 'added'}!`);
    close();
    renderSocials(document.getElementById('admin-content'));
  });

  document.getElementById('social-cancel').addEventListener('click', close);
}

function toggleSocialVisibility(index) {
  const data = DataStore.getData();
  data.socials[index].visible = !data.socials[index].visible;
  DataStore.saveAll(data);
  AdminUI.toast(data.socials[index].visible ? 'Social link visible' : 'Social link hidden');
  renderSocials(document.getElementById('admin-content'));
}

function deleteSocial(index) {
  const data = DataStore.getData();
  const s = data.socials[index];
  AdminUI.confirm('Delete Social Link', `Remove ${s.platform} link?`, () => {
    data.socials.splice(index, 1);
    DataStore.saveAll(data);
    AdminAuth.logAction('Deleted', `Social: ${s.platform}`);
    AdminUI.toast('Social link deleted');
    renderSocials(document.getElementById('admin-content'));
  });
}
