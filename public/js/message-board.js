/* ============================================
   MESSAGE BOARD — Full CRUD with API
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
      await API.messages.create({ name, message, rating: mbSelectedRating, session_token: getSessionToken() });
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
  const sessionToken = getSessionToken();

  if (mbMessages.length === 0) {
    container.innerHTML = `<div class="empty-state" style="grid-column: 1 / -1;"><div class="empty-state__icon">💬</div><h3 class="empty-state__title">No messages yet</h3><p class="empty-state__text">Be the first to leave a message!</p></div>`;
    if (loadMoreWrapper) loadMoreWrapper.style.display = 'none';
    return;
  }
  container.innerHTML = visible.map(msg => renderMessageCard(msg, sessionToken)).join('');
  if (loadMoreWrapper) {
    loadMoreWrapper.style.display = mbCurrentPage < totalPages ? 'block' : 'none';
    const btn = document.getElementById('mb-load-more-btn');
    if (btn) { btn.onclick = () => { mbCurrentPage++; renderMessages(); }; }
  }
}

function renderMessageCard(msg, sessionToken) {
  const isOwner = msg.sessionToken === sessionToken;
  const initials = getInitials(msg.name);
  const timeAgo = getRelativeTime(msg.timestamp);
  return `<div class="mb-card" data-id="${msg.id}" id="mb-card-${msg.id}"><div class="mb-card__header"><div class="avatar">${initials}</div><div class="mb-card__meta"><div class="mb-card__name">${escapeHtml(msg.name)}</div><div class="mb-card__time">${timeAgo}</div></div></div><p class="mb-card__text" id="mb-text-${msg.id}">${escapeHtml(msg.message)}</p><div class="mb-card__footer">${msg.rating ? renderStars(msg.rating) : '<span></span>'}${isOwner ? `<div class="mb-card__actions"><button class="mb-card__action" onclick="startEditMessage('${msg.id}')">✎ Edit</button><button class="mb-card__action mb-card__action--delete" onclick="confirmDeleteMessage('${msg.id}')">✕ Delete</button></div>` : ''}</div><div id="mb-edit-${msg.id}"></div><div id="mb-confirm-${msg.id}"></div></div>`;
}

function startEditMessage(id) {
  const msg = mbMessages.find(m => m.id === id);
  if (!msg) return;
  const editContainer = document.getElementById(`mb-edit-${id}`);
  if (!editContainer) return;
  editContainer.innerHTML = `<div class="mb-edit-form"><textarea class="form-textarea" id="mb-edit-textarea-${id}" maxlength="300" rows="3">${escapeHtml(msg.message)}</textarea><p class="form-hint"><span id="mb-edit-count-${id}">${msg.message.length}</span> / 300</p><div class="mb-edit-form__actions"><button class="btn btn--primary btn--sm" onclick="saveEditMessage('${id}')">Save</button><button class="btn btn--secondary btn--sm" onclick="cancelEditMessage('${id}')">Cancel</button></div></div>`;
  const ta = document.getElementById(`mb-edit-textarea-${id}`);
  const counter = document.getElementById(`mb-edit-count-${id}`);
  ta.addEventListener('input', () => { counter.textContent = ta.value.length; });
  ta.focus();
}

async function saveEditMessage(id) {
  const textarea = document.getElementById(`mb-edit-textarea-${id}`);
  if (!textarea) return;
  const newText = textarea.value.trim();
  if (!newText) return;
  try { await API.messages.update(id, { message: newText }); await loadAndRenderMessages(); showToast('Message updated! ✏️', 'success'); }
  catch (err) { showToast(err.message || 'Failed to update', 'error'); }
}

function cancelEditMessage(id) { const c = document.getElementById(`mb-edit-${id}`); if (c) c.innerHTML = ''; }

function confirmDeleteMessage(id) {
  const c = document.getElementById(`mb-confirm-${id}`);
  if (!c) return;
  c.innerHTML = `<div class="mb-confirm"><span>Are you sure you want to delete this message?</span><button class="btn btn--primary btn--sm" style="background: var(--error);" onclick="deleteMessage('${id}')">Delete</button><button class="btn btn--secondary btn--sm" onclick="cancelDelete('${id}')">Cancel</button></div>`;
}

async function deleteMessage(id) {
  const card = document.getElementById(`mb-card-${id}`);
  if (card) { card.classList.add('deleting'); }
  setTimeout(async () => {
    try { await API.messages.delete(id); await loadAndRenderMessages(); showToast('Message deleted', 'success'); }
    catch (err) { showToast(err.message || 'Failed to delete', 'error'); }
  }, 300);
}

function cancelDelete(id) { const c = document.getElementById(`mb-confirm-${id}`); if (c) c.innerHTML = ''; }
function bindMessageActions() { }

function showToast(message, type = 'success') {
  const toast = document.getElementById('toast');
  if (!toast) return;
  toast.className = `toast toast--${type} show`;
  toast.textContent = message;
  setTimeout(() => { toast.classList.remove('show'); }, 3000);
}

function escapeHtml(text) { const div = document.createElement('div'); div.textContent = text; return div.innerHTML; }
