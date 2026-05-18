@extends('layouts.portfolio')

@section('title', "Skills & Expertise — Rama Adin")
@section('description', "Explore Rama Adin's technical skills — frontend, backend, design tools, and more. Click any skill for details.")

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/lightbox.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/skills.css') }}" />
@endpush

@section('content')
<!-- Page Header -->
  <header class="page-header">
    <span class="section-label" style="justify-content: center;">What I Know</span>
    <h1 class="page-header__title reveal">Skills & <span class="text-accent">Expertise</span></h1>
    <p class="page-header__subtitle reveal stagger-1">Technologies and tools I use to bring ideas to life</p>
  </header>

  <!-- Filter Tabs -->
  <section class="section--compact">
    <div class="container">
      <div class="filter-tabs reveal" id="skill-filter-tabs">
        <button class="filter-tab active" data-filter="all">All</button>
        <button class="filter-tab" data-filter="Frontend">Frontend</button>
        <button class="filter-tab" data-filter="Backend">Backend</button>
        <button class="filter-tab" data-filter="Tools">Tools</button>
        <button class="filter-tab" data-filter="Design">Design</button>
      </div>

      <!-- Skills Grid -->
      <div class="skills-grid" id="skills-grid">
        <!-- Rendered by JS -->
      </div>
    </div>
  </section>

  <!-- Skill Detail Modal -->
  <div class="skill-modal" id="skill-modal">
    <div class="skill-modal__overlay" id="skill-modal-overlay"></div>
    <div class="skill-modal__content" id="skill-modal-content">
      <!-- Rendered by JS -->
    </div>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('js/lightbox.js') }}"></script>
  <script src="{{ asset('js/skills.js') }}"></script>
@endpush
