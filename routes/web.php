<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// 投稿
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
Route::get('/api/posts/load_more', [PostController::class, 'load_more'])->name('posts.load_more');

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
Route::get('/search', [SearchController::class, 'index']);   // JSONで返す想定