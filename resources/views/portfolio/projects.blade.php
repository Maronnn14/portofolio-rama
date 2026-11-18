@php
  use App\Support\Icon;
@endphp

@extends('layouts.portfolio')

@section('title', 'Coding Projects')
@section('description', 'Browse coding projects and case studies: full-stack applications, frontends, APIs, and experiments.')

@section('content')
  <header class="page-header">
    <span class="section-label justify-center">Portfolio</span>
    <h1 class="page-header__title reveal">Coding <span class="text-accent">Projects</span></h1>
    <p class="page-header__subtitle reveal stagger-1">A collection of work I've built and contributed to</p>
  </header>

  <section class="section--compact">
    <div class="container">
      <div class="project-toolbar reveal">
        <div class="filter-tabs" id="project-filter-tabs" aria-label="Filter projects by category">
          <a href="{{ route('projects.index', array_filter(['q' => $filters['q'] ?: null, 'tech' => $filters['tech'] ?: null])) }}" class="filter-tab{{ ! $filters['category'] ? ' active' : '' }}" @if(! $filters['category']) aria-current="page" @endif>All</a>
          @foreach($categories as $category)
            <a href="{{ route('projects.index', array_filter(['q' => $filters['q'] ?: null, 'tech' => $filters['tech'] ?: null, 'category' => $category])) }}" class="filter-tab{{ $filters['category'] === $category ? ' active' : '' }}" @if($filters['category'] === $category) aria-current="page" @endif>{{ $category }}</a>
          @endforeach
        </div>

        <form method="GET" action="{{ route('projects.index') }}" class="search-form" role="search">
          @if($filters['category'])<input type="hidden" name="category" value="{{ $filters['category'] }}">@endif
          <label for="project-tech-filter" class="sr-only">Filter projects by technology</label>
          <select id="project-tech-filter" name="tech" class="form-input search-form__select" onchange="this.form.submit()">
            <option value="">All technologies</option>
            @foreach($techs as $tech)
              <option value="{{ $tech }}" @selected($filters['tech'] === $tech)>{{ $tech }}</option>
            @endforeach
          </select>
          <label for="project-search" class="sr-only">Search projects</label>
          <input id="project-search" type="search" name="q" value="{{ $filters['q'] }}" class="form-input search-form__input" placeholder="Search projects…">
          <button type="submit" class="btn btn--secondary btn--sm">Search</button>
        </form>
      </div>

      <p class="results-count" role="status">{{ $projects->count() }} {{ \Illuminate\Support\Str::plural('project', $projects->count()) }} found</p>

      <div class="projects-grid" id="projects-grid">
        @forelse($projects as $proj)
          <article class="project-card reveal stagger-{{ min($loop->iteration, 6) }}" data-category="{{ $proj->category }}">
            <a href="{{ route('projects.show', $proj) }}" class="project-card__link">
              <div class="project-card__thumbnail">
                @if($proj->featured)<span class="project-card__badge">Featured</span>@endif
                @if($proj->thumbnail)
                  <img src="{{ $proj->thumbnail }}" alt="{{ $proj->name }}" loading="lazy" />
                @else
                  <span class="project-card__placeholder" aria-hidden="true">{{ Icon::initials($proj->name) }}</span>
                @endif
                <span class="project-card__overlay"><span class="project-card__overlay-btn">View Case Study <span aria-hidden="true">→</span></span></span>
              </div>
              <div class="project-card__body">
                <span class="project-card__category">{{ $proj->category }}</span>
                <h2 class="project-card__name">{{ $proj->name }}</h2>
                <p class="project-card__desc">{{ $proj->short_desc }}</p>
                <div class="project-card__tech">
                  @foreach(array_slice($proj->tech ?? [], 0, 4) as $tech)<span class="tag">{{ $tech }}</span>@endforeach
                </div>
              </div>
            </a>
          </article>
        @empty
          <div class="empty-state col-span-full">
            <h2 class="empty-state__title">No projects found</h2>
            <p class="empty-state__text">Try a different search term or category.</p>
            <a href="{{ route('projects.index') }}" class="btn btn--secondary btn--sm">Clear filters</a>
          </div>
        @endforelse
      </div>
    </div>
  </section>
@endsection
