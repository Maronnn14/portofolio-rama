<?php

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

Route::post('media', [MediaController::class, 'store']);

Route::apiResource('projects', ProjectController::class);
Route::apiResource('skills', SkillController::class);
Route::apiResource('experiences', ExperienceController::class);
Route::apiResource('socials', SocialController::class);
Route::apiResource('interests', InterestController::class);

// Messages — custom resource binding via message_id string
Route::get('messages', [MessageController::class, 'index']);
Route::post('messages', [MessageController::class, 'store']);
Route::put('messages/{id}', [MessageController::class, 'update']);
Route::delete('messages/bulk', [MessageController::class, 'bulkDestroy']);
Route::delete('messages/{id}', [MessageController::class, 'destroy']);

// Gallery — with bulk delete
Route::apiResource('gallery', GalleryController::class);
Route::delete('gallery-bulk', [GalleryController::class, 'bulkDestroy']);

// Interests — bulk update for profile page
Route::put('interests-bulk', [InterestController::class, 'bulkUpdate']);

// Personal info — key/value store
Route::get('personal-info', [PersonalInfoController::class, 'index']);
Route::put('personal-info', [PersonalInfoController::class, 'update']);

// Site settings — key/value store
Route::get('settings', [SiteSettingController::class, 'index']);
Route::put('settings', [SiteSettingController::class, 'update']);
