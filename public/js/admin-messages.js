/* ============================================
   ADMIN MESSAGES — Board Manager (API-backed)
   ============================================ */

let _adminMessages = [];

async function renderMessages(container) {
  container.innerHTML = '<div class="admin-loading"><span class="spinner"></span> Loading messages...</div>';
  try { _adminMessages = await API.messages.list(); } catch (err) {
    container.innerHTML = '<div class="admin-empty"><div class="admin-empty__icon">⚠️</div><h3 class="admin-empty__title">Failed to load</h3></div>'; return;
  }
  const settings = await API.settings.get().catch(() => ({}));
  container.innerHTML = `
    <div class="admin-section-card" style="margin-bottom:var(--space-xl);"><div class="admin-section-card__header"><h3 class="admin-section-card__title">Message Board Settings</h3></div>
      <div style="display:flex;flex-wrap:wrap;gap:var(--space-2xl);align-items:flex-start;">
        <label style="display:flex;align-items:center;gap:var(--space-sm);cursor:pointer;"><input type="checkbox" id="mb-allow-posts" ${settings.allowPosts !== false ? 'checked' : ''} onchange="updateMBSetting('allowPosts', this.checked)" /><span style="font-size:var(--fs-sm);color:var(--text-secondary);">Allow new messages</span></label>
        <label style="display:flex;align-items:center;gap:var(--space-sm);cursor:pointer;"><input type="checkbox" id="mb-moderation" ${settings.moderationMode ? 'checked' : ''} onchange="updateMBSetting('moderationMode', this.checked)" /><span style="font-size:var(--fs-sm);color:var(--text-secondary);">Require approval</span></label>
        <div style="display:flex;align-items:center;gap:var(--space-sm);"><span style="font-size:var(--fs-sm);color:var(--text-secondary);">Max length:</span><input type="number" class="form-input" style="width:80px;padding:var(--space-xs) var(--space-sm);" value="${settings.maxLength || 300}" id="mb-max-length" onchange="updateMBSetting('maxLength', parseInt(this.value))" /></div>
      </div>
    </div>
    <div class="admin-section-card"><div class="admin-section-card__header"><h3 class="admin-section-card__title">Messages (${_adminMessages.length})</h3>
        <div style="display:flex;gap:var(--space-md);align-items:center;"><input type="text" class="form-input" placeholder="Search..." id="msg-search" style="width:180px;padding:var(--space-sm) var(--space-md);" /><button class="btn btn--secondary btn--sm" onclick="bulkDeleteMessages()" id="msg-bulk-delete" style="display:none;">Delete Selected</button></div></div>
      <div style="display:flex;gap:var(--space-sm);margin-bottom:var(--space-lg);flex-wrap:wrap;">
        <button class="filter-tab active" onclick="filterMessages('all', this)">All (${_adminMessages.length})</button>
        <button class="filter-tab" onclick="filterMessages('visible', this)">Visible (${_adminMessages.filter(m => !m.hidden).length})</button>
        <button class="filter-tab" onclick="filterMessages('hidden', this)">Hidden (${_adminMessages.filter(m => m.hidden).length})</button>
        <button class="filter-tab" onclick="filterMessages('flagged', this)">Flagged (${_adminMessages.filter(m => m.flagged).length})</button>
        <button class="filter-tab" onclick="filterMessages('pinned', this)">Pinned (${_adminMessages.filter(m => m.pinned).length})</button>
      </div>
      <div id="admin-messages-table">${renderMessagesTable(_adminMessages)}</div>
    </div>`;
  const search = document.getElementById('msg-search');
  if (search) { search.addEventListener('input', () => { const q = search.value.toLowerCase(); document.querySelectorAll('#admin-messages-table tr[data-msg-id]').forEach(r => { r.style.display = (r.dataset.name + ' ' + r.dataset.msg).toLowerCase().includes(q) ? '' : 'none'; }); }); }
}

function renderMessagesTable(messages) {
  if (!messages.length) return '<div class="admin-empty"><div class="admin-empty__icon">💬</div><h3 class="admin-empty__title">No messages</h3></div>';
  return `<table class="admin-table"><thead><tr><th><input type="checkbox" id="msg-select-all" onchange="toggleAllMessages(this.checked)" /></th><th>Name</th><th>Message</th><th>Rating</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead><tbody>${messages.map(m => {
    const statusClass = m.flagged ? 'error' : m.hidden ? 'default' : m.pinned ? 'accent' : 'success';
    const statusLabel = m.flagged ? 'Flagged' : m.hidden ? 'Hidden' : m.pinned ? 'Pinned' : 'Visible';
    return `<tr data-msg-id="${m.id}" data-name="${AdminUI.escapeHtml(m.name)}" data-msg="${AdminUI.escapeHtml(m.message)}" data-status="${m.flagged?'flagged':m.hidden?'hidden':m.pinned?'pinned':'visible'}" style="${m.flagged?'background:rgba(239,68,68,0.05);':''}"><td><input type="checkbox" class="msg-checkbox" value="${m.id}" onchange="updateBulkBtn()" /></td><td style="color:var(--text-primary);font-weight:var(--fw-medium);">${AdminUI.escapeHtml(m.name)}</td><td style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${AdminUI.escapeHtml(m.message)}</td><td>${m.rating ? '⭐'.repeat(m.rating) : '—'}</td><td style="white-space:nowrap;">${AdminUI.timeAgo(m.timestamp)}</td><td>${AdminUI.badge(statusLabel, statusClass)}</td><td><div class="admin-table__actions"><button class="admin-table__btn" onclick="toggleMsgVisibility('${m.id}')" title="Show/Hide">${m.hidden?'👁':'👁‍🗨'}</button><button class="admin-table__btn" onclick="toggleMsgFlag('${m.id}')" title="Flag">🚩</button><button class="admin-table__btn" onclick="toggleMsgPin('${m.id}')" title="Pin">📌</button><button class="admin-table__btn admin-table__btn--danger" onclick="adminDeleteMsg('${m.id}')" title="Delete">🗑️</button></div></td></tr>`;
  }).join('')}</tbody></table>`;
}

function filterMessages(filter, btn) {
  btn.parentElement.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('#admin-messages-table tr[data-msg-id]').forEach(r => {
    if (filter === 'all') { r.style.display = ''; return; }
    r.style.display = r.dataset.status === filter ? '' : 'none';
  });
}

async function updateMBSetting(key, value) {
  try { await API.settings.update({ [key]: value }); AdminUI.toast('Setting updated'); }
  catch { AdminUI.toast('Failed to save setting', 'error'); }
}

async function toggleMsgVisibility(id) {
  const msg = _adminMessages.find(m => m.id === id);
  if (!msg) return;
  try { await API.messages.update(id, { hidden: !msg.hidden }); renderMessages(document.getElementById('admin-content')); }
  catch (err) { AdminUI.toast(err.message, 'error'); }
}

async function toggleMsgFlag(id) {
  const msg = _adminMessages.find(m => m.id === id);
  if (!msg) return;
  try { await API.messages.update(id, { flagged: !msg.flagged }); renderMessages(document.getElementById('admin-content')); }
  catch (err) { AdminUI.toast(err.message, 'error'); }
}

async function toggleMsgPin(id) {
  const msg = _adminMessages.find(m => m.id === id);
  if (!msg) return;
  try { await API.messages.update(id, { pinned: !msg.pinned }); renderMessages(document.getElementById('admin-content')); }
  catch (err) { AdminUI.toast(err.message, 'error'); }
}

function adminDeleteMsg(id) {
  AdminUI.confirm('Delete Message', 'Permanently remove this message?', async () => {
    try { await API.messages.delete(id); AdminAuth.logAction('Deleted', 'Message'); AdminUI.toast('Message deleted'); renderMessages(document.getElementById('admin-content')); }
    catch (err) { AdminUI.toast(err.message, 'error'); }
  });
}

function toggleAllMessages(checked) { document.querySelectorAll('.msg-checkbox').forEach(cb => { cb.checked = checked; }); updateBulkBtn(); }
function updateBulkBtn() { const c = document.querySelectorAll('.msg-checkbox:checked').length; const b = document.getElementById('msg-bulk-delete'); if (b) b.style.display = c > 0 ? '' : 'none'; }

function bulkDeleteMessages() {
  const ids = Array.from(document.querySelectorAll('.msg-checkbox:checked')).map(cb => cb.value);
  if (!ids.length) return;
  AdminUI.confirm('Bulk Delete', `Delete ${ids.length} selected message(s)?`, async () => {
    try { await API.messages.bulkDelete(ids); AdminAuth.logAction('Deleted', `${ids.length} messages`); AdminUI.toast(`${ids.length} messages deleted`); renderMessages(document.getElementById('admin-content')); }
    catch (err) { AdminUI.toast(err.message, 'error'); }
  });
}
