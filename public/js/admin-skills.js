/* ============================================
   ADMIN SKILLS — CRUD Manager (API-backed)
   ============================================ */

let _adminSkills = [];

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
      <div id="admin-skills-grid">${renderSkillsTable(_adminSkills)}</div>
    </div>`;
}

function renderSkillsTable(skills) {
  if (!skills.length) return '<div class="admin-empty"><div class="admin-empty__icon">🛠</div><h3 class="admin-empty__title">No skills yet</h3><p class="admin-empty__text">Add your technical skills.</p></div>';
  return `<table class="admin-table">
    <thead><tr><th>Skill</th><th>Category</th><th>Proficiency</th><th>Actions</th></tr></thead>
    <tbody>${skills.map((s, i) => `
        <tr data-category="${s.category}">
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

function openSkillModal(editIndex = null) {
  const isEdit = editIndex !== null;
  const skill = isEdit ? _adminSkills[editIndex] : {};
  const categories = [...new Set((_adminSkills || []).map(s => s.category))];
  const formHTML = `<div class="admin-form">
      <div class="form-group"><label class="form-label">Skill Name *</label><input type="text" class="form-input" id="skill-name" value="${AdminUI.escapeHtml(skill.name || '')}" /></div>
      <div class="admin-form__row"><div class="form-group"><label class="form-label">Category</label><select class="form-input" id="skill-category">${categories.map(c => `<option value="${c}" ${skill.category===c?'selected':''}>${c}</option>`).join('')}<option value="__new">+ New Category...</option></select></div><div class="form-group" id="skill-new-cat-wrap" style="display:none;"><label class="form-label">New Category</label><input type="text" class="form-input" id="skill-new-cat" placeholder="e.g., DevOps" /></div></div>
      <div class="form-group"><label class="form-label">Icon Key</label><input type="text" class="form-input" id="skill-icon" value="${AdminUI.escapeHtml(skill.icon || '')}" /></div>
      <div class="form-group"><label class="form-label">Short Description</label><input type="text" class="form-input" id="skill-short" value="${AdminUI.escapeHtml(skill.teaser || '')}" /></div>
      <div class="form-group"><label class="form-label">Full Description</label><textarea class="form-textarea" id="skill-desc" rows="3">${AdminUI.escapeHtml(skill.description || '')}</textarea></div>
      <div class="form-group"><label class="form-label">Proficiency: <strong id="skill-prof-label">${skill.proficiency||50}%</strong></label><input type="range" id="skill-proficiency" min="0" max="100" value="${skill.proficiency||50}" style="width:100%;accent-color:var(--accent);" /></div>
      <div class="form-group"><label class="form-label">Related Projects</label><div id="skill-projects-tags"></div></div>
      <div class="admin-form__actions"><button class="btn btn--primary" id="skill-save">${isEdit?'Update':'Add'} Skill</button><button class="btn btn--secondary" id="skill-cancel">Cancel</button></div>
    </div>`;
  const { modal, close } = AdminUI.openModal(isEdit ? 'Edit Skill' : 'Add Skill', formHTML);
  const catSelect = document.getElementById('skill-category');
  document.getElementById('skill-new-cat-wrap');
  catSelect.addEventListener('change', () => { document.getElementById('skill-new-cat-wrap').style.display = catSelect.value === '__new' ? '' : 'none'; });
  const slider = document.getElementById('skill-proficiency');
  const label = document.getElementById('skill-prof-label');
  slider.addEventListener('input', () => { label.textContent = slider.value + '%'; });
  AdminUI.createTagInput(document.getElementById('skill-projects-tags'), [...(skill.related_projects || [])]);

  document.getElementById('skill-save').addEventListener('click', async () => {
    const name = document.getElementById('skill-name').value.trim();
    if (!name) { AdminUI.toast('Skill name is required', 'error'); return; }
    let category = catSelect.value;
    if (category === '__new') { category = document.getElementById('skill-new-cat').value.trim(); if (!category) { AdminUI.toast('Enter a category name', 'error'); return; } }
    const projectTags = document.getElementById('skill-projects-tags');
    const relProjects = Array.from(projectTags.querySelectorAll('.admin-tag-input__tag')).map(t => t.textContent.replace('×','').trim());
    const payload = { name, category, icon: document.getElementById('skill-icon').value.trim(), teaser: document.getElementById('skill-short').value.trim(), description: document.getElementById('skill-desc').value.trim(), proficiency: parseInt(slider.value), related_projects: relProjects, gallery: skill.gallery || [] };
    const saveBtn = document.getElementById('skill-save');
    saveBtn.disabled = true; saveBtn.textContent = 'Saving...';
    try {
      if (isEdit) { await API.skills.update(skill.id, payload); } else { await API.skills.create(payload); }
      AdminAuth.logAction(isEdit ? 'Updated' : 'Created', `Skill: ${name}`);
      AdminUI.toast(`Skill ${isEdit ? 'updated' : 'added'}!`); close(); renderSkills(document.getElementById('admin-content'));
    } catch (err) { AdminUI.toast(err.message || 'Failed to save', 'error'); saveBtn.disabled = false; saveBtn.textContent = isEdit ? 'Update Skill' : 'Add Skill'; }
  });
  document.getElementById('skill-cancel').addEventListener('click', close);
}

function deleteSkill(index) {
  const skill = _adminSkills[index];
  AdminUI.confirm('Delete Skill', `Remove "${skill.name}"?`, async () => {
    try { await API.skills.delete(skill.id); AdminAuth.logAction('Deleted', `Skill: ${skill.name}`); AdminUI.toast('Skill deleted'); renderSkills(document.getElementById('admin-content')); }
    catch (err) { AdminUI.toast(err.message || 'Failed to delete', 'error'); }
  });
}
