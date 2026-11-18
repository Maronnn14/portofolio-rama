@php
  use App\Support\Icon;
@endphp

@extends('layouts.portfolio')

@section('title', 'Daily Blog')
@section('description', 'Catatan harian, devlog, dan pelajaran kecil dari proses membangun produk.')

@section('content')
  <header class="page-header">
    <span class="section-label justify-center">Journal</span>
    <h1 class="page-header__title reveal">Daily <span class="text-accent">Blog</span></h1>
    <p class="page-header__subtitle reveal stagger-1">Catatan harian: proses, pelajaran, dan hal kecil yang layak dibagikan</p>
  </header>

  @include('layouts.partials.about-nav')

  <section class="section--compact">
    <div class="container">
      <div class="blog-toolbar reveal">
        <div class="blog-filters">
          <a href="{{ route('about.blog') }}" class="filter-tab{{ blank($filters['category']) ? ' active' : '' }}">All</a>
          @foreach($categories as $category)
            <a href="{{ route('about.blog', ['category' => $category]) }}" class="filter-tab{{ $filters['category'] === $category ? ' active' : '' }}">{{ $category }}</a>
          @endforeach
        </div>

        <form method="GET" action="{{ route('about.blog') }}" class="search-form" role="search">
          @if(filled($filters['category']))
            <input type="hidden" name="category" value="{{ $filters['category'] }}">
          @endif
          <label for="blog-search" class="sr-only">Cari tulisan</label>
          <input id="blog-search" type="search" name="q" value="{{ $filters['q'] }}" class="form-input search-form__input" placeholder="Cari tulisan…">
          <button type="submit" class="btn btn--secondary btn--sm">Search</button>
        </form>
      </div>

      <div class="projects-grid" id="blog-grid">
        @forelse($posts as $post)
          <article class="project-card reveal stagger-{{ min($loop->iteration, 6) }}" data-category="{{ $post->category }}">
            <a href="{{ route('about.blog.show', $post) }}" class="project-card__link">
              <div class="project-card__thumbnail">
                @if($post->category)<span class="project-card__badge">{{ $post->category }}</span>@endif
                @if($post->cover_url)
                  <img src="{{ $post->cover_url }}" alt="{{ $post->title }}" loading="lazy" />
                @else
                  <span class="project-card__placeholder" aria-hidden="true">{{ Icon::initials($post->title) }}</span>
                @endif
                <span class="project-card__overlay"><span class="project-card__overlay-btn">Baca tulisan <span aria-hidden="true">→</span></span></span>
              </div>
              <div class="project-card__body">
                <span class="project-card__category">
                  {{ optional($post->display_date)->translatedFormat('d M Y') }} · {{ $post->reading_minutes }} min read
                </span>
                <h2 class="project-card__name">{{ $post->title }}</h2>
                <p class="project-card__desc">{{ $post->summary }}</p>
                <div class="project-card__tech">
                  @foreach(array_slice($post->tags ?? [], 0, 4) as $tag)<span class="tag">{{ $tag }}</span>@endforeach
                </div>
              </div>
            </a>
          </article>
        @empty
          <div class="empty-state col-span-full">
            <h2 class="empty-state__title">Belum ada tulisan</h2>
            <p class="empty-state__text">{{ filled($filters['q']) || filled($filters['category']) ? 'Coba kata kunci atau kategori lain.' : 'Tulisan harian akan muncul di sini.' }}</p>
            @if(filled($filters['q']) || filled($filters['category']))
              <a href="{{ route('about.blog') }}" class="btn btn--secondary btn--sm">Reset filter</a>
            @endif
          </div>
        @endforelse
      </div>

      @if($posts->hasPages())
        <nav class="blog-pagination reveal" aria-label="Blog pages">
          @if($posts->onFirstPage())
            <span class="blog-pagination__link is-disabled" aria-disabled="true">← Terbaru</span>
          @else
            <a href="{{ $posts->previousPageUrl() }}" class="blog-pagination__link">← Terbaru</a>
          @endif

          <span class="blog-pagination__status">Halaman {{ $posts->currentPage() }} / {{ $posts->lastPage() }}</span>

          @if($posts->hasMorePages())
            <a href="{{ $posts->nextPageUrl() }}" class="blog-pagination__link">Lebih lama →</a>
          @else
            <span class="blog-pagination__link is-disabled" aria-disabled="true">Lebih lama →</span>
          @endif
        </nav>
      @endif
    </div>
  </section>
@endsection
