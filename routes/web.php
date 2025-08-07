<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SearchApiController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile',        [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

// 認証
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/signup', [AuthController::class, 'showSignupForm'])->name('signup');
Route::post('/signup', [AuthController::class, 'register']);

// ログアウト(ログイン時のみ有効)
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// 検索ページ
Route::get('/search', fn () => view('search.index'));          // 画面
Route::get('/api/search', [SearchApiController::class, 'index']); // JSON