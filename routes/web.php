<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ActorsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ProfileController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/series/{id}', [HomeController::class, 'series'])->name('series.show');

// Search API route
Route::get('/api/search', [HomeController::class, 'search'])->name('api.search');

// News routes
Route::get('/noticias', [NewsController::class, 'index'])->name('news.index');
Route::get('/noticias/categoria/{category}', [NewsController::class, 'category'])->name('news.category');
Route::get('/noticias/{news:slug}', [NewsController::class, 'show'])->name('news.show');

// Actors routes
Route::get('/actores', [ActorsController::class, 'index'])->name('actors.index');
Route::get('/actores/{id}', [ActorsController::class, 'show'])->name('actors.show');

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Google OAuth routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/perfil', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/perfil/editar', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/perfil/lista-seguimiento', [ProfileController::class, 'watchlist'])->name('profile.watchlist');
    Route::get('/perfil/calificaciones', [ProfileController::class, 'ratings'])->name('profile.ratings');
    Route::get('/perfil/series-vistas', [ProfileController::class, 'watchedSeries'])->name('profile.watched');
});

// Public profile routes
Route::get('/usuario/{user}', [ProfileController::class, 'show'])->name('user.profile');
Route::get('/usuario/{user}/lista-seguimiento', [ProfileController::class, 'watchlist'])->name('user.watchlist');
Route::get('/usuario/{user}/calificaciones', [ProfileController::class, 'ratings'])->name('user.ratings');
Route::get('/usuario/{user}/series-vistas', [ProfileController::class, 'watchedSeries'])->name('user.watched');

// Rating routes (AJAX)
Route::middleware('auth')->group(function () {
    Route::post('/series/{series}/rate', [HomeController::class, 'rateTitle'])->name('series.rate');
    Route::delete('/series/{series}/rate', [HomeController::class, 'removeRating'])->name('series.rate.remove');
    
    // Watchlist routes (AJAX)
    Route::post('/series/{series}/watchlist', [HomeController::class, 'toggleWatchlist'])->name('series.watchlist.toggle');
    Route::put('/series/{series}/watchlist', [HomeController::class, 'updateWatchlistStatus'])->name('series.watchlist.update');
    
    // Watch history routes (AJAX)
    Route::post('/series/{series}/watched', [HomeController::class, 'markAsWatched'])->name('series.mark.watched');
    
    // Comment routes (AJAX)
    Route::post('/series/{series}/comments', [HomeController::class, 'storeComment'])->name('series.comments.store');
    Route::post('/actores/{actor}/comments', [ActorsController::class, 'storeComment'])->name('actors.comments.store');
});

// Debug route
Route::get('/debug', function() {
    return [
        'series_count' => \App\Models\Series::count(),
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'view_exists' => view()->exists('home'),
        'layout_exists' => view()->exists('layouts.app')
    ];
});
