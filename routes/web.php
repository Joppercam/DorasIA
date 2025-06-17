<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ActorsController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ProfileController;

// === RUTAS PRINCIPALES ===
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/explorar', [HomeController::class, 'browse'])->name('browse');
Route::get('/series/{id}', [HomeController::class, 'series'])->name('series.show');

// === API ROUTES ===
// Search API route - Rate limited to 30 requests per minute
Route::get('/api/search', [HomeController::class, 'search'])
    ->middleware('rate.limit:search,30,1')
    ->name('api.search');

// Actors autocomplete API
Route::get('/api/actors/autocomplete', [ActorsController::class, 'autocomplete'])
    ->middleware('rate.limit:search,60,1')
    ->name('api.actors.autocomplete');

// === CONTENT ROUTES ===
// News routes
Route::get('/noticias', [NewsController::class, 'index'])->name('news.index');
Route::get('/noticias/categoria/{category}', [NewsController::class, 'category'])->name('news.category');
Route::get('/noticias/{news:slug}', [NewsController::class, 'show'])->name('news.show');

// Movies routes
Route::get('/peliculas', [MovieController::class, 'index'])->name('movies.index');
Route::get('/peliculas/{movie}', function($movie) {
    // First check if this ID exists as a movie
    $movieExists = \App\Models\Movie::find($movie);
    if ($movieExists) {
        return app(\App\Http\Controllers\MovieController::class)->show($movie);
    }
    
    // If not found as movie, check if it exists as a series
    $seriesExists = \App\Models\Series::find($movie);
    if ($seriesExists) {
        // Redirect to the correct series URL
        return redirect()->route('series.show', $movie)->with('info', 'Te hemos redirigido a la serie correcta');
    }
    
    // If neither exists, show 404
    abort(404, 'Película no encontrada');
})->name('movies.show');

// Actors routes
Route::get('/actores', [ActorsController::class, 'index'])->name('actors.index');
Route::get('/actores/{id}', [ActorsController::class, 'show'])->name('actors.show');

// Upcoming series routes
Route::get('/proximamente', [App\Http\Controllers\UpcomingController::class, 'index'])->name('upcoming.index');
Route::get('/proximamente/{upcomingSeries}', [App\Http\Controllers\UpcomingController::class, 'show'])->name('upcoming.show');



// Temporary admin login route
Route::get('/admin-login', function() {
    $user = \App\Models\User::where('is_admin', true)->first();
    if ($user) {
        Auth::login($user);
        return redirect('/admin')->with('success', 'Logueado como administrador');
    }
    return redirect('/')->with('error', 'No se encontró un usuario administrador');
});

// === AUTHENTICATION ROUTES ===
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Mobile-friendly logout route
Route::get('/logout-mobile', function() {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home')->with('success', 'Has cerrado sesión exitosamente');
});

// Auth check API
Route::get('/auth/check', [AuthController::class, 'check'])->name('auth.check');

// Simple auth test after login
Route::get('/test-auth-simple', function() {
    if (Auth::check()) {
        return 'Estás logueado como: ' . Auth::user()->name . ' (ID: ' . Auth::id() . ')';
    } else {
        return 'No estás logueado';
    }
});

// Google OAuth routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// === UTILITY ROUTES ===
// CSRF refresh route
Route::get('/refresh-csrf', function() {
    $token = csrf_token();
    return response()->json([
        'csrf_token' => $token,
        'session_id' => session()->getId(),
        'time' => now()->toISOString()
    ])->header('Cache-Control', 'no-cache, no-store, must-revalidate');
});

// === AUTHENTICATED ROUTES ===
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/perfil', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/perfil/editar', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/perfil/lista-seguimiento', [ProfileController::class, 'watchlist'])->name('profile.watchlist');
    Route::get('/perfil/calificaciones', [ProfileController::class, 'ratings'])->name('profile.ratings');
    Route::get('/perfil/series-vistas', [ProfileController::class, 'watchedSeries'])->name('profile.watched');

    // Rating routes (AJAX)
    Route::post('/series/{series}/rate', [HomeController::class, 'rateTitle'])->name('series.rate');
    Route::delete('/series/{series}/rate', [HomeController::class, 'removeRating'])->name('series.rate.remove');
    
    // Movie rating routes (AJAX)
    Route::post('/movies/{movie}/rate', [MovieController::class, 'rate'])->name('movies.rate');
    Route::delete('/movies/{movie}/rate', [MovieController::class, 'removeRating'])->name('movies.rate.remove');
    
    // Watchlist routes (AJAX)
    Route::post('/series/{series}/watchlist', [HomeController::class, 'toggleWatchlist'])->name('series.watchlist.toggle');
    Route::put('/series/{series}/watchlist', [HomeController::class, 'updateWatchlistStatus'])->name('series.watchlist.update');
    
    // Movie watchlist routes (AJAX)
    Route::post('/movies/{movie}/watchlist', [MovieController::class, 'toggleWatchlist'])->name('movies.watchlist.toggle');
    
    // Watch history routes (AJAX)
    Route::post('/series/{series}/watched', [HomeController::class, 'markAsWatched'])->name('series.mark.watched');
    
    // Actor follow routes (AJAX)
    Route::post('/actors/{actor}/follow', [ActorsController::class, 'follow'])->name('actors.follow');
    Route::delete('/actors/{actor}/follow', [ActorsController::class, 'unfollow'])->name('actors.unfollow');

    // Upcoming series interaction
    Route::post('/upcoming/{upcomingSeries}/interest', [App\Http\Controllers\UpcomingController::class, 'toggleInterest'])->name('upcoming.interest');
});

// === PUBLIC PROFILE ROUTES ===
Route::get('/usuario/{user}', [ProfileController::class, 'show'])->name('user.profile');
Route::get('/usuario/{user}/lista-seguimiento', [ProfileController::class, 'watchlist'])->name('user.watchlist');
Route::get('/usuario/{user}/calificaciones', [ProfileController::class, 'ratings'])->name('user.ratings');
Route::get('/usuario/{user}/series-vistas', [ProfileController::class, 'watchedSeries'])->name('user.watched');

// === COMMENT ROUTES (AJAX) ===
// Outside auth group to work with StaticAuth
Route::post('/series/{series}/comments', [HomeController::class, 'storeComment'])->name('series.comments.store');
Route::post('/actores/{actor}/comments', [ActorsController::class, 'storeComment'])->name('actors.comments.store');

// === EPISODE PROGRESS ROUTES (AJAX) ===
// Outside auth group to work with StaticAuth
Route::post('/episodes/{episode}/watched', [HomeController::class, 'markEpisodeAsWatched'])->name('episodes.mark.watched');
Route::delete('/episodes/{episode}/watched', [HomeController::class, 'markEpisodeAsUnwatched'])->name('episodes.mark.unwatched');
Route::put('/episodes/{episode}/progress', [HomeController::class, 'updateEpisodeProgress'])->name('episodes.update.progress');
Route::get('/series/{series}/progress', [HomeController::class, 'getSeriesProgress'])->name('series.progress');
Route::get('/series/{series}/episodes', [HomeController::class, 'getEpisodesList'])->name('series.episodes.list');

// === UPCOMING API ROUTES ===
Route::prefix('api/upcoming')->group(function () {
    Route::get('/', [App\Http\Controllers\UpcomingController::class, 'api'])->name('api.upcoming.index');
    Route::get('/widget', [App\Http\Controllers\UpcomingController::class, 'widget'])->name('api.upcoming.widget');
    Route::get('/calendar', [App\Http\Controllers\UpcomingController::class, 'calendar'])->name('api.upcoming.calendar');
    Route::get('/by-date', [App\Http\Controllers\UpcomingController::class, 'byDate'])->name('api.upcoming.by-date');
});

// === ADMIN ROUTES ===
Route::prefix('admin')->middleware('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');
    
    // Series management
    Route::get('/series', [App\Http\Controllers\Admin\AdminController::class, 'series'])->name('series');
    Route::get('/series/{series}/edit', [App\Http\Controllers\Admin\AdminController::class, 'editSeries'])->name('series.edit');
    Route::put('/series/{series}', [App\Http\Controllers\Admin\AdminController::class, 'updateSeries'])->name('series.update');
    Route::delete('/series/{series}', [App\Http\Controllers\Admin\AdminController::class, 'deleteSeries'])->name('series.delete');
    
    // Movies management
    Route::get('/movies', [App\Http\Controllers\Admin\AdminController::class, 'movies'])->name('movies');
    Route::get('/movies/{movie}/edit', [App\Http\Controllers\Admin\AdminController::class, 'editMovie'])->name('movies.edit');
    Route::put('/movies/{movie}', [App\Http\Controllers\Admin\AdminController::class, 'updateMovie'])->name('movies.update');
    Route::delete('/movies/{movie}', [App\Http\Controllers\Admin\AdminController::class, 'deleteMovie'])->name('movies.delete');
    
    // Users management
    Route::get('/users', [App\Http\Controllers\Admin\AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/toggle-admin', [App\Http\Controllers\Admin\AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::delete('/users/{user}', [App\Http\Controllers\Admin\AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Comments management
    Route::get('/comments', [App\Http\Controllers\Admin\AdminController::class, 'comments'])->name('comments');
    Route::delete('/comments/{comment}', [App\Http\Controllers\Admin\AdminController::class, 'deleteComment'])->name('comments.delete');
});