@php
  use Illuminate\Support\Str;
@endphp

@extends('layouts.portfolio')

@section('title', $post->title)
@section('description', $post->summary)

@section('content')
  <article class="container article-page">
    <a class="project-detail__back" href="{{ route('about.blog') }}">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg> Semua tulisan
    </a>

    <header class="article-header reveal">
      <div class="article-meta">
        <time datetime="{{ optional($post->display_date)->toDateString() }}">{{ optional($post->display_date)->translatedFormat('d F Y') }}</time>
        @if($post->category)<span class="tag tag--accent">{{ $post->category }}</span>@endif
        <span class="reading-time">{{ $post->reading_minutes }} min read</span>
      </div>
      <h1 class="article-header__title">{{ $post->title }}</h1>
      @if($post->summary)<p class="article-header__subtitle">{{ $post->summary }}</p>@endif
    </header>

    @if($post->cover_url)
      <div class="article-cover reveal stagger-1">
        <img src="{{ $post->cover_url }}" alt="{{ $post->title }}" loading="eager" />
      </div>
    @endif

    @if($post->body)
      <div class="article-content reveal stagger-2">
        {!! Str::markdown($post->body, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
      </div>
    @endif

    @if(!empty($post->tags))
      <footer class="article-footer reveal">
        <div class="tags-list">@foreach($post->tags as $tag)<span class="tag">{{ $tag }}</span>@endforeach</div>
      </footer>
    @endif
  </article>

  @if($related->isNotEmpty())
    <section class="section--compact">
      <div class="container">
        <div class="section-header reveal">
          <span class="section-label">Journal</span>
          <h2 class="section-heading">Tulisan Lainnya</h2>
        </div>
        <div class="projects-grid">
          @foreach($related as $other)
            <article class="project-card reveal stagger-{{ $loop->iteration }}">
              <a href="{{ route('about.blog.show', $other) }}" class="project-card__link">
                <div class="project-card__thumbnail">
                  @if($other->category)<span class="project-card__badge">{{ $other->category }}</span>@endif
                  @if($other->cover_url)
                    <img src="{{ $other->cover_url }}" alt="{{ $other->title }}" loading="lazy" />
                  @else
                    <span class="project-card__placeholder" aria-hidden="true">{{ \App\Support\Icon::initials($other->title) }}</span>
                  @endif
                  <span class="project-card__overlay"><span class="project-card__overlay-btn">Baca tulisan <span aria-hidden="true">→</span></span></span>
                </div>
                <div class="project-card__body">
                  <span class="project-card__category">
                    {{ optional($other->display_date)->translatedFormat('d M Y') }} · {{ $other->reading_minutes }} min read
                  </span>
                  <h3 class="project-card__name">{{ $other->title }}</h3>
                  <p class="project-card__desc">{{ $other->summary }}</p>
                </div>
              </a>
            </article>
          @endforeach
        </div>
      </div>
    </section>
  @endif
@endsection
