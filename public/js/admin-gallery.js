/* ============================================
   ADMIN GALLERY — Image Manager
   ============================================ */

function renderGallery(container) {
  const data = DataStore.getData();
  const gallery = data.gallery || [];

  container.innerHTML = `
    <div class="admin-section-card">
      <div class="admin-section-card__header">
        <h3 class="admin-section-card__title">Gallery (${gallery.length} images)</h3>
        <div style="display:flex;gap:var(--space-md);">
          <button class="btn btn--secondary btn--sm" onclick="bulkDeleteGallery()" id="gallery-bulk-delete" style="display:none;">Delete Selected</button>
          <button class="btn btn--primary btn--sm" onclick="openGalleryUpload()">+ Upload Photos</button>
        </div>
      </div>

      ${gallery.length ? `
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:var(--space-md);" id="admin-gallery-grid">
          ${gallery.map((img, i) => `
            <div style="position:relative;border-radius:var(--radius-md);overflow:hidden;border:1px solid var(--border-subtle);transition:border-color 0.2s;" class="gallery-admin-item" data-idx="${i}">
              <img src="${img.url || img}" alt="${img.alt || ''}" style="width:100%;aspect-ratio:1;object-fit:cover;display:block;" onerror="this.src='https://picsum.photos/200'" />
              <div style="position:absolute;inset:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;gap:var(--space-sm);opacity:0;transition:opacity 0.2s;" 
                   onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0">
                <input type="checkbox" class="gallery-checkbox" value="${i}" style="position:absolute;top:8px;left:8px;" onchange="updateGalleryBulk()" />
                <button class="admin-table__btn" style="background:var(--bg-secondary);" onclick="editGalleryItem(${i})">✏️</button>
                <button class="admin-table__btn admin-table__btn--danger" style="background:var(--bg-secondary);" onclick="deleteGalleryItem(${i})">🗑️</button>
              </div>
              ${img.visible === false ? '<div style="position:absolute;top:8px;right:8px;background:var(--bg-primary);padding:2px 6px;border-radius:4px;font-size:0.65rem;color:var(--text-muted);">Hidden</div>' : ''}
            </div>
          `).join('')}
        </div>
      ` : `
        <div class="admin-empty">
          <div class="admin-empty__icon">🖼</div>
          <h3 class="admin-empty__title">No gallery images</h3>
          <p class="admin-empty__text">Upload photos to your gallery.</p>
          <button class="btn btn--primary" onclick="openGalleryUpload()">Upload Photos</button>
        </div>
      `}
    </div>
  `;
}

function openGalleryUpload() {
  const formHTML = `
    <div class="admin-form">
      <div class="form-group">
        <label class="form-label">Upload Images</label>
        <div class="admin-upload-zone" id="gallery-upload-zone" onclick="document.getElementById('gallery-file-input').click()">
          <div class="admin-upload-zone__icon">📁</div>
          <div class="admin-upload-zone__text">
            Click or drag images here<br>
            <small style="color:var(--text-muted);">JPG, PNG, WebP, GIF • Max 10MB each • Multiple files</small>
          </div>
          <input type="file" id="gallery-file-input" accept="image/*" multiple style="display:none;" />
        </div>
      </div>
      <div id="gallery-upload-preview" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:var(--space-sm);margin-bottom:var(--space-lg);"></div>
      <div class="form-group">
        <label class="form-label">Or add image URLs (one per line)</label>
        <textarea class="form-textarea" id="gallery-urls" rows="3" placeholder="https://example.com/image1.jpg&#10;https://example.com/image2.jpg"></textarea>
      </div>
      <div class="admin-form__actions">
        <button class="btn btn--primary" id="gallery-upload-save">Add to Gallery</button>
        <button class="btn btn--secondary" id="gallery-upload-cancel">Cancel</button>
      </div>
    </div>`;

  const { modal, close } = AdminUI.openModal('Upload Photos', formHTML);

  let uploadedFiles = [];

  // File input handler
  const fileInput = document.getElementById('gallery-file-input');
  const preview = document.getElementById('gallery-upload-preview');

  fileInput.addEventListener('change', (e) => {
    Array.from(e.target.files).forEach(file => {
      if (file.size > 10 * 1024 * 1024) { AdminUI.toast(`${file.name} is too large (max 10MB)`, 'error'); return; }
      const reader = new FileReader();
      reader.onload = (ev) => {
        uploadedFiles.push({ url: ev.target.result, alt: file.name, visible: true });
        preview.innerHTML += `<img src="${ev.target.result}" style="width:100%;aspect-ratio:1;object-fit:cover;border-radius:4px;" />`;
      };
      reader.readAsDataURL(file);
    });
  });

  // Drag and drop
  const zone = document.getElementById('gallery-upload-zone');
  zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
  zone.addEventListener('drop', e => {
    e.preventDefault(); zone.classList.remove('dragover');
    if (e.dataTransfer.files.length) { fileInput.files = e.dataTransfer.files; fileInput.dispatchEvent(new Event('change')); }
  });

  // Save
  document.getElementById('gallery-upload-save').addEventListener('click', () => {
    const data = DataStore.getData();
    if (!data.gallery) data.gallery = [];

    // Add uploaded files
    data.gallery.push(...uploadedFiles);

    // Add URLs
    const urls = document.getElementById('gallery-urls').value.trim();
    if (urls) {
      urls.split('\n').filter(u => u.trim()).forEach(url => {
        data.gallery.push({ url: url.trim(), alt: '', visible: true });
      });
    }

    if (uploadedFiles.length === 0 && !urls.trim()) { AdminUI.toast('No images to add', 'warning'); return; }

    DataStore.saveAll(data);
    const count = uploadedFiles.length + (urls ? urls.split('\n').filter(u => u.trim()).length : 0);
    AdminAuth.logAction('Uploaded', `${count} gallery image(s)`);
    AdminUI.toast(`${count} image(s) added to gallery!`);
    close();
    renderGallery(document.getElementById('admin-content'));
  });

  document.getElementById('gallery-upload-cancel').addEventListener('click', close);
}

function editGalleryItem(index) {
  const data = DataStore.getData();
  const img = data.gallery[index];
  const imgObj = typeof img === 'string' ? { url: img, alt: '', visible: true } : img;

  const formHTML = `
    <div class="admin-form">
      <div style="text-align:center;margin-bottom:var(--space-xl);">
        <img src="${imgObj.url}" alt="" style="max-width:300px;max-height:200px;border-radius:8px;" />
      </div>
      <div class="form-group">
        <label class="form-label">Alt Text / Description</label>
        <input type="text" class="form-input" id="gallery-edit-alt" value="${AdminUI.escapeHtml(imgObj.alt || '')}" placeholder="Describe this image..." />
      </div>
      <div class="form-group">
        <label class="form-label">Category Tag</label>
        <input type="text" class="form-input" id="gallery-edit-cat" value="${AdminUI.escapeHtml(imgObj.category || '')}" placeholder="e.g., Work, Personal" />
      </div>
      <div class="form-group">
        <label style="display:flex;align-items:center;gap:var(--space-sm);cursor:pointer;">
          <input type="checkbox" id="gallery-edit-visible" ${imgObj.visible !== false ? 'checked' : ''} />
          <span class="form-label" style="margin:0;">Visible in gallery</span>
        </label>
      </div>
      <div class="admin-form__actions">
        <button class="btn btn--primary" id="gallery-edit-save">Save</button>
        <button class="btn btn--secondary" id="gallery-edit-cancel">Cancel</button>
      </div>
    </div>`;

  const { modal, close } = AdminUI.openModal('Edit Image', formHTML, { maxWidth: '480px' });

  document.getElementById('gallery-edit-save').addEventListener('click', () => {
    const d = DataStore.getData();
    d.gallery[index] = {
      url: imgObj.url,
      alt: document.getElementById('gallery-edit-alt').value.trim(),
      category: document.getElementById('gallery-edit-cat').value.trim(),
      visible: document.getElementById('gallery-edit-visible').checked,
    };
    DataStore.saveAll(d);
    AdminUI.toast('Image updated');
    close();
    renderGallery(document.getElementById('admin-content'));
  });

  document.getElementById('gallery-edit-cancel').addEventListener('click', close);
}

function deleteGalleryItem(index) {
  AdminUI.confirm('Delete Image', 'Remove this image from the gallery?', () => {
    const data = DataStore.getData();
    data.gallery.splice(index, 1);
    DataStore.saveAll(data);
    AdminAuth.logAction('Deleted', 'Gallery image');
    AdminUI.toast('Image deleted');
    renderGallery(document.getElementById('admin-content'));
  });
}

function updateGalleryBulk() {
  const checked = document.querySelectorAll('.gallery-checkbox:checked').length;
  const btn = document.getElementById('gallery-bulk-delete');
  if (btn) btn.style.display = checked > 0 ? '' : 'none';
}

function bulkDeleteGallery() {
  const indices = Array.from(document.querySelectorAll('.gallery-checkbox:checked'))
    .map(cb => parseInt(cb.value)).sort((a, b) => b - a);
  if (!indices.length) return;

  AdminUI.confirm('Bulk Delete', `Delete ${indices.length} selected image(s)?`, () => {
    const data = DataStore.getData();
    indices.forEach(i => data.gallery.splice(i, 1));
    DataStore.saveAll(data);
    AdminAuth.logAction('Deleted', `${indices.length} gallery images`);
    AdminUI.toast(`${indices.length} images deleted`);
    renderGallery(document.getElementById('admin-content'));
  });
}
