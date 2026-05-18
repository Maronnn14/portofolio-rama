/* ============================================
   LIGHTBOX — Reusable Image Viewer Component
   ============================================ */

class Lightbox {
  constructor() {
    this.images = [];
    this.currentIndex = 0;
    this.element = null;
    this.isOpen = false;
    this.createDOM();
    this.bindEvents();
  }

  createDOM() {
    const div = document.createElement('div');
    div.className = 'lightbox';
    div.id = 'lightbox';
    div.innerHTML = `
      <button class="lightbox__close" aria-label="Close lightbox">&times;</button>
      <button class="lightbox__nav lightbox__nav--prev" aria-label="Previous image">&#8249;</button>
      <div class="lightbox__image-wrapper">
        <img class="lightbox__image" src="" alt="" />
      </div>
      <button class="lightbox__nav lightbox__nav--next" aria-label="Next image">&#8250;</button>
      <div class="lightbox__counter"></div>
    `;
    document.body.appendChild(div);
    this.element = div;
    this.imgEl = div.querySelector('.lightbox__image');
    this.counterEl = div.querySelector('.lightbox__counter');
    this.prevBtn = div.querySelector('.lightbox__nav--prev');
    this.nextBtn = div.querySelector('.lightbox__nav--next');
    this.closeBtn = div.querySelector('.lightbox__close');
  }

  bindEvents() {
    this.closeBtn.addEventListener('click', () => this.close());
    this.prevBtn.addEventListener('click', () => this.prev());
    this.nextBtn.addEventListener('click', () => this.next());

    this.element.addEventListener('click', (e) => {
      if (e.target === this.element) this.close();
    });

    document.addEventListener('keydown', (e) => {
      if (!this.isOpen) return;
      if (e.key === 'Escape') this.close();
      if (e.key === 'ArrowLeft') this.prev();
      if (e.key === 'ArrowRight') this.next();
    });
  }

  open(images, startIndex = 0) {
    this.images = images;
    this.currentIndex = startIndex;
    this.show();
    this.element.classList.add('open');
    this.isOpen = true;
    document.body.style.overflow = 'hidden';
  }

  close() {
    this.element.classList.remove('open');
    this.isOpen = false;
    document.body.style.overflow = '';
  }

  prev() {
    this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
    this.show();
  }

  next() {
    this.currentIndex = (this.currentIndex + 1) % this.images.length;
    this.show();
  }

  show() {
    const img = this.images[this.currentIndex];
    const src = typeof img === 'string' ? img : img.src;
    const alt = typeof img === 'string' ? '' : (img.alt || '');
    this.imgEl.src = src;
    this.imgEl.alt = alt;
    this.counterEl.textContent = `${this.currentIndex + 1} / ${this.images.length}`;

    // Hide nav if only one image
    const singleImage = this.images.length <= 1;
    this.prevBtn.style.display = singleImage ? 'none' : '';
    this.nextBtn.style.display = singleImage ? 'none' : '';
    this.counterEl.style.display = singleImage ? 'none' : '';
  }
}

// Initialize global lightbox instance
const lightbox = new Lightbox();

/* ---- Helper: attach lightbox to a set of images ---- */
function initLightboxGallery(containerSelector) {
  const containers = document.querySelectorAll(containerSelector);
  containers.forEach(container => {
    const images = container.querySelectorAll('[data-lightbox]');
    const srcs = Array.from(images).map(img => ({
      src: img.dataset.lightbox || img.src,
      alt: img.alt || ''
    }));

    images.forEach((img, index) => {
      img.style.cursor = 'pointer';
      img.addEventListener('click', () => {
        lightbox.open(srcs, index);
      });
    });
  });
}
