/* ============================================
   SKILLS.JS — Filtering & Detail Modal (SSR grid)
   ============================================ */

// The grids are server-rendered, so filtering must not wait for the API
// payload (it loads skill details for the modal only).
document.addEventListener('DOMContentLoaded', () => {
  bindFilterTabs();
  bindViewToggle();
  applySkillFilters();
  PortfolioData.load().catch(() => {});
});

function _escapeHtml(text) {
  const d = document.createElement('div');
  d.textContent = text;
  return d.innerHTML;
}

const skillIconMap = { html5:'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg', css3:'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg', javascript:'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg', react:'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/react/react-original.svg', vuejs:'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vuejs/vuejs-original.svg', tailwindcss:'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/tailwindcss/tailwindcss-original.svg', php:'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg', laravel:'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/laravel/laravel-original.svg', mysql:'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg', nodejs:'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nodejs/nodejs-original.svg', git:'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/git/git-original.svg', docker:'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/docker/docker-original.svg', figma:'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/figma/figma-original.svg', python:'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/python/python-original.svg', typescript:'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/typescript/typescript-original.svg', bootstrap:'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/bootstrap/bootstrap-original.svg' };

function renderSkillsGrid() {
  const c = document.getElementById('skills-grid'); if (!c) return;
  c.innerHTML = PortfolioData.skills.map((s,i) => `<div class="skill-card reveal stagger-${Math.min((i%8)+1,8)}" data-category="${s.category}" data-skill-id="${s.id}" onclick="openSkillModal('${s.id}')"><div class="skill-card__header"><div class="skill-card__icon"><img src="${skillIconMap[s.icon]||''}" alt="${s.name}" loading="lazy" /></div><div><div class="skill-card__name">${s.name}</div><div class="skill-card__category">${s.category}</div></div></div><p class="skill-card__teaser">${s.teaser}</p><span class="skill-card__arrow">→</span></div>`).join('');
  initScrollReveal();
}

let skillView = 'all';
let skillCategory = 'all';

// Views: 'all' | 'tech' | 'other' | 'soft'. 'hard' is kept as an alias of 'tech'.
const normalizeView = (view) => (view === 'hard' ? 'tech' : view);

function bindFilterTabs() {
  document.querySelectorAll('#skill-filter-tabs .filter-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('#skill-filter-tabs .filter-tab').forEach(t => {
        t.classList.remove('active');
        t.setAttribute('aria-selected', 'false');
      });
      tab.classList.add('active');
      tab.setAttribute('aria-selected', 'true');
      skillCategory = tab.dataset.filter;

      // Categories only exist for tech stack skills, so switch to that view.
      if (skillView !== 'tech') setSkillView('tech');
      else applySkillFilters();
    });
  });
}

function bindViewToggle() {
  document.querySelectorAll('#skill-view-toggle .skill-view-toggle__btn').forEach(btn => {
    btn.addEventListener('click', () => setSkillView(btn.dataset.view));
  });
}

function setSkillView(view) {
  skillView = normalizeView(view);

  document.querySelectorAll('#skill-view-toggle .skill-view-toggle__btn').forEach(btn => {
    const active = normalizeView(btn.dataset.view) === skillView;
    btn.classList.toggle('active', active);
    btn.setAttribute('aria-selected', String(active));
  });

  if (skillView !== 'tech') {
    // Category tabs belong to the tech stack view only.
    skillCategory = 'all';
    document.querySelectorAll('#skill-filter-tabs .filter-tab').forEach(tab => {
      const isAll = tab.dataset.filter === 'all';
      tab.classList.toggle('active', isAll);
      tab.setAttribute('aria-selected', String(isAll));
    });
  }

  applySkillFilters();
}

function applySkillFilters() {
  const techWrapper = document.getElementById('skills-groups');
  const otherSection = document.getElementById('other-skills-section');
  const softSection = document.getElementById('soft-skills-section');
  const tabs = document.getElementById('skill-filter-tabs');

  const showTech = skillView === 'all' || skillView === 'tech';
  const showOther = skillView === 'all' || skillView === 'other';
  const showSoft = skillView === 'all' || skillView === 'soft';

  if (techWrapper) techWrapper.classList.toggle('hidden', !showTech);
  if (otherSection) otherSection.classList.toggle('hidden', !showOther);
  if (softSection) softSection.classList.toggle('hidden', !showSoft);

  // Sections that were hidden never got their scroll-reveal pass, so reveal
  // them now instead of showing blank space.
  [showTech && techWrapper, showOther && otherSection, showSoft && softSection]
    .filter(Boolean)
    .forEach(section => section
      .querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale')
      .forEach(node => node.classList.add('revealed')));
  // Category tabs only make sense when the tech stack grid is on screen.
  if (tabs) tabs.classList.toggle('hidden', !showTech);

  let techCount = 0;

  if (showTech) {
    document.querySelectorAll('#skills-groups .skill-card').forEach(card => {
      const match = skillCategory === 'all' || card.dataset.category === skillCategory;
      card.classList.toggle('hidden', !match);
      if (match) techCount++;
    });

    document.querySelectorAll('[data-skill-group]').forEach(group => {
      const visible = Array.from(group.querySelectorAll('.skill-card')).some(card => !card.classList.contains('hidden'));
      group.classList.toggle('hidden', !visible);
    });
  }

  const otherCount = document.querySelectorAll('#other-skills-section .skill-card').length;
  const softCount = document.querySelectorAll('#soft-skills-section .soft-skill-card').length;
  const hint = document.getElementById('skills-view-hint');

  if (hint) {
    const plural = (n, word) => `${n} ${word}${n === 1 ? '' : 's'}`;

    if (skillView === 'soft') {
      hint.textContent = `Showing ${plural(softCount, 'soft skill')} — the human side of shipping good work.`;
    } else if (skillView === 'other') {
      hint.textContent = `Showing ${plural(otherCount, 'other hard skill')} — spreadsheets, design, media, and daily work tools.`;
    } else if (skillView === 'tech') {
      hint.textContent = `Showing ${plural(techCount, 'tech stack skill')}${skillCategory !== 'all' ? ' in ' + skillCategory : ''}.`;
    } else {
      hint.textContent = `Showing ${plural(techCount + otherCount, 'hard skill')} and ${plural(softCount, 'soft skill')}.`;
    }
  }
}

function filterSkills(cat) {
  skillCategory = cat;
  applySkillFilters();
}

async function openSkillModal(skillId) {
  let s = PortfolioData.skills.find(x => String(x.id)===String(skillId));
  if (!s) {
    // First click before the background load finished.
    try { await PortfolioData.load(); } catch { return; }
    s = PortfolioData.skills.find(x => String(x.id)===String(skillId));
  }
  if (!s) return;
  const modal = document.getElementById('skill-modal'), content = document.getElementById('skill-modal-content');
  const pl = s.projectLinks || [];
  const gi = s.galleryItems || [];
  content.innerHTML = `<button class="skill-modal__close" onclick="closeSkillModal()">&times;</button><div class="skill-modal__header"><div class="skill-modal__icon"><img src="${skillIconMap[s.icon]||''}" alt="${s.name}" /></div><div><h2 class="skill-modal__title">${s.name}</h2><span class="tag tag--accent">${s.category}</span></div></div><div class="skill-modal__section"><h3 class="skill-modal__section-title">Description</h3><p class="skill-modal__desc">${s.description}</p></div><div class="skill-modal__section"><h3 class="skill-modal__section-title">Proficiency</h3><div class="proficiency-bar"><div class="proficiency-bar__fill" id="proficiency-fill" style="width:0%"></div></div><div class="proficiency-info"><span class="proficiency-info__level">${s.level}</span><span class="proficiency-info__percent">${s.proficiency}%</span></div></div>${pl.length?`<div class="skill-modal__section"><h3 class="skill-modal__section-title">Related Projects</h3><div class="skill-modal__projects">${pl.map(l=>`<a href="${l.url}" target="_blank" rel="noopener noreferrer" class="skill-modal__project-link"><div><div class="skill-modal__project-name">${_escapeHtml(l.label)}</div>${l.description?`<div class="skill-modal__project-desc">${_escapeHtml(l.description)}</div>`:''}</div></a>`).join('')}</div></div>`:''}${gi.length?`<div class="skill-modal__section"><h3 class="skill-modal__section-title">Gallery</h3><div class="skill-modal__gallery" id="skill-gallery-${s.id}">${gi.map((img,i)=>`<div class="skill-modal__gallery-item" data-lightbox="${img.image_url}"><img src="${img.image_url}" alt="${s.name} ${i+1}" loading="lazy" />${img.caption?`<span class="skill-modal__gallery-caption">${_escapeHtml(img.caption)}</span>`:''}</div>`).join('')}</div></div>`:''}`;
  modal.classList.add('open'); document.body.style.overflow='hidden';
  setTimeout(() => { const f=document.getElementById('proficiency-fill'); if(f) f.style.width=s.proficiency+'%'; }, 300);
  if(gi.length) { const gItems=content.querySelectorAll(`#skill-gallery-${s.id} .skill-modal__gallery-item`); const gimg=gi.map(item=>({src:item.image_url,alt:item.caption||s.name})); gItems.forEach((item,idx)=>{ item.addEventListener('click',()=>{ lightbox.open(gimg,idx); }); }); }
}

function closeSkillModal() { document.getElementById('skill-modal').classList.remove('open'); document.body.style.overflow=''; }

document.addEventListener('DOMContentLoaded', () => {
  const o = document.getElementById('skill-modal-overlay');
  if(o) o.addEventListener('click', closeSkillModal);
  document.addEventListener('keydown', e => { if(e.key==='Escape') closeSkillModal(); });
});
