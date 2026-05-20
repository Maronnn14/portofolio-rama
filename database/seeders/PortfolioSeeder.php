<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\GalleryImage;
use App\Models\Interest;
use App\Models\PersonalInfo;
use App\Models\PortfolioMessage;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Social;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPersonalInfo();
        $this->seedSocials();
        $this->seedExperiences();
        $this->seedInterests();
        $this->seedSkills();
        $this->seedProjects();
        $this->seedGalleryImages();
        $this->seedMessages();
    }

    private function seedPersonalInfo(): void
    {
        $info = [
            'name' => 'Rama',
            'fullName' => 'Rama Adin',
            'role' => 'Full Stack Developer',
            'tagline' => 'Crafting digital experiences with code & creativity',
            'shortBio' => 'A passionate developer who transforms ideas into elegant, functional digital solutions. With a keen eye for detail and a love for clean code, I build applications that make a difference.',
            'fullBio' => "I'm a Full Stack Developer based in Indonesia with a deep passion for building modern web applications. My journey in tech started with curiosity — taking things apart to understand how they work — and evolved into a career creating digital products that solve real problems.\n\nOver the years, I've worked across the full spectrum of web development, from crafting pixel-perfect frontends to architecting robust backend systems. I believe in writing code that's not just functional, but maintainable, scalable, and elegant.\n\nWhen I'm not coding, you'll find me exploring new technologies, contributing to open-source projects, or sharing knowledge with the developer community. I'm driven by the belief that great software can genuinely improve people's lives.",
            'email' => 'rama@example.com',
            'location' => 'Indonesia',
            'profileImage' => 'https://picsum.photos/seed/ramaprofile/400/400',
            'github' => 'Maronnn14',
        ];

        foreach ($info as $key => $value) {
            PersonalInfo::create(['key' => $key, 'value' => $value]);
        }
    }

    private function seedSocials(): void
    {
        $socials = [
            ['platform' => 'GitHub', 'url' => 'https://github.com/Maronnn14', 'icon' => 'github', 'label' => '', 'visible' => true],
            ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/in/', 'icon' => 'linkedin', 'label' => '', 'visible' => true],
            ['platform' => 'Instagram', 'url' => 'https://instagram.com/', 'icon' => 'instagram', 'label' => '', 'visible' => true],
            ['platform' => 'Twitter', 'url' => 'https://x.com/', 'icon' => 'twitter', 'label' => '', 'visible' => true],
            ['platform' => 'YouTube', 'url' => 'https://youtube.com/', 'icon' => 'youtube', 'label' => '', 'visible' => true],
        ];

        foreach ($socials as $i => $s) {
            Social::create(array_merge($s, ['sort_order' => $i]));
        }
    }

    private function seedExperiences(): void
    {
        $experiences = [
            ['role' => 'Full Stack Developer', 'company' => 'Tech Solutions Inc.', 'type' => 'Full-time', 'start_date' => '2024', 'end_date' => 'Present', 'description' => 'Lead development of enterprise web applications using Laravel and Vue.js. Architected scalable REST APIs serving 10K+ daily users. Implemented CI/CD pipelines and automated testing workflows.', 'tech' => ['Laravel', 'Vue.js', 'MySQL', 'Docker']],
            ['role' => 'Frontend Developer', 'company' => 'Digital Creative Agency', 'type' => 'Contract', 'start_date' => '2023', 'end_date' => '2024', 'description' => 'Built responsive, high-performance websites for diverse clients. Focused on accessibility, animation, and cross-browser compatibility. Delivered 15+ projects on time and within budget.', 'tech' => ['React', 'Tailwind CSS', 'GSAP', 'Figma']],
            ['role' => 'Web Developer Intern', 'company' => 'StartUp Hub', 'type' => 'Internship', 'start_date' => '2022', 'end_date' => '2023', 'description' => 'Contributed to the development of a SaaS platform. Worked on both frontend UI components and backend API endpoints. Gained hands-on experience with agile methodologies.', 'tech' => ['PHP', 'JavaScript', 'Bootstrap', 'Git']],
            ['role' => 'Computer Science Student', 'company' => 'University of Technology', 'type' => 'Education', 'start_date' => '2020', 'end_date' => '2024', 'description' => "Bachelor's degree in Computer Science. Coursework in algorithms, data structures, software engineering, and database systems. Active member of the campus coding club.", 'tech' => ['Python', 'Java', 'C++', 'SQL']],
        ];

        foreach ($experiences as $i => $e) {
            Experience::create(array_merge($e, ['sort_order' => $i]));
        }
    }

    private function seedInterests(): void
    {
        $interests = [
            ['name' => 'Open Source', 'icon' => '🌐', 'description' => 'Contributing to community-driven software projects'],
            ['name' => 'Photography', 'icon' => '📷', 'description' => 'Capturing moments through street and landscape photography'],
            ['name' => 'Gaming', 'icon' => '🎮', 'description' => 'Story-driven RPGs and competitive strategy games'],
            ['name' => 'Music', 'icon' => '🎵', 'description' => 'Exploring genres from lo-fi beats to progressive rock'],
            ['name' => 'Reading', 'icon' => '📚', 'description' => 'Tech blogs, sci-fi novels, and design thinking books'],
            ['name' => 'Fitness', 'icon' => '💪', 'description' => 'Morning runs and calisthenics to stay sharp and focused'],
        ];

        foreach ($interests as $i => $int) {
            Interest::create(array_merge($int, ['sort_order' => $i]));
        }
    }

    private function seedSkills(): void
    {
        $skills = [
            ['name' => 'HTML5', 'category' => 'Frontend', 'icon' => 'html5', 'teaser' => 'Semantic markup & accessibility', 'description' => 'Expert in semantic HTML5, creating accessible and SEO-friendly web structures. Proficient with modern HTML APIs including Canvas, Web Components, and local storage.', 'proficiency' => 95, 'level' => 'Expert', 'related_projects' => [], 'gallery' => ['https://picsum.photos/seed/html1/600/400', 'https://picsum.photos/seed/html2/600/400']],
            ['name' => 'CSS3', 'category' => 'Frontend', 'icon' => 'css3', 'teaser' => 'Modern layouts & animations', 'description' => 'Advanced CSS including Grid, Flexbox, custom properties, animations, and responsive design. Experience with CSS architectures like BEM and utility-first approaches.', 'proficiency' => 90, 'level' => 'Expert', 'related_projects' => [], 'gallery' => ['https://picsum.photos/seed/css1/600/400', 'https://picsum.photos/seed/css2/600/400']],
            ['name' => 'JavaScript', 'category' => 'Frontend', 'icon' => 'javascript', 'teaser' => 'ES6+ & DOM manipulation', 'description' => 'Strong command of modern JavaScript (ES6+), including async/await, modules, closures, and the event loop. Experience building complex SPAs and interactive UIs from scratch.', 'proficiency' => 88, 'level' => 'Advanced', 'related_projects' => [], 'gallery' => ['https://picsum.photos/seed/js1/600/400']],
            ['name' => 'React', 'category' => 'Frontend', 'icon' => 'react', 'teaser' => 'Component-based UI development', 'description' => 'Proficient in React ecosystem including hooks, context, React Router, and state management. Experience with Next.js for server-side rendering and static generation.', 'proficiency' => 82, 'level' => 'Advanced', 'related_projects' => [], 'gallery' => ['https://picsum.photos/seed/react1/600/400']],
            ['name' => 'Vue.js', 'category' => 'Frontend', 'icon' => 'vuejs', 'teaser' => 'Progressive framework mastery', 'description' => 'Experienced with Vue 3 Composition API, Vuex/Pinia state management, and Vue Router. Built production applications with Inertia.js integration.', 'proficiency' => 85, 'level' => 'Advanced', 'related_projects' => [], 'gallery' => ['https://picsum.photos/seed/vue1/600/400']],
            ['name' => 'Tailwind CSS', 'category' => 'Frontend', 'icon' => 'tailwindcss', 'teaser' => 'Utility-first CSS framework', 'description' => 'Proficient in Tailwind CSS for rapid UI development. Experience customizing design systems, creating reusable component patterns, and optimizing production builds.', 'proficiency' => 87, 'level' => 'Advanced', 'related_projects' => [], 'gallery' => ['https://picsum.photos/seed/tw1/600/400']],
            ['name' => 'PHP', 'category' => 'Backend', 'icon' => 'php', 'teaser' => 'Server-side application logic', 'description' => 'Strong PHP fundamentals with OOP, design patterns, and modern PHP 8 features. Experience building APIs, authentication systems, and complex business logic.', 'proficiency' => 86, 'level' => 'Advanced', 'related_projects' => [], 'gallery' => ['https://picsum.photos/seed/php1/600/400']],
            ['name' => 'Laravel', 'category' => 'Backend', 'icon' => 'laravel', 'teaser' => 'Elegant PHP framework', 'description' => 'Extensive experience with Laravel including Eloquent ORM, middleware, queues, events, and testing. Built production-grade applications with complex authorization and API layers.', 'proficiency' => 90, 'level' => 'Expert', 'related_projects' => [], 'gallery' => ['https://picsum.photos/seed/lara1/600/400', 'https://picsum.photos/seed/lara2/600/400']],
            ['name' => 'MySQL', 'category' => 'Backend', 'icon' => 'mysql', 'teaser' => 'Relational database design', 'description' => 'Proficient in MySQL database design, query optimization, indexing strategies, and migration management. Experience with complex joins, subqueries, and stored procedures.', 'proficiency' => 83, 'level' => 'Advanced', 'related_projects' => [], 'gallery' => ['https://picsum.photos/seed/mysql1/600/400']],
            ['name' => 'Node.js', 'category' => 'Backend', 'icon' => 'nodejs', 'teaser' => 'JavaScript runtime & APIs', 'description' => 'Experience building REST APIs and real-time applications with Node.js and Express. Familiar with npm ecosystem, middleware patterns, and deployment strategies.', 'proficiency' => 75, 'level' => 'Intermediate', 'related_projects' => [], 'gallery' => ['https://picsum.photos/seed/node1/600/400']],
            ['name' => 'Git', 'category' => 'Tools', 'icon' => 'git', 'teaser' => 'Version control & collaboration', 'description' => 'Proficient with Git workflows including branching strategies, rebasing, conflict resolution, and collaborative development via GitHub pull requests and code reviews.', 'proficiency' => 88, 'level' => 'Advanced', 'related_projects' => [], 'gallery' => []],
            ['name' => 'Docker', 'category' => 'Tools', 'icon' => 'docker', 'teaser' => 'Containerization & deployment', 'description' => 'Experience with Docker for development and production environments. Familiar with Dockerfiles, docker-compose, multi-stage builds, and container orchestration basics.', 'proficiency' => 70, 'level' => 'Intermediate', 'related_projects' => [], 'gallery' => ['https://picsum.photos/seed/docker1/600/400']],
            ['name' => 'Figma', 'category' => 'Design', 'icon' => 'figma', 'teaser' => 'UI/UX design & prototyping', 'description' => 'Proficient in Figma for UI design, prototyping, and design systems. Experience translating designs to code and collaborating with design teams.', 'proficiency' => 78, 'level' => 'Advanced', 'related_projects' => [], 'gallery' => ['https://picsum.photos/seed/figma1/600/400']],
            ['name' => 'Python', 'category' => 'Backend', 'icon' => 'python', 'teaser' => 'Scripting & automation', 'description' => 'Experience with Python for scripting, automation, data processing, and basic machine learning. Familiar with Flask, pandas, and various utility libraries.', 'proficiency' => 72, 'level' => 'Intermediate', 'related_projects' => [], 'gallery' => []],
            ['name' => 'TypeScript', 'category' => 'Frontend', 'icon' => 'typescript', 'teaser' => 'Type-safe JavaScript development', 'description' => 'Growing expertise in TypeScript for building type-safe applications. Experience with interfaces, generics, and integrating TypeScript into React and Node.js projects.', 'proficiency' => 73, 'level' => 'Intermediate', 'related_projects' => [], 'gallery' => []],
        ];

        foreach ($skills as $i => $s) {
            Skill::create(array_merge($s, ['sort_order' => $i]));
        }
    }

    private function seedProjects(): void
    {
        $projects = [
            ['name' => 'Portfolio Website', 'category' => 'Frontend', 'short_desc' => 'A stunning personal portfolio with dark editorial design', 'full_desc' => 'A meticulously crafted personal portfolio website featuring a dark editorial luxury aesthetic. Built with vanilla HTML, CSS, and JavaScript, it showcases advanced CSS animations, a full CRUD message board, lightbox galleries, and responsive design across all breakpoints.', 'thumbnail' => 'https://picsum.photos/seed/portfolio/800/500', 'tech' => ['HTML5', 'CSS3', 'JavaScript'], 'gallery' => ['https://picsum.photos/seed/port1/800/500', 'https://picsum.photos/seed/port2/800/500', 'https://picsum.photos/seed/port3/800/500'], 'live_url' => '#', 'source_url' => 'https://github.com/Maronnn14/portofolio-rama', 'featured' => true],
            ['name' => 'E-Commerce Platform', 'category' => 'Full Stack', 'short_desc' => 'Modern online store with real-time inventory', 'full_desc' => 'A full-featured e-commerce platform built with React and Node.js. Features include user authentication, product catalog with search and filtering, shopping cart with persistent state, Stripe payment integration, order tracking, and an admin dashboard for inventory management.', 'thumbnail' => 'https://picsum.photos/seed/ecommerce/800/500', 'tech' => ['React', 'Node.js', 'MongoDB', 'Tailwind CSS'], 'gallery' => ['https://picsum.photos/seed/ecom1/800/500', 'https://picsum.photos/seed/ecom2/800/500', 'https://picsum.photos/seed/ecom3/800/500'], 'live_url' => '#', 'source_url' => '#', 'featured' => true],
            ['name' => 'Exam Management System', 'category' => 'Full Stack', 'short_desc' => 'Automated online examination platform for educators', 'full_desc' => 'A comprehensive exam management system built with Laravel and Vue.js via Inertia.js. Features include question bank management, timed exam sessions, auto-grading, real-time violation monitoring, student dashboards, and detailed score reporting with Excel export.', 'thumbnail' => 'https://picsum.photos/seed/examsys/800/500', 'tech' => ['Laravel', 'Vue.js', 'MySQL', 'Inertia.js'], 'gallery' => ['https://picsum.photos/seed/exam1/800/500', 'https://picsum.photos/seed/exam2/800/500', 'https://picsum.photos/seed/exam3/800/500', 'https://picsum.photos/seed/exam4/800/500'], 'live_url' => '#', 'source_url' => '#', 'featured' => true],
            ['name' => 'Task Manager Pro', 'category' => 'Full Stack', 'short_desc' => 'Collaborative task management with real-time updates', 'full_desc' => 'A collaborative task management application inspired by Trello and Notion. Built with Laravel backend and Vue.js frontend, featuring drag-and-drop task boards, real-time collaboration via WebSockets, team workspaces, and activity logs.', 'thumbnail' => 'https://picsum.photos/seed/taskman/800/500', 'tech' => ['Laravel', 'Vue.js', 'Tailwind CSS', 'Docker'], 'gallery' => ['https://picsum.photos/seed/task1/800/500', 'https://picsum.photos/seed/task2/800/500'], 'live_url' => '#', 'source_url' => '#', 'featured' => false],
            ['name' => 'API Gateway Service', 'category' => 'Backend', 'short_desc' => 'Centralized API gateway with rate limiting & auth', 'full_desc' => 'A centralized API gateway service built with Laravel that handles authentication, rate limiting, request routing, and API versioning for a microservices architecture.', 'thumbnail' => 'https://picsum.photos/seed/apigate/800/500', 'tech' => ['Laravel', 'PHP', 'MySQL', 'Docker'], 'gallery' => ['https://picsum.photos/seed/api1/800/500', 'https://picsum.photos/seed/api2/800/500'], 'live_url' => null, 'source_url' => '#', 'featured' => false],
            ['name' => 'Weather Dashboard', 'category' => 'Frontend', 'short_desc' => 'Real-time weather monitoring with interactive charts', 'full_desc' => 'A beautiful weather dashboard that displays real-time weather data with interactive charts and maps. Built with vanilla JavaScript, it fetches data from the OpenWeather API.', 'thumbnail' => 'https://picsum.photos/seed/weather/800/500', 'tech' => ['JavaScript', 'Chart.js', 'CSS3', 'REST API'], 'gallery' => ['https://picsum.photos/seed/weath1/800/500', 'https://picsum.photos/seed/weath2/800/500'], 'live_url' => '#', 'source_url' => '#', 'featured' => false],
        ];

        foreach ($projects as $i => $p) {
            Project::create(array_merge($p, ['sort_order' => $i, 'status' => 'published']));
        }
    }

    private function seedGalleryImages(): void
    {
        $images = [
            ['url' => 'https://picsum.photos/seed/gal1/600/400', 'alt' => 'Creative workspace setup'],
            ['url' => 'https://picsum.photos/seed/gal2/400/600', 'alt' => 'Code on screen'],
            ['url' => 'https://picsum.photos/seed/gal3/600/400', 'alt' => 'Team collaboration'],
            ['url' => 'https://picsum.photos/seed/gal4/500/500', 'alt' => 'Design mockup on tablet'],
            ['url' => 'https://picsum.photos/seed/gal5/600/400', 'alt' => 'Coffee and laptop'],
            ['url' => 'https://picsum.photos/seed/gal6/400/600', 'alt' => 'Urban architecture'],
            ['url' => 'https://picsum.photos/seed/gal7/600/400', 'alt' => 'Nature landscape'],
            ['url' => 'https://picsum.photos/seed/gal8/500/500', 'alt' => 'Tech conference'],
            ['url' => 'https://picsum.photos/seed/gal9/600/400', 'alt' => 'Sunset view'],
            ['url' => 'https://picsum.photos/seed/gal10/400/600', 'alt' => 'Night city lights'],
            ['url' => 'https://picsum.photos/seed/gal11/600/400', 'alt' => 'Mountain trail'],
            ['url' => 'https://picsum.photos/seed/gal12/500/500', 'alt' => 'Studio headphones'],
        ];

        foreach ($images as $i => $img) {
            GalleryImage::create(array_merge($img, ['visible' => true, 'sort_order' => $i]));
        }
    }

    private function seedMessages(): void
    {
        $messages = [
            ['name' => 'Sarah Chen', 'message' => 'Amazing portfolio! The dark theme with gold accents is really elegant. Love the attention to detail in every section. Keep up the great work! 🔥', 'rating' => 5, 'days_ago' => 3],
            ['name' => 'Marco Rivera', 'message' => 'Really impressed by the exam management system project. The auto-grading feature sounds incredibly useful for educators. Would love to see a demo!', 'rating' => 4, 'days_ago' => 7],
            ['name' => 'Aiko Tanaka', 'message' => 'Clean code, beautiful design, and great project selection. This portfolio shows real skill and passion. Bookmarked for inspiration! ✨', 'rating' => 5, 'days_ago' => 14],
            ['name' => 'David Okonkwo', 'message' => 'The message board feature is a really nice touch — adds a personal, community feel to the site. Smart implementation!', 'rating' => 4, 'days_ago' => 21],
        ];

        foreach ($messages as $i => $m) {
            PortfolioMessage::create([
                'message_id' => 'seed_' . $i . '_' . time(),
                'name' => $m['name'],
                'message' => $m['message'],
                'rating' => $m['rating'],
                'posted_at_ms' => (int) ((time() - $m['days_ago'] * 86400) * 1000),
                'session_token' => 'seed_visitor_' . $i,
                'hidden' => false,
                'flagged' => false,
                'pinned' => false,
            ]);
        }
    }
}
