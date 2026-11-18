<?php

use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::controller(PortfolioController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/index.html', 'home')->name('home.html');

    Route::get('/about', fn () => redirect()->route('about.biodata'))->name('about');
    Route::get('/about/biodata', 'biodata')->name('about.biodata');
    Route::get('/about/cv', 'cv')->name('about.cv');
    Route::get('/about/interests', 'interests')->name('about.interests');
    Route::get('/about/blog', 'blog')->name('about.blog');
    Route::get('/about/blog/{blogPost:slug}', 'blogShow')->name('about.blog.show');
    Route::get('/about.html', 'about')->name('about.html');

    Route::get('/skills', 'skills')->name('skills');
    Route::get('/skills.html', 'skills')->name('skills.html');

    Route::get('/projects', 'projects')->name('projects.index');
    Route::get('/projects.html', 'projects')->name('projects.html');
    Route::get('/projects/{project:slug}', 'projectShow')->name('projects.show');
    Route::get('/project-detail.html', 'legacyProjectDetail')->name('project.detail.html');

    Route::get('/experience', 'experience')->name('experience.index');
    Route::get('/experience/{experience:slug}', 'experienceShow')->name('experience.show');

    Route::get('/contributions', 'contributions')->name('contributions.index');
    Route::get('/contributions/{contribution:slug}', 'contributionShow')->name('contributions.show');

    Route::get('/contact', 'contact')->name('contact');
    Route::get('/contact.html', 'contact')->name('contact.html');
    Route::post('/contact', 'storeContact')->middleware('throttle:10,1')->name('contact.store');
});

Route::redirect('/admin.html', '/admin');
