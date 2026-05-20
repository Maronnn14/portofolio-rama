@extends('layouts.portfolio')

@section('title', "Projects — Rama Adin")
@section('description', "Explore Rama Adin's portfolio of web development projects — full-stack applications, frontends, and more.")

@section('content')
<!-- Page Header -->
  <header class="page-header">
    <span class="section-label" style="justify-content: center;">Portfolio</span>
    <h1 class="page-header__title reveal">My <span class="text-accent">Projects</span></h1>
    <p class="page-header__subtitle reveal stagger-1">A collection of work I've built and contributed to</p>
  </header>

  <section class="section--compact">
    <div class="container">
      <!-- Filter Tabs -->
      <div class="filter-tabs reveal" id="project-filter-tabs">
        <button class="filter-tab active" data-filter="all">All</button>
        <button class="filter-tab" data-filter="Frontend">Frontend</button>
        <button class="filter-tab" data-filter="Backend">Backend</button>
        <button class="filter-tab" data-filter="Full Stack">Full Stack</button>
      </div>

      <!-- Projects Grid -->
      <div class="projects-grid" id="projects-grid">
        <!-- Rendered by JS -->
      </div>
    </div>
  </section>
@endsection

@push('scripts')
  <script src="{{ asset('js/projects.js') }}"></script>
@endpush
