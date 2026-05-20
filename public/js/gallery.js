/* ============================================
   GALLERY.JS — Masonry grid with lightbox
   ============================================ */

document.addEventListener('DOMContentLoaded', async () => {
  await PortfolioData.load();
  renderGallery();
});

function renderGallery() {
  const container = document.getElementById('gallery-grid');
  if (!container) return;

  const images = PortfolioData.galleryImages;

  container.innerHTML = images.map((img, i) => `
    <div class="gallery-item reveal stagger-${Math.min((i % 8) + 1, 8)}" 
         data-lightbox="${img.src}" 
         style="opacity: 0;">
      <img src="${img.src}" alt="${img.alt}" loading="lazy" />
    </div>
  `).join('');

  // Fade in stagger
  const items = container.querySelectorAll('.gallery-item');
  items.forEach((item, i) => {
    setTimeout(() => {
      item.style.transition = 'opacity 0.6s var(--ease-out)';
      item.style.opacity = '1';
    }, 100 + i * 80);
  });

  // Bind lightbox
  const galleryImages = images.map(img => ({ src: img.src, alt: img.alt }));
  items.forEach((item, index) => {
    item.addEventListener('click', () => {
      lightbox.open(galleryImages, index);
    });
  });

  initScrollReveal();
}
