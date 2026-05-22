<?php

use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\InterestController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\PersonalInfoController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SiteSettingController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\SocialController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Portfolio REST API Routes
|--------------------------------------------------------------------------
*/

// Public routes (read-only for visitors)
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

// Messages — public read/write (session_token for ownership)
Route::get('messages', [MessageController::class, 'index']);
Route::post('messages', [MessageController::class, 'store']);
Route::put('messages/{id}', [MessageController::class, 'update']);
Route::delete('messages/bulk', [MessageController::class, 'bulkDestroy'])->middleware(['auth:sanctum', 'ability:admin:write']);
Route::delete('messages/{id}', [MessageController::class, 'destroy']);

// Auth (rate-limited)
Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

// Protected routes (admin only)
Route::middleware(['auth:sanctum', 'ability:admin:write', 'throttle:120,1'])->group(function () {
    // Media upload
    Route::post('media', [MediaController::class, 'store']);

    // Projects
    Route::post('projects', [ProjectController::class, 'store']);
    Route::put('projects/{project}', [ProjectController::class, 'update']);
    Route::delete('projects/{project}', [ProjectController::class, 'destroy']);
    Route::delete('projects-bulk', [ProjectController::class, 'bulkDestroy']);

    // Skills
    Route::post('skills', [SkillController::class, 'store']);
    Route::put('skills/{skill}', [SkillController::class, 'update']);
    Route::delete('skills/{skill}', [SkillController::class, 'destroy']);
    Route::put('skills/{skill}/links', [SkillController::class, 'syncLinks']);
    Route::post('skills/{skill}/gallery', [SkillController::class, 'uploadGalleryImage']);
    Route::put('skills/{skill}/gallery', [SkillController::class, 'updateGallery']);
    Route::delete('skills/{skill}/gallery/{item}', [SkillController::class, 'deleteGalleryItem']);
    Route::delete('skills-bulk', [SkillController::class, 'bulkDestroy']);

    // Experiences
    Route::post('experiences', [ExperienceController::class, 'store']);
    Route::put('experiences/{experience}', [ExperienceController::class, 'update']);
    Route::delete('experiences/{experience}', [ExperienceController::class, 'destroy']);
    Route::delete('experiences-bulk', [ExperienceController::class, 'bulkDestroy']);

    // Socials
    Route::post('socials', [SocialController::class, 'store']);
    Route::put('socials/{social}', [SocialController::class, 'update']);
    Route::delete('socials/{social}', [SocialController::class, 'destroy']);
    Route::delete('socials-bulk', [SocialController::class, 'bulkDestroy']);

    // Interests
    Route::post('interests', [InterestController::class, 'store']);
    Route::put('interests/{interest}', [InterestController::class, 'update']);
    Route::delete('interests/{interest}', [InterestController::class, 'destroy']);
    Route::put('interests-bulk', [InterestController::class, 'bulkUpdate']);

    // Gallery
    Route::post('gallery', [GalleryController::class, 'store']);
    Route::put('gallery/{gallery}', [GalleryController::class, 'update']);
    Route::delete('gallery/{gallery}', [GalleryController::class, 'destroy']);
    Route::delete('gallery-bulk', [GalleryController::class, 'bulkDestroy']);

    // Personal info
    Route::put('personal-info', [PersonalInfoController::class, 'update']);

    // About section (quote + stats)
    Route::put('about', [AboutController::class, 'update']);

    // Site settings
    Route::put('settings', [SiteSettingController::class, 'update']);

    // Auth management
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/check', [AuthController::class, 'check']);
    Route::post('auth/change-password', [AuthController::class, 'changePassword']);
});
