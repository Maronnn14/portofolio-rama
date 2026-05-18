/* ============================================
   SKILLS.JS — Filtering & Detail Modal
   ============================================ */

document.addEventListener('DOMContentLoaded', () => {
  renderSkillsGrid();
  bindFilterTabs();
});

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

/* ---- Render Skills Grid ---- */
function renderSkillsGrid() {
  const container = document.getElementById('skills-grid');
  if (!container) return;

  container.innerHTML = PORTFOLIO_DATA.skills.map((skill, i) => `
    <div class="skill-card reveal stagger-${Math.min((i % 8) + 1, 8)}" 
         data-category="${skill.category}" 
         data-skill-id="${skill.id}"
         onclick="openSkillModal('${skill.id}')">
      <div class="skill-card__header">
        <div class="skill-card__icon">
          <img src="${skillIconMap[skill.icon] || ''}" alt="${skill.name}" loading="lazy" />
        </div>
        <div>
          <div class="skill-card__name">${skill.name}</div>
          <div class="skill-card__category">${skill.category}</div>
        </div>
      </div>
      <p class="skill-card__teaser">${skill.teaser}</p>
      <span class="skill-card__arrow">→</span>
    </div>
  `).join('');

  initScrollReveal();
}

/* ---- Filter Tabs ---- */
function bindFilterTabs() {
  const tabs = document.querySelectorAll('#skill-filter-tabs .filter-tab');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      // Update active state
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      const filter = tab.dataset.filter;
      filterSkills(filter);
    });
  });
}

function filterSkills(category) {
  const cards = document.querySelectorAll('.skill-card');
  cards.forEach(card => {
    if (category === 'all' || card.dataset.category === category) {
      card.classList.remove('hidden');
      card.style.animation = 'fadeInUp 0.4s var(--ease-out) forwards';
    } else {
      card.classList.add('hidden');
    }
  });
}

/* ---- Skill Detail Modal ---- */
function openSkillModal(skillId) {
  const skill = PORTFOLIO_DATA.skills.find(s => s.id === skillId);
  if (!skill) return;

  const modal = document.getElementById('skill-modal');
  const content = document.getElementById('skill-modal-content');

  // Find related projects
  const relatedProjects = (skill.relatedProjects || [])
    .map(pid => PORTFOLIO_DATA.projects.find(p => p.id === pid))
    .filter(Boolean);

  content.innerHTML = `
    <button class="skill-modal__close" onclick="closeSkillModal()" aria-label="Close">&times;</button>
    
    <div class="skill-modal__header">
      <div class="skill-modal__icon">
        <img src="${skillIconMap[skill.icon] || ''}" alt="${skill.name}" />
      </div>
      <div>
        <h2 class="skill-modal__title">${skill.name}</h2>
        <span class="tag tag--accent skill-modal__category-tag">${skill.category}</span>
      </div>
    </div>

    <div class="skill-modal__section">
      <h3 class="skill-modal__section-title">Description</h3>
      <p class="skill-modal__desc">${skill.description}</p>
    </div>

    <div class="skill-modal__section">
      <h3 class="skill-modal__section-title">Proficiency Level</h3>
      <div class="proficiency-bar">
        <div class="proficiency-bar__fill" id="proficiency-fill" style="width: 0%"></div>
      </div>
      <div class="proficiency-info">
        <span class="proficiency-info__level">${skill.level}</span>
        <span class="proficiency-info__percent">${skill.proficiency}%</span>
      </div>
    </div>

    ${relatedProjects.length > 0 ? `
    <div class="skill-modal__section">
      <h3 class="skill-modal__section-title">Related Projects</h3>
      <div class="skill-modal__projects">
        ${relatedProjects.map(proj => `
          <a href="project-detail.html?id=${proj.id}" class="skill-modal__project-link">
            <div>
              <div class="skill-modal__project-name">${proj.name}</div>
              <div class="skill-modal__project-desc">${proj.shortDesc}</div>
            </div>
          </a>
        `).join('')}
      </div>
    </div>
    ` : ''}

    ${skill.gallery && skill.gallery.length > 0 ? `
    <div class="skill-modal__section">
      <h3 class="skill-modal__section-title">Gallery</h3>
      <div class="skill-modal__gallery" id="skill-gallery-${skill.id}">
        ${skill.gallery.map((img, i) => `
          <div class="skill-modal__gallery-item" data-lightbox="${img}">
            <img src="${img}" alt="${skill.name} work example ${i + 1}" loading="lazy" />
          </div>
        `).join('')}
      </div>
    </div>
    ` : ''}
  `;

  // Open modal
  modal.classList.add('open');
  document.body.style.overflow = 'hidden';

  // Animate proficiency bar
  setTimeout(() => {
    const fill = document.getElementById('proficiency-fill');
    if (fill) fill.style.width = skill.proficiency + '%';
  }, 300);

  // Bind lightbox to gallery
  if (skill.gallery && skill.gallery.length > 0) {
    const galleryItems = content.querySelectorAll(`#skill-gallery-${skill.id} .skill-modal__gallery-item`);
    const galleryImages = skill.gallery.map(src => ({ src, alt: skill.name }));
    galleryItems.forEach((item, index) => {
      item.addEventListener('click', () => {
        lightbox.open(galleryImages, index);
      });
    });
  }
}

function closeSkillModal() {
  const modal = document.getElementById('skill-modal');
  modal.classList.remove('open');
  document.body.style.overflow = '';
}

// Close on overlay click
document.addEventListener('DOMContentLoaded', () => {
  const overlay = document.getElementById('skill-modal-overlay');
  if (overlay) {
    overlay.addEventListener('click', closeSkillModal);
  }

  // Close on Escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeSkillModal();
  });
});
