/* ============================================
   PROJECTS.JS — Card Grid & Filtering
   ============================================ */

document.addEventListener('DOMContentLoaded', async () => {
  await PortfolioData.load();
  renderProjectsGrid();
  bindProjectFilters();
});

let currentFilter = 'all';

function renderProjectsGrid(filter = 'all') {
  const grid = document.getElementById('projects-grid');
  if (!grid) return;
  const projects = PortfolioData.projects;
  const filtered = filter === 'all' ? projects : projects.filter(p => p.category === filter);
  grid.innerHTML = filtered.map((project, i) => `
    <article class="project-card reveal stagger-${Math.min(i + 1, 6)}" data-project-id="${project.id}" data-category="${project.category}" onclick="navigateToProject('${project.id}')">
      <div class="project-card__thumbnail">${project.featured ? '<span class="project-card__badge">Featured</span>' : ''}<img src="${project.thumbnail}" alt="${project.name}" loading="lazy" /><div class="project-card__overlay"><span class="project-card__overlay-btn">View Details <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span></div></div>
      <div class="project-card__body"><span class="project-card__category">${project.category}</span><h3 class="project-card__name">${project.name}</h3><p class="project-card__desc">${project.shortDesc}</p><div class="project-card__tech">${project.tech.map(t => `<span class="tag">${t}</span>`).join('')}</div></div>
    </article>`).join('');
  initScrollReveal();
}

function bindProjectFilters() {
  const tabContainer = document.getElementById('project-filter-tabs');
  if (!tabContainer) return;
  tabContainer.addEventListener('click', (e) => {
    const tab = e.target.closest('.filter-tab');
    if (!tab || tab.classList.contains('active')) return;
    tabContainer.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    const filter = tab.dataset.filter; currentFilter = filter;
    const grid = document.getElementById('projects-grid');
    grid.querySelectorAll('.project-card').forEach(card => card.classList.add('hiding'));
    setTimeout(() => { renderProjectsGrid(filter); const newCards = grid.querySelectorAll('.project-card'); newCards.forEach((card, i) => { card.style.animationDelay = `${i * 0.08}s`; card.classList.add('showing'); }); }, 300);
  });
}

function navigateToProject(projectId) { window.location.href = `project-detail.html?id=${projectId}`; }
