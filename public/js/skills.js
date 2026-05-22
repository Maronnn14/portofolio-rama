/* ============================================
   SKILLS.JS — Filtering & Detail Modal (SSR grid)
   ============================================ */

document.addEventListener('DOMContentLoaded', async () => {
  await PortfolioData.load();
  bindFilterTabs();
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

function bindFilterTabs() {
  document.querySelectorAll('#skill-filter-tabs .filter-tab').forEach(tab => {
    tab.addEventListener('click', () => { document.querySelectorAll('#skill-filter-tabs .filter-tab').forEach(t => t.classList.remove('active')); tab.classList.add('active'); filterSkills(tab.dataset.filter); });
  });
}

function filterSkills(cat) {
  document.querySelectorAll('.skill-card').forEach(card => {
    if (cat==='all'||card.dataset.category===cat) { card.classList.remove('hidden'); card.style.animation='fadeInUp 0.4s var(--ease-out) forwards'; } else card.classList.add('hidden');
  });
}

function openSkillModal(skillId) {
  const s = PortfolioData.skills.find(x => String(x.id)===String(skillId)); if (!s) return;
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
