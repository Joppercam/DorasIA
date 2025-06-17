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

// === AUTHENTICATION ROUTES ===
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('rate.limit:auth,5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('rate.limit:auth,3,1');

// Registro simple sin CSRF
Route::get('/registro', function() {
    return view('auth.register-simple');
})->name('register.simple.form');

Route::post('/registro', [AuthController::class, 'registerSimple'])->name('register.simple');

// Simple session debug like test-cookie
Route::get('/debug-session', function() {
    // First set cookie like in test-cookie
    setcookie('session_test', 'working', time() + 3600, '/', '', false, false);
    
    return 'Session cookie set! Should work like test-cookie.';
});

// Debug route with explicit session middleware
Route::get('/debug-auth', function() {
    // Force session to start
    session()->put('debug_test', 'test_value');
    session()->save();
    
    return response()->json([
        'authenticated' => Auth::check(),
        'user_id' => Auth::id(),
        'user' => Auth::user(),
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'cookies' => request()->cookies->all(),
        'headers' => request()->headers->all(),
        'session_config' => [
            'driver' => config('session.driver'),
            'cookie' => config('session.cookie'),
            'domain' => config('session.domain'),
            'path' => config('session.path'),
            'secure' => config('session.secure'),
            'http_only' => config('session.http_only'),
            'same_site' => config('session.same_site'),
        ]
    ]);
})->middleware('web');

// Temporary admin login route
Route::get('/admin-login', function() {
    $user = \App\Models\User::where('is_admin', true)->first();
    if ($user) {
        Auth::login($user);
        return redirect('/admin')->with('success', 'Logueado como administrador');
    }
    return redirect('/')->with('error', 'No se encontró un usuario administrador');
});

// Simple login routes without CSRF issues
Route::get('/login-simple', function() {
    return view('auth.login-simple');
})->name('login.simple');

Route::post('/login-simple', [AuthController::class, 'loginSimple'])->name('login.simple.post');

// Working login bypass using manual cookies
Route::get('/test-login', function() {
    $user = \App\Models\User::where('email', 'jpablo.basualdo@gmail.com')->first();
    if ($user) {
        // Set cookie manually using setcookie
        setcookie('user_logged_in', $user->id, time() + (480 * 60), '/', '', false, false);
        setcookie('user_auth_token', hash('sha256', $user->id . $user->email), time() + (480 * 60), '/', '', false, false);
        
        return redirect('/admin-manual')
            ->with('success', 'Login de prueba exitoso con cookies manuales');
    }
    return 'Usuario no encontrado';
});

// Manual admin access using cookies
Route::get('/admin-manual', function() {
    $userId = $_COOKIE['user_logged_in'] ?? null;
    $authToken = $_COOKIE['user_auth_token'] ?? null;
    
    if ($userId && $authToken) {
        $user = \App\Models\User::find($userId);
        if ($user && hash('sha256', $user->id . $user->email) === $authToken && $user->is_admin) {
            // Temporarily log in the user for this request
            Auth::login($user);
            // Use the actual controller method
            return app(\App\Http\Controllers\Admin\AdminController::class)->dashboard();
        }
    }
    
    return redirect('/login-simple')->with('error', 'Acceso denegado');
});

// Test cookie setting
Route::get('/test-cookie', function() {
    // Use setcookie directly
    setcookie('test_cookie', 'test_value', time() + 3600, '/', '', false, false);
    return 'Cookie set via setcookie! Check /debug-auth to see if it appears.';
});

// Mobile-friendly logout route
Route::get('/working-logout', function() {
    $userId = Auth::id();
    
    // Limpiar cookies manuales
    setcookie('user_logged_in', '', time() - 3600, '/', '', false, false);
    setcookie('user_auth_token', '', time() - 3600, '/', '', false, false);
    
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    \Log::info('Usuario cerró sesión (mobile)', ['user_id' => $userId]);

    return redirect()->route('home')->with('success', 'Has cerrado sesión exitosamente');
});

// Force login with cookies disabled
Route::get('/force-login', function() {
    $user = \App\Models\User::where('email', 'jpablo.basualdo@gmail.com')->first();
    if ($user) {
        // Bypass normal session system
        session()->flush();
        session()->regenerate();
        
        Auth::loginUsingId($user->id, true);
        
        // Force session data
        session(['auth.password_confirmed_at' => time()]);
        session()->save();
        
        return redirect('/admin');
    }
    return 'Usuario no encontrado';
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