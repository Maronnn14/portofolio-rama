@extends('layouts.portfolio')

@section('title', "About Me — Rama Adin")
@section('description', "Learn about Rama Adin — background, experience, interests, and journey as a Full Stack Developer.")

@section('content')
<!-- Page Header -->
  <header class="page-header">
    <span class="section-label" style="justify-content: center;">Get to Know Me</span>
    <h1 class="page-header__title reveal">About <span class="text-accent">Me</span></h1>
    <p class="page-header__subtitle reveal stagger-1">The story behind the code</p>
  </header>

  <!-- Background / Story -->
  <section class="section about-story">
    <div class="container">
      <div class="about-story__grid">
        <div class="about-story__image-col reveal-left">
          <div class="about-story__image-wrapper">
            <img src="https://picsum.photos/seed/ramaprofile/400/500" alt="Rama Adin" class="about-story__image" id="about-profile-image" loading="lazy" />
            <div class="about-story__image-accent"></div>
          </div>
          <div class="about-story__stats">
            <div class="about-story__stat">
              <span class="about-story__stat-number">3+</span>
              <span class="about-story__stat-label">Years Experience</span>
            </div>
            <div class="about-story__stat">
              <span class="about-story__stat-number">15+</span>
              <span class="about-story__stat-label">Projects Delivered</span>
            </div>
            <div class="about-story__stat">
              <span class="about-story__stat-number">10+</span>
              <span class="about-story__stat-label">Happy Clients</span>
            </div>
          </div>
        </div>
        <div class="about-story__content reveal-right">
          <h2 class="section-heading">My <span class="text-accent">Story</span></h2>
          <div class="divider"></div>
          <div class="about-story__text" id="about-story-text">
            <!-- Rendered by JS from data -->
          </div>
          <blockquote class="about-story__quote">
            <p>"Great software is built with empathy — understanding both the user and the problem."</p>
          </blockquote>
        </div>
      </div>
    </div>
  </section>

  <!-- Experience Timeline -->
  <section class="section experience-section" id="experience">
    <div class="container">
      <div class="section-header section-header--center reveal">
        <span class="section-label" style="justify-content: center;">Career Path</span>
        <h2 class="section-heading">Experience & <span class="text-accent">Education</span></h2>
        <p class="section-subheading">My professional journey and growth over the years</p>
      </div>
      <div class="timeline" id="timeline">
        <!-- Rendered by JS -->
      </div>
    </div>
  </section>

  <!-- Interests -->
  <section class="section interests-section">
    <div class="container">
      <div class="section-header section-header--center reveal">
        <span class="section-label" style="justify-content: center;">Beyond Code</span>
        <h2 class="section-heading">Interests & <span class="text-accent">Hobbies</span></h2>
      </div>
      <div class="interests-grid" id="interests-grid">
        <!-- Rendered by JS -->
      </div>
    </div>
  </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {
      await PortfolioData.load(true);
      const d = PORTFOLIO_DATA;

      const aboutImage = document.getElementById('about-profile-image');
      const aboutImageSrc = d.personal.aboutProfileImage || d.personal.photo || d.personal.profileImage;
      if (aboutImage && aboutImageSrc) {
        aboutImage.src = aboutImageSrc;
      }

      // Render bio
      const storyEl = document.getElementById('about-story-text');
      if (storyEl) {
        const story = d.personal.fullBio || d.personal.shortBio || 'No background story has been added yet.';
        storyEl.innerHTML = story
          .split('\n\n')
          .map(p => `<p>${p.trim()}</p>`)
          .join('');
      }

      // Render timeline
      const timelineEl = document.getElementById('timeline');
      if (timelineEl) {
        timelineEl.innerHTML = d.experience.map((exp, i) => `
          <div class="timeline__item reveal ${i % 2 === 0 ? 'timeline__item--left' : 'timeline__item--right'} stagger-${Math.min(i + 1, 6)}">
            <div class="timeline__dot"></div>
            <div class="timeline__content">
              <span class="timeline__date">${exp.startDate} — ${exp.endDate}</span>
              <span class="timeline__type tag tag--accent">${exp.type}</span>
              <h3 class="timeline__role">${exp.role}</h3>
              <h4 class="timeline__company">${exp.company}</h4>
              <p class="timeline__desc">${exp.description}</p>
              <div class="tags-list" style="margin-top: var(--space-md);">
                ${exp.tech.map(t => `<span class="tag">${t}</span>`).join('')}
              </div>
            </div>
          </div>
        `).join('<div class="timeline__connector"></div>');
      }

      // Render interests
      const interestsEl = document.getElementById('interests-grid');
      if (interestsEl) {
        interestsEl.innerHTML = d.interests.map((int, i) => `
          <div class="interest-card reveal stagger-${Math.min(i + 1, 6)}">
            <span class="interest-card__icon">${int.icon}</span>
            <h3 class="interest-card__name">${int.name}</h3>
            <p class="interest-card__desc">${int.desc}</p>
          </div>
        `).join('');
      }

      initScrollReveal();
    });
  </script>
@endpush
