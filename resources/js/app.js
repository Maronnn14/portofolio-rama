/* Public navigation enhancements: dropdowns, mobile accordions, keyboard support. */
document.addEventListener('DOMContentLoaded', () => {
  initDesktopDropdowns();
  initMobileAccordions();
  initMobileClose();
});

function initDesktopDropdowns() {
  const dropdowns = Array.from(document.querySelectorAll('[data-dropdown]'));
  if (!dropdowns.length) return;

  const isDesktop = () => window.matchMedia('(min-width: 769px)').matches;
  let closeTimer = null;

  const setOpen = (dropdown, open) => {
    dropdown.classList.toggle('open', open);
    dropdown.querySelector('.navbar__trigger')?.setAttribute('aria-expanded', String(open));
  };

  const closeAll = (except = null) => {
    dropdowns.forEach((dropdown) => {
      if (dropdown === except) return;
      setOpen(dropdown, false);
    });
  };

  const cancelClose = () => {
    if (closeTimer) {
      window.clearTimeout(closeTimer);
      closeTimer = null;
    }
  };

  // Give the pointer time to travel from the trigger into the menu (and back)
  // before closing, so the dropdown no longer disappears on the way down.
  const scheduleClose = (dropdown) => {
    cancelClose();
    closeTimer = window.setTimeout(() => {
      closeTimer = null;
      if (dropdown.matches(':hover') || dropdown.contains(document.activeElement)) return;
      setOpen(dropdown, false);
    }, 300);
  };

  dropdowns.forEach((dropdown) => {
    const trigger = dropdown.querySelector('.navbar__trigger');
    if (!trigger) return;

    trigger.addEventListener('click', (event) => {
      event.stopPropagation();
      cancelClose();
      const open = dropdown.classList.contains('open');
      closeAll(dropdown);
      setOpen(dropdown, !open);
    });

    dropdown.addEventListener('mouseenter', () => {
      if (!isDesktop()) return;
      cancelClose();
      closeAll(dropdown);
      setOpen(dropdown, true);
    });

    dropdown.addEventListener('mouseleave', () => {
      if (!isDesktop()) return;
      scheduleClose(dropdown);
    });

    dropdown.addEventListener('focusout', (event) => {
      if (dropdown.contains(event.relatedTarget)) return;
      scheduleClose(dropdown);
    });
  });

  document.addEventListener('click', (event) => {
    if (!event.target.closest('[data-dropdown]')) {
      cancelClose();
      closeAll();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    closeAll();
    const openTrigger = document.querySelector('.navbar__trigger[aria-expanded="true"]');
    openTrigger?.focus();
  });
}

function initMobileAccordions() {
  document.querySelectorAll('[data-mobile-group]').forEach((group) => {
    const trigger = group.querySelector('.navbar__mobile-trigger');
    const submenu = group.querySelector('.navbar__mobile-submenu');
    if (!trigger || !submenu) return;

    trigger.addEventListener('click', () => {
      const open = trigger.getAttribute('aria-expanded') === 'true';
      document.querySelectorAll('[data-mobile-group] .navbar__mobile-trigger').forEach((other) => {
        if (other === trigger) return;
        other.setAttribute('aria-expanded', 'false');
        other.closest('[data-mobile-group]')?.querySelector('.navbar__mobile-submenu')?.setAttribute('hidden', '');
      });

      trigger.setAttribute('aria-expanded', String(!open));
      submenu.toggleAttribute('hidden', open);
    });
  });
}

function initMobileClose() {
  const menu = document.getElementById('mobile-menu');
  const overlay = document.getElementById('mobile-overlay');
  const hamburger = document.querySelector('.navbar__hamburger');

  const close = () => {
    menu?.classList.remove('open');
    overlay?.classList.remove('open');
    hamburger?.classList.remove('open');
    hamburger?.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  };

  document.querySelectorAll('[data-close-mobile]').forEach((button) => {
    button.addEventListener('click', close);
  });
  overlay?.addEventListener('click', close);
  menu?.querySelectorAll('a').forEach((link) => link.addEventListener('click', close));

  if (hamburger) {
    hamburger.addEventListener('click', () => {
      window.setTimeout(() => {
        hamburger.setAttribute('aria-expanded', String(menu?.classList.contains('open') ?? false));
      });
    });
  }

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && menu?.classList.contains('open')) close();
  });
}
