@php
  use App\Models\PersonalInfo;
  use App\Support\Icon;

  $name = PersonalInfo::displayName($personal);
  $profileImage = $personal['aboutProfileImage'] ?? $personal['photo'] ?? $personal['profileImage'] ?? null;
  $branding = $personal['branding_statement'] ?? '';
  $bio = $personal['fullBio'] ?? $personal['shortBio'] ?? '';
  $bioParagraphs = array_filter(preg_split('/\R{2,}/', $bio));
  $details = collect([
      'Full name' => $personal['fullName'] ?? $personal['name'] ?? null,
      'Preferred name' => $personal['nickname'] ?? null,
      'Role' => $personal['role'] ?? null,
      'Location' => $personal['location'] ?? null,
      'Email' => $personal['email'] ?? null,
      'GitHub' => ($personal['github'] ?? null) ? '@'.$personal['github'] : null,
  ])->filter();
@endphp

@extends('layouts.portfolio')

@section('title', 'Biodata & Personal Branding')
@section('description', trim($branding.' '.($personal['shortBio'] ?? '')))

@section('content')
  <header class="page-header">
    <span class="section-label justify-center">About Me</span>
    <h1 class="page-header__title reveal">Biodata &amp; <span class="text-accent">Branding</span></h1>
    <p class="page-header__subtitle reveal stagger-1">Who I am, what I value, and how I work</p>
  </header>

  @include('layouts.partials.about-nav')

  <section class="section--compact about-profile-section">
    <div class="container">
      <div class="profile-grid">
        <div class="profile-card reveal-left">
          @if($profileImage)
            <img src="{{ $profileImage }}" alt="{{ $name }}" class="profile-card__image" loading="eager" />
          @else
            <div class="profile-card__image profile-card__image--placeholder" aria-hidden="true">{{ Icon::initials($name) }}</div>
          @endif
          <div class="profile-card__body">
            <h1 class="profile-card__name">{{ $name }}</h1>
            @if($personal['role'] ?? null)<p class="profile-card__role">{{ $personal['role'] }}</p>@endif
            @if($personal['location'] ?? null)<p class="profile-card__location">{{ $personal['location'] }}</p>@endif
            <div class="profile-card__actions">
              <a href="{{ route('about.cv') }}" class="btn btn--primary btn--sm">View CV</a>
              <a href="{{ route('contact') }}" class="btn btn--secondary btn--sm">Contact</a>
            </div>
          </div>
        </div>

        <div class="profile-content reveal-right">
          @if($branding)
            <blockquote class="branding-statement">
              <p>{{ $branding }}</p>
            </blockquote>
          @endif

          <h2 class="section-heading">Personal <span class="text-accent">Summary</span></h2>
          <div class="divider"></div>
          @forelse($bioParagraphs as $paragraph)
            <p class="profile-content__paragraph">{{ trim($paragraph) }}</p>
          @empty
            <p class="profile-content__paragraph">No personal summary has been added yet.</p>
          @endforelse

          @if($details->isNotEmpty())
            <dl class="profile-details">
              @foreach($details as $label => $value)
                <div class="profile-details__row">
                  <dt class="profile-details__label">{{ $label }}</dt>
                  <dd class="profile-details__value">
                    @if($label === 'Email')
                      <a href="mailto:{{ $value }}">{{ $value }}</a>
                    @elseif($label === 'GitHub')
                      <a href="https://github.com/{{ $personal['github'] }}" target="_blank" rel="noopener noreferrer">{{ $value }}</a>
                    @else
                      {{ $value }}
                    @endif
                  </dd>
                </div>
              @endforeach
            </dl>
          @endif
        </div>
      </div>
    </div>
  </section>

  <section class="section" id="education">
    <div class="container">
      <div class="section-header reveal">
        <span class="section-label">Education</span>
        <h2 class="section-heading">Academic <span class="text-accent">Background</span></h2>
      </div>
      <div class="timeline timeline--single">
        @forelse($education as $item)
          <article class="timeline__item timeline__item--left reveal stagger-{{ min($loop->iteration, 6) }}">
            <div class="timeline__dot"></div>
            <div class="timeline__content">
              <span class="timeline__date">{{ $item->start_year }} — {{ $item->end_year ?: 'Present' }}</span>
              <h3 class="timeline__role">{{ $item->program }}</h3>
              <h4 class="timeline__company">{{ $item->institution }}@if($item->location) · {{ $item->location }}@endif</h4>
              @if($item->description)<p class="timeline__desc">{{ $item->description }}</p>@endif
              @if(!empty($item->highlights))
                <ul class="article-list">
                  @foreach($item->highlights as $highlight)
                    <li>{{ $highlight }}</li>
                  @endforeach
                </ul>
              @endif
            </div>
          </article>
          @unless($loop->last)<div class="timeline__connector"></div>@endunless
        @empty
          <div class="empty-state"><p class="empty-state__text">No education entries yet.</p></div>
        @endforelse
      </div>
    </div>
  </section>

  <section class="section section--compact">
    <div class="container">
      <div class="section-header reveal">
        <span class="section-label">How I Work</span>
        <h2 class="section-heading">Soft <span class="text-accent">Skills</span></h2>
      </div>
      <div class="soft-skills-grid">
        @forelse($softSkills as $skill)
          <article class="soft-skill-card reveal stagger-{{ min($loop->iteration, 6) }}">
            <div class="soft-skill-card__icon" aria-hidden="true">{{ Icon::initials($skill->name) }}</div>
            <h3 class="soft-skill-card__name">{{ $skill->name }}</h3>
            <p class="soft-skill-card__desc">{{ $skill->description }}</p>
          </article>
        @empty
          <div class="empty-state"><p class="empty-state__text">No soft skills listed yet.</p></div>
        @endforelse
      </div>
    </div>
  </section>

  <section class="section section--compact">
    <div class="container">
      <div class="section-header reveal">
        <span class="section-label">Experience</span>
        <h2 class="section-heading">Recent <span class="text-accent">Roles</span></h2>
      </div>
      <div class="experience-preview__list">
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
          <div class="empty-state"><p class="empty-state__text">No work experience listed yet.</p></div>
        @endforelse
      </div>
      <div class="mt-12"><a href="{{ route('experience.index') }}" class="btn btn--ghost">All Work Experience <span class="btn-arrow">→</span></a></div>
    </div>
  </section>
@endsection
