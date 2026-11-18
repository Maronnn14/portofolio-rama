/* ============================================
   MESSAGE BOARD — publik: baca + kirim pesan.
   Moderasi (sunting/hapus) hanya untuk admin, lewat panel /admin.
   ============================================ */

document.addEventListener('DOMContentLoaded', () => { initMessageBoard(); });

const MB_PER_PAGE = 6;
let mbCurrentPage = 1;
let mbSelectedRating = 0;
let mbMessages = [];

async function initMessageBoard() {
  bindStarInput();
  bindMessageForm();
  await loadAndRenderMessages();
}

async function loadAndRenderMessages() {
  try { mbMessages = await API.messages.list(); } catch { mbMessages = []; }
  renderMessages();
}

function bindStarInput() {
  const starContainer = document.getElementById('mb-star-input');
  if (!starContainer) return;
  const stars = starContainer.querySelectorAll('.star-rating__star');
  stars.forEach(star => {
    star.addEventListener('click', () => { const rating = parseInt(star.dataset.rating); mbSelectedRating = mbSelectedRating === rating ? 0 : rating; updateStarDisplay(stars, mbSelectedRating); });
    star.addEventListener('mouseenter', () => { updateStarDisplay(stars, parseInt(star.dataset.rating)); });
  });
  starContainer.addEventListener('mouseleave', () => { updateStarDisplay(stars, mbSelectedRating); });
}

function updateStarDisplay(stars, rating) { stars.forEach(s => { s.classList.toggle('active', parseInt(s.dataset.rating) <= rating); }); }

function bindMessageForm() {
  const form = document.getElementById('mb-form');
  const textarea = document.getElementById('mb-message');
  const charCount = document.getElementById('mb-char-count');
  const submitBtn = document.getElementById('mb-submit');
  if (!form) return;
  textarea.addEventListener('input', () => { charCount.textContent = textarea.value.length; });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const name = document.getElementById('mb-name').value.trim();
    const message = textarea.value.trim();
    if (!name || !message) return;
    submitBtn.classList.add('btn--loading');
    submitBtn.innerHTML = '<span class="spinner"></span> Posting...';
    try {
      await API.messages.create({ name, message, rating: mbSelectedRating });
      form.reset(); charCount.textContent = '0'; mbSelectedRating = 0;
      const stars = document.querySelectorAll('#mb-star-input .star-rating__star'); updateStarDisplay(stars, 0);
      mbCurrentPage = 1; await loadAndRenderMessages();
      showToast('Message posted successfully! ✨', 'success');
    } catch (err) {
      showToast(err.message || 'Failed to post message', 'error');
    } finally {
      submitBtn.classList.remove('btn--loading'); submitBtn.innerHTML = 'Post Message';
    }
  });
}

function renderMessages() {
  const container = document.getElementById('mb-messages');
  const loadMoreWrapper = document.getElementById('mb-load-more');
  if (!container) return;
  const totalPages = Math.ceil(mbMessages.length / MB_PER_PAGE);
  const visible = mbMessages.slice(0, mbCurrentPage * MB_PER_PAGE);

  if (mbMessages.length === 0) {
    container.innerHTML = `<div class="empty-state" style="grid-column: 1 / -1;"><div class="empty-state__icon">💬</div><h3 class="empty-state__title">No messages yet</h3><p class="empty-state__text">Be the first to leave a message!</p></div>`;
    if (loadMoreWrapper) loadMoreWrapper.style.display = 'none';
    return;
  }
  container.innerHTML = visible.map(msg => renderMessageCard(msg)).join('');
  if (loadMoreWrapper) {
    loadMoreWrapper.style.display = mbCurrentPage < totalPages ? 'block' : 'none';
    const btn = document.getElementById('mb-load-more-btn');
    if (btn) { btn.onclick = () => { mbCurrentPage++; renderMessages(); }; }
  }
}

function renderMessageCard(msg) {
  const initials = getInitials(msg.name);
  const timeAgo = getRelativeTime(msg.timestamp);
  return `<div class="mb-card" data-id="${msg.id}" id="mb-card-${msg.id}"><div class="mb-card__header"><div class="avatar">${initials}</div><div class="mb-card__meta"><div class="mb-card__name">${escapeHtml(msg.name)}</div><div class="mb-card__time">${timeAgo}</div></div></div><p class="mb-card__text" id="mb-text-${msg.id}">${escapeHtml(msg.message)}</p><div class="mb-card__footer">${msg.rating ? renderStars(msg.rating) : '<span></span>'}</div></div>`;
}

function showToast(message, type = 'success') {
  const toast = document.getElementById('toast');
  if (!toast) return;
  toast.className = `toast toast--${type} show`;
  toast.textContent = message;
  setTimeout(() => { toast.classList.remove('show'); }, 3000);
}

function escapeHtml(text) { const div = document.createElement('div'); div.textContent = text; return div.innerHTML; }
