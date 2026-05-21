/* ============================================
   DATA LAYER — Loads portfolio data from API
   Replaces the old localStorage-based DataStore
   ============================================ */

const PortfolioData = {
  personal: {},
  socials: [],
  experience: [],
  interests: [],
  skills: [],
  projects: [],
  galleryImages: [],
  messages: [],
  github: {
    username: 'Maronnn14',
    profileUrl: 'https://github.com/Maronnn14',
    repos: [
      { name: 'portofolio-rama', desc: 'Personal portfolio website', stars: 2, lang: 'JavaScript' },
      { name: 'task-manager-app', desc: 'Full-stack task management tool', stars: 5, lang: 'PHP' },
      { name: 'weather-dashboard', desc: 'Real-time weather monitoring', stars: 3, lang: 'Vue' },
    ]
  },

  _loaded: false,

  /**
   * Load all data from the API. Called once on page load.
   */
  async load(force = false) {
    if (this._loaded && !force) return;

    try {
      const [personal, socials, experience, interests, skills, projects, gallery, messages] = await Promise.all([
        API.personalInfo.get(),
        API.socials.list(),
        API.experiences.list(),
        API.interests.list(),
        API.skills.list(),
        API.projects.list(),
        API.gallery.list(),
        API.messages.list(),
      ]);

      this.personal = {
        name: personal.nickname || personal.name || 'Rama',
        nickname: personal.nickname || personal.name || 'Rama',
        fullName: personal.fullName || 'Rama Adin',
        role: personal.role || 'Full Stack Developer',
        tagline: personal.tagline || '',
        shortBio: personal.shortBio || personal.bio || '',
        fullBio: personal.fullBio || personal.story || '',
        email: personal.email || '',
        location: personal.location || '',
        profileImage: personal.profileImage || '',
        photo: personal.photo || '',
        homeProfileImage: personal.homeProfileImage || personal.photo || personal.profileImage || '',
        aboutProfileImage: personal.aboutProfileImage || personal.photo || personal.profileImage || '',
        bio: personal.shortBio || personal.bio || '',
        story: personal.fullBio || personal.story || '',
        github: personal.github || 'Maronnn14',
      };

      if (personal.github) {
        this.github.username = personal.github;
        this.github.profileUrl = `https://github.com/${personal.github}`;
      }

      this.socials = socials.map(s => ({
        name: s.platform,
        url: s.url,
        icon: s.icon || s.platform.toLowerCase(),
        label: s.label || '',
        platform: s.platform,
        visible: s.visible !== false,
        id: s.id,
      }));

      this.experience = experience.map(e => ({
        id: e.id,
        role: e.role,
        company: e.company,
        location: e.location || '',
        type: e.type || 'Work',
        startDate: e.start_date,
        endDate: e.end_date || 'Present',
        description: e.description || '',
        tech: e.tech || [],
      }));

      this.interests = interests.map(i => ({
        id: i.id,
        name: i.name,
        icon: i.icon || '',
        desc: i.description || '',
      }));

      this.skills = skills.map(s => ({
        id: s.id,
        name: s.name,
        category: s.category || 'Frontend',
        icon: s.icon || '',
        teaser: s.teaser || '',
        description: s.description || '',
        proficiency: s.proficiency || 50,
        level: s.level || 'Intermediate',
        relatedProjects: s.related_projects || [],
        gallery: s.gallery || [],
        shortDesc: s.teaser || '',
      }));

      this.projects = projects.map(p => ({
        id: p.id,
        name: p.name,
        category: p.category || 'Frontend',
        shortDesc: p.short_desc || '',
        fullDesc: p.full_desc || '',
        description: p.full_desc || '',
        thumbnail: p.thumbnail || '',
        tech: p.tech || [],
        gallery: p.gallery || [],
        liveUrl: p.live_url || '',
        sourceUrl: p.source_url || '',
        repoUrl: p.source_url || '',
        featured: p.featured || false,
        status: p.status || 'published',
      }));

      this.galleryImages = gallery.map(g => ({
        id: g.id,
        src: g.url,
        url: g.url,
        alt: g.alt || '',
        category: g.category || '',
        visible: g.visible !== false,
      }));

      this.messages = messages;

      this._loaded = true;
    } catch (err) {
      console.error('PortfolioData: Failed to load from API', err);
    }
  },

  invalidate() {
    this._loaded = false;
  },
};

/* Legacy compatibility — PORTFOLIO_DATA points to PortfolioData */
const PORTFOLIO_DATA = PortfolioData;

/* Clean up stale localStorage keys from the old architecture */
(function cleanupLegacyStorage() {
  try {
    localStorage.removeItem('portfolio_data_store');
    localStorage.removeItem('portfolio_messages');
    localStorage.removeItem('portfolio_mb_settings');
    localStorage.removeItem('portfolio_site_settings');
  } catch { /* ignore */ }
})();

/* Make available globally */
if (typeof window !== 'undefined') {
  window.PORTFOLIO_DATA = PORTFOLIO_DATA;
  window.PortfolioData = PortfolioData;
}
