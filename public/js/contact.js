/* ============================================
   CONTACT.JS — Accessible contact form submission
   ============================================ */

document.addEventListener('DOMContentLoaded', () => {
  initContactForm();
  initServerSuccess();
  initCopyEmail();
});

function initCopyEmail() {
  const button = document.getElementById('copy-email-btn');
  if (!button) return;

  const label = button.querySelector('.contact-info__copy-text');
  const original = label?.textContent || 'Copy';
  let timer;

  button.addEventListener('click', async () => {
    const value = button.dataset.copy || '';
    if (!value) return;

    const copied = await copyText(value);

    button.classList.toggle('is-copied', copied);
    if (label) label.textContent = copied ? 'Copied' : 'Copy failed';

    clearTimeout(timer);
    timer = setTimeout(() => {
      button.classList.remove('is-copied');
      if (label) label.textContent = original;
    }, 2000);
  });
}

async function copyText(value) {
  try {
    if (navigator.clipboard?.writeText) {
      await navigator.clipboard.writeText(value);
      return true;
    }
  } catch {
    // fall through to the legacy path below
  }

  try {
    const field = document.createElement('textarea');
    field.value = value;
    field.setAttribute('readonly', '');
    field.style.position = 'fixed';
    field.style.top = '-1000px';
    field.style.opacity = '0';
    document.body.appendChild(field);
    field.select();
    const ok = document.execCommand('copy');
    field.remove();
    return ok;
  } catch {
    return false;
  }
}

function initServerSuccess() {
  const success = document.getElementById('contact-success-server');
  const form = document.getElementById('contact-form');
  if (success && form) form.hidden = true;
}

function initContactForm() {
  const form = document.getElementById('contact-form');
  if (!form) return;

  const messageField = document.getElementById('contact-message');
  const charCount = document.getElementById('contact-char-count');
  if (messageField && charCount) {
    messageField.addEventListener('input', () => {
      charCount.textContent = messageField.value.length;
    });
  }

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearErrors(form);
    if (!validateContactForm()) return;

    const submitBtn = document.getElementById('contact-submit');
    const submitText = submitBtn?.querySelector('.contact-form__submit-text');
    const errorBox = document.getElementById('contact-error');
    const payload = {
      name: document.getElementById('contact-name').value.trim(),
      email: document.getElementById('contact-email-input').value.trim(),
      subject: document.getElementById('contact-subject').value.trim(),
      message: document.getElementById('contact-message').value.trim(),
    };

    if (submitBtn) submitBtn.disabled = true;
    if (submitText) submitText.textContent = 'Sending...';
    if (errorBox) errorBox.hidden = true;

    try {
      const response = await fetch('/api/contact', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        },
        body: JSON.stringify(payload),
      });

      if (!response.ok) {
        const body = await response.json().catch(() => ({}));
        const message = body.message || 'I could not send your message. Please try again.';
        showFormError(message, body.errors || {});
        if (submitText) submitText.textContent = 'Send Message';
        if (submitBtn) submitBtn.disabled = false;
        return;
      }

      form.hidden = true;
      const success = document.getElementById('contact-success');
      if (success) {
        success.style.display = 'block';
        success.hidden = false;
        success.focus?.();
      }
    } catch {
      showFormError('Network error — please check your connection and try again.');
      if (submitText) submitText.textContent = 'Send Message';
      if (submitBtn) submitBtn.disabled = false;
    }
  });
}

function validateContactForm() {
  const fields = [
    { id: 'contact-name', label: 'Name', max: 100 },
    { id: 'contact-email-input', label: 'Email', max: 190 },
    { id: 'contact-subject', label: 'Subject', max: 150 },
    { id: 'contact-message', label: 'Message', max: 2000, min: 10 },
  ];

  let valid = true;
  for (const field of fields) {
    const element = document.getElementById(field.id);
    if (!element) continue;
    const value = element.value.trim();
    let message = '';

    if (!value) message = `${field.label} is required.`;
    else if (field.max && value.length > field.max) message = `${field.label} must be ${field.max} characters or fewer.`;
    else if (field.min && value.length < field.min) message = `${field.label} must be at least ${field.min} characters.`;

    if (field.id === 'contact-email-input' && value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
      message = 'Enter a valid email address.';
    }

    if (message) {
      setFieldError(element, message);
      valid = false;
    }
  }

  if (!valid) {
    document.querySelector('#contact-form .form-input--error')?.focus();
    const errorBox = document.getElementById('contact-error');
    if (errorBox) {
      errorBox.textContent = 'Please fix the highlighted fields.';
      errorBox.hidden = false;
    }
  }

  return valid;
}

function setFieldError(element, message) {
  element.classList.add('form-input--error');
  element.setAttribute('aria-invalid', 'true');
  const group = element.closest('.form-group');
  if (!group) return;
  let error = group.querySelector('.form-error--js');
  if (!error) {
    error = document.createElement('p');
    error.className = 'form-error form-error--js';
    group.appendChild(error);
  }
  error.textContent = message;
}

function showFormError(message, errors) {
  const errorBox = document.getElementById('contact-error');
  if (errorBox) {
    errorBox.textContent = message;
    errorBox.hidden = false;
  }

  Object.entries(errors || {}).forEach(([name, messages]) => {
    const element = document.querySelector(`#contact-form [name="${name}"]`);
    if (element && messages?.[0]) setFieldError(element, messages[0]);
  });
}

function clearErrors(form) {
  form.querySelectorAll('.form-input--error').forEach((element) => {
    element.classList.remove('form-input--error');
    element.removeAttribute('aria-invalid');
  });
  form.querySelectorAll('.form-error--js').forEach((element) => element.remove());
  const errorBox = document.getElementById('contact-error');
  if (errorBox) errorBox.hidden = true;
}

function resetContactForm() {
  const form = document.getElementById('contact-form');
  const success = document.getElementById('contact-success');
  if (form) {
    form.reset();
    form.hidden = false;
    form.style.display = '';
    const count = document.getElementById('contact-char-count');
    if (count) count.textContent = '0';
    form.querySelectorAll('.form-error--js').forEach((element) => element.remove());
  }
  if (success) {
    success.style.display = 'none';
    success.hidden = true;
  }
}
