/* ============================================
   ADMIN APPEARANCE — Theme Controls
   ============================================ */

const APPEARANCE_KEY = 'portfolio_appearance';
const COLOR_PRESETS = [
  { name: 'Gold', value: '#d4a853' },
  { name: 'Blue', value: '#3b82f6' },
  { name: 'Emerald', value: '#10b981' },
  { name: 'Rose', value: '#f43f5e' },
  { name: 'Violet', value: '#8b5cf6' },
  { name: 'Orange', value: '#f97316' },
  { name: 'Cyan', value: '#06b6d4' },
  { name: 'Lime', value: '#84cc16' },
  { name: 'Amber', value: '#f59e0b' },
  { name: 'Slate', value: '#94a3b8' },
];

/* ---- Color Utility Helpers ---- */

/** Convert a HEX color string (e.g. "#d4a853") to an {r, g, b} object. */
function hexToRgb(hex) {
  hex = hex.replace(/^#/, '');
  if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
  const n = parseInt(hex, 16);
  return { r: (n >> 16) & 255, g: (n >> 8) & 255, b: n & 255 };
}

/** Convert an {r, g, b} object back to a "#rrggbb" HEX string. */
function rgbToHex(r, g, b) {
  return '#' + [r, g, b].map(v => Math.max(0, Math.min(255, Math.round(v))).toString(16).padStart(2, '0')).join('');
}

/** Return a HEX color lightened by `percent` (0-100). */
function lightenHex(hex, percent) {
  const { r, g, b } = hexToRgb(hex);
  const t = percent / 100;
  return rgbToHex(
    r + (255 - r) * t,
    g + (255 - g) * t,
    b + (255 - b) * t
  );
}

/** Return a HEX color darkened by `percent` (0-100). */
function darkenHex(hex, percent) {
  const { r, g, b } = hexToRgb(hex);
  const t = 1 - percent / 100;
  return rgbToHex(r * t, g * t, b * t);
}

/* ---- Centralized Accent Variable Setter ---- */

/**
 * Sets ALL accent-related CSS custom properties on :root so that
 * every element referencing any of these variables updates in sync.
 */
function setAllAccentVars(hex) {
  const root = document.documentElement.style;
  const { r, g, b } = hexToRgb(hex);
  const light = lightenHex(hex, 15);
  const dark  = darkenHex(hex, 15);

  root.setProperty('--accent',             hex);
  root.setProperty('--accent-light',       light);
  root.setProperty('--accent-dark',        dark);
  root.setProperty('--accent-glow',        `rgba(${r}, ${g}, ${b}, 0.15)`);
  root.setProperty('--accent-glow-strong', `rgba(${r}, ${g}, ${b}, 0.3)`);
  root.setProperty('--border-accent',      `rgba(${r}, ${g}, ${b}, 0.3)`);
  root.setProperty('--shadow-glow',        `0 0 30px rgba(${r}, ${g}, ${b}, 0.15)`);
  root.setProperty('--shadow-glow-strong', `0 0 50px rgba(${r}, ${g}, ${b}, 0.25)`);
  // Tailwind v4 @theme variables — so text-accent, bg-accent, etc. update
  root.setProperty('--color-accent',       hex);
  root.setProperty('--color-accent-light', light);
  root.setProperty('--color-accent-dark',  dark);
}

/**
 * Removes ALL accent-related inline overrides from :root so the
 * browser falls back to the defaults declared in variables.css.
 */
function removeAllAccentVars() {
  const root = document.documentElement.style;
  [
    '--accent',
    '--accent-light',
    '--accent-dark',
    '--accent-glow',
    '--accent-glow-strong',
    '--border-accent',
    '--shadow-glow',
    '--shadow-glow-strong',
    '--color-accent',
    '--color-accent-light',
    '--color-accent-dark',
  ].forEach(v => root.removeProperty(v));
}

/* ---- Persistence ---- */

function getAppearance() {
  try { return JSON.parse(localStorage.getItem(APPEARANCE_KEY) || '{}'); } catch { return {}; }
}
function saveAppearance(s) { localStorage.setItem(APPEARANCE_KEY, JSON.stringify(s)); }

/* ---- Render Appearance Panel ---- */

function renderAppearance(container) {
  const app = getAppearance();
  const accent = app.accentColor || '#d4a853';
  const radius = app.borderRadius ?? 12;

  container.innerHTML = `
    <div class="admin-section-card">
      <h3 class="admin-section-card__title" style="margin-bottom:var(--space-xl);">Accent Color</h3>
      <div style="display:flex;gap:var(--space-2xl);flex-wrap:wrap;">
        <div style="flex:1;min-width:260px;">
          <div class="form-group">
            <label class="form-label">Color</label>
            <div style="display:flex;gap:var(--space-md);align-items:center;">
              <input type="color" id="accent-picker" value="${accent}" style="width:50px;height:40px;border:none;cursor:pointer;" />
              <input type="text" class="form-input" id="accent-hex" value="${accent}" style="width:120px;font-family:var(--font-mono);" />
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Presets</label>
            <div class="admin-color-presets">
              ${COLOR_PRESETS.map(p => `<div class="admin-color-swatch ${p.value===accent?'active':''}" style="background:${p.value};" title="${p.name}" onclick="selectPreset('${p.value}')"></div>`).join('')}
            </div>
          </div>
          <div style="display:flex;gap:var(--space-md);margin-top:var(--space-xl);">
            <button class="btn btn--primary" onclick="applyAccent()">Apply</button>
            <button class="btn btn--secondary" onclick="resetAccent()">Reset</button>
          </div>
        </div>
        <div style="flex:1;min-width:260px;">
          <label class="form-label">Preview</label>
          <div id="accent-preview" style="background:var(--bg-secondary);border:1px solid var(--border-subtle);border-radius:12px;padding:var(--space-xl);">
            <button style="background:${accent};color:#0a0a0a;border:none;padding:8px 20px;border-radius:6px;font-weight:600;">Button</button>
            <p style="color:${accent};margin:8px 0;font-size:14px;">Active item</p>
            <div style="height:6px;background:#1a1a1a;border-radius:3px;margin-top:12px;"><div style="width:70%;height:100%;background:${accent};border-radius:3px;"></div></div>
          </div>
        </div>
      </div>
    </div>
    <div class="admin-section-card">
      <h3 class="admin-section-card__title" style="margin-bottom:var(--space-xl);">Border Radius</h3>
      <div class="form-group">
        <label class="form-label">Radius: <strong id="radius-label">${radius}px</strong></label>
        <input type="range" id="radius-slider" min="0" max="24" value="${radius}" style="width:100%;max-width:400px;accent-color:var(--accent);" />
      </div>
      <button class="btn btn--primary" onclick="applyRadius()">Apply Radius</button>
    </div>`;

  document.getElementById('accent-picker').addEventListener('input', e => {
    document.getElementById('accent-hex').value = e.target.value;
    updatePreview(e.target.value);
  });
  document.getElementById('accent-hex').addEventListener('input', e => {
    if (/^#[0-9a-f]{6}$/i.test(e.target.value)) {
      document.getElementById('accent-picker').value = e.target.value;
      updatePreview(e.target.value);
    }
  });
  document.getElementById('radius-slider').addEventListener('input', e => {
    document.getElementById('radius-label').textContent = e.target.value + 'px';
  });
}

function updatePreview(c) {
  const p = document.getElementById('accent-preview');
  if (!p) return;
  p.querySelector('button').style.background = c;
  p.querySelector('p').style.color = c;
  const bar = p.querySelector('[style*="width:70%"]');
  if (bar) bar.style.background = c;
}

function selectPreset(c) {
  document.getElementById('accent-picker').value = c;
  document.getElementById('accent-hex').value = c;
  updatePreview(c);
  document.querySelectorAll('.admin-color-swatch').forEach(s => s.classList.remove('active'));
  event.target.classList.add('active');
}

/* ---- Apply / Reset ---- */

function applyAccent() {
  const c = document.getElementById('accent-hex').value;
  if (!/^#[0-9a-f]{6}$/i.test(c)) { AdminUI.toast('Invalid color', 'error'); return; }
  const a = getAppearance(); a.accentColor = c; saveAppearance(a);
  setAllAccentVars(c);
  AdminAuth.logAction('Updated', 'Accent color: ' + c);
  AdminUI.toast('Accent color applied!');
}

function resetAccent() {
  const a = getAppearance(); delete a.accentColor; saveAppearance(a);
  removeAllAccentVars();
  AdminUI.toast('Reset to default');
  renderAppearance(document.getElementById('admin-content'));
}

function applyRadius() {
  const v = parseInt(document.getElementById('radius-slider').value);
  const a = getAppearance(); a.borderRadius = v; saveAppearance(a);
  AdminAuth.logAction('Updated', 'Border radius: ' + v + 'px');
  AdminUI.toast('Border radius applied!');
}
