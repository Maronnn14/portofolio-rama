@php
  use App\Support\Icon;
@endphp

@extends('layouts.portfolio')

@section('title', 'Contributions, Activities & Organizations')
@section('description', 'Community contributions, organizational roles, volunteering, and activities beyond day-to-day work.')

@section('content')
  <header class="page-header">
    <span class="section-label justify-center">Community</span>
    <h1 class="page-header__title reveal">Contributions &amp; <span class="text-accent">Activities</span></h1>
    <p class="page-header__subtitle reveal stagger-1">Organizations, volunteering, and community work</p>
  </header>

  <section class="section--compact">
    <div class="container">
      <div class="project-toolbar reval">
        <div></div>
        <form method="GET" action="{{ route('contributions.index') }}" class="search-form" role="search">
          <label for="contribution-search" class="sr-only">Search contributions</label>
          <input id="contribution-search" type="search" name="q" value="{{ $filters['q'] }}" class="form-input search-form__input" placeholder="Search contributions…">
          <button type="submit" class="btn btn--secondary btn--sm">Search</button>
        </form>
      </div>

      <div class="contribution-grid">
        @forelse($contributions as $contribution)
          <article class="contribution-card reveal stagger-{{ min($loop->iteration, 6) }}">
            <div class="contribution-card__media">
              @if($contribution->cover_url)
                <img src="{{ $contribution->cover_url }}" alt="{{ $contribution->name }}" loading="lazy" />
              @else
                <span aria-hidden="true">{{ Icon::initials($contribution->name) }}</span>
              @endif
              @if($contribution->role)<span class="contribution-card__role">{{ $contribution->role }}</span>@endif
            </div>
            <div class="contribution-card__body">
              <div class="article-meta">
                @if($contribution->period_start || $contribution->period_end)
                  <time>{{ $contribution->period_start }}@if($contribution->period_start && $contribution->period_end) — @endif{{ $contribution->period_end ?: ($contribution->period_start ? 'Present' : '') }}</time>
                @endif
              </div>
              <h2 class="contribution-card__title"><a href="{{ route('contributions.show', $contribution) }}">{{ $contribution->name }}</a></h2>
              @if($contribution->organization)<p class="contribution-card__org">{{ $contribution->organization }}</p>@endif
              <p class="contribution-card__desc">{{ $contribution->description }}</p>
              <a href="{{ route('contributions.show', $contribution) }}" class="feed-card__more">Read more <span aria-hidden="true">→</span></a>
            </div>
          </article>
        @empty
          <div class="empty-state col-span-full">
            <h2 class="empty-state__title">No contributions found</h2>
            <p class="empty-state__text">Try a different search term.</p>
            <a href="{{ route('contributions.index') }}" class="btn btn--secondary btn--sm">Clear search</a>
          </div>
        @endforelse
      </div>
    </div>
  </section>
@endsection
