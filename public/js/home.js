/* ============================================
   HOME.JS — Hero animations & preview sections
   ============================================ */

document.addEventListener('DOMContentLoaded', async () => {
  await PortfolioData.load(true);
  renderHomeProfile();
  initHeroAnimations();
  renderExperiencePreview();
  renderSkillsPreview();
  renderProjectsPreview();
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

/* ---- Profile Content ---- */
function renderHomeProfile() {
  const personal = PortfolioData.personal || {};
  const fullName = personal.fullName || 'Rama Adin';
  const nameParts = fullName.trim().split(/\s+/);
  const firstName = personal.name || nameParts[0] || 'Rama';
  const accentName = nameParts.length > 1 ? nameParts.slice(1).join(' ') : '';

  const heroFirst = document.getElementById('hero-name-first');
  const heroAccent = document.getElementById('hero-name-accent');
  const heroTagline = document.getElementById('hero-tagline');
  const aboutHeading = document.getElementById('home-about-heading');
  const aboutBio = document.getElementById('home-about-bio');
  const image = document.getElementById('home-profile-image');

  if (heroFirst) heroFirst.textContent = firstName;
  if (heroAccent) heroAccent.textContent = accentName;
  if (heroTagline) heroTagline.textContent = personal.tagline || 'Crafting digital experiences with code & creativity';
  if (aboutHeading) {
    aboutHeading.textContent = '';
    aboutHeading.append("Hello, I'm ");
    const accent = document.createElement('span');
    accent.className = 'text-accent';
    accent.textContent = firstName;
    aboutHeading.append(accent);
  }
  if (aboutBio) {
    aboutBio.textContent = personal.shortBio || 'A passionate developer who transforms ideas into elegant, functional digital solutions. With a keen eye for detail and a love for clean code, I build applications that make a difference.';
  }

  const src = personal.homeProfileImage || personal.photo || personal.profileImage;
  if (image && src) image.src = src;
}

/* ---- Experience Preview ---- */
function renderExperiencePreview() {
  const container = document.getElementById('experience-preview-list');
  if (!container) return;
  const items = PortfolioData.experience.slice(0, 3);
  container.innerHTML = items.map((exp, i) => `<div class="exp-preview-item reveal stagger-${i + 1}"><span class="exp-preview-item__date">${exp.startDate} — ${exp.endDate}</span><div><div class="exp-preview-item__role">${exp.role}</div><div class="exp-preview-item__company">${exp.company}</div></div><span class="exp-preview-item__type">${exp.type}</span></div>`).join('');
  initScrollReveal();
}

/* ---- Skills Preview ---- */
function renderSkillsPreview() {
  const container = document.getElementById('skills-preview-grid');
  if (!container) return;
  const skillIconMap = { html5: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg', css3: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg', javascript: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg', react: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/react/react-original.svg', vuejs: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vuejs/vuejs-original.svg', tailwindcss: 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/tailwindcss/tailwindcss-original.svg', php: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg', laravel: 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/laravel/laravel-original.svg', mysql: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg', nodejs: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nodejs/nodejs-original.svg', git: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/git/git-original.svg', docker: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/docker/docker-original.svg', figma: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/figma/figma-original.svg', python: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/python/python-original.svg', typescript: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/typescript/typescript-original.svg' };
  const skills = PortfolioData.skills.slice(0, 10);
  container.innerHTML = skills.map((skill, i) => `<div class="skill-preview-item reveal stagger-${Math.min(i + 1, 8)}"><div class="skill-preview-item__icon"><img src="${skillIconMap[skill.icon] || ''}" alt="${skill.name}" loading="lazy" /></div><span class="skill-preview-item__name">${skill.name}</span></div>`).join('');
  initScrollReveal();
}

/* ---- Projects Preview ---- */
function renderProjectsPreview() {
  const container = document.getElementById('projects-preview-grid');
  if (!container) return;
  const featured = PortfolioData.projects.filter(p => p.featured).slice(0, 3);
  container.innerHTML = featured.map((proj, i) => `<a href="project-detail.html?id=${proj.id}" class="project-preview-card reveal stagger-${i + 1}"><div class="project-preview-card__image-wrapper"><img src="${proj.thumbnail}" alt="${proj.name}" class="project-preview-card__image" loading="lazy" /><div class="project-preview-card__overlay"></div></div><div class="project-preview-card__body"><div class="project-preview-card__category">${proj.category}</div><h3 class="project-preview-card__title">${proj.name}</h3><p class="project-preview-card__desc">${proj.shortDesc}</p><div class="project-preview-card__tags tags-list">${proj.tech.map(t => `<span class="tag">${t}</span>`).join('')}</div></div></a>`).join('');
  initScrollReveal();
}
