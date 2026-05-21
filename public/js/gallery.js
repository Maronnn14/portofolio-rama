/* ============================================
   GALLERY.JS — Lightbox + fade-in (SSR grid)
   ============================================ */

document.addEventListener('DOMContentLoaded', async () => {
  await PortfolioData.load();
  bindGallery();
});

function bindGallery() {
  const container = document.getElementById('gallery-grid');
  if (!container) return;

  const items = container.querySelectorAll('.gallery-item');
  const images = PortfolioData.galleryImages;

  // Fade in stagger
  items.forEach((item, i) => {
    setTimeout(() => {
      item.style.transition = 'opacity 0.6s var(--ease-out)';
      item.style.opacity = '1';
    }, 100 + i * 80);
  });

  // Bind lightbox
  items.forEach((item, index) => {
    item.addEventListener('click', () => {
      lightbox.open(images, index);
    });
  });
}
