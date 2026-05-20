/* ============================================
   ADMIN PROJECTS — CRUD Manager
   ============================================ */

function renderProjects(container) {
  const data = DataStore.getData();
  const projects = data.projects || [];

  container.innerHTML = `
    <div class="admin-section-card">
      <div class="admin-section-card__header">
        <h3 class="admin-section-card__title">Projects (${projects.length})</h3>
        <div style="display:flex;gap:var(--space-md);align-items:center;">
          <input type="text" class="form-input" placeholder="Search projects..." id="project-search"
                 style="width:200px;padding:var(--space-sm) var(--space-md);" />
          <button class="btn btn--primary btn--sm" onclick="openProjectModal()">+ Add Project</button>
        </div>
      </div>
      <div id="admin-projects-table">
        ${renderProjectsTable(projects)}
      </div>
    </div>
  `;

  // Bind search
  const search = document.getElementById('project-search');
  if (search) {
    search.addEventListener('input', () => {
      const q = search.value.toLowerCase();
      const rows = document.querySelectorAll('#admin-projects-table tr[data-name]');
      rows.forEach(r => { r.style.display = r.dataset.name.toLowerCase().includes(q) ? '' : 'none'; });
    });
  }
}

function renderProjectsTable(projects) {
  if (!projects.length) return '<div class="admin-empty"><div class="admin-empty__icon">📁</div><h3 class="admin-empty__title">No projects</h3><p class="admin-empty__text">Add your first project.</p></div>';

  return `<div class="admin-table-responsive">
    <table class="admin-table">
      <thead><tr><th style="width:50px;"></th><th>Name</th><th>Category</th><th>Status</th><th>Featured</th><th>Actions</th></tr></thead>
      <tbody>
        ${projects.map((p, i) => `
          <tr data-name="${AdminUI.escapeHtml(p.name)}">
            <td><img src="${p.thumbnail}" alt="" style="width:40px;height:30px;object-fit:cover;border-radius:4px;background:var(--bg-tertiary);" onerror="this.style.display='none'" /></td>
            <td style="color:var(--text-primary);font-weight:var(--fw-medium);">${AdminUI.escapeHtml(p.name)}</td>
            <td>${AdminUI.badge(p.category || 'Uncategorized', 'accent')}</td>
            <td>${AdminUI.badge(p.status === 'draft' ? 'Draft' : 'Published', p.status === 'draft' ? 'warning' : 'success')}</td>
            <td>${p.featured ? '⭐' : '—'}</td>
            <td>
              <div class="admin-table__actions">
                <button class="admin-table__btn" onclick="openProjectModal(${i})">✏️</button>
                <button class="admin-table__btn admin-table__btn--danger" onclick="deleteProject(${i})">🗑️</button>
              </div>
            </td>
          </tr>
        `).join('')}
      </tbody>
    </table>
  </div>`;
}

function openProjectModal(editIndex = null) {
  const data = DataStore.getData();
  const isEdit = editIndex !== null;
  const proj = isEdit ? data.projects[editIndex] : {};

  let thumbBase64 = proj.thumbnail || '';

  const formHTML = `
    <div class="admin-form" style="max-width:100%;">
      <div class="form-group">
        <label class="form-label">Project Name *</label>
        <input type="text" class="form-input" id="proj-name" value="${AdminUI.escapeHtml(proj.name || '')}" />
      </div>
      <div class="form-group">
        <label class="form-label">Short Description *</label>
        <input type="text" class="form-input" id="proj-short" value="${AdminUI.escapeHtml(proj.shortDesc || '')}" />
      </div>
      <div class="form-group">
        <label class="form-label">Full Description</label>
        <textarea class="form-textarea" id="proj-desc" rows="4">${AdminUI.escapeHtml(proj.description || '')}</textarea>
      </div>
      <div class="admin-form__row">
        <div class="form-group">
          <label class="form-label">Category</label>
          <select class="form-input" id="proj-category">
            <option value="Frontend" ${proj.category === 'Frontend' ? 'selected' : ''}>Frontend</option>
            <option value="Backend" ${proj.category === 'Backend' ? 'selected' : ''}>Backend</option>
            <option value="Full Stack" ${proj.category === 'Full Stack' ? 'selected' : ''}>Full Stack</option>
            <option value="Design" ${proj.category === 'Design' ? 'selected' : ''}>Design</option>
            <option value="Mobile" ${proj.category === 'Mobile' ? 'selected' : ''}>Mobile</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select class="form-input" id="proj-status">
            <option value="published" ${proj.status !== 'draft' ? 'selected' : ''}>Published</option>
            <option value="draft" ${proj.status === 'draft' ? 'selected' : ''}>Draft</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Technologies</label>
        <div id="proj-tech-tags"></div>
      </div>
      <div class="admin-form__row">
        <div class="form-group">
          <label class="form-label">Live Demo URL</label>
          <input type="url" class="form-input" id="proj-demo" value="${AdminUI.escapeHtml(proj.liveUrl || '')}" placeholder="https://..." />
        </div>
        <div class="form-group">
          <label class="form-label">Source Code URL</label>
          <input type="url" class="form-input" id="proj-repo" value="${AdminUI.escapeHtml(proj.repoUrl || '')}" placeholder="https://github.com/..." />
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Thumbnail</label>
        <div id="proj-thumb-upload">${AdminUI.createImageUpload(thumbBase64)}</div>
      </div>
      <div class="form-group" style="display:flex;align-items:center;gap:var(--space-xl);">
        <label style="display:flex;align-items:center;gap:var(--space-sm);cursor:pointer;">
          <input type="checkbox" id="proj-featured" ${proj.featured ? 'checked' : ''} />
          <span class="form-label" style="margin:0;">⭐ Featured Project</span>
        </label>
      </div>
      <div class="admin-form__actions">
        <button class="btn btn--primary" id="proj-save">${isEdit ? 'Update' : 'Add'} Project</button>
        <button class="btn btn--secondary" id="proj-cancel">Cancel</button>
      </div>
    </div>`;

  const { modal, close } = AdminUI.openModal(isEdit ? 'Edit Project' : 'Add Project', formHTML, { maxWidth: '700px' });

  // Init tech tags
  AdminUI.createTagInput(document.getElementById('proj-tech-tags'), [...(proj.tech || [])]);

  // Bind image upload
  const uploadZone = modal.querySelector('.admin-upload-zone');
  if (uploadZone) {
    AdminUI.bindImageUpload(uploadZone.id, (base64) => { thumbBase64 = base64; });
  }

  // Save
  document.getElementById('proj-save').addEventListener('click', () => {
    const name = document.getElementById('proj-name').value.trim();
    const shortDesc = document.getElementById('proj-short').value.trim();
    if (!name || !shortDesc) { AdminUI.toast('Name and description required', 'error'); return; }

    const techEl = document.getElementById('proj-tech-tags');
    const tech = Array.from(techEl.querySelectorAll('.admin-tag-input__tag')).map(t => t.textContent.replace('×','').trim());

    const newProj = {
      id: proj.id || AdminUI.generateId('proj'),
      name, shortDesc,
      description: document.getElementById('proj-desc').value.trim(),
      category: document.getElementById('proj-category').value,
      status: document.getElementById('proj-status').value,
      tech,
      thumbnail: thumbBase64 || proj.thumbnail || 'https://picsum.photos/seed/' + Date.now() + '/800/500',
      liveUrl: document.getElementById('proj-demo').value.trim(),
      repoUrl: document.getElementById('proj-repo').value.trim(),
      featured: document.getElementById('proj-featured').checked,
      gallery: proj.gallery || [],
    };

    const d = DataStore.getData();
    if (isEdit) { d.projects[editIndex] = newProj; } else { d.projects.unshift(newProj); }
    DataStore.saveAll(d);
    AdminAuth.logAction(isEdit ? 'Updated' : 'Created', `Project: ${name}`);
    AdminUI.toast(`Project ${isEdit ? 'updated' : 'added'}!`);
    close();
    renderProjects(document.getElementById('admin-content'));
  });

  document.getElementById('proj-cancel').addEventListener('click', close);
}

function deleteProject(index) {
  const data = DataStore.getData();
  const proj = data.projects[index];
  AdminUI.confirm('Delete Project', `Delete "${proj.name}"? This is permanent.`, () => {
    data.projects.splice(index, 1);
    DataStore.saveAll(data);
    AdminAuth.logAction('Deleted', `Project: ${proj.name}`);
    AdminUI.toast('Project deleted');
    renderProjects(document.getElementById('admin-content'));
  });
}
