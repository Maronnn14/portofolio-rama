@php
  use Illuminate\Support\Str;

  $body = $experience->body ?: $experience->description;
@endphp

@extends('layouts.portfolio')

@section('title', $experience->role.' · '.$experience->company)
@section('description', Str::limit(strip_tags($experience->description), 160))

@section('content')
  <article class="container article-page">
    <a class="project-detail__back" href="{{ route('experience.index') }}">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg> All Work Experience
    </a>

    <header class="article-header reveal">
      <div class="article-meta">
        <span class="tag tag--accent">{{ $experience->type }}</span>
        <time>{{ $experience->start_date }} — {{ $experience->end_date ?? 'Present' }}</time>
        <span>{{ $experience->location }}</span>
      </div>
      <h1 class="article-header__title">{{ $experience->role }}</h1>
      <p class="article-header__subtitle">{{ $experience->company }}</p>
    </header>

    @if($experience->cover_url)
      <div class="article-cover reveal stagger-1">
        <img src="{{ $experience->cover_url }}" alt="{{ $experience->role }} at {{ $experience->company }}" loading="eager" />
      </div>
    @endif

    @if($body)
      <div class="article-content reveal stagger-2">
        {!! Str::markdown($body, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
      </div>
    @endif

    @if(!empty($experience->achievements))
      <section class="article-section reveal">
        <h2>Key Achievements</h2>
        <ul class="article-list">
          @foreach($experience->achievements as $achievement)<li>{{ $achievement }}</li>@endforeach
        </ul>
      </section>
    @endif

    @if(!empty($experience->tech))
      <footer class="article-footer reveal">
        <h2 class="article-footer__heading">Technologies</h2>
        <div class="tags-list">@foreach($experience->tech as $tech)<span class="tag">{{ $tech }}</span>@endforeach</div>
      </footer>
    @endif
  </article>
@endsection
