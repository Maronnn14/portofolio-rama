@php
  use App\Support\Icon;
@endphp

@extends('layouts.portfolio')

@section('title', 'Skills & Expertise')
@section('description', 'Tech stack skills across frontend, backend, tools — plus other hard skills like spreadsheets, design, and collaboration soft skills.')

@section('content')
  <header class="page-header">
    <span class="section-label justify-center">What I Know</span>
    <h1 class="page-header__title reveal">Skills &amp; <span class="text-accent">Expertise</span></h1>
    <p class="page-header__subtitle reveal stagger-1">Technologies and tools I use to bring ideas to life</p>
  </header>

  <section class="section--compact">
    <div class="container">
      <div class="section-header reveal">
        <h2 class="section-heading">What I <span class="text-accent">Code</span> With</h2>
        <p class="section-subheading">Languages, frameworks, and tools I use day to day</p>
      </div>

      <div class="skills-toolbar reveal">
        <div class="skill-view-toggle" id="skill-view-toggle" role="tablist" aria-label="Choose skill type">
          <button type="button" class="skill-view-toggle__btn active" data-view="all" role="tab" aria-selected="true">
            All Skills<span class="skill-view-toggle__count">{{ $techSkills->count() + $otherSkills->count() + $softSkills->count() }}</span>
          </button>
          <button type="button" class="skill-view-toggle__btn" data-view="tech" role="tab" aria-selected="false">
            Tech Stack<span class="skill-view-toggle__count">{{ $techSkills->count() }}</span>
          </button>
          <button type="button" class="skill-view-toggle__btn" data-view="other" role="tab" aria-selected="false">
            Other Skills<span class="skill-view-toggle__count">{{ $otherSkills->count() }}</span>
          </button>
          <button type="button" class="skill-view-toggle__btn" data-view="soft" role="tab" aria-selected="false">
            Soft Skills<span class="skill-view-toggle__count">{{ $softSkills->count() }}</span>
          </button>
        </div>
        <p class="skills-toolbar__hint" id="skills-view-hint">Tech stack for the build, other skills for everything around it.</p>
      </div>

      <div class="filter-tabs reveal" id="skill-filter-tabs" role="tablist" aria-label="Filter tech stack skills by category">
        <button class="filter-tab active" data-filter="all" role="tab" aria-selected="true">All</button>
        @foreach($categories as $category)
          <button class="filter-tab" data-filter="{{ $category }}" role="tab" aria-selected="false">{{ $category }}</button>
        @endforeach
      </div>

      <div class="skills-groups" id="skills-groups">
        @forelse($hardSkills as $category => $items)
          <section class="skill-group" data-skill-group data-category="{{ $category ?: 'Other' }}">
            <div class="skill-group__header reveal">
              <h2 class="skill-group__title">{{ $category ?: 'Other' }}</h2>
              <span class="skill-group__count">{{ $items->count() }} {{ \Illuminate\Support\Str::plural('skill', $items->count()) }}</span>
            </div>

            <div class="skills-grid" id="skills-grid-{{ $loop->index }}">
              @foreach($items as $skill)
                @include('portfolio.partials.skill-card', [
                  'skill' => $skill,
                  'stagger' => ($loop->index % 8) + 1,
                ])
              @endforeach
            </div>
          </section>
        @empty
          <div class="empty-state col-span-full"><p class="empty-state__text">No tech stack skills listed yet.</p></div>
        @endforelse
      </div>
    </div>
  </section>

  <section class="section section--compact other-skills-section" id="other-skills-section">
    <div class="container">
      <div class="section-header reveal">
        <span class="section-label">Beyond the Code</span>
        <h2 class="section-heading">Other <span class="text-accent">Skills</span></h2>
        <p class="section-subheading">Hard skills outside programming — spreadsheets, design, media, and daily work tools</p>
      </div>

      @if($otherSkills->isNotEmpty())
        <div class="skills-grid">
          @foreach($otherSkills as $skill)
            @include('portfolio.partials.skill-card', [
              'skill' => $skill,
              'stagger' => ($loop->iteration % 8),
            ])
          @endforeach
        </div>
      @else
        <div class="empty-state"><p class="empty-state__text">No other skills listed yet.</p></div>
      @endif
    </div>
  </section>

  <section class="section section--compact soft-skills-section" id="soft-skills-section">
    <div class="container">
      <div class="section-header reveal">
        <span class="section-label">Collaboration</span>
        <h2 class="section-heading">Soft <span class="text-accent">Skills</span></h2>
        <p class="section-subheading">The human side of shipping good work</p>
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

  <div class="skill-modal" id="skill-modal" role="dialog" aria-modal="true" aria-label="Skill details">
    <div class="skill-modal__overlay" id="skill-modal-overlay"></div>
    <div class="skill-modal__content" id="skill-modal-content"></div>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('js/lightbox.js') }}"></script>
  <script src="{{ asset('js/skills.js') }}"></script>
@endpush
