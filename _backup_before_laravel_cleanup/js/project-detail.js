/* ============================================
   PROJECT-DETAIL.JS — Dynamic Detail Page
   ============================================ */

document.addEventListener('DOMContentLoaded', () => {
  loadProjectDetail();
});

function loadProjectDetail() {
  const container = document.getElementById('project-detail');
  if (!container) return;

  const params = new URLSearchParams(window.location.search);
  const projectId = params.get('id');

  if (!projectId) { renderNotFound(container); return; }

  const project = PORTFOLIO_DATA.projects.find(p => p.id === projectId);
  if (!project) { renderNotFound(container); return; }

  document.title = `${project.name} — Rama Adin`;

  const linksHtml = buildLinksHtml(project);
  const galleryHtml = buildGalleryHtml(project);

  container.innerHTML = `
    <div class="container">
      <a class="project-detail__back" onclick="history.back()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back to Projects
      </a>
      <header class="project-detail__header reveal">
        <span class="project-detail__category">${project.category}</span>
        <h1 class="project-detail__title">${project.name}</h1>
        <p class="project-detail__desc">${project.fullDesc}</p>
      </header>
      <div class="project-detail__hero-img reveal stagger-1">
        <img src="${project.thumbnail}" alt="${project.name}" />
      </div>
      <div class="project-detail__meta reveal stagger-2">
        <div class="project-detail__meta-item"><h4>Category</h4><span class="tag tag--accent">${project.category}</span></div>
        <div class="project-detail__meta-item"><h4>Tech Stack</h4><div class="tags-list">${project.tech.map(t => `<span class="tag">${t}</span>`).join('')}</div></div>
        <div class="project-detail__meta-item"><h4>Links</h4><div class="project-detail__links">${linksHtml}</div></div>
      </div>
      ${galleryHtml}
    </div>`;

  initGalleryLightbox(container, project);
  initScrollReveal();
}

function buildLinksHtml(project) {
  let html = '';
  if (project.liveUrl && project.liveUrl !== '#') {
    html += `<a href="${project.liveUrl}" target="_blank" rel="noopener noreferrer" class="btn btn--primary btn--sm">Live Demo</a>`;
  }
  if (project.sourceUrl && project.sourceUrl !== '#') {
    html += `<a href="${project.sourceUrl}" target="_blank" rel="noopener noreferrer" class="btn btn--secondary btn--sm">Source Code</a>`;
  }
  if (!html) html = '<span class="tag" style="opacity:0.6">Links coming soon</span>';
  return html;
}

function buildGalleryHtml(project) {
  if (!project.gallery || project.gallery.length === 0) return '';
  const items = project.gallery.map((img, i) =>
    `<div class="project-detail__gallery-item" data-lightbox-index="${i}"><img src="${img}" alt="${project.name} screenshot ${i+1}" loading="lazy" /></div>`
  ).join('');
  return `<div class="project-detail__gallery reveal stagger-3"><h3>Project <span class="text-accent">Gallery</span></h3><div class="project-detail__gallery-grid">${items}</div></div>`;
}

function initGalleryLightbox(container, project) {
  if (!project.gallery || !project.gallery.length || typeof Lightbox === 'undefined') return;
  const lightbox = new Lightbox();
  const images = project.gallery.map((src, i) => ({ src, alt: `${project.name} screenshot ${i+1}` }));
  container.querySelectorAll('.project-detail__gallery-item').forEach((item, i) => {
    item.addEventListener('click', () => lightbox.open(images, i));
  });
}

function renderNotFound(container) {
  container.innerHTML = `<div class="container"><div class="project-detail__not-found"><h2>Project Not Found</h2><p>The project you're looking for doesn't exist or has been removed.</p><a href="projects.html" class="btn btn--primary">Back to Projects</a></div></div>`;
}
