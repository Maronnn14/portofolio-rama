/* ============================================
   LIGHTBOX.JS — Accessible image viewer
   ============================================ */

(function () {
  let modal = null;
  let images = [];
  let index = 0;
  let lastFocus = null;

  function ensureModal() {
    if (modal) return modal;

    modal = document.createElement('div');
    modal.className = 'lightbox';
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-modal', 'true');
    modal.setAttribute('aria-label', 'Image viewer');
    modal.innerHTML = [
      '<button type="button" class="lightbox__close" aria-label="Close image viewer">&times;</button>',
      '<button type="button" class="lightbox__nav lightbox__nav--prev" aria-label="Previous image">&#10095;</button>',
      '<div class="lightbox__image-wrapper"><img class="lightbox__image" alt="" /></div>',
      '<button type="button" class="lightbox__nav lightbox__nav--next" aria-label="Next image">&#10095;</button>',
      '<div class="lightbox__counter" aria-live="polite"></div>',
    ].join('');
    document.body.appendChild(modal);

    modal.querySelector('.lightbox__close').addEventListener('click', close);
    modal.querySelector('.lightbox__nav--prev').addEventListener('click', () => show(index - 1));
    modal.querySelector('.lightbox__nav--next').addEventListener('click', () => show(index + 1));
    modal.addEventListener('click', (event) => {
      if (event.target === modal || event.target.classList.contains('lightbox__image-wrapper')) close();
    });

    return modal;
  }

  function show(next) {
    if (!images.length) return;
    index = (next + images.length) % images.length;
    const image = images[index];
    const src = typeof image === 'string' ? image : image.src;
    const alt = typeof image === 'string' ? '' : (image.alt || '');

    modal.querySelector('.lightbox__image').src = src;
    modal.querySelector('.lightbox__image').alt = alt;
    modal.querySelector('.lightbox__counter').textContent = `${index + 1} / ${images.length}`;
    modal.querySelector('.lightbox__nav--prev').hidden = images.length < 2;
    modal.querySelector('.lightbox__nav--next').hidden = images.length < 2;
  }

  function open(list, startIndex = 0) {
    images = (list || []).filter(Boolean);
    if (!images.length) return;
    lastFocus = document.activeElement;
    ensureModal();
    show(Number(startIndex) || 0);
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
    modal.querySelector('.lightbox__close').focus();
  }

  function close() {
    if (!modal) return;
    modal.classList.remove('open');
    modal.querySelector('.lightbox__image').src = '';
    document.body.style.overflow = '';
    lastFocus?.focus?.();
  }

  document.addEventListener('keydown', (event) => {
    if (!modal?.classList.contains('open')) return;
    if (event.key === 'Escape') close();
    if (event.key === 'ArrowRight') show(index + 1);
    if (event.key === 'ArrowLeft') show(index - 1);
  });

  window.lightbox = { open, close, next: () => show(index + 1), prev: () => show(index - 1) };
})();
