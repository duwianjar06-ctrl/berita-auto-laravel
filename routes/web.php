<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{PublicController,AdminController,InstagramController,HealthController,AdminAuthController};
use App\Http\Middleware\EnsureAdmin;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/artikel/{slug}', [PublicController::class, 'article'])->name('article');
Route::get('/berita/{slug}', [PublicController::class, 'article'])->name('berita');
Route::get('/kategori/{category}', [PublicController::class, 'category'])->name('category');
Route::get('/sitemap.xml', [PublicController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [PublicController::class, 'robots'])->name('robots');
Route::get('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login');
Route::get('/admin/callback', [AdminAuthController::class, 'callback'])->name('admin.callback');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
Route::middleware(EnsureAdmin::class)->group(function () {
 Route::get('/admin-berita', [AdminController::class, 'index'])->name('admin.news');
 Route::get('/admin-instagram', [InstagramController::class, 'index'])->name('admin.instagram');
 Route::get('/admin-instagram/queue', [InstagramController::class, 'queue'])->name('admin.instagram.queue');
 Route::get('/admin-instagram/attention', [InstagramController::class, 'attention'])->name('admin.instagram.attention');
 Route::get('/admin-instagram/history', [InstagramController::class, 'history'])->name('admin.instagram.history');
 Route::get('/health', [HealthController::class, 'index'])->name('health');
});
