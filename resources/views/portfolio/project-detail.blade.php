@php
  use App\Support\Icon;
  use Illuminate\Support\Str;

  $body = $project?->body ?: $project?->full_desc;
  $gallery = $project ? $project->galleryUrls() : [];
@endphp

@extends('layouts.portfolio')

@section('title', $project?->name ?? 'Project Not Found')
@section('description', $project?->short_desc ?? '')

@section('content')
  <main class="project-detail" id="project-detail">
    @if($project)
      <div class="container">
        <a class="project-detail__back" href="{{ route('projects.index') }}">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg> Back to Projects
        </a>

        <header class="project-detail__header reveal">
          <div class="article-meta">
            @if($project->category)<span class="tag tag--accent">{{ $project->category }}</span>@endif
            @if($project->published_at)<time datetime="{{ $project->published_at->toDateString() }}">{{ $project->published_at->translatedFormat('F j, Y') }}</time>@endif
            <span>{{ $project->readingMinutes() }} min read</span>
          </div>
          <h1 class="project-detail__title">{{ $project->name }}</h1>
          @if($project->short_desc)<p class="project-detail__desc">{{ $project->short_desc }}</p>@endif
        </header>

        @if($project->thumbnail)
          <div class="project-detail__hero-img reveal stagger-1">
            <img src="{{ $project->thumbnail }}" alt="{{ $project->name }} cover image" loading="eager" />
          </div>
        @else
          <div class="project-detail__hero-img project-detail__hero-img--placeholder reveal stagger-1" aria-hidden="true">{{ Icon::initials($project->name) }}</div>
        @endif

        <div class="project-detail__meta reveal stagger-2">
          @if($project->category)
            <div class="project-detail__meta-item">
              <h2>Category</h2>
              <span class="tag tag--accent">{{ $project->category }}</span>
            </div>
          @endif
          @if(!empty($project->tech))
            <div class="project-detail__meta-item">
              <h2>Tech Stack</h2>
              <div class="tags-list">@foreach($project->tech as $tech)<span class="tag">{{ $tech }}</span>@endforeach</div>
            </div>
          @endif
          <div class="project-detail__meta-item">
            <h2>Links</h2>
            <div class="project-detail__links">
              @if($project->live_url && $project->live_url !== '#')
                <a href="{{ $project->live_url }}" target="_blank" rel="noopener noreferrer" class="btn btn--primary btn--sm">Live Demo</a>
              @endif
              @if($project->source_url && $project->source_url !== '#')
                <a href="{{ $project->source_url }}" target="_blank" rel="noopener noreferrer" class="btn btn--secondary btn--sm">Source Code</a>
              @endif
              @if((!$project->live_url || $project->live_url === '#') && (!$project->source_url || $project->source_url === '#'))
                <span class="tag opacity-60">Links coming soon</span>
              @endif
            </div>
          </div>
        </div>

        @if($body)
          <article class="article-content reveal stagger-3">
            {!! Str::markdown($body, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
          </article>
        @endif

        @if(count($gallery))
          <section class="project-detail__gallery reveal">
            <h2>Project <span class="text-accent">Gallery</span></h2>
            <div class="project-detail__gallery-grid" id="project-gallery">
              @foreach($gallery as $image)
                <button type="button" class="project-detail__gallery-item gallery-item" data-lightbox-index="{{ $loop->index }}" aria-label="Open screenshot {{ $loop->iteration }}">
                  <img src="{{ $image['url'] }}" alt="{{ $image['caption'] ?: $project->name.' screenshot '.$loop->iteration }}" loading="lazy" />
                </button>
              @endforeach
            </div>
          </section>
        @endif

        <nav class="article-pagination" aria-label="Project navigation">
          @if($previous)
            <a href="{{ route('projects.show', $previous) }}" class="article-pagination__link">
              <span class="article-pagination__label">Previous project</span>
              <span class="article-pagination__title">← {{ $previous->name }}</span>
            </a>
          @else
            <span></span>
          @endif
          @if($next)
            <a href="{{ route('projects.show', $next) }}" class="article-pagination__link article-pagination__link--next">
              <span class="article-pagination__label">Next project</span>
              <span class="article-pagination__title">{{ $next->name }} →</span>
            </a>
          @endif
        </nav>
      </div>
    @else
      <div class="container">
        <div class="project-detail__not-found">
          <h1>Project Not Found</h1>
          <p>The project you're looking for doesn't exist or is no longer published.</p>
          <a href="{{ route('projects.index') }}" class="btn btn--primary">Back to Projects</a>
        </div>
      </div>
    @endif
  </main>
@endsection

@push('scripts')
  <script src="{{ asset('js/lightbox.js') }}"></script>
  @if($project && count($gallery))
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var images = @json($gallery);
        var trigger = document.getElementById('project-gallery');
        if (!trigger || typeof lightbox === 'undefined') return;
        trigger.addEventListener('click', function (event) {
          var item = event.target.closest('[data-lightbox-index]');
          if (!item) return;
          lightbox.open(images.map(function (img) { return { src: img.url, alt: img.caption || '' }; }), Number(item.dataset.lightboxIndex));
        });
      });
    </script>
  @endif
@endpush
