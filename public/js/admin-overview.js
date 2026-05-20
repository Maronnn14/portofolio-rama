/* ============================================
   ADMIN OVERVIEW — Dashboard home (API-backed)
   ============================================ */

async function renderOverview(container) {
  container.innerHTML = '<div class="admin-loading"><span class="spinner"></span> Loading dashboard...</div>';

  let projects = [], skills = [], messages = [], gallery = [];
  try {
    [projects, skills, messages, gallery] = await Promise.all([
      API.projects.list(), API.skills.list(), API.messages.list(), API.gallery.list()
    ]);
  } catch { /* use empty arrays */ }

  const log = AdminAuth.getActivityLog();
  const lastLogin = AdminAuth.getLastLogin();
  const username = AdminAuth.getUsername();

  container.innerHTML = `
    <div class="admin-welcome"><div class="admin-welcome__text"><h2>Welcome back, ${AdminUI.escapeHtml(username)} 👋</h2><p>Last login: ${lastLogin ? new Date(lastLogin).toLocaleString() : 'First session'}</p></div><a href="index.html" target="_blank" class="btn btn--secondary">View Site →</a></div>
    <div class="admin-stats">
      <div class="admin-stat-card"><div class="admin-stat-card__icon">📁</div><div class="admin-stat-card__label">Projects</div><div class="admin-stat-card__value">${projects.length}</div></div>
      <div class="admin-stat-card"><div class="admin-stat-card__icon">🛠</div><div class="admin-stat-card__label">Skills</div><div class="admin-stat-card__value">${skills.length}</div></div>
      <div class="admin-stat-card"><div class="admin-stat-card__icon">💬</div><div class="admin-stat-card__label">Messages</div><div class="admin-stat-card__value">${messages.length}</div></div>
      <div class="admin-stat-card"><div class="admin-stat-card__icon">🖼</div><div class="admin-stat-card__label">Gallery Items</div><div class="admin-stat-card__value">${gallery.length}</div></div>
    </div>
    <div class="admin-quick-actions">
      <button class="admin-quick-action" onclick="AdminRouter.navigate('projects')">+ New Project</button>
      <button class="admin-quick-action" onclick="AdminRouter.navigate('skills')">+ New Skill</button>
      <button class="admin-quick-action" onclick="AdminRouter.navigate('experience')">+ New Experience</button>
      <button class="admin-quick-action" onclick="AdminRouter.navigate('gallery')">+ Upload Photo</button>
    </div>
    <div class="admin-section-card"><div class="admin-section-card__header"><h3 class="admin-section-card__title">Recent Activity</h3></div>
      ${log.length ? `<ul class="admin-activity">${log.slice(0, 10).map(entry => `<li class="admin-activity__item"><div class="admin-activity__icon">${getActivityIcon(entry.type)}</div><div class="admin-activity__text"><strong>${AdminUI.escapeHtml(entry.type)}</strong> ${AdminUI.escapeHtml(entry.item)}</div><span class="admin-activity__time">${AdminUI.timeAgo(entry.timestamp)}</span></li>`).join('')}</ul>` : `<div class="admin-empty"><div class="admin-empty__icon">📋</div><p class="admin-empty__text">No activity yet.</p></div>`}
    </div>`;
}

function getActivityIcon(type) {
  const icons = { 'Created': '✨', 'Updated': '✏️', 'Deleted': '🗑️', 'Saved': '💾', 'Uploaded': '📤', 'Imported': '📥', 'Exported': '📦', 'Reset': '🔄' };
  return icons[type] || '📌';
}
