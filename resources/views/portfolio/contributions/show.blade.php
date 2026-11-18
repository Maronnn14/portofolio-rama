@php
  use Illuminate\Support\Str;

  $body = $contribution->body ?: $contribution->description;
  $images = $contribution->imageUrls();
@endphp

@extends('layouts.portfolio')

@section('title', $contribution->name)
@section('description', Str::limit(strip_tags($contribution->description), 160))

@section('content')
  <article class="container article-page">
    <a class="project-detail__back" href="{{ route('contributions.index') }}">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg> All Contributions
    </a>

    <header class="article-header reveal">
      <div class="article-meta">
        @if($contribution->role)<span class="tag tag--accent">{{ $contribution->role }}</span>@endif
        @if($contribution->period_start || $contribution->period_end)
          <time>{{ $contribution->period_start }}@if($contribution->period_start && $contribution->period_end) — @endif{{ $contribution->period_end ?: ($contribution->period_start ? 'Present' : '') }}</time>
        @endif
      </div>
      <h1 class="article-header__title">{{ $contribution->name }}</h1>
      @if($contribution->organization)<p class="article-header__subtitle">{{ $contribution->organization }}</p>@endif
    </header>

    @if($contribution->cover_url)
      <div class="article-cover reveal stagger-1">
        <img src="{{ $contribution->cover_url }}" alt="{{ $contribution->name }}" loading="eager" />
      </div>
    @endif

    @if($body)
      <div class="article-content reveal stagger-2">
        {!! Str::markdown($body, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
      </div>
    @endif

    @if(count($images))
      <section class="article-section reveal">
        <h2>Gallery</h2>
        <div class="project-detail__gallery-grid" id="contribution-gallery">
          @foreach($images as $image)
            <button type="button" class="project-detail__gallery-item gallery-item" data-lightbox-index="{{ $loop->index }}" aria-label="Open image {{ $loop->iteration }}">
              <img src="{{ $image['url'] }}" alt="{{ $contribution->name }} image {{ $loop->iteration }}" loading="lazy" />
            </button>
          @endforeach
        </div>
      </section>
    @endif

    @if(!empty($contribution->links))
      <footer class="article-footer reveal">
        <h2 class="article-footer__heading">Links</h2>
        <div class="article-links">
          @foreach($contribution->links as $link)
            @if(!empty($link['url']))
              <a href="{{ $link['url'] }}" class="btn btn--secondary btn--sm" target="_blank" rel="noopener noreferrer">{{ $link['label'] ?? 'External link' }}</a>
            @endif
          @endforeach
        </div>
      </footer>
    @endif
  </article>
@endsection

@push('scripts')
  <script src="{{ asset('js/lightbox.js') }}"></script>
  @if(count($images))
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var images = @json($images);
        var trigger = document.getElementById('contribution-gallery');
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
