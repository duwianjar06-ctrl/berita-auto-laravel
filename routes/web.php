<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{PublicController,AdminController,InstagramController,HealthController,AdminAuthController};
use App\Http\Middleware\EnsureAdmin;
Route::get('/',[PublicController::class,'home'])->name('home');
Route::get('/artikel/{slug}',[PublicController::class,'article'])->name('article');
Route::get('/berita/{slug}',[PublicController::class,'article'])->name('berita');
Route::get('/kategori/{category}',[PublicController::class,'category'])->name('category');
Route::get('/tentang-kami',[PublicController::class,'editorialPage'])->defaults('page','tentang-kami')->name('about');
Route::get('/kontak',[PublicController::class,'editorialPage'])->defaults('page','kontak')->name('contact');
Route::get('/kebijakan-privasi',[PublicController::class,'editorialPage'])->defaults('page','kebijakan-privasi')->name('privacy');
Route::get('/disclaimer',[PublicController::class,'editorialPage'])->defaults('page','disclaimer')->name('disclaimer');
Route::get('/editorial-policy',[PublicController::class,'editorialPage'])->defaults('page','editorial-policy')->name('editorial.policy');
Route::get('/sitemap.xml',[PublicController::class,'sitemap'])->name('sitemap');
Route::get('/robots.txt',[PublicController::class,'robots'])->name('robots');
Route::get('/admin/login',[AdminAuthController::class,'login'])->name('admin.login');
Route::get('/admin/callback',[AdminAuthController::class,'callback'])->name('admin.callback');
Route::post('/admin/logout',[AdminAuthController::class,'logout'])->name('admin.logout');
Route::middleware(EnsureAdmin::class)->group(function(){Route::get('/admin-berita',[AdminController::class,'index'])->name('admin.news');Route::get('/admin-berita/{article}/edit',[AdminController::class,'edit'])->name('admin.news.edit');Route::put('/admin-berita/{article}',[AdminController::class,'update'])->name('admin.news.update');Route::post('/admin-berita/{article}/approve',[AdminController::class,'approve'])->name('admin.news.approve');Route::post('/admin-berita/{article}/reject',[AdminController::class,'reject'])->name('admin.news.reject');Route::post('/admin-berita/{article}/publish',[AdminController::class,'publish'])->name('admin.news.publish');Route::post('/admin-berita/{article}/schedule',[AdminController::class,'schedule'])->name('admin.news.schedule');Route::get('/admin-instagram',[InstagramController::class,'index'])->name('admin.instagram');Route::get('/admin-instagram/queue',[InstagramController::class,'queue'])->name('admin.instagram.queue');Route::get('/admin-instagram/attention',[InstagramController::class,'attention'])->name('admin.instagram.attention');Route::get('/admin-instagram/history',[InstagramController::class,'history'])->name('admin.instagram.history');Route::get('/health',[HealthController::class,'index'])->name('health');});
