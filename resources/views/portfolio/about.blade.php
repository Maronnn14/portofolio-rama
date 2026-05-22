@php
  $aboutFullName = $personal['fullName'] ?? $personal['name'] ?? 'Rama Adin';
  $aboutImageSrc = $personal['aboutProfileImage'] ?? $personal['photo'] ?? $personal['profileImage'] ?? null;
  $defaultAboutImage = 'https://picsum.photos/seed/ramaprofile/400/500';
  $aboutStory = $personal['fullBio'] ?? $personal['shortBio'] ?? '';
  $aboutStoryParagraphs = $aboutStory ? explode("\n\n", $aboutStory) : [];
@endphp

@extends('layouts.portfolio')

@section('title', "About Me — $aboutFullName")
@section('description', "Learn about $aboutFullName — background, experience, interests, and journey as a Full Stack Developer.")

@section('content')
  <header class="page-header">
    <span class="section-label justify-center">Get to Know Me</span>
    <h1 class="page-header__title reveal">About <span class="text-accent">Me</span></h1>
    <p class="page-header__subtitle reveal stagger-1">The story behind the code</p>
  </header>

  <section class="section about-story">
    <div class="container">
      <div class="about-story__grid">
        <div class="about-story__image-col reveal-left">
          <div class="about-story__image-wrapper">
            <img src="{{ $aboutImageSrc ?? $defaultAboutImage }}" alt="{{ $aboutFullName }}" class="about-story__image" id="about-profile-image" loading="eager" />
            <div class="about-story__image-accent"></div>
          </div>
          <div class="about-story__stats" id="about-stats-container">
            <div class="about-story__stat">
              <span class="about-story__stat-number skeleton-text" id="stat-1-value">&nbsp;</span>
              <span class="about-story__stat-label skeleton-text" id="stat-1-label">&nbsp;</span>
            </div>
            <div class="about-story__stat">
              <span class="about-story__stat-number skeleton-text" id="stat-2-value">&nbsp;</span>
              <span class="about-story__stat-label skeleton-text" id="stat-2-label">&nbsp;</span>
            </div>
            <div class="about-story__stat">
              <span class="about-story__stat-number skeleton-text" id="stat-3-value">&nbsp;</span>
              <span class="about-story__stat-label skeleton-text" id="stat-3-label">&nbsp;</span>
            </div>
          </div>
        </div>
        <div class="about-story__content reveal-right">
          <h2 class="section-heading">My <span class="text-accent">Story</span></h2>
          <div class="divider"></div>
          <div class="about-story__text" id="about-story-text">
            @forelse($aboutStoryParagraphs as $paragraph)
              <p>{{ trim($paragraph) }}</p>
            @empty
              <p>No background story has been added yet.</p>
            @endforelse
          </div>
          <blockquote class="about-story__quote" id="about-quote-container">
            <p id="about-quote-text" class="skeleton-text">&nbsp;</p>
          </blockquote>
        </div>
      </div>
    </div>
  </section>

  <section class="section experience-section" id="experience">
    <div class="container">
      <div class="section-header section-header--center reveal">
        <span class="section-label justify-center">Career Path</span>
        <h2 class="section-heading">Experience & <span class="text-accent">Education</span></h2>
        <p class="section-subheading">My professional journey and growth over the years</p>
      </div>
      <div class="timeline" id="timeline">
        @forelse($experience as $exp)
          <div class="timeline__item reveal {{ $loop->index % 2 === 0 ? 'timeline__item--left' : 'timeline__item--right' }} stagger-{{ min($loop->iteration, 6) }}">
            <div class="timeline__dot"></div>
            <div class="timeline__content">
              <span class="timeline__date">{{ $exp->start_date }} — {{ $exp->end_date ?? 'Present' }}</span>
              <span class="timeline__type tag tag--accent">{{ $exp->type }}</span>
              <h3 class="timeline__role">{{ $exp->role }}</h3>
              <h4 class="timeline__company">{{ $exp->company }}</h4>
              <p class="timeline__desc">{{ $exp->description }}</p>
              @if(!empty($exp->tech))
                <div class="tags-list mt-4">
                  @foreach($exp->tech as $tech)
                    <span class="tag">{{ $tech }}</span>
                  @endforeach
                </div>
              @endif
            </div>
          </div>
          @unless($loop->last)
            <div class="timeline__connector"></div>
          @endunless
        @empty
          <div class="empty-state">
            <p class="empty-state__text">No experience entries yet.</p>
          </div>
        @endforelse
      </div>
    </div>
  </section>

  <section class="section interests-section">
    <div class="container">
      <div class="section-header section-header--center reveal">
        <span class="section-label justify-center">Beyond Code</span>
        <h2 class="section-heading">Interests & <span class="text-accent">Hobbies</span></h2>
      </div>
      <div class="interests-grid" id="interests-grid">
        @forelse($interests as $int)
          <div class="interest-card reveal stagger-{{ min($loop->iteration, 6) }}">
            <div class="interest-card__icon-wrapper">
              @if($int->icon)
                <img class="interest-card__icon" src="{{ $int->icon }}" alt="{{ $int->name }}" loading="lazy" />
              @else
                <span class="interest-card__icon">🎯</span>
              @endif
            </div>
            <h3 class="interest-card__name">{{ $int->name }}</h3>
            <p class="interest-card__desc">{{ $int->description }}</p>
          </div>
        @empty
          <div class="empty-state col-span-full">
            <p class="empty-state__text">No interests listed yet.</p>
          </div>
        @endforelse
      </div>
    </div>
  </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function loadAbout() {
  var quoteEl = document.getElementById('about-quote-text');
  var statIds = [
    'stat-1-value', 'stat-1-label',
    'stat-2-value', 'stat-2-label',
    'stat-3-value', 'stat-3-label',
  ];
  var statEls = statIds.map(function (id) { return document.getElementById(id); });
  try {
    var data = await API.about.get();
    if (quoteEl) {
      quoteEl.textContent = '"' + (data.quote || '') + '"';
      quoteEl.classList.remove('skeleton-text');
    }
    if (data.stats && data.stats.length) {
      data.stats.forEach(function (s, i) {
        var valueEl = statEls[i * 2];
        var labelEl = statEls[i * 2 + 1];
        if (valueEl) { valueEl.textContent = s.value || ''; valueEl.classList.remove('skeleton-text'); }
        if (labelEl) { labelEl.textContent = s.label || ''; labelEl.classList.remove('skeleton-text'); }
      });
    }
  } catch (_) {
    if (quoteEl) { quoteEl.textContent = ''; quoteEl.classList.remove('skeleton-text'); }
    statEls.forEach(function (el) { if (el) { el.textContent = ''; el.classList.remove('skeleton-text'); } });
  }
});
</script>
@endpush
