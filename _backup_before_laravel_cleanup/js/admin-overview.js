/* ============================================
   ADMIN OVERVIEW — Dashboard home
   ============================================ */

function renderOverview(container) {
  const data = DataStore.getData();
  const messages = JSON.parse(localStorage.getItem('portfolio_messages') || '[]');
  const log = AdminAuth.getActivityLog();
  const lastLogin = AdminAuth.getLastLogin();
  const username = AdminAuth.getUsername();

  container.innerHTML = `
    <!-- Welcome Banner -->
    <div class="admin-welcome">
      <div class="admin-welcome__text">
        <h2>Welcome back, ${AdminUI.escapeHtml(username)} 👋</h2>
        <p>Last login: ${lastLogin ? new Date(lastLogin).toLocaleString() : 'First session'}</p>
      </div>
      <a href="index.html" target="_blank" class="btn btn--secondary">View Site →</a>
    </div>

    <!-- Stat Cards -->
    <div class="admin-stats">
      <div class="admin-stat-card">
        <div class="admin-stat-card__icon">📁</div>
        <div class="admin-stat-card__label">Projects</div>
        <div class="admin-stat-card__value">${data.projects ? data.projects.length : 0}</div>
      </div>
      <div class="admin-stat-card">
        <div class="admin-stat-card__icon">🛠</div>
        <div class="admin-stat-card__label">Skills</div>
        <div class="admin-stat-card__value">${data.skills ? data.skills.length : 0}</div>
      </div>
      <div class="admin-stat-card">
        <div class="admin-stat-card__icon">💬</div>
        <div class="admin-stat-card__label">Messages</div>
        <div class="admin-stat-card__value">${messages.length}</div>
      </div>
      <div class="admin-stat-card">
        <div class="admin-stat-card__icon">🖼</div>
        <div class="admin-stat-card__label">Gallery Items</div>
        <div class="admin-stat-card__value">${data.gallery ? data.gallery.length : 0}</div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="admin-quick-actions">
      <button class="admin-quick-action" onclick="AdminRouter.navigate('projects')">+ New Project</button>
      <button class="admin-quick-action" onclick="AdminRouter.navigate('skills')">+ New Skill</button>
      <button class="admin-quick-action" onclick="AdminRouter.navigate('experience')">+ New Experience</button>
      <button class="admin-quick-action" onclick="AdminRouter.navigate('gallery')">+ Upload Photo</button>
    </div>

    <!-- Activity Log -->
    <div class="admin-section-card">
      <div class="admin-section-card__header">
        <h3 class="admin-section-card__title">Recent Activity</h3>
      </div>
      ${log.length ? `
        <ul class="admin-activity">
          ${log.slice(0, 10).map(entry => `
            <li class="admin-activity__item">
              <div class="admin-activity__icon">${getActivityIcon(entry.type)}</div>
              <div class="admin-activity__text">
                <strong>${AdminUI.escapeHtml(entry.type)}</strong> ${AdminUI.escapeHtml(entry.item)}
              </div>
              <span class="admin-activity__time">${AdminUI.timeAgo(entry.timestamp)}</span>
            </li>
          `).join('')}
        </ul>
      ` : `
        <div class="admin-empty">
          <div class="admin-empty__icon">📋</div>
          <p class="admin-empty__text">No activity yet. Start managing your content!</p>
        </div>
      `}
    </div>
  `;
}

function getActivityIcon(type) {
  const icons = {
    'Created': '✨', 'Updated': '✏️', 'Deleted': '🗑️',
    'Saved': '💾', 'Uploaded': '📤', 'Imported': '📥',
    'Exported': '📦', 'Reset': '🔄',
  };
  return icons[type] || '📌';
}
