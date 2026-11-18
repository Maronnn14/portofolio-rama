@php
  use App\Models\PersonalInfo;
  use App\Support\Icon;

  $homeName = PersonalInfo::displayName($personal);
  $homeNickname = $personal['nickname'] ?? $personal['name'] ?? $homeName;
  $homeRole = $personal['role'] ?? '';
  $homeTagline = $personal['tagline'] ?? '';
  $homeShortBio = $personal['shortBio'] ?? $personal['branding_statement'] ?? '';
  $homeImage = $personal['homeProfileImage'] ?? $personal['photo'] ?? $personal['profileImage'] ?? null;
  $branding = $personal['branding_statement'] ?? '';
@endphp

@extends('layouts.portfolio')

@section('title', '')
@section('description', trim($homeRole.' '.$homeTagline) ?: ($siteSettings['meta_description'] ?? ''))

@section('content')
  @php
    $heroNameParts = preg_split('/\s+/', trim($homeName)) ?: [];
    $heroLast = count($heroNameParts) > 1 ? array_pop($heroNameParts) : '';
    $heroFirst = trim(implode(' ', $heroNameParts));
  @endphp
  <section class="hero" id="hero">
    <div class="hero__bg" aria-hidden="true">
      <div class="hero__panel"></div>
      <div class="hero__panel-line"></div>
    </div>
    <p class="hero__watermark" id="hero-watermark" aria-hidden="true">{{ $heroLast ?: $homeNickname }}</p>

    <div class="hero__inner container">
      <div class="hero__media">
        <div class="hero__frame">
          @if($homeImage)
            <img src="{{ $homeImage }}" alt="{{ $homeName }}" class="hero__portrait" loading="eager" />
          @else
            <div class="hero__portrait hero__portrait--placeholder" aria-hidden="true">{{ Icon::initials($homeName) }}</div>
          @endif
        </div>
        <span class="hero__meta hero__meta--bl">{{ $siteSettings['location'] ?? 'Indonesia' }}</span>
      </div>

      <div class="hero__content">
        <span class="section-label">Welcome to my portfolio</span>
        <h1 class="hero__title">
          <span class="hero__name" id="hero-name">{{ $homeName }}</span>
        </h1>

        @if($homeRole)
          <p class="hero__role" id="hero-role">{{ $homeRole }}</p>
        @endif
        @if($homeTagline)
          <p class="hero__tagline" id="hero-tagline">{{ $homeTagline }}</p>
        @endif
        @if($branding)
          <p class="hero__tagline hero__branding">{{ $branding }}</p>
        @endif

        @include('portfolio.partials.hero-cta', ['personal' => $personal, 'homeSettings' => $homeSettings, 'homeName' => $homeName])

        <div class="hero__scroll-indicator">
          <span>Scroll</span>
          <div class="hero__scroll-line"></div>
        </div>
      </div>
    </div>
  </section>

  <section class="section about-preview" id="about-preview">
    <div class="container">
      <div class="about-preview__grid reveal">
        <div class="about-preview__image-wrapper">
          @if($homeImage)
            <img src="{{ $homeImage }}" alt="{{ $homeName }} — profile photo" class="about-preview__image" id="home-profile-image" loading="eager" />
          @else
            <div class="about-preview__image about-preview__image--placeholder" aria-hidden="true">{{ Icon::initials($homeName) }}</div>
          @endif
          <div class="about-preview__image-border"></div>
        </div>
        <div class="about-preview__content">
          <span class="section-label">About Me</span>
          <h2 class="section-heading" id="home-about-heading">Hello, I'm <span class="text-accent">{{ $homeNickname }}</span></h2>
          <p class="about-preview__bio" id="home-about-bio">{{ $homeSettings['about_teaser'] ?? $homeShortBio }}</p>
          <a href="{{ route('about.biodata') }}" class="btn btn--ghost">
            Read More <span class="btn-arrow">→</span>
          </a>
        </div>
      </div>
    </div>
  </section>

  <section class="section section--compact skills-preview" id="skills-preview">
    <div class="container">
      <div class="section-header section-header--center reveal">
        <span class="section-label justify-center">Skills</span>
        <h2 class="section-heading">Tech Stack</h2>
        <p class="section-subheading">Technologies and tools I work with every day</p>
      </div>
      <div class="skills-preview__grid" id="skills-preview-grid">
        @forelse($skills->take(10) as $skill)
          @php $icon = Icon::skill($skill->icon, $skill->name); @endphp
          <div class="skill-preview-item reveal stagger-{{ min($loop->iteration, 8) }}">
            <div class="skill-preview-item__icon">
              @if($icon)
                <img src="{{ $icon }}" alt="" loading="lazy" />
              @else
                <span aria-hidden="true">{{ Icon::initials($skill->name) }}</span>
              @endif
            </div>
            <span class="skill-preview-item__name">{{ $skill->name }}</span>
          </div>
        @empty
          <div class="empty-state col-span-full"><p class="empty-state__text">No highlighted skills yet.</p></div>
        @endforelse
      </div>
      <div class="reveal text-center mt-12">
        <a href="{{ route('skills') }}" class="btn btn--ghost">View All Skills <span class="btn-arrow">→</span></a>
      </div>
    </div>
  </section>

  <section class="section projects-preview" id="projects-preview">
    <div class="container">
      <div class="section-header reveal">
        <span class="section-label">Projects</span>
        <h2 class="section-heading">Featured Work</h2>
        <p class="section-subheading">A selection of projects I'm most proud of</p>
      </div>
      <div class="projects-preview__grid" id="projects-preview-grid">
        @forelse($projects as $proj)
          <a href="{{ route('projects.show', $proj) }}" class="project-preview-card reveal stagger-{{ min($loop->iteration, 3) }}">
            <div class="project-preview-card__image-wrapper">
              @if($proj->thumbnail)
                <img src="{{ $proj->thumbnail }}" alt="{{ $proj->name }}" class="project-preview-card__image" loading="lazy" />
              @else
                <div class="project-preview-card__image project-preview-card__image--placeholder">{{ Icon::initials($proj->name) }}</div>
              @endif
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
          <div class="empty-state col-span-full"><p class="empty-state__text">No featured projects yet.</p></div>
        @endforelse
      </div>
      <div class="reveal mt-12">
        <a href="{{ route('projects.index') }}" class="btn btn--ghost">View All Projects <span class="btn-arrow">→</span></a>
      </div>
    </div>
  </section>

  <section class="section section--compact experience-preview" id="experience-preview">
    <div class="container">
      <div class="section-header reveal">
        <span class="section-label">Experience</span>
        <h2 class="section-heading">Where I've Worked</h2>
      </div>
      <div class="experience-preview__list" id="experience-preview-list">
        @forelse($experience as $exp)
          <a class="exp-preview-item reveal stagger-{{ $loop->iteration }}" href="{{ route('experience.show', $exp) }}">
            <span class="exp-preview-item__date">{{ $exp->start_date }} — {{ $exp->end_date ?? 'Present' }}</span>
            <div>
              <div class="exp-preview-item__role">{{ $exp->role }}</div>
              <div class="exp-preview-item__company">{{ $exp->company }}</div>
            </div>
            <span class="exp-preview-item__type">{{ $exp->type }}</span>
          </a>
        @empty
          <div class="empty-state"><p class="empty-state__text">No experience listed yet.</p></div>
        @endforelse
      </div>
      <div class="reveal mt-12">
        <a href="{{ route('experience.index') }}" class="btn btn--ghost">See Full Experience <span class="btn-arrow">→</span></a>
      </div>
    </div>
  </section>

  @if($blogPosts->isNotEmpty())
  <section class="section section--compact home-blog" id="home-blog">
    <div class="container">
      <div class="section-header reveal">
        <span class="section-label">Journal</span>
        <h2 class="section-heading">Catatan <span class="text-accent">Terbaru</span></h2>
        <p class="section-subheading">Tulisan harian tentang proses, pelajaran, dan hal kecil yang layak dibagikan</p>
      </div>
      <div class="projects-grid">
        @foreach($blogPosts as $post)
          <article class="project-card reveal stagger-{{ $loop->iteration }}">
            <a href="{{ route('about.blog.show', $post) }}" class="project-card__link">
              <div class="project-card__thumbnail">
                @if($post->category)<span class="project-card__badge">{{ $post->category }}</span>@endif
                @if($post->cover_url)
                  <img src="{{ $post->cover_url }}" alt="{{ $post->title }}" loading="lazy" />
                @else
                  <span class="project-card__placeholder" aria-hidden="true">{{ \App\Support\Icon::initials($post->title) }}</span>
                @endif
                <span class="project-card__overlay"><span class="project-card__overlay-btn">Baca tulisan <span aria-hidden="true">→</span></span></span>
              </div>
              <div class="project-card__body">
                <span class="project-card__category">
                  {{ optional($post->display_date)->translatedFormat('d M Y') }} · {{ $post->reading_minutes }} min read
                </span>
                <h3 class="project-card__name">{{ $post->title }}</h3>
                <p class="project-card__desc">{{ $post->summary }}</p>
                <div class="project-card__tech">
                  @foreach(array_slice($post->tags ?? [], 0, 4) as $tag)<span class="tag">{{ $tag }}</span>@endforeach
                </div>
              </div>
            </a>
          </article>
        @endforeach
      </div>
      <div class="reveal mt-12">
        <a href="{{ route('about.blog') }}" class="btn btn--ghost">Semua Tulisan <span class="btn-arrow">→</span></a>
      </div>
    </div>
  </section>
  @endif

  <section class="section contact-cta" id="contact-cta">
    <div class="contact-cta__bg"></div>
    <div class="container text-center relative z-10">
      <div class="reveal">
        <span class="section-label justify-center">Let's Connect</span>
        <h2 class="contact-cta__heading">{{ $homeSettings['contact_cta_heading'] ?? "Let's Work Together" }}</h2>
        <p class="contact-cta__text">{{ $homeSettings['contact_cta_text'] ?? '' }}</p>
        <a href="{{ route('contact') }}" class="btn btn--primary btn--lg">Get In Touch <span class="btn-arrow">→</span></a>
        @php $ctaEmail = $personal['email'] ?? $siteSettings['contact_email'] ?? null; @endphp
        @if($ctaEmail)
          <div class="contact-cta__email-row">
          <a href="mailto:{{ $ctaEmail }}" class="contact-cta__email">
            <span class="contact-cta__email-icon" aria-hidden="true">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="m3 6.5 9 6 9-6"/></svg>
            </span>
            <span class="contact-cta__email-text">
              <span class="contact-cta__email-label">Email</span>
              <span class="contact-cta__email-value">{{ $ctaEmail }}</span>
            </span>
            <span class="contact-cta__email-arrow" aria-hidden="true">→</span>
          </a>
          </div>
        @endif
        @if($socials->isNotEmpty())
          <div class="contact-cta__socials">
            @foreach($socials as $social)
              <a href="{{ $social->url }}" class="contact-cta__social" target="_blank" rel="noopener noreferrer">{{ $social->platform }}</a>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </section>

  <section class="section message-board" id="message-board">
    <div class="container">
      <div class="section-header section-header--center reveal">
        <span class="section-label justify-center">Guestbook</span>
        <h2 class="section-heading">Leave a <span class="text-accent">Message</span></h2>
        <p class="section-subheading">Share your thoughts — messages are public and visible to all visitors</p>
      </div>

      <div class="mb-form-wrapper reveal" id="mb-form-wrapper">
        <form class="mb-form" id="mb-form">
          <div class="mb-form__row">
            <div class="form-group flex-1">
              <label for="mb-name" class="form-label">Your Name *</label>
              <input type="text" id="mb-name" class="form-input" placeholder="Enter your name" required maxlength="50" />
            </div>
            <div class="form-group flex-none">
              <label class="form-label">Rating (optional)</label>
              <div class="star-rating" id="mb-star-input">
                @for($i = 1; $i <= 5; $i++)
                  <span class="star-rating__star" data-rating="{{ $i }}">★</span>
                @endfor
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
        <div class="empty-state col-span-full">
          <div class="empty-state__icon">💬</div>
          <h3 class="empty-state__title">No messages yet</h3>
          <p class="empty-state__text">Be the first to leave a message!</p>
        </div>
      </div>

      <div class="mb-load-more text-center hidden" id="mb-load-more">
        <button class="btn btn--secondary" id="mb-load-more-btn">Load More Messages</button>
      </div>
    </div>
  </section>

  <section class="section gallery-section" id="gallery">
    <div class="container">
      <div class="section-header section-header--center reveal">
        <span class="section-label justify-center">Gallery</span>
        <h2 class="section-heading">Moments & <span class="text-accent">Memories</span></h2>
      </div>
      <div class="gallery-grid" id="gallery-grid">
        @forelse($gallery as $img)
          <button type="button" class="gallery-item reveal stagger-{{ min(($loop->index % 8) + 1, 8) }}" data-lightbox-index="{{ $loop->index }}" aria-label="Open image: {{ $img->alt ?: 'Gallery image' }}">
            <img src="{{ $img->url }}" alt="{{ $img->alt ?: 'Gallery image' }}" loading="lazy" />
          </button>
        @empty
          <div class="empty-state gallery-grid__empty"><p class="empty-state__text">No gallery images yet.</p></div>
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
