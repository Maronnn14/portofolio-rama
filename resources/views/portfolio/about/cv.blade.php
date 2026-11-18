@php
  use App\Models\PersonalInfo;

  $name = PersonalInfo::displayName($personal);
  $cvUrl = $personal['cv_file'] ?? null;
  $cvLabel = $personal['cv_label'] ?? 'Curriculum Vitae';
  $cvVersion = $personal['cv_version'] ?? null;
  $cvUpdatedAt = \App\Models\PersonalInfo::cvUpdatedLabel();
@endphp

@extends('layouts.portfolio')

@section('title', 'Download CV')
@section('description', 'Download the latest '.$cvLabel.' of '.$name.'.')

@section('content')
  <header class="page-header">
    <span class="section-label justify-center">Resume</span>
    <h1 class="page-header__title reveal">Download <span class="text-accent">CV</span></h1>
    <p class="page-header__subtitle reveal stagger-1">A concise overview of my experience, education, and skills</p>
  </header>

  @include('layouts.partials.about-nav')

  <section class="section--compact">
    <div class="container">
      <div class="cv-download-card reveal">
        <div class="cv-download-card__icon" aria-hidden="true">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 15h6"/><path d="M9 11h2"/></svg>
        </div>
        <div class="cv-download-card__body">
          <h2 class="cv-download-card__title">{{ $cvLabel }}</h2>
          <p class="cv-download-card__meta">
            @if($cvVersion)Version {{ $cvVersion }}@endif
            @if($cvVersion && $cvUpdatedAt) · @endif
            @if($cvUpdatedAt)Updated {{ $cvUpdatedAt }}@endif
            @if(!$cvVersion && !$cvUpdatedAt)PDF resume, updated from the admin panel.@endif
          </p>
          @if($cvUrl)
            <div class="cv-download-card__actions">
              <a href="{{ $cvUrl }}" class="btn btn--primary" download="{{ \Illuminate\Support\Str::slug($name) }}-cv.pdf">Download CV</a>
              <a href="{{ $cvUrl }}" class="btn btn--secondary" target="_blank" rel="noopener noreferrer">Preview in Browser</a>
            </div>
          @else
            <div class="cv-download-card__empty">
              <p>The CV file has not been uploaded yet. Add it in <strong>Admin → Profile → CV / Resume</strong>.</p>
              <a href="{{ route('contact') }}" class="btn btn--secondary">Contact Me Instead</a>
            </div>
          @endif
        </div>
      </div>
    </div>
  </section>

  <section class="section cv-preview-section">
    <div class="container">
      <div class="cv-document">
        <header class="cv-document__header">
          <h2 class="cv-document__name">{{ $name }}</h2>
          @if($personal['role'] ?? null)<p class="cv-document__role">{{ $personal['role'] }}</p>@endif
          <div class="cv-document__contact">
            @if($personal['email'] ?? null)<span>{{ $personal['email'] }}</span>@endif
            @if($personal['location'] ?? null)<span>{{ $personal['location'] }}</span>@endif
            @if($personal['github'] ?? null)<a href="https://github.com/{{ $personal['github'] }}" target="_blank" rel="noopener noreferrer">github.com/{{ $personal['github'] }}</a>@endif
          </div>
        </header>

        @if($personal['shortBio'] ?? null)
          <section class="cv-document__section">
            <h3 class="cv-document__heading">Profile</h3>
            <p>{{ $personal['shortBio'] }}</p>
          </section>
        @endif

        <section class="cv-document__section">
          <h3 class="cv-document__heading">Work Experience</h3>
          @forelse($experience as $exp)
            <article class="cv-entry">
              <div class="cv-entry__top">
                <h4 class="cv-entry__title">{{ $exp->role }}</h4>
                <span class="cv-entry__date">{{ $exp->start_date }} — {{ $exp->end_date ?? 'Present' }}</span>
              </div>
              <p class="cv-entry__subtitle">{{ $exp->company }}@if($exp->location) · {{ $exp->location }}@endif</p>
              @if($exp->description)<p>{{ $exp->description }}</p>@endif
              @if(!empty($exp->achievements))
                <ul class="article-list">
                  @foreach($exp->achievements as $achievement)<li>{{ $achievement }}</li>@endforeach
                </ul>
              @endif
            </article>
          @empty
            <p>No work experience added yet.</p>
          @endforelse
        </section>

        <section class="cv-document__section">
          <h3 class="cv-document__heading">Education</h3>
          @forelse($education as $item)
            <article class="cv-entry">
              <div class="cv-entry__top">
                <h4 class="cv-entry__title">{{ $item->program }}</h4>
                <span class="cv-entry__date">{{ $item->start_year }} — {{ $item->end_year ?: 'Present' }}</span>
              </div>
              <p class="cv-entry__subtitle">{{ $item->institution }}@if($item->location) · {{ $item->location }}@endif</p>
              @if($item->description)<p>{{ $item->description }}</p>@endif
            </article>
          @empty
            <p>No education added yet.</p>
          @endforelse
        </section>

        <section class="cv-document__section">
          <h3 class="cv-document__heading">Skills</h3>
          <div class="cv-skills">
            @foreach($skills as $category => $items)
              <div class="cv-skills__group">
                <h4 class="cv-skills__category">{{ $category }}</h4>
                <div class="tags-list">
                  @foreach($items as $skill)<span class="tag">{{ $skill->name }}</span>@endforeach
                </div>
              </div>
            @endforeach
          </div>
        </section>
      </div>
    </div>
  </section>
@endsection
