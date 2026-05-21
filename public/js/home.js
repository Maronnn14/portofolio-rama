/* ============================================
   HOME.JS — Hero animations (SSR data preserved)
   ============================================ */

document.addEventListener('DOMContentLoaded', async () => {
  await PortfolioData.load();
  initHeroAnimations();
});

/* ---- Typewriter Effect ---- */
function typeWriter(element, text, speed = 50, callback) {
  let i = 0;
  element.innerHTML = '<span class="cursor"></span>';
  function type() {
    if (i < text.length) { element.innerHTML = text.substring(0, i + 1) + '<span class="cursor"></span>'; i++; setTimeout(type, speed); }
    else { setTimeout(() => { element.innerHTML = text; if (callback) callback(); }, 1000); }
  }
  type();
}

/* ---- Hero Animations ---- */
function initHeroAnimations() {
  const greetingEl = document.getElementById('hero-greeting');
  const roleEl = document.getElementById('hero-role');
  const role = PortfolioData.personal.role || 'Full Stack Developer';
  if (greetingEl) { setTimeout(() => { typeWriter(greetingEl, "Hi there, I'm", 60, () => { if (roleEl) { typeWriter(roleEl, `> ${role}`, 40); } }); }, 500); }
  const heroElements = document.querySelectorAll('.hero__label, .hero__title, .hero__tagline, .hero__cta, .hero__scroll-indicator');
  heroElements.forEach((el, i) => { el.style.opacity = '0'; el.style.transform = 'translateY(20px)'; setTimeout(() => { el.style.transition = `opacity 0.6s var(--ease-out), transform 0.6s var(--ease-out)`; el.style.opacity = '1'; el.style.transform = 'translateY(0)'; }, 300 + i * 150); });
}
