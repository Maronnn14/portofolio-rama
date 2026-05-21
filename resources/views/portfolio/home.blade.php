@php
  $homeFullName = $personal['fullName'] ?? $personal['name'] ?? 'Rama Adin';
  $homeNameParts = explode(' ', $homeFullName, 2);
  $homeFirstName = $personal['name'] ?? $homeNameParts[0] ?? 'Rama';
  $homeAccentName = $homeNameParts[1] ?? '';
  $homeRole = $personal['role'] ?? 'Full Stack Developer';
  $homeTagline = $personal['tagline'] ?? 'Crafting digital experiences with code & creativity';
  $homeShortBio = $personal['shortBio'] ?? $personal['bio'] ?? 'A passionate developer who transforms ideas into elegant, functional digital solutions. With a keen eye for detail and a love for clean code, I build applications that make a difference.';
  $homeImage = $personal['homeProfileImage'] ?? $personal['photo'] ?? $personal['profileImage'] ?? null;
  $defaultHomeImage = 'https://picsum.photos/seed/ramaprofile/400/400';
@endphp

@extends('layouts.portfolio')

@section('title', "$homeFullName — $homeRole")
@section('description', "$homeRole $homeTagline")

@section('content')

<!-- ====== HERO SECTION ====== -->
  <section class="hero" id="hero">
    <div class="hero__bg">
      <div class="hero__gradient"></div>
      <div class="hero__grid-pattern"></div>
      <div class="hero__orb hero__orb--1"></div>
      <div class="hero__orb hero__orb--2"></div>
    </div>
    <div class="container hero__content">
      <div class="hero__label section-label" style="margin-bottom: var(--space-xl);">
        Welcome to my world
      </div>
      <h1 class="hero__title">
        <span class="hero__greeting" id="hero-greeting"></span>
        <span class="hero__name" id="hero-name"><span id="hero-name-first">{{ $homeFirstName }}</span> <span class="text-accent" id="hero-name-accent">{{ $homeAccentName }}</span></span>
      </h1>
      <p class="hero__role" id="hero-role"></p>
      <p class="hero__tagline" id="hero-tagline">{{ $homeTagline }}</p>
      <div class="hero__cta">
        <a href="{{ url('/projects.html') }}" class="btn btn--primary btn--lg">
          See My Work
          <span class="btn-arrow">→</span>
        </a>
        <a href="{{ url('/contact.html') }}" class="btn btn--secondary btn--lg">
          Contact Me
        </a>
      </div>
      <div class="hero__scroll-indicator">
        <span>Scroll</span>
        <div class="hero__scroll-line"></div>
      </div>
    </div>
  </section>

  <!-- ====== ABOUT PREVIEW ====== -->
  <section class="section about-preview" id="about-preview">
    <div class="container">
      <div class="about-preview__grid reveal">
        <div class="about-preview__image-wrapper">
          <img src="{{ $homeImage ?? $defaultHomeImage }}" alt="{{ $homeFullName }} - Profile Photo" class="about-preview__image" id="home-profile-image" loading="eager" />
          <div class="about-preview__image-border"></div>
        </div>
          <div class="about-preview__content">
          <span class="section-label">About Me</span>
          <h2 class="section-heading" id="home-about-heading">Hello, I'm <span class="text-accent">{{ $homeFirstName }}</span></h2>
          <p class="about-preview__bio" id="home-about-bio">{{ $homeShortBio }}</p>
          <a href="{{ url('/about.html') }}" class="btn btn--ghost">
            Read More <span class="btn-arrow">→</span>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- ====== EXPERIENCE PREVIEW ====== -->
  <section class="section section--compact experience-preview" id="experience-preview">
    <div class="container">
      <div class="section-header reveal">
        <span class="section-label">Experience</span>
        <h2 class="section-heading">Where I've Worked</h2>
      </div>
      <div class="experience-preview__list" id="experience-preview-list">
        @forelse($experience->take(3) as $exp)
          <div class="exp-preview-item reveal stagger-{{ $loop->iteration }}">
            <span class="exp-preview-item__date">{{ $exp->start_date }} — {{ $exp->end_date ?? 'Present' }}</span>
            <div>
              <div class="exp-preview-item__role">{{ $exp->role }}</div>
              <div class="exp-preview-item__company">{{ $exp->company }}</div>
            </div>
            <span class="exp-preview-item__type">{{ $exp->type }}</span>
          </div>
        @empty
          <div class="empty-state">
            <p class="empty-state__text">No experience listed yet.</p>
          </div>
        @endforelse
      </div>
      <div class="reveal" style="margin-top: var(--space-2xl);">
        <a href="{{ url('/about.html') }}#experience" class="btn btn--ghost">
          See Full Experience <span class="btn-arrow">→</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ====== SKILLS PREVIEW ====== -->
  <section class="section section--compact skills-preview" id="skills-preview">
    <div class="container">
      <div class="section-header section-header--center reveal">
        <span class="section-label" style="justify-content: center;">Skills</span>
        <h2 class="section-heading">Tech Stack</h2>
        <p class="section-subheading">Technologies and tools I work with every day</p>
      </div>
      <div class="skills-preview__grid" id="skills-preview-grid">
        @forelse($skills->take(10) as $skill)
          <div class="skill-preview-item reveal stagger-{{ min($loop->iteration, 8) }}">
            <div class="skill-preview-item__icon">
              <img src="{{ $skill->icon ? 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/' . $skill->icon . '/' . $skill->icon . '-original.svg' : '' }}" alt="{{ $skill->name }}" loading="lazy" />
            </div>
            <span class="skill-preview-item__name">{{ $skill->name }}</span>
          </div>
        @empty
          <div class="empty-state" style="grid-column: 1/-1;">
            <p class="empty-state__text">No skills listed yet.</p>
          </div>
        @endforelse
      </div>
      <div class="reveal text-center" style="margin-top: var(--space-2xl);">
        <a href="{{ url('/skills.html') }}" class="btn btn--ghost">
          View All Skills <span class="btn-arrow">→</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ====== PROJECTS PREVIEW ====== -->
  <section class="section projects-preview" id="projects-preview">
    <div class="container">
      <div class="section-header reveal">
        <span class="section-label">Projects</span>
        <h2 class="section-heading">Featured Work</h2>
        <p class="section-subheading">A selection of projects I'm most proud of</p>
      </div>
      <div class="projects-preview__grid" id="projects-preview-grid">
        @forelse($projects as $proj)
          <a href="{{ url('/project-detail.html?id=' . $proj->id) }}" class="project-preview-card reveal stagger-{{ min($loop->iteration, 3) }}">
            <div class="project-preview-card__image-wrapper">
              <img src="{{ $proj->thumbnail }}" alt="{{ $proj->name }}" class="project-preview-card__image" loading="lazy" />
              <div class="project-preview-card__overlay"></div>
            </div>
            <div class="project-preview-card__body">
              <div class="project-preview-card__category">{{ $proj->category }}</div>
              <h3 class="project-preview-card__title">{{ $proj->name }}</h3>
              <p class="project-preview-card__desc">{{ $proj->short_desc }}</p>
              <div class="project-preview-card__tags tags-list">
                @foreach($proj->tech ?? [] as $tech)
                  <span class="tag">{{ $tech }}</span>
                @endforeach
              </div>
            </div>
          </a>
        @empty
          <div class="empty-state" style="grid-column: 1/-1;">
            <p class="empty-state__text">No featured projects yet.</p>
          </div>
        @endforelse
      </div>
      <div class="reveal" style="margin-top: var(--space-2xl);">
        <a href="{{ url('/projects.html') }}" class="btn btn--ghost">
          See All Projects <span class="btn-arrow">→</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ====== CONTACT CTA ====== -->
  <section class="section contact-cta" id="contact-cta">
    <div class="contact-cta__bg"></div>
    <div class="container text-center" style="position: relative; z-index: 1;">
      <div class="reveal">
        <span class="section-label" style="justify-content: center;">Let's Connect</span>
        <h2 class="contact-cta__heading">Let's Work <span class="text-accent">Together</span></h2>
        <p class="contact-cta__text">
          Have a project in mind or want to collaborate? I'd love to hear from you.
          Let's build something amazing together.
        </p>
        <a href="{{ url('/contact.html') }}" class="btn btn--primary btn--lg">
          Get In Touch <span class="btn-arrow">→</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ====== MESSAGE BOARD ====== -->
  <section class="section message-board" id="message-board">
    <div class="container">
      <div class="section-header section-header--center reveal">
        <span class="section-label" style="justify-content: center;">Guestbook</span>
        <h2 class="section-heading">Leave a <span class="text-accent">Message</span></h2>
        <p class="section-subheading">Share your thoughts — messages are public and visible to all visitors</p>
      </div>

      <div class="mb-form-wrapper reveal" id="mb-form-wrapper">
        <form class="mb-form" id="mb-form">
          <div class="mb-form__row">
            <div class="form-group" style="flex: 1;">
              <label for="mb-name" class="form-label">Your Name *</label>
              <input type="text" id="mb-name" class="form-input" placeholder="Enter your name" required maxlength="50" />
            </div>
            <div class="form-group" style="flex: 0 0 auto;">
              <label class="form-label">Rating (optional)</label>
              <div class="star-rating" id="mb-star-input">
                <span class="star-rating__star" data-rating="1">★</span>
                <span class="star-rating__star" data-rating="2">★</span>
                <span class="star-rating__star" data-rating="3">★</span>
                <span class="star-rating__star" data-rating="4">★</span>
                <span class="star-rating__star" data-rating="5">★</span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="mb-message" class="form-label">Your Message *</label>
            <textarea id="mb-message" class="form-textarea" placeholder="Write something nice..." required maxlength="300" rows="3"></textarea>
            <p class="form-hint"><span id="mb-char-count">0</span> / 300</p>
          </div>
          <button type="submit" class="btn btn--primary" id="mb-submit">Post Message</button>
        </form>
      </div>

      <div class="mb-messages" id="mb-messages">
        <div class="empty-state" style="grid-column: 1 / -1;">
          <div class="empty-state__icon">💬</div>
          <h3 class="empty-state__title">No messages yet</h3>
          <p class="empty-state__text">Be the first to leave a message!</p>
        </div>
      </div>

      <div class="mb-load-more text-center" id="mb-load-more" style="display: none;">
        <button class="btn btn--secondary" id="mb-load-more-btn">Load More Messages</button>
      </div>
    </div>
  </section>

  <!-- ====== GALLERY ====== -->
  <section class="section gallery-section" id="gallery">
    <div class="container">
      <div class="section-header section-header--center reveal">
        <span class="section-label" style="justify-content: center;">Gallery</span>
        <h2 class="section-heading">Moments & <span class="text-accent">Memories</span></h2>
      </div>
      <div class="gallery-grid" id="gallery-grid">
        @forelse($gallery as $img)
          <div class="gallery-item reveal stagger-{{ min(($loop->index % 8) + 1, 8) }}" data-lightbox="{{ $img->url }}" style="opacity: 0;">
            <img src="{{ $img->url }}" alt="{{ $img->alt }}" loading="lazy" />
          </div>
        @empty
          <div class="empty-state" style="grid-column: 1/-1;">
            <p class="empty-state__text">No gallery images yet.</p>
          </div>
        @endforelse
      </div>
    </div>
  </section>

  <div class="toast" id="toast"></div>

@endsection

@push('scripts')
  <script src="{{ asset('js/lightbox.js') }}"></script>
  <script src="{{ asset('js/home.js') }}"></script>
  <script src="{{ asset('js/message-board.js') }}"></script>
  <script src="{{ asset('js/gallery.js') }}"></script>
@endpush
