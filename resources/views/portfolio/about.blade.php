@php
  use App\Models\PersonalInfo;
  use App\Support\Icon;

  $aboutName = PersonalInfo::displayName($personal);
  $aboutImage = $personal['aboutProfileImage'] ?? $personal['photo'] ?? $personal['profileImage'] ?? null;
  $aboutStory = $personal['fullBio'] ?? $personal['shortBio'] ?? $personal['branding_statement'] ?? '';
  $aboutStoryParagraphs = array_filter(preg_split('/\R{2,}/', $aboutStory));
  $stats = collect(range(1, 3))
      ->map(fn ($i) => [
          'value' => $personal["stat_{$i}_value"] ?? null,
          'label' => $personal["stat_{$i}_label"] ?? null,
      ])
      ->filter(fn ($stat) => filled($stat['value']) || filled($stat['label']));
  $timeline = collect($education->map(fn ($item) => [
      'role' => $item->program,
      'company' => $item->institution,
      'start_date' => $item->start_year,
      'end_date' => $item->end_year,
      'type' => 'Education',
      'description' => $item->description,
      'tech' => [],
      'url' => route('about.biodata').'#education',
  ]))->merge($experience->map(fn ($item) => [
      'role' => $item->role,
      'company' => $item->company,
      'start_date' => $item->start_date,
      'end_date' => $item->end_date,
      'type' => $item->type,
      'description' => $item->description,
      'tech' => $item->tech ?? [],
      'url' => route('experience.index'),
  ]));
@endphp

@extends('layouts.portfolio')

@section('title', 'About Me')
@section('description', trim(($personal['shortBio'] ?? '').' '.($personal['branding_statement'] ?? '')))

@section('content')
  <header class="page-header">
    <span class="section-label justify-center">Get to Know Me</span>
    <h1 class="page-header__title reveal">About <span class="text-accent">Me</span></h1>
    <p class="page-header__subtitle reveal stagger-1">The story behind the code</p>
  </header>

  <nav class="container about-subnav reveal" aria-label="About sections">
    <a href="{{ route('about.biodata') }}" class="about-subnav__link">Biodata &amp; Branding</a>
    <a href="{{ route('about.cv') }}" class="about-subnav__link">Download CV</a>
    <a href="{{ route('about.interests') }}" class="about-subnav__link">Interests &amp; Hobbies</a>
  </nav>

  <section class="section about-story">
    <div class="container">
      <div class="about-story__grid">
        <div class="about-story__image-col reveal-left">
          <div class="about-story__image-wrapper">
            @if($aboutImage)
              <img src="{{ $aboutImage }}" alt="{{ $aboutName }}" class="about-story__image" id="about-profile-image" loading="eager" />
            @else
              <div class="about-story__image about-story__image--placeholder" aria-hidden="true">{{ Icon::initials($aboutName) }}</div>
            @endif
            <div class="about-story__image-accent"></div>
          </div>
          @if($stats->isNotEmpty())
            <div class="about-story__stats">
              @foreach($stats as $stat)
                <div class="about-story__stat">
                  <span class="about-story__stat-number">{{ $stat['value'] }}</span>
                  <span class="about-story__stat-label">{{ $stat['label'] }}</span>
                </div>
              @endforeach
            </div>
          @endif
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
          @if($personal['about_quote'] ?? null)
            <blockquote class="about-story__quote">
              <p>{{ $personal['about_quote'] }}</p>
            </blockquote>
          @endif
        </div>
      </div>
    </div>
  </section>

  <section class="section experience-section" id="experience">
    <div class="container">
      <div class="section-header section-header--center reveal">
        <span class="section-label justify-center">Career Path</span>
        <h2 class="section-heading">Experience &amp; <span class="text-accent">Education</span></h2>
        <p class="section-subheading">My professional journey and growth over the years</p>
      </div>
      <div class="timeline" id="timeline">
        @forelse($timeline as $item)
          <div class="timeline__item reveal {{ $loop->index % 2 === 0 ? 'timeline__item--left' : 'timeline__item--right' }} stagger-{{ min($loop->iteration, 6) }}">
            <div class="timeline__dot"></div>
            <div class="timeline__content">
              <span class="timeline__date">{{ $item['start_date'] }} — {{ $item['end_date'] ?: 'Present' }}</span>
              <span class="timeline__type tag tag--accent">{{ $item['type'] }}</span>
              <h3 class="timeline__role">{{ $item['role'] }}</h3>
              <h4 class="timeline__company">{{ $item['company'] }}</h4>
              <p class="timeline__desc">{{ $item['description'] }}</p>
              @if(!empty($item['tech']))
                <div class="tags-list mt-4">
                  @foreach($item['tech'] as $tech)<span class="tag">{{ $tech }}</span>@endforeach
                </div>
              @endif
            </div>
          </div>
          @unless($loop->last)<div class="timeline__connector"></div>@endunless
        @empty
          <div class="empty-state"><p class="empty-state__text">No experience entries yet.</p></div>
        @endforelse
      </div>
    </div>
  </section>

  <section class="section interests-section">
    <div class="container">
      <div class="section-header section-header--center reveal">
        <span class="section-label justify-center">Beyond Code</span>
        <h2 class="section-heading">Interests &amp; <span class="text-accent">Hobbies</span></h2>
      </div>
      <div class="interests-grid" id="interests-grid">
        @forelse($interests as $int)
          <div class="interest-card reveal stagger-{{ min($loop->iteration, 6) }}">
            <div class="interest-card__icon-wrapper">
              @if($int->icon)
                <img class="interest-card__icon" src="{{ $int->icon }}" alt="" loading="lazy" />
              @else
                <span class="interest-card__icon" aria-hidden="true">{{ Icon::initials($int->name) }}</span>
              @endif
            </div>
            <h3 class="interest-card__name">{{ $int->name }}</h3>
            <p class="interest-card__desc">{{ $int->description }}</p>
          </div>
        @empty
          <div class="empty-state col-span-full"><p class="empty-state__text">No interests listed yet.</p></div>
        @endforelse
      </div>
      <div class="text-center mt-12">
        <a href="{{ route('about.interests') }}" class="btn btn--ghost">Explore Interests <span class="btn-arrow">→</span></a>
      </div>
    </div>
  </section>
@endsection
