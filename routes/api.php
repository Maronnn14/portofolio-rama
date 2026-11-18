<?php

use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\InterestController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\PersonalInfoController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SiteSettingController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\SocialController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Portfolio REST API Routes (Public)
|--------------------------------------------------------------------------
| All admin CRUD operations are now handled via Filament 4 at /admin.
| These public endpoints serve the public frontend and visitor interactions.
|--------------------------------------------------------------------------
*/

// Public read-only routes for visitors
Route::get('projects', [ProjectController::class, 'index']);
Route::get('projects/{project}', [ProjectController::class, 'show']);
Route::get('skills', [SkillController::class, 'index']);
Route::get('skills/{skill}', [SkillController::class, 'show']);
Route::get('experiences', [ExperienceController::class, 'index']);
Route::get('experiences/{experience}', [ExperienceController::class, 'show']);
Route::get('socials', [SocialController::class, 'index']);
Route::get('socials/{social}', [SocialController::class, 'show']);
Route::get('interests', [InterestController::class, 'index']);
Route::get('interests/{interest}', [InterestController::class, 'show']);
Route::get('gallery', [GalleryController::class, 'index']);
Route::get('gallery/{gallery}', [GalleryController::class, 'show']);
Route::get('personal-info', [PersonalInfoController::class, 'index']);
Route::get('about', [AboutController::class, 'index']);
Route::get('settings', [SiteSettingController::class, 'index']);

// Contact form (public)
Route::post('contact', [ContactController::class, 'store']);

// Messages / Guestbook — public read/write (session_token for visitor ownership)
Route::get('messages', [MessageController::class, 'index']);
Route::post('messages', [MessageController::class, 'store']);
Route::put('messages/{id}', [MessageController::class, 'update']);
Route::delete('messages/{id}', [MessageController::class, 'destroy']);
