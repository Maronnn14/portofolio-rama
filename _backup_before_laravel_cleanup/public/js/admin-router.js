/* ============================================
   ADMIN ROUTER — Hash-based SPA routing
   ============================================ */

const AdminRouter = {
  sections: {
    overview:   { title: 'Overview',    render: 'renderOverview' },
    profile:    { title: 'Profile',     render: 'renderProfile' },
    experience: { title: 'Experience',  render: 'renderExperience' },
    skills:     { title: 'Skills',      render: 'renderSkills' },
    projects:   { title: 'Projects',    render: 'renderProjects' },
    socials:    { title: 'Social Media', render: 'renderSocials' },
    messages:   { title: 'Messages',    render: 'renderMessages' },
    gallery:    { title: 'Gallery',     render: 'renderGallery' },
    appearance: { title: 'Appearance',  render: 'renderAppearance' },
    settings:   { title: 'Settings',    render: 'renderSettings' },
  },

  init() {
    window.addEventListener('hashchange', () => this.route());
    this.route();
  },

  route() {
    const hash = window.location.hash.replace('#', '') || 'overview';
    const section = this.sections[hash];

    if (!section) {
      window.location.hash = '#overview';
      return;
    }

    // Update title
    const titleEl = document.getElementById('admin-page-title');
    if (titleEl) titleEl.textContent = section.title;

    // Update sidebar active state
    document.querySelectorAll('.admin-nav__item').forEach(item => {
      item.classList.toggle('active', item.dataset.section === hash);
    });

    // Render section content
    const content = document.getElementById('admin-content');
    if (content && typeof window[section.render] === 'function') {
      content.innerHTML = '<div style="display:flex;justify-content:center;padding:4rem;"><div class="spinner" style="width:32px;height:32px;"></div></div>';
      // Small delay for loading feel
      setTimeout(() => {
        window[section.render](content);
      }, 150);
    }

    // Close mobile sidebar on navigation
    const sidebar = document.getElementById('admin-sidebar');
    if (sidebar) sidebar.classList.remove('mobile-open');
  },

  navigate(section) {
    window.location.hash = '#' + section;
  }
};

if (typeof window !== 'undefined') {
  window.AdminRouter = AdminRouter;
}
