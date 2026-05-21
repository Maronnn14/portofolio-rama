@php
  $pdFullName = $personal['fullName'] ?? $personal['name'] ?? 'Rama Adin';
@endphp

@extends('layouts.portfolio')

@section('title', $project ? "$project->name — $pdFullName" : "Project Detail — $pdFullName")
@section('description', $project ? "Detailed view of $project->name by $pdFullName — description, tech stack, gallery, and links." : "Detailed view of a project by $pdFullName — description, tech stack, gallery, and links.")

@section('content')
  <main class="project-detail" id="project-detail">
    @if($project)
      <div class="container">
        <a class="project-detail__back" onclick="history.back()">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg> Back to Projects
        </a>
        <header class="project-detail__header reveal">
          <span class="project-detail__category">{{ $project->category }}</span>
          <h1 class="project-detail__title">{{ $project->name }}</h1>
          <p class="project-detail__desc">{{ $project->full_desc }}</p>
        </header>
        <div class="project-detail__hero-img reveal stagger-1">
          <img src="{{ $project->thumbnail }}" alt="{{ $project->name }}" loading="eager" />
        </div>
        <div class="project-detail__meta reveal stagger-2">
          <div class="project-detail__meta-item">
            <h4>Category</h4>
            <span class="tag tag--accent">{{ $project->category }}</span>
          </div>
          <div class="project-detail__meta-item">
            <h4>Tech Stack</h4>
            <div class="tags-list">
              @foreach($project->tech ?? [] as $tech)
                <span class="tag">{{ $tech }}</span>
              @endforeach
            </div>
          </div>
          <div class="project-detail__meta-item">
            <h4>Links</h4>
            <div class="project-detail__links">
              @if($project->live_url && $project->live_url !== '#')
                <a href="{{ $project->live_url }}" target="_blank" rel="noopener noreferrer" class="btn btn--primary btn--sm">Live Demo</a>
              @endif
              @if($project->source_url && $project->source_url !== '#')
                <a href="{{ $project->source_url }}" target="_blank" rel="noopener noreferrer" class="btn btn--secondary btn--sm">Source Code</a>
              @endif
              @if((!$project->live_url || $project->live_url === '#') && (!$project->source_url || $project->source_url === '#'))
                <span class="tag" style="opacity:0.6">Links coming soon</span>
              @endif
            </div>
          </div>
        </div>
        @if($project->gallery && count($project->gallery) > 0)
          <div class="project-detail__gallery reveal stagger-3">
            <h3>Project <span class="text-accent">Gallery</span></h3>
            <div class="project-detail__gallery-grid">
              @foreach($project->gallery as $img)
                <div class="project-detail__gallery-item" data-lightbox-index="{{ $loop->index }}">
                  <img src="{{ $img }}" alt="{{ $project->name }} screenshot {{ $loop->iteration }}" loading="lazy" />
                </div>
              @endforeach
            </div>
          </div>
        @endif
      </div>
    @else
      <div class="container">
        <div class="project-detail__not-found">
          <h2>Project Not Found</h2>
          <p>The project you're looking for doesn't exist.</p>
          <a href="{{ url('/projects.html') }}" class="btn btn--primary">Back to Projects</a>
        </div>
      </div>
    @endif
  </main>
@endsection

@push('scripts')
  <script src="{{ asset('js/lightbox.js') }}"></script>
  <script src="{{ asset('js/project-detail.js') }}"></script>
@endpush
