/* ============================================
   ADMIN SOCIALS — CRUD Manager (API-backed)
   ============================================ */

let _adminSocials = [];

async function renderSocials(container) {
  container.innerHTML = '<div class="admin-loading"><span class="spinner"></span> Loading socials...</div>';
  try { _adminSocials = await API.socials.list(); } catch (err) {
    container.innerHTML = '<div class="admin-empty"><div class="admin-empty__icon">⚠️</div><h3 class="admin-empty__title">Failed to load</h3></div>'; return;
  }
  container.innerHTML = `<div class="admin-section-card"><div class="admin-section-card__header"><h3 class="admin-section-card__title">Social Media Links</h3><button class="btn btn--primary btn--sm" onclick="openSocialModal()">+ Add Link</button></div>
    ${_adminSocials.length ? `<div style="display:flex;gap:var(--space-md);margin-bottom:var(--space-md);align-items:center;"><span style="font-size:var(--fs-xs);color:var(--text-muted);" id="social-selected-count">0 selected</span><button class="btn btn--sm btn--danger" id="social-bulk-delete" style="display:none;" onclick="bulkDeleteSocials()">Delete Selected</button></div><table class="admin-table"><thead><tr><th style="width:30px;"><input type="checkbox" id="social-select-all" onchange="toggleAllSocial(this.checked)" /></th><th>Platform</th><th>URL</th><th>Label</th><th>Visible</th><th>Actions</th></tr></thead><tbody>${_adminSocials.map((s, i) => `<tr data-id="${s.id}"><td><input type="checkbox" class="social-checkbox" value="${s.id}" onchange="updateSocialBulkBtn()" /></td><td style="color:var(--text-primary);font-weight:var(--fw-medium);">${getSocialIcon(s.platform)} ${AdminUI.escapeHtml(s.platform)}</td><td><a href="${s.url}" target="_blank" style="color:var(--accent);font-size:var(--fs-xs);">${AdminUI.escapeHtml(s.url).substring(0, 35)}...</a></td><td>${AdminUI.escapeHtml(s.label || '—')}</td><td>${s.visible !== false ? AdminUI.badge('Visible', 'success') : AdminUI.badge('Hidden', 'default')}</td><td><div class="admin-table__actions"><button class="admin-table__btn" onclick="toggleSocialVisibility(${i})">${s.visible !== false ? '👁' : '👁‍🗨'}</button><button class="admin-table__btn" onclick="openSocialModal(${i})">✏️</button><button class="admin-table__btn admin-table__btn--danger" onclick="deleteSocial(${i})">🗑️</button></div></td></tr>`).join('')}</tbody></table>` : `<div class="admin-empty"><div class="admin-empty__icon">🔗</div><h3 class="admin-empty__title">No social links</h3><p class="admin-empty__text">Add your social media profiles.</p></div>`}</div>`;
}

function getSocialIcon(platform) {
  const icons = { GitHub: '🐙', LinkedIn: '💼', Instagram: '📸', Twitter: '🐦', YouTube: '📺', Dribbble: '🏀', TikTok: '🎵', Facebook: '📘', Medium: '📝', Discord: '💬', Telegram: '✈️', WhatsApp: '📱', Behance: '🅱️', CodePen: '💻', Bootstrap: '📦' };
  return icons[platform] || '🔗';
}

function openSocialModal(editIndex = null) {
  const isEdit = editIndex !== null;
  const social = isEdit ? _adminSocials[editIndex] : {};
  const platforms = ['GitHub', 'LinkedIn', 'Instagram', 'Twitter', 'YouTube', 'Dribbble', 'TikTok', 'Facebook', 'Medium', 'Discord', 'Telegram', 'WhatsApp', 'Behance', 'CodePen', 'Bootstrap', 'Other'];
  const formHTML = `<div class="admin-form">
      <div class="form-group"><label class="form-label">Platform</label><select class="form-input" id="social-platform">${platforms.map(p => `<option value="${p}" ${social.platform===p?'selected':''}>${getSocialIcon(p)} ${p}</option>`).join('')}</select></div>
      <div class="form-group"><label class="form-label">Profile URL *</label><input type="url" class="form-input" id="social-url" value="${AdminUI.escapeHtml(social.url || '')}" placeholder="https://..." /></div>
      <div class="form-group"><label class="form-label">Display Label</label><input type="text" class="form-input" id="social-label" value="${AdminUI.escapeHtml(social.label || '')}" placeholder="e.g., @yourhandle" /></div>
      <div class="form-group"><label style="display:flex;align-items:center;gap:var(--space-sm);cursor:pointer;"><input type="checkbox" id="social-visible" ${social.visible !== false ? 'checked' : ''} /><span class="form-label" style="margin:0;">Visible on site</span></label></div>
      <div class="admin-form__actions"><button class="btn btn--primary" id="social-save">${isEdit?'Update':'Add'}</button><button class="btn btn--secondary" id="social-cancel">Cancel</button></div>
    </div>`;
  const { modal, close } = AdminUI.openModal(isEdit ? 'Edit Social Link' : 'Add Social Link', formHTML, { maxWidth: '480px' });

  document.getElementById('social-save').addEventListener('click', async () => {
    const url = document.getElementById('social-url').value.trim();
    if (!url) { AdminUI.toast('URL is required', 'error'); return; }
    const platform = document.getElementById('social-platform').value;
    const payload = { platform, url, label: document.getElementById('social-label').value.trim(), icon: platform.toLowerCase(), visible: document.getElementById('social-visible').checked };
    const saveBtn = document.getElementById('social-save'); saveBtn.disabled = true; saveBtn.textContent = 'Saving...';
    try {
      if (isEdit) { await API.socials.update(social.id, payload); } else { await API.socials.create(payload); }
      AdminAuth.logAction(isEdit ? 'Updated' : 'Created', `Social: ${platform}`);
      AdminUI.toast(`Social link ${isEdit ? 'updated' : 'added'}!`); close(); renderSocials(document.getElementById('admin-content'));
    } catch (err) { AdminUI.toast(err.message, 'error'); saveBtn.disabled = false; }
  });
  document.getElementById('social-cancel').addEventListener('click', close);
}

async function toggleSocialVisibility(index) {
  const s = _adminSocials[index];
  try { await API.socials.update(s.id, { visible: !s.visible }); AdminUI.toast(s.visible ? 'Social link hidden' : 'Social link visible'); renderSocials(document.getElementById('admin-content')); }
  catch (err) { AdminUI.toast(err.message, 'error'); }
}

function deleteSocial(index) {
  const s = _adminSocials[index];
  AdminUI.confirm('Delete Social Link', `Remove ${s.platform} link?`, async () => {
    try { await API.socials.delete(s.id); AdminAuth.logAction('Deleted', `Social: ${s.platform}`); AdminUI.toast('Social link deleted'); renderSocials(document.getElementById('admin-content')); }
    catch (err) { AdminUI.toast(err.message, 'error'); }
  });
}

function toggleAllSocial(checked) {
  document.querySelectorAll('.social-checkbox').forEach(cb => cb.checked = checked);
  updateSocialBulkBtn();
}

function updateSocialBulkBtn() {
  const checked = document.querySelectorAll('.social-checkbox:checked');
  const btn = document.getElementById('social-bulk-delete');
  const count = document.getElementById('social-selected-count');
  if (btn && count) {
    btn.style.display = checked.length ? '' : 'none';
    count.textContent = `${checked.length} selected`;
  }
}

function bulkDeleteSocials() {
  const checked = document.querySelectorAll('.social-checkbox:checked');
  const ids = Array.from(checked).map(cb => parseInt(cb.value));
  if (!ids.length) return;
  AdminUI.confirm('Delete Social Links', `Delete ${ids.length} selected social links? This is permanent.`, async () => {
    try {
      await API.socials.bulkDelete(ids);
      AdminAuth.logAction('Deleted', `${ids.length} social links`);
      AdminUI.toast(`${ids.length} social links deleted`);
      renderSocials(document.getElementById('admin-content'));
    } catch (err) {
      AdminUI.toast(err.message || 'Failed to delete', 'error');
    }
  });
}
