/* ============================================
   ADMIN EXPERIENCE — CRUD Manager (API-backed)
   ============================================ */

let _adminExperiences = [];

async function renderExperience(container) {
  container.innerHTML = '<div class="admin-loading"><span class="spinner"></span> Loading experience...</div>';
  try { _adminExperiences = await API.experiences.list(); } catch (err) {
    container.innerHTML = '<div class="admin-empty"><div class="admin-empty__icon">⚠️</div><h3 class="admin-empty__title">Failed to load</h3></div>'; return;
  }
  container.innerHTML = `<div class="admin-section-card"><div class="admin-section-card__header"><h3 class="admin-section-card__title">Experience & Education</h3><button class="btn btn--primary btn--sm" onclick="openExpModal()">+ Add Entry</button></div>
      ${_adminExperiences.length ? `<div style="display:flex;gap:var(--space-md);margin-bottom:var(--space-md);align-items:center;"><span style="font-size:var(--fs-xs);color:var(--text-muted);" id="exp-selected-count">0 selected</span><button class="btn btn--sm btn--danger" id="exp-bulk-delete" style="display:none;" onclick="bulkDeleteExperiences()">Delete Selected</button></div><table class="admin-table"><thead><tr><th style="width:30px;"><input type="checkbox" id="exp-select-all" onchange="toggleAllExp(this.checked)" /></th><th>Role</th><th>Company</th><th>Period</th><th>Type</th><th>Actions</th></tr></thead><tbody>${_adminExperiences.map((e, i) => `<tr data-id="${e.id}"><td><input type="checkbox" class="exp-checkbox" value="${e.id}" onchange="updateExpBulkBtn()" /></td><td style="color:var(--text-primary);font-weight:var(--fw-medium);">${AdminUI.escapeHtml(e.role)}</td><td>${AdminUI.escapeHtml(e.company)}</td><td style="white-space:nowrap;">${AdminUI.escapeHtml(e.start_date)} — ${AdminUI.escapeHtml(e.end_date)}</td><td>${AdminUI.badge(e.type, e.type==='Work'?'accent':e.type==='Education'?'success':'default')}</td><td><div class="admin-table__actions"><button class="admin-table__btn" onclick="openExpModal(${i})">✏️</button><button class="admin-table__btn admin-table__btn--danger" onclick="deleteExp(${i})">🗑️</button></div></td></tr>`).join('')}</tbody></table>` : `<div class="admin-empty"><div class="admin-empty__icon">🕐</div><h3 class="admin-empty__title">No experience entries</h3><p class="admin-empty__text">Add your work and education history.</p></div>`}
    </div>`;
}

function openExpModal(editIndex = null) {
  const isEdit = editIndex !== null;
  const entry = isEdit ? _adminExperiences[editIndex] : {};
  const formHTML = `<div class="admin-form">
      <div class="admin-form__row"><div class="form-group"><label class="form-label">Role / Title *</label><input type="text" class="form-input" id="exp-role" value="${AdminUI.escapeHtml(entry.role || '')}" /></div><div class="form-group"><label class="form-label">Company / Institution *</label><input type="text" class="form-input" id="exp-company" value="${AdminUI.escapeHtml(entry.company || '')}" /></div></div>
      <div class="form-group"><label class="form-label">Location</label><input type="text" class="form-input" id="exp-location" value="${AdminUI.escapeHtml(entry.location || '')}" /></div>
      <div class="admin-form__row"><div class="form-group"><label class="form-label">Start Date *</label><input type="text" class="form-input" id="exp-start" placeholder="e.g., Jan 2023" value="${AdminUI.escapeHtml(entry.start_date || '')}" /></div><div class="form-group"><label class="form-label">End Date</label><input type="text" class="form-input" id="exp-end" placeholder="e.g., Dec 2024 or Present" value="${AdminUI.escapeHtml(entry.end_date || '')}" /></div></div>
      <div class="form-group"><label class="form-label">Type</label><select class="form-input" id="exp-type"><option value="Work" ${entry.type==='Work'?'selected':''}>Work</option><option value="Education" ${entry.type==='Education'?'selected':''}>Education</option><option value="Freelance" ${entry.type==='Freelance'?'selected':''}>Freelance</option><option value="Volunteer" ${entry.type==='Volunteer'?'selected':''}>Volunteer</option></select></div>
      <div class="form-group"><label class="form-label">Description</label><textarea class="form-textarea" id="exp-desc" rows="3">${AdminUI.escapeHtml(entry.description || '')}</textarea></div>
      <div class="form-group"><label class="form-label">Technologies</label><div id="exp-tech-tags"></div></div>
      <div class="admin-form__actions"><button class="btn btn--primary" id="exp-save">${isEdit?'Update':'Add'} Entry</button><button class="btn btn--secondary" id="exp-cancel">Cancel</button></div>
    </div>`;
  const { modal, close } = AdminUI.openModal(isEdit ? 'Edit Experience' : 'Add Experience', formHTML);
  AdminUI.createTagInput(document.getElementById('exp-tech-tags'), [...(entry.tech || [])]);

  document.getElementById('exp-save').addEventListener('click', async () => {
    const role = document.getElementById('exp-role').value.trim();
    const company = document.getElementById('exp-company').value.trim();
    if (!role || !company) { AdminUI.toast('Role and Company are required', 'error'); return; }
    const techTags = document.getElementById('exp-tech-tags');
    const tags = Array.from(techTags.querySelectorAll('.admin-tag-input__tag')).map(t => t.textContent.replace('×','').trim());
    const payload = { role, company, location: document.getElementById('exp-location').value.trim(), start_date: document.getElementById('exp-start').value.trim(), end_date: document.getElementById('exp-end').value.trim() || 'Present', type: document.getElementById('exp-type').value, description: document.getElementById('exp-desc').value.trim(), tech: tags };
    const saveBtn = document.getElementById('exp-save'); saveBtn.disabled = true; saveBtn.textContent = 'Saving...';
    try {
      if (isEdit) { await API.experiences.update(entry.id, payload); } else { await API.experiences.create(payload); }
      AdminAuth.logAction(isEdit ? 'Updated' : 'Created', `Experience: ${role}`);
      AdminUI.toast(`Experience ${isEdit ? 'updated' : 'added'}!`); close(); renderExperience(document.getElementById('admin-content'));
    } catch (err) { AdminUI.toast(err.message || 'Failed to save', 'error'); saveBtn.disabled = false; saveBtn.textContent = isEdit ? 'Update Entry' : 'Add Entry'; }
  });
  document.getElementById('exp-cancel').addEventListener('click', close);
}

function deleteExp(index) {
  const entry = _adminExperiences[index];
  AdminUI.confirm('Delete Experience', `Remove "${entry.role} at ${entry.company}"?`, async () => {
    try { await API.experiences.delete(entry.id); AdminAuth.logAction('Deleted', `Experience: ${entry.role}`); AdminUI.toast('Experience deleted'); renderExperience(document.getElementById('admin-content')); }
    catch (err) { AdminUI.toast(err.message || 'Failed to delete', 'error'); }
  });
}

function toggleAllExp(checked) {
  document.querySelectorAll('.exp-checkbox').forEach(cb => cb.checked = checked);
  updateExpBulkBtn();
}

function updateExpBulkBtn() {
  const checked = document.querySelectorAll('.exp-checkbox:checked');
  const btn = document.getElementById('exp-bulk-delete');
  const count = document.getElementById('exp-selected-count');
  if (btn && count) {
    btn.style.display = checked.length ? '' : 'none';
    count.textContent = `${checked.length} selected`;
  }
}

function bulkDeleteExperiences() {
  const checked = document.querySelectorAll('.exp-checkbox:checked');
  const ids = Array.from(checked).map(cb => parseInt(cb.value));
  if (!ids.length) return;
  AdminUI.confirm('Delete Experiences', `Delete ${ids.length} selected entries? This is permanent.`, async () => {
    try {
      await API.experiences.bulkDelete(ids);
      AdminAuth.logAction('Deleted', `${ids.length} experiences`);
      AdminUI.toast(`${ids.length} experiences deleted`);
      renderExperience(document.getElementById('admin-content'));
    } catch (err) {
      AdminUI.toast(err.message || 'Failed to delete', 'error');
    }
  });
}
