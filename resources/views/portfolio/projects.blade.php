@php
  $projectsFullName = $personal['fullName'] ?? $personal['name'] ?? 'Rama Adin';
@endphp

@extends('layouts.portfolio')

@section('title', "Projects — $projectsFullName")
@section('description', "Explore $projectsFullName's portfolio of web development projects — full-stack applications, frontends, and more.")

@section('content')
  <header class="page-header">
    <span class="section-label justify-center">Portfolio</span>
    <h1 class="page-header__title reveal">My <span class="text-accent">Projects</span></h1>
    <p class="page-header__subtitle reveal stagger-1">A collection of work I've built and contributed to</p>
  </header>

  <section class="section--compact">
    <div class="container">
      <div class="filter-tabs reveal" id="project-filter-tabs">
        <button class="filter-tab active" data-filter="all">All</button>
        <button class="filter-tab" data-filter="Frontend">Frontend</button>
        <button class="filter-tab" data-filter="Backend">Backend</button>
        <button class="filter-tab" data-filter="Full Stack">Full Stack</button>
      </div>

      <div class="projects-grid" id="projects-grid">
        @forelse($projects as $proj)
          <article class="project-card reveal stagger-{{ min($loop->iteration, 6) }}" data-project-id="{{ $proj->id }}" data-category="{{ $proj->category }}" onclick="navigateToProject('{{ $proj->id }}')">
            <div class="project-card__thumbnail">
              @if($proj->featured)
                <span class="project-card__badge">Featured</span>
              @endif
              <img src="{{ $proj->thumbnail }}" alt="{{ $proj->name }}" loading="lazy" />
              <div class="project-card__overlay">
                <span class="project-card__overlay-btn">View Details <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>
              </div>
            </div>
            <div class="project-card__body">
              <span class="project-card__category">{{ $proj->category }}</span>
              <h3 class="project-card__name">{{ $proj->name }}</h3>
              <p class="project-card__desc">{{ $proj->short_desc }}</p>
              <div class="project-card__tech">
                @foreach($proj->tech ?? [] as $tech)
                  <span class="tag">{{ $tech }}</span>
                @endforeach
              </div>
            </div>
          </article>
        @empty
          <div class="empty-state col-span-full">
            <p class="empty-state__text">No projects yet.</p>
          </div>
        @endforelse
      </div>
    </div>
  </section>
@endsection

@push('scripts')
  <script src="{{ asset('js/projects.js') }}"></script>
@endpush
