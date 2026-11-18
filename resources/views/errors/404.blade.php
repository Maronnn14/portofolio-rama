@extends('layouts.portfolio')

@section('title', 'Page Not Found')
@section('description', 'The page you are looking for could not be found.')
@section('robots', 'noindex, follow')

@section('content')
  <header class="page-header">
    <span class="section-label justify-center">Error 404</span>
    <h1 class="page-header__title reveal">Page <span class="text-accent">Not Found</span></h1>
    <p class="page-header__subtitle reveal stagger-1">
      The page you are looking for has moved, been unpublished, or never existed.
    </p>
  </header>

  <section class="section--compact">
    <div class="container">
      <div class="empty-state reveal">
        <p class="empty-state__text">Try one of these instead:</p>
        <div class="flex flex-wrap justify-center gap-3 mt-6">
          <a href="{{ route('home') }}" class="btn btn--primary">Back to Home</a>
          <a href="{{ route('projects.index') }}" class="btn btn--secondary">View Projects</a>
          <a href="{{ route('contact') }}" class="btn btn--ghost">Contact Me</a>
        </div>
      </div>
    </div>
  </section>
@endsection
