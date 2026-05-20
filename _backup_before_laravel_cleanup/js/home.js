/* ============================================
   HOME.JS — Hero animations & preview sections
   ============================================ */

document.addEventListener('DOMContentLoaded', () => {
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
    if (i < text.length) {
      element.innerHTML = text.substring(0, i + 1) + '<span class="cursor"></span>';
      i++;
      setTimeout(type, speed);
    } else {
      // Remove cursor after typing
      setTimeout(() => {
        element.innerHTML = text;
        if (callback) callback();
      }, 1000);
    }
  }
  type();
}

/* ---- Hero Animations ---- */
function initHeroAnimations() {
  const greetingEl = document.getElementById('hero-greeting');
  const roleEl = document.getElementById('hero-role');

  if (greetingEl) {
    setTimeout(() => {
      typeWriter(greetingEl, "Hi there, I'm", 60, () => {
        if (roleEl) {
          const roleText = (PORTFOLIO_DATA.personal && PORTFOLIO_DATA.personal.role)
            ? `> ${PORTFOLIO_DATA.personal.role}`
            : '> Full Stack Developer';
          typeWriter(roleEl, roleText, 40);
        }
      });
    }, 500);
  }

  // Stagger entrance for hero elements
  const heroElements = document.querySelectorAll('.hero__label, .hero__title, .hero__tagline, .hero__cta, .hero__scroll-indicator');
  heroElements.forEach((el, i) => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    setTimeout(() => {
      el.style.transition = `opacity 0.6s var(--ease-out), transform 0.6s var(--ease-out)`;
      el.style.opacity = '1';
      el.style.transform = 'translateY(0)';
    }, 300 + i * 150);
  });
}

/* ---- Experience Preview (3 most recent) ---- */
function renderExperiencePreview() {
  const container = document.getElementById('experience-preview-list');
  if (!container) return;

  const items = PORTFOLIO_DATA.experience.slice(0, 3);

  container.innerHTML = items.map((exp, i) => `
    <div class="exp-preview-item reveal stagger-${i + 1}">
      <span class="exp-preview-item__date">${exp.startDate} — ${exp.endDate}</span>
      <div>
        <div class="exp-preview-item__role">${exp.role}</div>
        <div class="exp-preview-item__company">${exp.company}</div>
      </div>
      <span class="exp-preview-item__type">${exp.type}</span>
    </div>
  `).join('');

  // Re-init scroll reveal for dynamically added elements
  initScrollReveal();
}

/* ---- Skills Preview (top 10) ---- */
function renderSkillsPreview() {
  const container = document.getElementById('skills-preview-grid');
  if (!container) return;

  const skillIconMap = {
    html5: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg',
    css3: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg',
    javascript: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg',
    react: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/react/react-original.svg',
    vuejs: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vuejs/vuejs-original.svg',
    tailwindcss: 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/tailwindcss/tailwindcss-original.svg',
    php: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg',
    laravel: 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/laravel/laravel-original.svg',
    mysql: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg',
    nodejs: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nodejs/nodejs-original.svg',
    git: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/git/git-original.svg',
    docker: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/docker/docker-original.svg',
    figma: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/figma/figma-original.svg',
    python: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/python/python-original.svg',
    typescript: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/typescript/typescript-original.svg',
  };

  const skills = PORTFOLIO_DATA.skills.slice(0, 10);

  container.innerHTML = skills.map((skill, i) => `
    <div class="skill-preview-item reveal stagger-${Math.min(i + 1, 8)}">
      <div class="skill-preview-item__icon">
        <img src="${skillIconMap[skill.icon] || ''}" alt="${skill.name}" loading="lazy" />
      </div>
      <span class="skill-preview-item__name">${skill.name}</span>
    </div>
  `).join('');

  initScrollReveal();
}

/* ---- Projects Preview (featured only) ---- */
function renderProjectsPreview() {
  const container = document.getElementById('projects-preview-grid');
  if (!container) return;

  const featured = PORTFOLIO_DATA.projects.filter(p => p.featured).slice(0, 3);

  container.innerHTML = featured.map((proj, i) => `
    <a href="project-detail.html?id=${proj.id}" class="project-preview-card reveal stagger-${i + 1}">
      <div class="project-preview-card__image-wrapper">
        <img src="${proj.thumbnail}" alt="${proj.name}" class="project-preview-card__image" loading="lazy" />
        <div class="project-preview-card__overlay"></div>
      </div>
      <div class="project-preview-card__body">
        <div class="project-preview-card__category">${proj.category}</div>
        <h3 class="project-preview-card__title">${proj.name}</h3>
        <p class="project-preview-card__desc">${proj.shortDesc}</p>
        <div class="project-preview-card__tags tags-list">
          ${proj.tech.map(t => `<span class="tag">${t}</span>`).join('')}
        </div>
      </div>
    </a>
  `).join('');

  initScrollReveal();
}
