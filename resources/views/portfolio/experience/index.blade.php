@extends('layouts.portfolio')

@section('title', 'Work Experience')
@section('description', 'Professional experience across full-stack development, frontend engineering, and product delivery.')

@section('content')
  <header class="page-header">
    <span class="section-label justify-center">Career</span>
    <h1 class="page-header__title reveal">Work <span class="text-accent">Experience</span></h1>
    <p class="page-header__subtitle reveal stagger-1">Roles, responsibilities, and outcomes</p>
  </header>

  <section class="section--compact">
    <div class="container">
      <div class="project-toolbar reveal">
        <div class="filter-tabs" aria-label="Filter experience by type">
          <a href="{{ route('experience.index', array_filter(['q' => $filters['q'] ?: null])) }}" class="filter-tab{{ ! $filters['type'] ? ' active' : '' }}">All</a>
          @foreach($types as $type)
            <a href="{{ route('experience.index', array_filter(['q' => $filters['q'] ?: null, 'type' => $type])) }}" class="filter-tab{{ $filters['type'] === $type ? ' active' : '' }}">{{ $type }}</a>
          @endforeach
        </div>
        <form method="GET" action="{{ route('experience.index') }}" class="search-form" role="search">
          @if($filters['type'])<input type="hidden" name="type" value="{{ $filters['type'] }}">@endif
          <label for="experience-search" class="sr-only">Search work experience</label>
          <input id="experience-search" type="search" name="q" value="{{ $filters['q'] }}" class="form-input search-form__input" placeholder="Search experience…">
          <button type="submit" class="btn btn--secondary btn--sm">Search</button>
        </form>
      </div>

      <div class="article-feed">
        @forelse($experiences as $experience)
          <article class="feed-card reveal stagger-{{ min($loop->iteration, 5) }}">
            @if($experience->cover_url)
              <a href="{{ route('experience.show', $experience) }}" class="feed-card__media" tabindex="-1" aria-hidden="true">
                <img src="{{ $experience->cover_url }}" alt="" loading="lazy" />
              </a>
            @endif
            <div class="feed-card__body">
              <div class="article-meta">
                <span class="tag tag--accent">{{ $experience->type }}</span>
                <time>{{ $experience->start_date }} — {{ $experience->end_date ?? 'Present' }}</time>
              </div>
              <h2 class="feed-card__title"><a href="{{ route('experience.show', $experience) }}">{{ $experience->role }}</a></h2>
              <p class="feed-card__subtitle">{{ $experience->company }}@if($experience->location) · {{ $experience->location }}@endif</p>
              <p class="feed-card__excerpt">{{ $experience->description }}</p>
              @if(!empty($experience->tech))
                <div class="tags-list">@foreach($experience->tech as $tech)<span class="tag">{{ $tech }}</span>@endforeach</div>
              @endif
              <a href="{{ route('experience.show', $experience) }}" class="feed-card__more">Read the role <span aria-hidden="true">→</span></a>
            </div>
          </article>
        @empty
          <div class="empty-state">
            <h2 class="empty-state__title">No experience found</h2>
            <p class="empty-state__text">Try a different search term or filter.</p>
            <a href="{{ route('experience.index') }}" class="btn btn--secondary btn--sm">Clear filters</a>
          </div>
        @endforelse
      </div>
    </div>
  </section>
@endsection
