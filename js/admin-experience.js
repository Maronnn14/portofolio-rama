/* ============================================
   ADMIN EXPERIENCE — CRUD Manager
   ============================================ */

function renderExperience(container) {
  const data = DataStore.getData();
  const entries = data.experience || [];

  container.innerHTML = `
    <div class="admin-section-card">
      <div class="admin-section-card__header">
        <h3 class="admin-section-card__title">Experience & Education</h3>
        <button class="btn btn--primary btn--sm" onclick="openExpModal()">+ Add Entry</button>
      </div>
      ${entries.length ? `
        <table class="admin-table">
          <thead>
            <tr>
              <th>Role</th>
              <th>Company</th>
              <th>Period</th>
              <th>Type</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            ${entries.map((e, i) => `
              <tr>
                <td style="color:var(--text-primary);font-weight:var(--fw-medium);">${AdminUI.escapeHtml(e.role)}</td>
                <td>${AdminUI.escapeHtml(e.company)}</td>
                <td style="white-space:nowrap;">${AdminUI.escapeHtml(e.startDate)} — ${AdminUI.escapeHtml(e.endDate)}</td>
                <td>${AdminUI.badge(e.type, e.type === 'Work' ? 'accent' : e.type === 'Education' ? 'success' : 'default')}</td>
                <td>
                  <div class="admin-table__actions">
                    <button class="admin-table__btn" onclick="openExpModal(${i})">✏️</button>
                    <button class="admin-table__btn admin-table__btn--danger" onclick="deleteExp(${i})">🗑️</button>
                  </div>
                </td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      ` : `
        <div class="admin-empty">
          <div class="admin-empty__icon">🕐</div>
          <h3 class="admin-empty__title">No experience entries</h3>
          <p class="admin-empty__text">Add your work and education history.</p>
        </div>
      `}
    </div>
  `;
}

function openExpModal(editIndex = null) {
  const data = DataStore.getData();
  const isEdit = editIndex !== null;
  const entry = isEdit ? data.experience[editIndex] : {};

  const formHTML = `
    <div class="admin-form">
      <div class="admin-form__row">
        <div class="form-group">
          <label class="form-label">Role / Title *</label>
          <input type="text" class="form-input" id="exp-role" value="${AdminUI.escapeHtml(entry.role || '')}" required />
        </div>
        <div class="form-group">
          <label class="form-label">Company / Institution *</label>
          <input type="text" class="form-input" id="exp-company" value="${AdminUI.escapeHtml(entry.company || '')}" required />
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Location</label>
        <input type="text" class="form-input" id="exp-location" value="${AdminUI.escapeHtml(entry.location || '')}" />
      </div>
      <div class="admin-form__row">
        <div class="form-group">
          <label class="form-label">Start Date *</label>
          <input type="text" class="form-input" id="exp-start" placeholder="e.g., Jan 2023" value="${AdminUI.escapeHtml(entry.startDate || '')}" />
        </div>
        <div class="form-group">
          <label class="form-label">End Date</label>
          <input type="text" class="form-input" id="exp-end" placeholder="e.g., Dec 2024 or Present" value="${AdminUI.escapeHtml(entry.endDate || '')}" />
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Type</label>
        <select class="form-input" id="exp-type">
          <option value="Work" ${entry.type === 'Work' ? 'selected' : ''}>Work</option>
          <option value="Education" ${entry.type === 'Education' ? 'selected' : ''}>Education</option>
          <option value="Freelance" ${entry.type === 'Freelance' ? 'selected' : ''}>Freelance</option>
          <option value="Volunteer" ${entry.type === 'Volunteer' ? 'selected' : ''}>Volunteer</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea class="form-textarea" id="exp-desc" rows="3">${AdminUI.escapeHtml(entry.description || '')}</textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Technologies</label>
        <div id="exp-tech-tags"></div>
      </div>
      <div class="admin-form__actions">
        <button class="btn btn--primary" id="exp-save">${isEdit ? 'Update' : 'Add'} Entry</button>
        <button class="btn btn--secondary" id="exp-cancel">Cancel</button>
      </div>
    </div>`;

  const { modal, close } = AdminUI.openModal(isEdit ? 'Edit Experience' : 'Add Experience', formHTML);

  // Init tag input
  AdminUI.createTagInput(document.getElementById('exp-tech-tags'), [...(entry.tech || [])]);

  // Save handler
  document.getElementById('exp-save').addEventListener('click', () => {
    const role = document.getElementById('exp-role').value.trim();
    const company = document.getElementById('exp-company').value.trim();
    if (!role || !company) { AdminUI.toast('Role and Company are required', 'error'); return; }

    const techTags = document.getElementById('exp-tech-tags');
    const tags = Array.from(techTags.querySelectorAll('.admin-tag-input__tag')).map(t => t.textContent.replace('×', '').trim());

    const newEntry = {
      role, company,
      location: document.getElementById('exp-location').value.trim(),
      startDate: document.getElementById('exp-start').value.trim(),
      endDate: document.getElementById('exp-end').value.trim() || 'Present',
      type: document.getElementById('exp-type').value,
      description: document.getElementById('exp-desc').value.trim(),
      tech: tags,
    };

    const d = DataStore.getData();
    if (isEdit) { d.experience[editIndex] = newEntry; } else { d.experience.unshift(newEntry); }
    DataStore.saveAll(d);
    AdminAuth.logAction(isEdit ? 'Updated' : 'Created', `Experience: ${role}`);
    AdminUI.toast(`Experience ${isEdit ? 'updated' : 'added'}!`);
    close();
    renderExperience(document.getElementById('admin-content'));
  });

  document.getElementById('exp-cancel').addEventListener('click', close);
}

function deleteExp(index) {
  const data = DataStore.getData();
  const entry = data.experience[index];
  AdminUI.confirm('Delete Experience', `Remove "${entry.role} at ${entry.company}"? This cannot be undone.`, () => {
    data.experience.splice(index, 1);
    DataStore.saveAll(data);
    AdminAuth.logAction('Deleted', `Experience: ${entry.role}`);
    AdminUI.toast('Experience deleted');
    renderExperience(document.getElementById('admin-content'));
  });
}
