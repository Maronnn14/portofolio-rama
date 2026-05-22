/* ============================================
   ADMIN SKILLS — CRUD Manager (API-backed)
   Includes Related Project Links & Gallery sub-sections
   ============================================ */

let _adminSkills = [];
let _linkDraft = [];
let _galleryDraft = [];

async function renderSkills(container) {
  container.innerHTML = '<div class="admin-loading"><span class="spinner"></span> Loading skills...</div>';
  try {
    _adminSkills = await API.skills.list();
  } catch (err) {
    container.innerHTML = '<div class="admin-empty"><div class="admin-empty__icon">⚠️</div><h3 class="admin-empty__title">Failed to load skills</h3></div>';
    return;
  }
  const categories = [...new Set(_adminSkills.map(s => s.category))];
  container.innerHTML = `
    <div class="admin-section-card">
      <div class="admin-section-card__header">
        <h3 class="admin-section-card__title">Skills (${_adminSkills.length})</h3>
        <button class="btn btn--primary btn--sm" onclick="openSkillModal()">+ Add Skill</button>
      </div>
      <div style="display:flex;gap:var(--space-sm);margin-bottom:var(--space-xl);flex-wrap:wrap;">
        <button class="filter-tab active" onclick="filterAdminSkills('all', this)">All</button>
        ${categories.map(c => `<button class="filter-tab" onclick="filterAdminSkills('${c}', this)">${c}</button>`).join('')}
      </div>
      <div style="display:${_adminSkills.length ? 'flex' : 'none'};gap:var(--space-md);margin-bottom:var(--space-md);align-items:center;" id="skill-bulk-bar">
        <span style="font-size:var(--fs-xs);color:var(--text-muted);" id="skill-selected-count">0 selected</span>
        <button class="btn btn--sm btn--danger" id="skill-bulk-delete" style="display:none;" onclick="bulkDeleteSkills()">Delete Selected</button>
      </div>
      <div id="admin-skills-grid">${renderSkillsTable(_adminSkills)}</div>
    </div>`;
}

function renderSkillsTable(skills) {
  if (!skills.length) return '<div class="admin-empty"><div class="admin-empty__icon">🛠</div><h3 class="admin-empty__title">No skills yet</h3><p class="admin-empty__text">Add your technical skills.</p></div>';
  return `<table class="admin-table">
    <thead><tr><th style="width:30px;"><input type="checkbox" id="skill-select-all" onchange="toggleAllSkills(this.checked)" /></th><th>Skill</th><th>Category</th><th>Proficiency</th><th>Actions</th></tr></thead>
    <tbody>${skills.map((s, i) => `
        <tr data-category="${s.category}" data-id="${s.id}">
          <td><input type="checkbox" class="skill-checkbox" value="${s.id}" onchange="updateSkillBulkBtn()" /></td>
          <td style="color:var(--text-primary);font-weight:var(--fw-medium);">${AdminUI.escapeHtml(s.name)}</td>
          <td>${AdminUI.badge(s.category, 'accent')}</td>
          <td><div style="display:flex;align-items:center;gap:var(--space-sm);"><div style="flex:1;height:6px;background:var(--bg-tertiary);border-radius:3px;max-width:100px;"><div style="width:${s.proficiency||0}%;height:100%;background:var(--accent);border-radius:3px;"></div></div><span style="font-size:var(--fs-xs);color:var(--text-muted);">${s.proficiency||0}%</span></div></td>
          <td><div class="admin-table__actions"><button class="admin-table__btn" onclick="openSkillModal(${i})">✏️</button><button class="admin-table__btn admin-table__btn--danger" onclick="deleteSkill(${i})">🗑️</button></div></td>
        </tr>`).join('')}</tbody></table>`;
}

function filterAdminSkills(cat, btn) {
  btn.parentElement.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('#admin-skills-grid tr[data-category]').forEach(row => {
    row.style.display = (cat === 'all' || row.dataset.category === cat) ? '' : 'none';
  });
}

/* ---- Link row rendering helpers ---- */

function renderLinkRow(link, idx) {
  return `<div class="skill-link-row" data-link-idx="${idx}">
    <div class="skill-link-row__move">
      <button type="button" class="skill-link-row__arrow" onclick="moveLink(${idx}, -1)" title="Move up">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>
      </button>
      <button type="button" class="skill-link-row__arrow" onclick="moveLink(${idx}, 1)" title="Move down">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
      </button>
    </div>
    <div class="skill-link-row__fields">
      <input type="text" class="form-input skill-link-row__input" data-field="label" value="${AdminUI.escapeHtml(link.label||'')}" placeholder="Label (e.g., E-Commerce App)" />
      <input type="url" class="form-input skill-link-row__input" data-field="url" value="${AdminUI.escapeHtml(link.url||'')}" placeholder="URL" />
      <input type="text" class="form-input skill-link-row__input skill-link-row__input--desc" data-field="description" value="${AdminUI.escapeHtml(link.description||'')}" placeholder="Short description (optional)" />
    </div>
    <button type="button" class="skill-link-row__delete-btn" onclick="removeLink(${idx})" title="Delete Link">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
    </button>
  </div>`;
}

function moveLink(idx, dir) {
  syncLinkDraftFromDOM();
  const newIdx = idx + dir;
  if (newIdx < 0 || newIdx >= _linkDraft.length) return;
  [_linkDraft[idx], _linkDraft[newIdx]] = [_linkDraft[newIdx], _linkDraft[idx]];
  renderLinksUI();
}

function removeLink(idx) {
  syncLinkDraftFromDOM();
  _linkDraft.splice(idx, 1);
  renderLinksUI();
}

function addLink() {
  syncLinkDraftFromDOM();
  _linkDraft.push({ label: '', url: '', description: '' });
  renderLinksUI();
}

function renderLinksUI() {
  const container = document.getElementById('skill-links-container');
  if (!container) return;
  container.innerHTML = _linkDraft.length
    ? _linkDraft.map((l, i) => renderLinkRow(l, i)).join('')
    : '<p class="form-hint" style="padding:var(--space-md) 0;text-align:center;">No related project links yet. Click "Add Link" above.</p>';
}

function syncLinkDraftFromDOM() {
  const rows = document.querySelectorAll('#skill-links-container .skill-link-row');
  _linkDraft = Array.from(rows).map(row => ({
    label: row.querySelector('[data-field="label"]').value.trim(),
    url: row.querySelector('[data-field="url"]').value.trim(),
    description: row.querySelector('[data-field="description"]').value.trim(),
  }));
}

/* ---- Gallery helpers ---- */

function renderGalleryUploadWrapper() {
  const wrapper = document.getElementById('skill-gallery-upload-wrapper');
  if (!wrapper) return;

  if (!_editingSkillId) {
    wrapper.innerHTML = `
      <div class="skill-gallery-locked-card">
        <div class="skill-gallery-locked-card__icon">🔒</div>
        <div class="skill-gallery-locked-card__title">Gallery is locked</div>
        <div class="skill-gallery-locked-card__text">Save this skill first to unlock image uploads! Once saved, you can add beautiful showcase screenshots here.</div>
      </div>`;
    return;
  }

  wrapper.innerHTML = `
    <div class="skill-gallery-upload-zone" id="skill-gallery-drag-zone">
      <div class="skill-gallery-upload-zone__icon">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
      </div>
      <div class="skill-gallery-upload-zone__title">Drag & drop image here, or <span style="color:var(--accent);font-weight:var(--fw-medium);">browse files</span></div>
      <div class="skill-gallery-upload-zone__subtitle">Supports JPG, PNG, WebP, GIF • Max 2MB</div>
      <div id="skill-gallery-upload-progress" style="margin-top:var(--space-xs);width:100%;"></div>
    </div>`;

  const zone = document.getElementById('skill-gallery-drag-zone');
  const fileInput = document.getElementById('skill-gallery-file-input');

  zone.addEventListener('click', () => fileInput.click());

  zone.addEventListener('dragover', (e) => {
    e.preventDefault();
    zone.classList.add('dragover');
  });

  zone.addEventListener('dragleave', () => {
    zone.classList.remove('dragover');
  });

  zone.addEventListener('drop', (e) => {
    e.preventDefault();
    zone.classList.remove('dragover');
    if (e.dataTransfer.files.length) {
      handleGalleryUpload(e.dataTransfer.files[0]);
    }
  });
}

async function handleGalleryUpload(file) {
  if (!file) return;
  if (file.size > 2 * 1024 * 1024) {
    AdminUI.toast('Image too large. Maximum 2MB.', 'error');
    return;
  }

  const progress = document.getElementById('skill-gallery-upload-progress');
  if (progress) {
    progress.innerHTML = '<span class="spinner" style="display:inline-block;width:12px;height:12px;margin-right:var(--space-xs);"></span> Uploading image...';
  }

  try {
    if (!_editingSkillId) {
      AdminUI.toast('Save the skill first, then upload images.', 'warning');
      return;
    }

    // Sync captions first so we don't lose typed text
    syncGalleryDraftFromDOM();

    const result = await API.skills.uploadGalleryImage(_editingSkillId, file);
    _galleryDraft.push({
      id: result.id,
      image_path: result.image_path,
      image_url: result.image_url,
      caption: '',
      sort_order: _galleryDraft.length,
    });

    renderGalleryUI();
    if (progress) {
      progress.innerHTML = '<span style="color:var(--success);font-size:var(--fs-xs);font-weight:var(--fw-medium);">✨ Upload complete</span>';
      setTimeout(() => progress.innerHTML = '', 2000);
    }
  } catch (err) {
    if (progress) {
      progress.innerHTML = `<span style="color:var(--error);font-size:var(--fs-xs);font-weight:var(--fw-medium);">❌ ${err.message || 'Upload failed'}</span>`;
    }
  }
}

function renderGalleryUI() {
  const container = document.getElementById('skill-gallery-container');
  if (!container) return;
  if (!_galleryDraft.length) {
    container.innerHTML = '<p class="form-hint" style="padding:var(--space-md) 0;text-align:center;width:100%;">No gallery images yet. Upload images above.</p>';
    return;
  }

  container.innerHTML = `
    <div class="skill-gallery-grid">
      ${_galleryDraft.map((item, i) => `
        <div class="skill-gallery-item" data-gallery-idx="${i}">
          <div class="skill-gallery-item__img-wrap">
            <img src="${item.image_url || item.image_path}" alt="${item.caption||''}" />
            <div class="skill-gallery-item__overlay">
              <button type="button" class="skill-gallery-item__delete" onclick="deleteGalleryItem(${i})" title="Delete image">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
              </button>
              <div class="skill-gallery-item__overlay-controls">
                <div class="skill-gallery-item__overlay-move">
                  <button type="button" class="skill-gallery-item__arrow" onclick="moveGalleryItem(${i}, -1)" title="Move left">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                  </button>
                  <button type="button" class="skill-gallery-item__arrow" onclick="moveGalleryItem(${i}, 1)" title="Move right">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="skill-gallery-item__caption-wrap">
            <input type="text" class="form-input skill-gallery-item__caption" value="${AdminUI.escapeHtml(item.caption||'')}" placeholder="Caption (optional)" data-gallery-id="${item.id||''}" />
          </div>
        </div>
      `).join('')}
    </div>`;
}

function moveGalleryItem(idx, dir) {
  syncGalleryDraftFromDOM();
  const newIdx = idx + dir;
  if (newIdx < 0 || newIdx >= _galleryDraft.length) return;
  [_galleryDraft[idx], _galleryDraft[newIdx]] = [_galleryDraft[newIdx], _galleryDraft[idx]];
  renderGalleryUI();
}

function deleteGalleryItem(idx) {
  syncGalleryDraftFromDOM();
  const item = _galleryDraft[idx];
  if (item.id) {
    AdminUI.confirm('Delete Image', 'Remove this image? The file will be permanently deleted.', async () => {
      try {
        await API.skills.deleteGalleryItem(_editingSkillId, item.id);
        _galleryDraft.splice(idx, 1);
        renderGalleryUI();
        AdminUI.toast('Image deleted');
      } catch (err) {
        AdminUI.toast(err.message || 'Failed to delete', 'error');
      }
    });
  } else {
    _galleryDraft.splice(idx, 1);
    renderGalleryUI();
  }
}

function syncGalleryDraftFromDOM() {
  const items = document.querySelectorAll('#skill-gallery-container .skill-gallery-item');
  _galleryDraft = Array.from(items).map((el, i) => {
    const existing = _galleryDraft[i] || {};
    return {
      id: existing.id || null,
      image_path: existing.image_path || '',
      image_url: existing.image_url || existing.image_path || '',
      caption: el.querySelector('.skill-gallery-item__caption')?.value?.trim() || '',
      sort_order: i,
    };
  });
}


/* ---- Main skill modal ---- */

let _editingSkillId = null;

function openSkillModal(editIndex = null) {
  const isEdit = editIndex !== null;
  const skill = isEdit ? _adminSkills[editIndex] : {};
  _editingSkillId = isEdit ? skill.id : null;
  _linkDraft = isEdit ? (skill.project_links || []).map(l => ({ ...l })) : [];
  _galleryDraft = isEdit ? (skill.gallery_items || []).map(g => ({ ...g })) : [];

  const categories = [...new Set((_adminSkills || []).map(s => s.category))];
  const formHTML = `<div class="admin-form" style="max-width:800px;">
      <div class="form-group"><label class="form-label">Skill Name *</label><input type="text" class="form-input" id="skill-name" value="${AdminUI.escapeHtml(skill.name || '')}" /></div>
      <div class="admin-form__row"><div class="form-group"><label class="form-label">Category</label><select class="form-input" id="skill-category">${categories.map(c => `<option value="${c}" ${skill.category===c?'selected':''}>${c}</option>`).join('')}<option value="__new">+ New Category...</option></select></div><div class="form-group" id="skill-new-cat-wrap" style="display:none;"><label class="form-label">New Category</label><input type="text" class="form-input" id="skill-new-cat" placeholder="e.g., DevOps" /></div></div>
      <div class="form-group"><label class="form-label">Icon Key</label><input type="text" class="form-input" id="skill-icon" value="${AdminUI.escapeHtml(skill.icon || '')}" /></div>
      <div class="form-group"><label class="form-label">Short Description</label><input type="text" class="form-input" id="skill-short" value="${AdminUI.escapeHtml(skill.teaser || '')}" /></div>
      <div class="form-group"><label class="form-label">Full Description</label><textarea class="form-textarea" id="skill-desc" rows="3">${AdminUI.escapeHtml(skill.description || '')}</textarea></div>
      <div class="form-group"><label class="form-label">Proficiency: <strong id="skill-prof-label">${skill.proficiency||50}%</strong></label><input type="range" id="skill-proficiency" min="0" max="100" value="${skill.proficiency||50}" style="width:100%;accent-color:var(--accent);" /></div>

      <!-- Related Project Links -->
      <div class="skill-subsection">
        <div class="skill-subsection__header">
          <h4 class="skill-subsection__title">Related Project Links</h4>
          <button type="button" class="btn btn--sm btn--primary" onclick="addLink()">+ Add Link</button>
        </div>
        <div id="skill-links-container"></div>
      </div>

      <!-- Gallery -->
      <div class="skill-subsection">
        <div class="skill-subsection__header">
          <h4 class="skill-subsection__title">Gallery</h4>
        </div>
        
        <!-- Drag & Drop Zone / Locked Card -->
        <div id="skill-gallery-upload-wrapper"></div>
        
        <!-- Image Grid -->
        <div id="skill-gallery-container"></div>
        
        <input type="file" id="skill-gallery-file-input" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none;" />
      </div>

      <div class="admin-form__actions"><button class="btn btn--primary" id="skill-save">${isEdit?'Update':'Add'} Skill</button><button class="btn btn--secondary" id="skill-cancel">Cancel</button></div>
    </div>`;
  const { modal, close } = AdminUI.openModal(isEdit ? 'Edit Skill' : 'Add Skill', formHTML, { maxWidth: '800px' });

  // Render sub-sections
  renderGalleryUploadWrapper();
  renderLinksUI();
  renderGalleryUI();

  const catSelect = document.getElementById('skill-category');
  catSelect.addEventListener('change', () => { document.getElementById('skill-new-cat-wrap').style.display = catSelect.value === '__new' ? '' : 'none'; });
  const slider = document.getElementById('skill-proficiency');
  const label = document.getElementById('skill-prof-label');
  slider.addEventListener('input', () => { label.textContent = slider.value + '%'; });

  // Gallery file input listener
  document.getElementById('skill-gallery-file-input').addEventListener('change', (e) => {
    handleGalleryUpload(e.target.files[0]);
    e.target.value = '';
  });

  document.getElementById('skill-save').addEventListener('click', async () => {
    syncLinkDraftFromDOM();
    syncGalleryDraftFromDOM();

    const name = document.getElementById('skill-name').value.trim();
    if (!name) { AdminUI.toast('Skill name is required', 'error'); return; }
    let category = catSelect.value;
    if (category === '__new') { category = document.getElementById('skill-new-cat').value.trim(); if (!category) { AdminUI.toast('Enter a category name', 'error'); return; } }

    const payload = {
      name,
      category,
      icon: document.getElementById('skill-icon').value.trim(),
      teaser: document.getElementById('skill-short').value.trim(),
      description: document.getElementById('skill-desc').value.trim(),
      proficiency: parseInt(slider.value),
    };

    const saveBtn = document.getElementById('skill-save');
    saveBtn.disabled = true; saveBtn.textContent = 'Saving...';
    try {
      let skillResult;
      if (isEdit) {
        skillResult = await API.skills.update(skill.id, payload);
      } else {
        skillResult = await API.skills.create(payload);
      }
      const savedSkillId = skillResult.id;

      // Save links
      const links = _linkDraft.map((l, i) => ({
        label: l.label,
        url: l.url,
        description: l.description,
        sort_order: i,
      }));
      await API.skills.syncLinks(savedSkillId, links);

      // Save gallery captions & sort_order
      const galleryItems = _galleryDraft.map((item, i) => ({
        id: item.id,
        caption: item.caption || '',
        sort_order: i,
      })).filter(item => item.id);
      if (galleryItems.length) {
        await API.skills.updateGallery(savedSkillId, galleryItems);
      }

      AdminAuth.logAction(isEdit ? 'Updated' : 'Created', `Skill: ${name}`);
      AdminUI.toast(`Skill ${isEdit ? 'updated' : 'added'}!`);
      close();
      renderSkills(document.getElementById('admin-content'));
    } catch (err) {
      AdminUI.toast(err.message || 'Failed to save', 'error');
      saveBtn.disabled = false;
      saveBtn.textContent = isEdit ? 'Update Skill' : 'Add Skill';
    }
  });
  document.getElementById('skill-cancel').addEventListener('click', close);
}

function deleteSkill(index) {
  const skill = _adminSkills[index];
  AdminUI.confirm('Delete Skill', `Remove "${skill.name}"? All associated project links and gallery images will be permanently deleted.`, async () => {
    try {
      await API.skills.delete(skill.id);
      AdminAuth.logAction('Deleted', `Skill: ${skill.name}`);
      AdminUI.toast('Skill deleted');
      renderSkills(document.getElementById('admin-content'));
    } catch (err) {
      AdminUI.toast(err.message || 'Failed to delete', 'error');
    }
  });
}

function toggleAllSkills(checked) {
  document.querySelectorAll('.skill-checkbox').forEach(cb => cb.checked = checked);
  updateSkillBulkBtn();
}

function updateSkillBulkBtn() {
  const checked = document.querySelectorAll('.skill-checkbox:checked');
  const btn = document.getElementById('skill-bulk-delete');
  const count = document.getElementById('skill-selected-count');
  if (btn && count) {
    btn.style.display = checked.length ? '' : 'none';
    count.textContent = `${checked.length} selected`;
  }
}

function bulkDeleteSkills() {
  const checked = document.querySelectorAll('.skill-checkbox:checked');
  const ids = Array.from(checked).map(cb => parseInt(cb.value));
  if (!ids.length) return;
  AdminUI.confirm('Delete Skills', `Delete ${ids.length} selected skills? All associated project links and gallery images will be permanently deleted.`, async () => {
    try {
      await API.skills.bulkDelete(ids);
      AdminAuth.logAction('Deleted', `${ids.length} skills`);
      AdminUI.toast(`${ids.length} skills deleted`);
      renderSkills(document.getElementById('admin-content'));
    } catch (err) {
      AdminUI.toast(err.message || 'Failed to delete', 'error');
    }
  });
}
