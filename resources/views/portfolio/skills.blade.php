@php
  $skillsFullName = $personal['fullName'] ?? $personal['name'] ?? 'Rama Adin';
  $skillIconMap = ['html5' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg', 'css3' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg', 'javascript' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg', 'react' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/react/react-original.svg', 'vuejs' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vuejs/vuejs-original.svg', 'tailwindcss' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/tailwindcss/tailwindcss-original.svg', 'php' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg', 'laravel' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/laravel/laravel-original.svg', 'mysql' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg', 'nodejs' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nodejs/nodejs-original.svg', 'git' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/git/git-original.svg', 'docker' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/docker/docker-original.svg', 'figma' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/figma/figma-original.svg', 'python' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/python/python-original.svg', 'typescript' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/typescript/typescript-original.svg'];
@endphp

@extends('layouts.portfolio')

@section('title', "Skills & Expertise — $skillsFullName")
@section('description', "Explore $skillsFullName's technical skills — frontend, backend, design tools, and more. Click any skill for details.")

@section('content')
  <header class="page-header">
    <span class="section-label" style="justify-content: center;">What I Know</span>
    <h1 class="page-header__title reveal">Skills & <span class="text-accent">Expertise</span></h1>
    <p class="page-header__subtitle reveal stagger-1">Technologies and tools I use to bring ideas to life</p>
  </header>

  <section class="section--compact">
    <div class="container">
      <div class="filter-tabs reveal" id="skill-filter-tabs">
        <button class="filter-tab active" data-filter="all">All</button>
        <button class="filter-tab" data-filter="Frontend">Frontend</button>
        <button class="filter-tab" data-filter="Backend">Backend</button>
        <button class="filter-tab" data-filter="Tools">Tools</button>
        <button class="filter-tab" data-filter="Design">Design</button>
      </div>

      <div class="skills-grid" id="skills-grid">
        @forelse($skills as $skill)
          <div class="skill-card reveal stagger-{{ min(($loop->index % 8) + 1, 8) }}" data-category="{{ $skill->category }}" data-skill-id="{{ $skill->id }}" onclick="openSkillModal('{{ $skill->id }}')">
            <div class="skill-card__header">
              <div class="skill-card__icon">
                <img src="{{ $skill->icon ? ($skillIconMap[$skill->icon] ?? '') : '' }}" alt="{{ $skill->name }}" loading="lazy" />
              </div>
              <div>
                <div class="skill-card__name">{{ $skill->name }}</div>
                <div class="skill-card__category">{{ $skill->category }}</div>
              </div>
            </div>
            <p class="skill-card__teaser">{{ $skill->teaser }}</p>
            <span class="skill-card__arrow">→</span>
          </div>
        @empty
          <div class="empty-state" style="grid-column: 1/-1;">
            <p class="empty-state__text">No skills listed yet.</p>
          </div>
        @endforelse
      </div>
    </div>
  </section>

  <div class="skill-modal" id="skill-modal">
    <div class="skill-modal__overlay" id="skill-modal-overlay"></div>
    <div class="skill-modal__content" id="skill-modal-content"></div>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('js/lightbox.js') }}"></script>
  <script src="{{ asset('js/skills.js') }}"></script>
@endpush
