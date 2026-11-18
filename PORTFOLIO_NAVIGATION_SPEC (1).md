# Portfolio Website: Navigation Structure & Content Specification

> **Purpose of this document:** This file is a build specification for an AI coding assistant (or a developer). It describes the site's navigation hierarchy, the purpose of each page, and the content each page must contain. Follow it as the source of truth when generating the website.

---

## 1. Global Requirements

- **Site type:** Personal professional portfolio website.
- **Primary audience:** Recruiters, hiring managers, potential clients, and collaborators.
- **Primary goals:** Create a strong first impression, showcase technical ability, and make it easy to get in touch.
- **Navigation:** A persistent top navigation bar on every page, with dropdown menus where specified below. On mobile, collapse it into a hamburger menu with expandable sub-menus.
- **Design:** Clean, modern, professional, and fully responsive (mobile-first). Consistent typography, spacing, and color palette across all pages.
- **Quality:** Accessible (semantic HTML, alt text, keyboard navigation, sufficient contrast), fast-loading, and SEO-friendly (meta tags, Open Graph tags).
- **Content:** Use clearly marked placeholder content wherever real data is not provided, so it can be easily replaced.

### Navigation Tree

```
Home
About Me (dropdown)
 ├── Biodata & Personal Branding
 ├── Download CV (Resume)
 └── Interests & Hobbies
Skills
Projects & Experience (dropdown)
 ├── Coding Projects
 ├── Work Experience
 └── Contributions, Activities & Organizations
Contact
```

---

## 2. Page Specifications

### 2.1 Home

- **Purpose:** The landing page. Gives visitors a strong first impression and a teaser of the entire portfolio.
- **Content (sections in order):**
  1. **Hero:** Name, professional title/tagline, short introduction, and primary call-to-action buttons (e.g., "View Projects", "Contact Me").
  2. **About teaser:** A brief summary of who the owner is, with a link to the full About Me pages.
  3. **Tech stack highlight:** A compact showcase of the strongest technologies and tools, linking to the Skills page.
  4. **Featured projects:** A small selection of the best projects (cards with image, title, short description, tech tags), linking to the Coding Projects page.
  5. **Contact snippet:** A short call to action with email and social links, linking to the Contact page.

### 2.2 About Me (Dropdown Menu)

The "About Me" menu item opens a dropdown with the three sub-pages below.

#### 2.2.1 Biodata & Personal Branding
- **Purpose:** Present the professional profile and personal brand.
- **Content:**
  - Professional biodata (consistent with the CV).
  - A short personal branding statement describing values, strengths, and what makes the owner unique.
  - A professional profile photo.
  - Education journey presented as a timeline (institution, program, years, highlights).

#### 2.2.2 Download CV (Resume)
- **Purpose:** Let visitors quickly obtain the resume.
- **Content:**
  - A prominent download button/link for the CV in **PDF** format.
  - Optionally, an inline preview of the CV.

#### 2.2.3 Interests & Hobbies
- **Purpose:** Show the personal side of the owner beyond the professional profile.
- **Content:**
  - A relaxed, visual section (icons or images) listing interests and hobbies, each with a short description.

### 2.3 Skills

- **Purpose:** Present technical and non-technical capabilities in a structured, scannable way.
- **Content:**
  - **Hard Skills:** Grouped by category (e.g., Programming Languages, Frameworks & Libraries, Databases, Tools & Platforms). Use badges, icons, or proficiency indicators.
  - **Soft Skills:** Supporting skills valued in the workplace, such as communication, teamwork, and problem-solving, each with a brief description or example.

### 2.4 Projects & Experience (Dropdown Menu)

- **Concept:** This section is designed as an **interactive blog**. Each entry should read like a well-formatted article: a title, cover image, date, tags/categories, and a rich body with clean typography and layout. Keep all relevant supporting features (such as tags, filtering, search, and previous/next navigation) and polish their layout so the content is enjoyable to read.
- The "Projects & Experience" menu item opens a dropdown with three sub-pages.

#### 2.4.1 Coding Projects
- **Purpose:** Showcase software/web development projects.
- **Content per project:**
  - Title, cover image or screenshots, and a description.
  - Technologies used (tags).
  - Links to the live demo and the source repository.
  - Optional details: problem statement, role, challenges, and outcome.
- **Features:** Listing page with cards, filtering by technology/category, and an individual detail page per project.

#### 2.4.2 Work Experience
- **Purpose:** Document professional history, including jobs and internships.
- **Content per entry:**
  - Company/organization, role, and dates.
  - Key responsibilities and achievements (ideally with measurable results).
  - Technologies and tools used.
- **Layout:** Reverse-chronological timeline or article-style entries.

#### 2.4.3 Contributions, Activities & Organizations
- **Purpose:** Show involvement in communities, committees, organizations, and relevant open-source contributions.
- **Content per entry:**
  - Name of the community/organization/project, role, and period.
  - Description of contributions and impact.
  - Links to related profiles, pull requests, or certificates where applicable.

### 2.5 Contact

- **Purpose:** Make it easy for clients, recruiters, and collaborators to reach the owner.
- **Content:**
  - A **direct message form** (name, email, subject, message) with client-side validation and a success/error state.
  - Links to professional social profiles (**LinkedIn**, **GitHub**, and others as relevant).
  - The official **email address**.

---

## 3. Admin Dashboard (Content Management)

**Core requirement:** Every piece of content on the website must be editable through the admin dashboard, with no code changes or redeployment needed. The dashboard must follow the **existing admin dashboard already present in this project**: reuse its authentication, layout, UI components, data-access patterns, and conventions instead of creating a new or parallel system. If the project already has a dashboard module, extend it with the sections below.

### 3.1 Editable Content per Page

| Website Page | What the Admin Can Manage |
|---|---|
| **Home** | Hero text, tagline, call-to-action buttons, about teaser, selection of featured projects and highlighted tech stack. |
| **About Me: Biodata & Personal Branding** | Biodata fields, personal branding statement, profile photo (upload/replace), education timeline entries (add, edit, delete, reorder). |
| **About Me: Download CV** | Upload/replace the CV PDF file and update its label or version. |
| **About Me: Interests & Hobbies** | Add, edit, delete, and reorder hobby/interest items (title, description, icon or image). |
| **Skills** | Add, edit, delete, and reorder hard skills (with categories and levels) and soft skills (with descriptions). |
| **Coding Projects** | Full CRUD for projects: title, slug, cover image, gallery, description/article body, tech tags, demo and repository links, featured flag, publish/draft status. |
| **Work Experience** | Full CRUD for entries: company, role, dates, responsibilities, achievements, technologies, article body. |
| **Contributions, Activities & Organizations** | Full CRUD for entries: name, role, period, description, related links, images. |
| **Contact** | Email address, social media links (LinkedIn, GitHub, etc.), and a view of messages received through the contact form (read, mark as read, delete). |
| **Global / Site Settings** | Site title, meta description and Open Graph image, navigation labels and menu order, footer content, and theme basics where applicable. |

### 3.2 Dashboard Requirements

- **Authentication & authorization:** Admin-only access via login, reusing the project's existing auth mechanism. All dashboard routes and write APIs must be protected.
- **Content editing:** A rich-text or Markdown editor with live preview for article-style content (the Projects & Experience entries are rendered as blog-style articles).
- **Media management:** Image and file upload with validation (type and size), preview, replace, and delete.
- **List management:** Search, filter, sort, and drag-and-drop or manual reordering for lists of items.
- **Publishing workflow:** Draft and published states, so changes can be prepared before going live.
- **Instant reflection:** Changes saved in the dashboard must appear on the public website (use revalidation, cache invalidation, or dynamic fetching as appropriate).
- **Data source of truth:** All content is stored in the project's existing database or storage layer and rendered dynamically by the public pages. No hard-coded content in components.
- **UX:** Form validation, clear success and error messages, confirmation dialogs before deletion, and a responsive layout.
- **Seed data:** Provide initial seed or placeholder data so the website renders correctly before the admin adds real content.

---

## 4. Implementation Notes for the AI Builder

1. Generate the navigation component first (including dropdowns and the mobile menu), then build each page in the order listed above.
2. Reuse shared components (header, footer, cards, buttons, tags, section wrappers) to keep the design consistent.
3. Do not hard-code personal data (name, email, links, CV file path, project data) in pages or components. Read everything from the data layer that the admin dashboard manages (see Section 3), and keep a seed file for default values.
4. Before creating anything new for the dashboard, inspect the existing admin dashboard in the project and match its structure, naming, and patterns.
5. Use placeholder text and images where real content is missing, and mark them clearly (e.g., `TODO: replace`).
6. Ensure the Projects & Experience section supports article-style detail pages (for example, content written in Markdown and rendered with good typography).
7. Verify responsiveness, accessibility, link integrity, and that every editable field in the admin dashboard is reflected on the public site before considering the build complete.
