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
          <div class="about-story__stats">
            <div class="about-story__stat">
              <span class="about-story__stat-number">{{ $personal['yearsExperience'] ?? '3+' }}</span>
              <span class="about-story__stat-label">Years Experience</span>
            </div>
            <div class="about-story__stat">
              <span class="about-story__stat-number">{{ $personal['projectsDelivered'] ?? '15+' }}</span>
              <span class="about-story__stat-label">Projects Delivered</span>
            </div>
            <div class="about-story__stat">
              <span class="about-story__stat-number">{{ $personal['happyClients'] ?? '10+' }}</span>
              <span class="about-story__stat-label">Happy Clients</span>
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
          <blockquote class="about-story__quote">
            <p>"Great software is built with empathy — understanding both the user and the problem."</p>
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
