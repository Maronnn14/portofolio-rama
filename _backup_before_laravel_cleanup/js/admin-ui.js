/* ============================================
   ADMIN UI — Shared utilities & components
   ============================================ */

const AdminUI = {

  /* ---- Toast Notification ---- */
  toast(message, type = 'success') {
    // Remove existing toast
    const existing = document.getElementById('admin-toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.id = 'admin-toast';
    toast.className = `toast toast--${type}`;
    toast.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:2000;';
    const icons = { success: '✅', error: '❌', warning: '⚠️' };
    toast.textContent = `${icons[type] || ''} ${message}`;
    document.body.appendChild(toast);

    requestAnimationFrame(() => toast.classList.add('show'));
    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 300);
    }, 3500);
  },

  /* ---- Confirmation Modal ---- */
  confirm(title, message, onConfirm, confirmLabel = 'Delete', danger = true) {
    const id = 'admin-confirm-modal';
    const existing = document.getElementById(id);
    if (existing) existing.remove();

    const modal = document.createElement('div');
    modal.id = id;
    modal.className = 'admin-modal open';
    modal.innerHTML = `
      <div class="admin-modal__overlay"></div>
      <div class="admin-modal__card" style="max-width:440px;">
        <div class="admin-confirm">
          <div class="admin-confirm__icon">${danger ? '⚠️' : 'ℹ️'}</div>
          <h3 class="admin-confirm__title">${title}</h3>
          <p class="admin-confirm__text">${message}</p>
          <div class="admin-confirm__actions">
            <button class="btn btn--secondary" id="confirm-cancel">Cancel</button>
            <button class="btn ${danger ? 'btn--primary' : 'btn--primary'}" id="confirm-ok"
                    style="${danger ? 'background:var(--error);' : ''}">${confirmLabel}</button>
          </div>
        </div>
      </div>`;
    document.body.appendChild(modal);

    const close = () => { modal.classList.remove('open'); setTimeout(() => modal.remove(), 300); };
    modal.querySelector('.admin-modal__overlay').addEventListener('click', close);
    modal.querySelector('#confirm-cancel').addEventListener('click', close);
    modal.querySelector('#confirm-ok').addEventListener('click', () => { close(); onConfirm(); });
  },

  /* ---- Modal ---- */
  openModal(title, contentHTML, options = {}) {
    const id = options.id || 'admin-generic-modal';
    const existing = document.getElementById(id);
    if (existing) existing.remove();

    const modal = document.createElement('div');
    modal.id = id;
    modal.className = 'admin-modal open';
    modal.innerHTML = `
      <div class="admin-modal__overlay"></div>
      <div class="admin-modal__card" style="max-width:${options.maxWidth || '600px'};">
        <div class="admin-modal__header">
          <h2 class="admin-modal__title">${title}</h2>
          <button class="admin-modal__close" aria-label="Close">&times;</button>
        </div>
        <div class="admin-modal__body">${contentHTML}</div>
      </div>`;
    document.body.appendChild(modal);

    const close = () => { modal.classList.remove('open'); setTimeout(() => modal.remove(), 300); };
    modal.querySelector('.admin-modal__overlay').addEventListener('click', close);
    modal.querySelector('.admin-modal__close').addEventListener('click', close);

    if (options.onOpen) options.onOpen(modal);
    return { modal, close };
  },

  /* ---- Tag Input ---- */
  createTagInput(container, tags = [], onChange) {
    const render = () => {
      container.innerHTML = `
        <div class="admin-tag-input" id="${container.id}-wrap">
          ${tags.map((t, i) => `<span class="admin-tag-input__tag">${this.escapeHtml(t)}<span class="admin-tag-input__remove" data-idx="${i}">&times;</span></span>`).join('')}
          <input type="text" class="admin-tag-input__field" placeholder="Type & press Enter..." />
        </div>`;

      const input = container.querySelector('.admin-tag-input__field');
      const wrap = container.querySelector('.admin-tag-input');

      wrap.addEventListener('click', () => input.focus());

      input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && input.value.trim()) {
          e.preventDefault();
          tags.push(input.value.trim());
          input.value = '';
          render();
          if (onChange) onChange(tags);
        }
        if (e.key === 'Backspace' && !input.value && tags.length) {
          tags.pop();
          render();
          if (onChange) onChange(tags);
        }
      });

      container.querySelectorAll('.admin-tag-input__remove').forEach(btn => {
        btn.addEventListener('click', () => {
          tags.splice(parseInt(btn.dataset.idx), 1);
          render();
          if (onChange) onChange(tags);
        });
      });
    };
    render();
    return { getTags: () => [...tags], setTags: (newTags) => { tags.length = 0; tags.push(...newTags); render(); } };
  },

  /* ---- Toggle Switch ---- */
  createToggle(label, value, onChange) {
    const id = 'toggle_' + Math.random().toString(36).substring(2, 7);
    return `
      <div class="admin-toggle" onclick="(function(el){
        const track = el.querySelector('.admin-toggle__track');
        track.classList.toggle('active');
        const isActive = track.classList.contains('active');
        if (typeof ${onChange} === 'function') ${onChange}(isActive);
      })(this)">
        <div class="admin-toggle__track ${value ? 'active' : ''}" id="${id}">
          <div class="admin-toggle__thumb"></div>
        </div>
        <span class="admin-toggle__label">${label}</span>
      </div>`;
  },

  /* ---- Image Upload ---- */
  createImageUpload(currentSrc, onUpload, options = {}) {
    const maxSize = options.maxSize || 5 * 1024 * 1024; // 5MB
    const accepts = options.accepts || 'image/jpeg,image/png,image/webp,image/gif';
    const id = 'upload_' + Math.random().toString(36).substring(2, 7);

    return `
      <div class="admin-upload-zone" id="${id}" onclick="document.getElementById('${id}-input').click()">
        ${currentSrc
          ? `<img src="${currentSrc}" alt="Current" style="max-width:200px;max-height:150px;border-radius:8px;margin-bottom:8px;" /><br>`
          : '<div class="admin-upload-zone__icon">📁</div>'}
        <div class="admin-upload-zone__text">
          ${currentSrc ? 'Click to change' : 'Click or drag to upload'}<br>
          <small style="color:var(--text-muted);">Max ${Math.round(maxSize/1024/1024)}MB • JPG, PNG, WebP</small>
        </div>
        <input type="file" id="${id}-input" accept="${accepts}" style="display:none;" />
      </div>`;
  },

  bindImageUpload(containerId, onUpload) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const input = container.querySelector('input[type="file"]');
    if (!input) return;

    input.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = (ev) => {
        const base64 = ev.target.result;
        // Update preview
        const existingImg = container.querySelector('img');
        if (existingImg) {
          existingImg.src = base64;
        } else {
          const iconEl = container.querySelector('.admin-upload-zone__icon');
          if (iconEl) iconEl.outerHTML = `<img src="${base64}" alt="Upload" style="max-width:200px;max-height:150px;border-radius:8px;margin-bottom:8px;" />`;
        }
        if (onUpload) onUpload(base64);
      };
      reader.readAsDataURL(file);
    });

    // Drag and drop
    container.addEventListener('dragover', (e) => { e.preventDefault(); container.classList.add('dragover'); });
    container.addEventListener('dragleave', () => container.classList.remove('dragover'));
    container.addEventListener('drop', (e) => {
      e.preventDefault();
      container.classList.remove('dragover');
      if (e.dataTransfer.files.length) {
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
      }
    });
  },

  /* ---- Status Badge ---- */
  badge(text, type = 'default') {
    const colors = {
      default: 'var(--text-muted)',
      success: 'var(--success)',
      warning: 'var(--warning)',
      error: 'var(--error)',
      accent: 'var(--accent)',
    };
    const bgColors = {
      default: 'var(--bg-tertiary)',
      success: 'var(--success-bg)',
      warning: 'rgba(245,158,11,0.1)',
      error: 'var(--error-bg)',
      accent: 'var(--accent-glow)',
    };
    return `<span style="display:inline-flex;align-items:center;padding:2px 10px;border-radius:999px;font-size:var(--fs-xs);font-weight:var(--fw-medium);color:${colors[type]};background:${bgColors[type]};">${text}</span>`;
  },

  /* ---- Relative Time ---- */
  timeAgo(timestamp) {
    const diff = Date.now() - timestamp;
    const mins = Math.floor(diff / 60000);
    if (mins < 1) return 'just now';
    if (mins < 60) return `${mins}m ago`;
    const hours = Math.floor(mins / 60);
    if (hours < 24) return `${hours}h ago`;
    const days = Math.floor(hours / 24);
    if (days < 30) return `${days}d ago`;
    return new Date(timestamp).toLocaleDateString();
  },

  /* ---- Escape HTML ---- */
  escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  },

  /* ---- Generate ID ---- */
  generateId(prefix = 'item') {
    return `${prefix}_${Date.now()}_${Math.random().toString(36).substring(2, 7)}`;
  }
};

if (typeof window !== 'undefined') {
  window.AdminUI = AdminUI;
}
