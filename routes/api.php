<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AutomationController;

Route::get('/api/health', [AutomationController::class, 'health']);
Route::post('/api/cron/news-publish', [AutomationController::class, 'newsPublish']);
Route::post('/api/cron/social-prepare', [AutomationController::class, 'socialPrepare']);
Route::post('/api/cron/social-publish', [AutomationController::class, 'socialPublish']);
Route::get('/api/admin/news', [AutomationController::class, 'newsData']);
Route::get('/api/admin/instagram', [AutomationController::class, 'instagramData']);
Route::get('/api/admin/health', [AutomationController::class, 'health']);
