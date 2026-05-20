/* ============================================
   DATA STORE — All Portfolio Content
   Supports localStorage persistence for admin edits
   ============================================ */

const DATA_STORE_KEY = 'portfolio_data_store';

const DEFAULT_DATA = {

  /* ---- Personal Info ---- */
  personal: {
    name: 'Rama',
    fullName: 'Rama Adin',
    role: 'Full Stack Developer',
    tagline: 'Crafting digital experiences with code & creativity',
    shortBio: 'A passionate developer who transforms ideas into elegant, functional digital solutions. With a keen eye for detail and a love for clean code, I build applications that make a difference.',
    fullBio: `I'm a Full Stack Developer based in Indonesia with a deep passion for building modern web applications. My journey in tech started with curiosity — taking things apart to understand how they work — and evolved into a career creating digital products that solve real problems.

Over the years, I've worked across the full spectrum of web development, from crafting pixel-perfect frontends to architecting robust backend systems. I believe in writing code that's not just functional, but maintainable, scalable, and elegant.

When I'm not coding, you'll find me exploring new technologies, contributing to open-source projects, or sharing knowledge with the developer community. I'm driven by the belief that great software can genuinely improve people's lives.`,
    email: 'rama@example.com',
    location: 'Indonesia',
    profileImage: 'https://picsum.photos/seed/ramaprofile/400/400',
  },

  /* ---- Social Links ---- */
  socials: [
    { name: 'GitHub', url: 'https://github.com/Maronnn14', icon: 'github' },
    { name: 'LinkedIn', url: 'https://linkedin.com/in/', icon: 'linkedin' },
    { name: 'Instagram', url: 'https://instagram.com/', icon: 'instagram' },
    { name: 'Twitter / X', url: 'https://x.com/', icon: 'twitter' },
    { name: 'YouTube', url: 'https://youtube.com/', icon: 'youtube' },
  ],

  github: {
    username: 'Maronnn14',
    profileUrl: 'https://github.com/Maronnn14',
    repos: [
      { name: 'portofolio-rama', desc: 'Personal portfolio website', stars: 2, lang: 'JavaScript' },
      { name: 'task-manager-app', desc: 'Full-stack task management tool', stars: 5, lang: 'PHP' },
      { name: 'weather-dashboard', desc: 'Real-time weather monitoring', stars: 3, lang: 'Vue' },
    ]
  },

  /* ---- Experience ---- */
  experience: [
    {
      id: 'exp-1',
      role: 'Full Stack Developer',
      company: 'Tech Solutions Inc.',
      type: 'Full-time',
      startDate: '2024',
      endDate: 'Present',
      description: 'Lead development of enterprise web applications using Laravel and Vue.js. Architected scalable REST APIs serving 10K+ daily users. Implemented CI/CD pipelines and automated testing workflows.',
      tech: ['Laravel', 'Vue.js', 'MySQL', 'Docker']
    },
    {
      id: 'exp-2',
      role: 'Frontend Developer',
      company: 'Digital Creative Agency',
      type: 'Contract',
      startDate: '2023',
      endDate: '2024',
      description: 'Built responsive, high-performance websites for diverse clients. Focused on accessibility, animation, and cross-browser compatibility. Delivered 15+ projects on time and within budget.',
      tech: ['React', 'Tailwind CSS', 'GSAP', 'Figma']
    },
    {
      id: 'exp-3',
      role: 'Web Developer Intern',
      company: 'StartUp Hub',
      type: 'Internship',
      startDate: '2022',
      endDate: '2023',
      description: 'Contributed to the development of a SaaS platform. Worked on both frontend UI components and backend API endpoints. Gained hands-on experience with agile methodologies.',
      tech: ['PHP', 'JavaScript', 'Bootstrap', 'Git']
    },
    {
      id: 'exp-4',
      role: 'Computer Science Student',
      company: 'University of Technology',
      type: 'Education',
      startDate: '2020',
      endDate: '2024',
      description: 'Bachelor\'s degree in Computer Science. Coursework in algorithms, data structures, software engineering, and database systems. Active member of the campus coding club.',
      tech: ['Python', 'Java', 'C++', 'SQL']
    },
  ],

  /* ---- Interests ---- */
  interests: [
    { name: 'Open Source', icon: '🌐', desc: 'Contributing to community-driven software projects' },
    { name: 'Photography', icon: '📷', desc: 'Capturing moments through street and landscape photography' },
    { name: 'Gaming', icon: '🎮', desc: 'Story-driven RPGs and competitive strategy games' },
    { name: 'Music', icon: '🎵', desc: 'Exploring genres from lo-fi beats to progressive rock' },
    { name: 'Reading', icon: '📚', desc: 'Tech blogs, sci-fi novels, and design thinking books' },
    { name: 'Fitness', icon: '💪', desc: 'Morning runs and calisthenics to stay sharp and focused' },
  ],

  /* ---- Skills ---- */
  skills: [
    {
      id: 'html',
      name: 'HTML5',
      category: 'Frontend',
      icon: 'html5',
      teaser: 'Semantic markup & accessibility',
      description: 'Expert in semantic HTML5, creating accessible and SEO-friendly web structures. Proficient with modern HTML APIs including Canvas, Web Components, and local storage.',
      proficiency: 95,
      level: 'Expert',
      relatedProjects: ['proj-1', 'proj-2', 'proj-3'],
      gallery: [
        'https://picsum.photos/seed/html1/600/400',
        'https://picsum.photos/seed/html2/600/400',
      ]
    },
    {
      id: 'css',
      name: 'CSS3',
      category: 'Frontend',
      icon: 'css3',
      teaser: 'Modern layouts & animations',
      description: 'Advanced CSS including Grid, Flexbox, custom properties, animations, and responsive design. Experience with CSS architectures like BEM and utility-first approaches.',
      proficiency: 90,
      level: 'Expert',
      relatedProjects: ['proj-1', 'proj-2'],
      gallery: [
        'https://picsum.photos/seed/css1/600/400',
        'https://picsum.photos/seed/css2/600/400',
      ]
    },
    {
      id: 'javascript',
      name: 'JavaScript',
      category: 'Frontend',
      icon: 'javascript',
      teaser: 'ES6+ & DOM manipulation',
      description: 'Strong command of modern JavaScript (ES6+), including async/await, modules, closures, and the event loop. Experience building complex SPAs and interactive UIs from scratch.',
      proficiency: 88,
      level: 'Advanced',
      relatedProjects: ['proj-1', 'proj-2', 'proj-3'],
      gallery: [
        'https://picsum.photos/seed/js1/600/400',
      ]
    },
    {
      id: 'react',
      name: 'React',
      category: 'Frontend',
      icon: 'react',
      teaser: 'Component-based UI development',
      description: 'Proficient in React ecosystem including hooks, context, React Router, and state management. Experience with Next.js for server-side rendering and static generation.',
      proficiency: 82,
      level: 'Advanced',
      relatedProjects: ['proj-2'],
      gallery: [
        'https://picsum.photos/seed/react1/600/400',
      ]
    },
    {
      id: 'vue',
      name: 'Vue.js',
      category: 'Frontend',
      icon: 'vuejs',
      teaser: 'Progressive framework mastery',
      description: 'Experienced with Vue 3 Composition API, Vuex/Pinia state management, and Vue Router. Built production applications with Inertia.js integration.',
      proficiency: 85,
      level: 'Advanced',
      relatedProjects: ['proj-3', 'proj-4'],
      gallery: [
        'https://picsum.photos/seed/vue1/600/400',
      ]
    },
    {
      id: 'tailwind',
      name: 'Tailwind CSS',
      category: 'Frontend',
      icon: 'tailwindcss',
      teaser: 'Utility-first CSS framework',
      description: 'Proficient in Tailwind CSS for rapid UI development. Experience customizing design systems, creating reusable component patterns, and optimizing production builds.',
      proficiency: 87,
      level: 'Advanced',
      relatedProjects: ['proj-2', 'proj-4'],
      gallery: [
        'https://picsum.photos/seed/tw1/600/400',
      ]
    },
    {
      id: 'php',
      name: 'PHP',
      category: 'Backend',
      icon: 'php',
      teaser: 'Server-side application logic',
      description: 'Strong PHP fundamentals with OOP, design patterns, and modern PHP 8 features. Experience building APIs, authentication systems, and complex business logic.',
      proficiency: 86,
      level: 'Advanced',
      relatedProjects: ['proj-3', 'proj-4', 'proj-5'],
      gallery: [
        'https://picsum.photos/seed/php1/600/400',
      ]
    },
    {
      id: 'laravel',
      name: 'Laravel',
      category: 'Backend',
      icon: 'laravel',
      teaser: 'Elegant PHP framework',
      description: 'Extensive experience with Laravel including Eloquent ORM, middleware, queues, events, and testing. Built production-grade applications with complex authorization and API layers.',
      proficiency: 90,
      level: 'Expert',
      relatedProjects: ['proj-3', 'proj-4', 'proj-5'],
      gallery: [
        'https://picsum.photos/seed/lara1/600/400',
        'https://picsum.photos/seed/lara2/600/400',
      ]
    },
    {
      id: 'mysql',
      name: 'MySQL',
      category: 'Backend',
      icon: 'mysql',
      teaser: 'Relational database design',
      description: 'Proficient in MySQL database design, query optimization, indexing strategies, and migration management. Experience with complex joins, subqueries, and stored procedures.',
      proficiency: 83,
      level: 'Advanced',
      relatedProjects: ['proj-3', 'proj-5'],
      gallery: [
        'https://picsum.photos/seed/mysql1/600/400',
      ]
    },
    {
      id: 'nodejs',
      name: 'Node.js',
      category: 'Backend',
      icon: 'nodejs',
      teaser: 'JavaScript runtime & APIs',
      description: 'Experience building REST APIs and real-time applications with Node.js and Express. Familiar with npm ecosystem, middleware patterns, and deployment strategies.',
      proficiency: 75,
      level: 'Intermediate',
      relatedProjects: ['proj-2'],
      gallery: [
        'https://picsum.photos/seed/node1/600/400',
      ]
    },
    {
      id: 'git',
      name: 'Git',
      category: 'Tools',
      icon: 'git',
      teaser: 'Version control & collaboration',
      description: 'Proficient with Git workflows including branching strategies, rebasing, conflict resolution, and collaborative development via GitHub pull requests and code reviews.',
      proficiency: 88,
      level: 'Advanced',
      relatedProjects: ['proj-1', 'proj-2', 'proj-3', 'proj-4', 'proj-5'],
      gallery: []
    },
    {
      id: 'docker',
      name: 'Docker',
      category: 'Tools',
      icon: 'docker',
      teaser: 'Containerization & deployment',
      description: 'Experience with Docker for development and production environments. Familiar with Dockerfiles, docker-compose, multi-stage builds, and container orchestration basics.',
      proficiency: 70,
      level: 'Intermediate',
      relatedProjects: ['proj-4', 'proj-5'],
      gallery: [
        'https://picsum.photos/seed/docker1/600/400',
      ]
    },
    {
      id: 'figma',
      name: 'Figma',
      category: 'Design',
      icon: 'figma',
      teaser: 'UI/UX design & prototyping',
      description: 'Proficient in Figma for UI design, prototyping, and design systems. Experience translating designs to code and collaborating with design teams.',
      proficiency: 78,
      level: 'Advanced',
      relatedProjects: ['proj-1', 'proj-2'],
      gallery: [
        'https://picsum.photos/seed/figma1/600/400',
      ]
    },
    {
      id: 'python',
      name: 'Python',
      category: 'Backend',
      icon: 'python',
      teaser: 'Scripting & automation',
      description: 'Experience with Python for scripting, automation, data processing, and basic machine learning. Familiar with Flask, pandas, and various utility libraries.',
      proficiency: 72,
      level: 'Intermediate',
      relatedProjects: ['proj-6'],
      gallery: []
    },
    {
      id: 'typescript',
      name: 'TypeScript',
      category: 'Frontend',
      icon: 'typescript',
      teaser: 'Type-safe JavaScript development',
      description: 'Growing expertise in TypeScript for building type-safe applications. Experience with interfaces, generics, and integrating TypeScript into React and Node.js projects.',
      proficiency: 73,
      level: 'Intermediate',
      relatedProjects: ['proj-2'],
      gallery: []
    },
  ],

  /* ---- Projects ---- */
  projects: [
    {
      id: 'proj-1',
      name: 'Portfolio Website',
      category: 'Frontend',
      shortDesc: 'A stunning personal portfolio with dark editorial design',
      fullDesc: 'A meticulously crafted personal portfolio website featuring a dark editorial luxury aesthetic. Built with vanilla HTML, CSS, and JavaScript, it showcases advanced CSS animations, a full CRUD message board, lightbox galleries, and responsive design across all breakpoints. Every detail — from typography to micro-interactions — is intentionally designed to create a memorable visitor experience.',
      thumbnail: 'https://picsum.photos/seed/portfolio/800/500',
      tech: ['HTML5', 'CSS3', 'JavaScript'],
      gallery: [
        'https://picsum.photos/seed/port1/800/500',
        'https://picsum.photos/seed/port2/800/500',
        'https://picsum.photos/seed/port3/800/500',
      ],
      liveUrl: '#',
      sourceUrl: 'https://github.com/Maronnn14/portofolio-rama',
      featured: true,
    },
    {
      id: 'proj-2',
      name: 'E-Commerce Platform',
      category: 'Full Stack',
      shortDesc: 'Modern online store with real-time inventory',
      fullDesc: 'A full-featured e-commerce platform built with React and Node.js. Features include user authentication, product catalog with search and filtering, shopping cart with persistent state, Stripe payment integration, order tracking, and an admin dashboard for inventory management. Designed for scalability with a microservices-inspired backend architecture.',
      thumbnail: 'https://picsum.photos/seed/ecommerce/800/500',
      tech: ['React', 'Node.js', 'MongoDB', 'Tailwind CSS'],
      gallery: [
        'https://picsum.photos/seed/ecom1/800/500',
        'https://picsum.photos/seed/ecom2/800/500',
        'https://picsum.photos/seed/ecom3/800/500',
      ],
      liveUrl: '#',
      sourceUrl: '#',
      featured: true,
    },
    {
      id: 'proj-3',
      name: 'Exam Management System',
      category: 'Full Stack',
      shortDesc: 'Automated online examination platform for educators',
      fullDesc: 'A comprehensive exam management system built with Laravel and Vue.js via Inertia.js. Features include question bank management, timed exam sessions, auto-grading for multiple choice and essay questions, real-time violation monitoring, student dashboards, and detailed score reporting with Excel export. Used in production by educational institutions.',
      thumbnail: 'https://picsum.photos/seed/examsys/800/500',
      tech: ['Laravel', 'Vue.js', 'MySQL', 'Inertia.js'],
      gallery: [
        'https://picsum.photos/seed/exam1/800/500',
        'https://picsum.photos/seed/exam2/800/500',
        'https://picsum.photos/seed/exam3/800/500',
        'https://picsum.photos/seed/exam4/800/500',
      ],
      liveUrl: '#',
      sourceUrl: '#',
      featured: true,
    },
    {
      id: 'proj-4',
      name: 'Task Manager Pro',
      category: 'Full Stack',
      shortDesc: 'Collaborative task management with real-time updates',
      fullDesc: 'A collaborative task management application inspired by Trello and Notion. Built with Laravel backend and Vue.js frontend, featuring drag-and-drop task boards, real-time collaboration via WebSockets, team workspaces, task assignments, due date tracking, file attachments, and activity logs. Containerized with Docker for easy deployment.',
      thumbnail: 'https://picsum.photos/seed/taskman/800/500',
      tech: ['Laravel', 'Vue.js', 'Tailwind CSS', 'Docker'],
      gallery: [
        'https://picsum.photos/seed/task1/800/500',
        'https://picsum.photos/seed/task2/800/500',
      ],
      liveUrl: '#',
      sourceUrl: '#',
      featured: false,
    },
    {
      id: 'proj-5',
      name: 'API Gateway Service',
      category: 'Backend',
      shortDesc: 'Centralized API gateway with rate limiting & auth',
      fullDesc: 'A centralized API gateway service built with Laravel that handles authentication, rate limiting, request routing, and API versioning for a microservices architecture. Features include JWT-based auth, role-based access control, request/response logging, health checks, and automated API documentation generation.',
      thumbnail: 'https://picsum.photos/seed/apigate/800/500',
      tech: ['Laravel', 'PHP', 'MySQL', 'Docker'],
      gallery: [
        'https://picsum.photos/seed/api1/800/500',
        'https://picsum.photos/seed/api2/800/500',
      ],
      liveUrl: null,
      sourceUrl: '#',
      featured: false,
    },
    {
      id: 'proj-6',
      name: 'Weather Dashboard',
      category: 'Frontend',
      shortDesc: 'Real-time weather monitoring with interactive charts',
      fullDesc: 'A beautiful weather dashboard that displays real-time weather data with interactive charts and maps. Built with vanilla JavaScript, it fetches data from the OpenWeather API and presents it with smooth animations and a clean, intuitive interface. Features include 7-day forecasts, hourly breakdowns, location search, and weather alerts.',
      thumbnail: 'https://picsum.photos/seed/weather/800/500',
      tech: ['JavaScript', 'Chart.js', 'CSS3', 'REST API'],
      gallery: [
        'https://picsum.photos/seed/weath1/800/500',
        'https://picsum.photos/seed/weath2/800/500',
      ],
      liveUrl: '#',
      sourceUrl: '#',
      featured: false,
    },
  ],

  /* ---- Gallery Images (for random gallery on home) ---- */
  galleryImages: [
    { src: 'https://picsum.photos/seed/gal1/600/400', alt: 'Creative workspace setup' },
    { src: 'https://picsum.photos/seed/gal2/400/600', alt: 'Code on screen' },
    { src: 'https://picsum.photos/seed/gal3/600/400', alt: 'Team collaboration' },
    { src: 'https://picsum.photos/seed/gal4/500/500', alt: 'Design mockup on tablet' },
    { src: 'https://picsum.photos/seed/gal5/600/400', alt: 'Coffee and laptop' },
    { src: 'https://picsum.photos/seed/gal6/400/600', alt: 'Urban architecture' },
    { src: 'https://picsum.photos/seed/gal7/600/400', alt: 'Nature landscape' },
    { src: 'https://picsum.photos/seed/gal8/500/500', alt: 'Tech conference' },
    { src: 'https://picsum.photos/seed/gal9/600/400', alt: 'Sunset view' },
    { src: 'https://picsum.photos/seed/gal10/400/600', alt: 'Night city lights' },
    { src: 'https://picsum.photos/seed/gal11/600/400', alt: 'Mountain trail' },
    { src: 'https://picsum.photos/seed/gal12/500/500', alt: 'Studio headphones' },
  ],

  /* ---- Placeholder Messages (pre-seeded) ---- */
  seedMessages: [
    {
      name: 'Sarah Chen',
      message: 'Amazing portfolio! The dark theme with gold accents is really elegant. Love the attention to detail in every section. Keep up the great work! 🔥',
      rating: 5,
      timestamp: Date.now() - 86400000 * 3,
    },
    {
      name: 'Marco Rivera',
      message: 'Really impressed by the exam management system project. The auto-grading feature sounds incredibly useful for educators. Would love to see a demo!',
      rating: 4,
      timestamp: Date.now() - 86400000 * 7,
    },
    {
      name: 'Aiko Tanaka',
      message: 'Clean code, beautiful design, and great project selection. This portfolio shows real skill and passion. Bookmarked for inspiration! ✨',
      rating: 5,
      timestamp: Date.now() - 86400000 * 14,
    },
    {
      name: 'David Okonkwo',
      message: 'The message board feature is a really nice touch — adds a personal, community feel to the site. Smart implementation with localStorage too.',
      rating: 4,
      timestamp: Date.now() - 86400000 * 21,
    },
  ],
};

/* ============================================
   DataStore — localStorage Read/Write Layer
   ============================================ */

const DataStore = {
  _cache: null,

  /* Get all data (localStorage overrides defaults) */
  getData() {
    if (this._cache) return this._cache;
    try {
      const stored = localStorage.getItem(DATA_STORE_KEY);
      if (stored) {
        const parsed = JSON.parse(stored);
        // Deep merge: stored data overrides defaults
        this._cache = this._deepMerge(JSON.parse(JSON.stringify(DEFAULT_DATA)), parsed);
      } else {
        this._cache = JSON.parse(JSON.stringify(DEFAULT_DATA));
      }
    } catch {
      this._cache = JSON.parse(JSON.stringify(DEFAULT_DATA));
    }
    // Normalize: admin saves uploaded photo to `personal.photo`, unify into `profileImage`
    if (this._cache.personal) {
      if (this._cache.personal.photo) {
        this._cache.personal.profileImage = this._cache.personal.photo;
      }
    }
    return this._cache;
  },

  /* Get a specific section */
  getSection(key) {
    return this.getData()[key];
  },

  /* Save a specific section */
  saveSection(key, data) {
    const all = this.getData();
    all[key] = data;
    this._cache = all;
    this._persist();
  },

  /* Save entire dataset */
  saveAll(data) {
    this._cache = data;
    this._persist();
  },

  /* Reset to defaults (clear localStorage) */
  reset() {
    localStorage.removeItem(DATA_STORE_KEY);
    this._cache = null;
  },

  /* Export as JSON string */
  export() {
    return JSON.stringify(this.getData(), null, 2);
  },

  /* Import from JSON string */
  import(jsonString) {
    try {
      const data = JSON.parse(jsonString);
      this.saveAll(data);
      return true;
    } catch {
      return false;
    }
  },

  /* Invalidate cache (forces re-read from localStorage) */
  invalidate() {
    this._cache = null;
  },

  /* Persist cache to localStorage */
  _persist() {
    try {
      localStorage.setItem(DATA_STORE_KEY, JSON.stringify(this._cache));
    } catch (e) {
      console.error('DataStore: Failed to save to localStorage', e);
    }
  },

  /* Deep merge utility */
  _deepMerge(target, source) {
    for (const key of Object.keys(source)) {
      if (source[key] && typeof source[key] === 'object' && !Array.isArray(source[key])
          && target[key] && typeof target[key] === 'object' && !Array.isArray(target[key])) {
        this._deepMerge(target[key], source[key]);
      } else {
        target[key] = source[key];
      }
    }
    return target;
  }
};

/* PORTFOLIO_DATA — live reference used by all public pages */
const PORTFOLIO_DATA = DataStore.getData();

/* Make available globally */
if (typeof window !== 'undefined') {
  window.PORTFOLIO_DATA = PORTFOLIO_DATA;
  window.DEFAULT_DATA = DEFAULT_DATA;
  window.DataStore = DataStore;
}
