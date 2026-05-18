<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

Route::controller(PortfolioController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/index.html', 'home')->name('home.html');

    Route::get('/about.html', 'about')->name('about.html');

    Route::get('/projects.html', 'projects')->name('projects.html');

    Route::get('/project-detail.html', 'projectDetail')->name('project.detail.html');

    Route::get('/skills.html', 'skills')->name('skills.html');

    Route::get('/contact.html', 'contact')->name('contact.html');
});

Route::controller(AdminController::class)->group(function () {
    Route::get('/admin.html', 'dashboard')->name('admin.dashboard.html');
});
