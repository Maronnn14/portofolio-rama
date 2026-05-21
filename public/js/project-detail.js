/* ============================================
   PROJECT-DETAIL.JS — Lightbox only (SSR content)
   ============================================ */

document.addEventListener('DOMContentLoaded', async () => {
  await PortfolioData.load();
  const project = getCurrentProject();
  if (project) {
    initGalleryLightbox(project);
    initScrollReveal();
  }
});

function getCurrentProject() {
  const params = new URLSearchParams(window.location.search);
  const projectId = params.get('id');
  if (!projectId) return null;
  return PortfolioData.projects.find(p => String(p.id) === String(projectId));
}

function initGalleryLightbox(project) {
  const items = document.querySelectorAll('.project-detail__gallery-item');
  if (!items.length || typeof Lightbox === 'undefined') return;
  const lb = new Lightbox();
  const images = (project.gallery || []).map((src, i) => ({ src, alt: `${project.name} screenshot ${i+1}` }));
  items.forEach((item, i) => { item.addEventListener('click', () => lb.open(images, i)); });
}
