<?php

use App\Http\Controllers\PortfolioDataController;
use Illuminate\Support\Facades\Route;

Route::get('/portfolio-data', [PortfolioDataController::class, 'show']);
Route::put('/portfolio-data', [PortfolioDataController::class, 'update']);

Route::get('/portfolio-messages', [PortfolioDataController::class, 'messages']);
Route::put('/portfolio-messages', [PortfolioDataController::class, 'syncMessages']);
