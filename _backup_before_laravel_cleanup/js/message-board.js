/* ============================================
   MESSAGE BOARD — Full CRUD with localStorage
   ============================================ */

document.addEventListener('DOMContentLoaded', () => {
  initMessageBoard();
});

const MB_STORAGE_KEY = 'portfolio_messages';
const MB_PER_PAGE = 6;
let mbCurrentPage = 1;
let mbSelectedRating = 0;

function initMessageBoard() {
  seedMessagesIfNeeded();
  bindMessageForm();
  bindStarInput();
  renderMessages();
}

/* ---- Seed placeholder messages on first visit ---- */
function seedMessagesIfNeeded() {
  const existing = localStorage.getItem(MB_STORAGE_KEY);
  if (!existing) {
    const sessionToken = getSessionToken();
    const seeded = PORTFOLIO_DATA.seedMessages.map((msg, i) => ({
      id: 'seed_' + i + '_' + Date.now(),
      name: msg.name,
      message: msg.message,
      rating: msg.rating,
      timestamp: msg.timestamp,
      sessionToken: 'seed_visitor_' + i, // Not owned by current user
    }));
    localStorage.setItem(MB_STORAGE_KEY, JSON.stringify(seeded));
  }
}

/* ---- Get / Save Messages ---- */
function getMessages() {
  try {
    return JSON.parse(localStorage.getItem(MB_STORAGE_KEY)) || [];
  } catch {
    return [];
  }
}

function saveMessages(messages) {
  localStorage.setItem(MB_STORAGE_KEY, JSON.stringify(messages));
}

/* ---- Star Rating Input ---- */
function bindStarInput() {
  const starContainer = document.getElementById('mb-star-input');
  if (!starContainer) return;

  const stars = starContainer.querySelectorAll('.star-rating__star');
  stars.forEach(star => {
    star.addEventListener('click', () => {
      const rating = parseInt(star.dataset.rating);
      mbSelectedRating = mbSelectedRating === rating ? 0 : rating; // Toggle
      updateStarDisplay(stars, mbSelectedRating);
    });

    star.addEventListener('mouseenter', () => {
      const rating = parseInt(star.dataset.rating);
      updateStarDisplay(stars, rating);
    });
  });

  starContainer.addEventListener('mouseleave', () => {
    updateStarDisplay(stars, mbSelectedRating);
  });
}

function updateStarDisplay(stars, rating) {
  stars.forEach(s => {
    const r = parseInt(s.dataset.rating);
    s.classList.toggle('active', r <= rating);
  });
}

/* ---- Form Submission ---- */
function bindMessageForm() {
  const form = document.getElementById('mb-form');
  const textarea = document.getElementById('mb-message');
  const charCount = document.getElementById('mb-char-count');
  const submitBtn = document.getElementById('mb-submit');

  if (!form) return;

  // Character counter
  textarea.addEventListener('input', () => {
    charCount.textContent = textarea.value.length;
  });

  form.addEventListener('submit', (e) => {
    e.preventDefault();

    const name = document.getElementById('mb-name').value.trim();
    const message = textarea.value.trim();

    if (!name || !message) return;

    // Loading state
    submitBtn.classList.add('btn--loading');
    submitBtn.innerHTML = '<span class="spinner"></span> Posting...';

    // Simulate slight delay for UX
    setTimeout(() => {
      const newMessage = {
        id: 'msg_' + Date.now() + '_' + Math.random().toString(36).substring(2, 7),
        name,
        message,
        rating: mbSelectedRating,
        timestamp: Date.now(),
        sessionToken: getSessionToken(),
      };

      const messages = getMessages();
      messages.unshift(newMessage);
      saveMessages(messages);

      // Reset form
      form.reset();
      charCount.textContent = '0';
      mbSelectedRating = 0;
      const stars = document.querySelectorAll('#mb-star-input .star-rating__star');
      updateStarDisplay(stars, 0);

      submitBtn.classList.remove('btn--loading');
      submitBtn.innerHTML = 'Post Message';

      // Re-render
      mbCurrentPage = 1;
      renderMessages();

      showToast('Message posted successfully! ✨', 'success');
    }, 600);
  });
}

/* ---- Render Messages ---- */
function renderMessages() {
  const container = document.getElementById('mb-messages');
  const loadMoreWrapper = document.getElementById('mb-load-more');
  if (!container) return;

  const messages = getMessages();
  const totalPages = Math.ceil(messages.length / MB_PER_PAGE);
  const visible = messages.slice(0, mbCurrentPage * MB_PER_PAGE);
  const sessionToken = getSessionToken();

  if (messages.length === 0) {
    container.innerHTML = `
      <div class="empty-state" style="grid-column: 1 / -1;">
        <div class="empty-state__icon">💬</div>
        <h3 class="empty-state__title">No messages yet</h3>
        <p class="empty-state__text">Be the first to leave a message!</p>
      </div>
    `;
    loadMoreWrapper.style.display = 'none';
    return;
  }

  container.innerHTML = visible.map(msg => renderMessageCard(msg, sessionToken)).join('');

  // Show/hide load more
  if (loadMoreWrapper) {
    loadMoreWrapper.style.display = mbCurrentPage < totalPages ? 'block' : 'none';
    const btn = document.getElementById('mb-load-more-btn');
    if (btn) {
      btn.onclick = () => {
        mbCurrentPage++;
        renderMessages();
      };
    }
  }

  // Bind edit/delete actions
  bindMessageActions();
}

function renderMessageCard(msg, sessionToken) {
  const isOwner = msg.sessionToken === sessionToken;
  const initials = getInitials(msg.name);
  const timeAgo = getRelativeTime(msg.timestamp);

  return `
    <div class="mb-card" data-id="${msg.id}" id="mb-card-${msg.id}">
      <div class="mb-card__header">
        <div class="avatar">${initials}</div>
        <div class="mb-card__meta">
          <div class="mb-card__name">${escapeHtml(msg.name)}</div>
          <div class="mb-card__time">${timeAgo}</div>
        </div>
      </div>
      <p class="mb-card__text" id="mb-text-${msg.id}">${escapeHtml(msg.message)}</p>
      <div class="mb-card__footer">
        ${msg.rating ? renderStars(msg.rating) : '<span></span>'}
        ${isOwner ? `
          <div class="mb-card__actions">
            <button class="mb-card__action" onclick="startEditMessage('${msg.id}')">✎ Edit</button>
            <button class="mb-card__action mb-card__action--delete" onclick="confirmDeleteMessage('${msg.id}')">✕ Delete</button>
          </div>
        ` : ''}
      </div>
      <div id="mb-edit-${msg.id}"></div>
      <div id="mb-confirm-${msg.id}"></div>
    </div>
  `;
}

/* ---- Edit Message ---- */
function startEditMessage(id) {
  const messages = getMessages();
  const msg = messages.find(m => m.id === id);
  if (!msg) return;

  const editContainer = document.getElementById(`mb-edit-${id}`);
  if (!editContainer) return;

  editContainer.innerHTML = `
    <div class="mb-edit-form">
      <textarea class="form-textarea" id="mb-edit-textarea-${id}" maxlength="300" rows="3">${escapeHtml(msg.message)}</textarea>
      <p class="form-hint"><span id="mb-edit-count-${id}">${msg.message.length}</span> / 300</p>
      <div class="mb-edit-form__actions">
        <button class="btn btn--primary btn--sm" onclick="saveEditMessage('${id}')">Save</button>
        <button class="btn btn--secondary btn--sm" onclick="cancelEditMessage('${id}')">Cancel</button>
      </div>
    </div>
  `;

  // Bind char counter
  const ta = document.getElementById(`mb-edit-textarea-${id}`);
  const counter = document.getElementById(`mb-edit-count-${id}`);
  ta.addEventListener('input', () => {
    counter.textContent = ta.value.length;
  });
  ta.focus();
}

function saveEditMessage(id) {
  const textarea = document.getElementById(`mb-edit-textarea-${id}`);
  if (!textarea) return;

  const newText = textarea.value.trim();
  if (!newText) return;

  const messages = getMessages();
  const idx = messages.findIndex(m => m.id === id);
  if (idx === -1) return;

  messages[idx].message = newText;
  saveMessages(messages);
  renderMessages();
  showToast('Message updated! ✏️', 'success');
}

function cancelEditMessage(id) {
  const editContainer = document.getElementById(`mb-edit-${id}`);
  if (editContainer) editContainer.innerHTML = '';
}

/* ---- Delete Message ---- */
function confirmDeleteMessage(id) {
  const confirmContainer = document.getElementById(`mb-confirm-${id}`);
  if (!confirmContainer) return;

  confirmContainer.innerHTML = `
    <div class="mb-confirm">
      <span>Are you sure you want to delete this message?</span>
      <button class="btn btn--primary btn--sm" style="background: var(--error);" onclick="deleteMessage('${id}')">Delete</button>
      <button class="btn btn--secondary btn--sm" onclick="cancelDelete('${id}')">Cancel</button>
    </div>
  `;
}

function deleteMessage(id) {
  const card = document.getElementById(`mb-card-${id}`);
  if (card) {
    card.classList.add('deleting');
    setTimeout(() => {
      const messages = getMessages().filter(m => m.id !== id);
      saveMessages(messages);
      renderMessages();
      showToast('Message deleted', 'success');
    }, 300);
  }
}

function cancelDelete(id) {
  const confirmContainer = document.getElementById(`mb-confirm-${id}`);
  if (confirmContainer) confirmContainer.innerHTML = '';
}

/* ---- Bind Actions (unused since using inline onclick, but kept for extensibility) ---- */
function bindMessageActions() {
  // Actions are bound via inline onclick for simplicity
}

/* ---- Toast Notification ---- */
function showToast(message, type = 'success') {
  const toast = document.getElementById('toast');
  if (!toast) return;

  toast.className = `toast toast--${type} show`;
  toast.textContent = message;

  setTimeout(() => {
    toast.classList.remove('show');
  }, 3000);
}

/* ---- Escape HTML ---- */
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}
