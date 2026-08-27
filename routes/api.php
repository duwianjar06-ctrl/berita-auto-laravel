<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AutomationController;

Route::get('/health', [AutomationController::class, 'health']);
Route::post('/cron/news-publish', [AutomationController::class, 'newsPublish']);
Route::post('/cron/social-prepare', [AutomationController::class, 'socialPrepare']);
Route::post('/cron/social-publish', [AutomationController::class, 'socialPublish']);
Route::get('/admin/news', [AutomationController::class, 'newsData']);
Route::get('/admin/instagram', [AutomationController::class, 'instagramData']);
