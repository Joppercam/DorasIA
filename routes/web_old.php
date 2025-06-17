<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ActorsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ProfileController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/explorar', [HomeController::class, 'browse'])->name('browse');
Route::get('/series/{id}', [HomeController::class, 'series'])->name('series.show');

// Search API route - Rate limited to 30 requests per minute
Route::get('/api/search', [HomeController::class, 'search'])
    ->middleware('rate.limit:search,30,1')
    ->name('api.search');

// Actors autocomplete API
Route::get('/api/actors/autocomplete', [ActorsController::class, 'autocomplete'])
    ->middleware('rate.limit:search,60,1')
    ->name('api.actors.autocomplete');

// News routes
Route::get('/noticias', [NewsController::class, 'index'])->name('news.index');
Route::get('/noticias/categoria/{category}', [NewsController::class, 'category'])->name('news.category');
Route::get('/noticias/{news:slug}', [NewsController::class, 'show'])->name('news.show');

// Movies routes
Route::get('/peliculas', [MovieController::class, 'index'])->name('movies.index');
Route::get('/peliculas/{movie}', [MovieController::class, 'show'])->name('movies.show');

// Actors routes
Route::get('/actores', [ActorsController::class, 'index'])->name('actors.index');
Route::get('/actores/{id}', [ActorsController::class, 'show'])->name('actors.show');

// CSRF refresh route
Route::get('/refresh-csrf', function() {
    $token = csrf_token();
    return response()->json([
        'csrf_token' => $token,
        'session_id' => session()->getId(),
        'time' => now()->toISOString()
    ])->header('Cache-Control', 'no-cache, no-store, must-revalidate');
});

// Authentication routes - Rate limited to prevent brute force attacks
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])
    ->middleware('rate.limit:auth,5,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::get('/register-debug', function() {
    return view('auth.register-debug');
});
// Route::post('/register', [RegisterController::class, 'register']);
Route::post('/register', [\App\Http\Controllers\Auth\SimpleRegisterController::class, 'register']);

// Ruta de registro sin CSRF para móviles
Route::post('/register-mobile', [RegisterController::class, 'register'])
    ->middleware('rate.limit:auth,3,1')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

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
});

// Comment routes (AJAX) - Outside auth group to work with StaticAuth
Route::post('/series/{series}/comments', [HomeController::class, 'storeComment'])->name('series.comments.store');
Route::post('/actores/{actor}/comments', [ActorsController::class, 'storeComment'])->name('actors.comments.store');

// Episode progress routes (AJAX) - Outside auth group to work with StaticAuth
Route::post('/episodes/{episode}/watched', [HomeController::class, 'markEpisodeAsWatched'])->name('episodes.mark.watched');
Route::delete('/episodes/{episode}/watched', [HomeController::class, 'markEpisodeAsUnwatched'])->name('episodes.mark.unwatched');
Route::put('/episodes/{episode}/progress', [HomeController::class, 'updateEpisodeProgress'])->name('episodes.update.progress');
Route::get('/series/{series}/progress', [HomeController::class, 'getSeriesProgress'])->name('series.progress');
Route::get('/series/{series}/episodes', [HomeController::class, 'getEpisodesList'])->name('series.episodes.list');

// Upcoming series routes
Route::get('/proximamente', [App\Http\Controllers\UpcomingController::class, 'index'])->name('upcoming.index');
Route::get('/proximamente/{upcomingSeries}', [App\Http\Controllers\UpcomingController::class, 'show'])->name('upcoming.show');

// Upcoming API routes
Route::prefix('api/upcoming')->group(function () {
    Route::get('/', [App\Http\Controllers\UpcomingController::class, 'api'])->name('api.upcoming.index');
    Route::get('/widget', [App\Http\Controllers\UpcomingController::class, 'widget'])->name('api.upcoming.widget');
    Route::get('/calendar', [App\Http\Controllers\UpcomingController::class, 'calendar'])->name('api.upcoming.calendar');
    Route::get('/by-date', [App\Http\Controllers\UpcomingController::class, 'byDate'])->name('api.upcoming.by-date');
});

// Authenticated upcoming routes
Route::middleware('auth')->group(function () {
    Route::post('/upcoming/{upcomingSeries}/interest', [App\Http\Controllers\UpcomingController::class, 'toggleInterest'])->name('upcoming.interest');
});

// Debug route
Route::get('/cookie-test', function() {
    // Intentar setear una cookie de prueba
    return response(view('cookie-test'))
        ->cookie('test_cookie', 'test_value_' . time(), 60);
});

Route::get('/debug', function() {
    return [
        'series_count' => \App\Models\Series::count(),
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'view_exists' => view()->exists('home'),
        'layout_exists' => view()->exists('layouts.app'),
        'session_driver' => config('session.driver'),
        'csrf_enabled' => config('session.encrypt'),
        'session_id' => session()->getId(),
        'can_write_sessions' => is_writable(storage_path('framework/sessions')),
        'user_authenticated' => auth()->check(),
        'current_user' => auth()->user() ? auth()->user()->only(['id', 'name', 'email']) : null,
        'users_count' => \App\Models\User::count()
    ];
});

// Test routes
Route::get('/test-form', function() {
    return view('test-form');
});

// Test de registro simple
Route::get('/test-register', function() {
    return view('auth.register-test');
});

Route::post('/test-register-simple', function(\Illuminate\Http\Request $request) {
    try {
        \Log::info('Test register attempt', $request->all());
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
        
        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);
        
        \Illuminate\Support\Facades\Auth::login($user);
        
        \Log::info('User registered successfully', ['user_id' => $user->id]);
        
        return redirect('/')->with('success', '¡Registro exitoso! Bienvenido a Dorasia.');
        
    } catch (\Exception $e) {
        \Log::error('Registration error: ' . $e->getMessage());
        return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
    }
})->name('test-register-simple');

Route::post('/test-register', function(\Illuminate\Http\Request $request) {
    return response()->json([
        'status' => 'OK',
        'data' => $request->all(),
        'session_id' => session()->getId(),
        'csrf_token' => csrf_token()
    ]);
})->withoutMiddleware(['web']);

// Test login routes
Route::get('/test-login', function() {
    return view('test-login');
});

Route::post('/test-login-submit', function(\Illuminate\Http\Request $request) {
    $credentials = $request->only('email', 'password');
    
    if (Auth::attempt($credentials)) {
        // NO regenerar sesión para conservar ID
        // $request->session()->regenerate();
        
        \Log::info('Test login successful', [
            'user_id' => auth()->id(),
            'session_id' => session()->getId()
        ]);
        
        // Redirigir directamente al cookie-test para ver el estado
        return redirect('/cookie-test')->with('message', 'Login exitoso!');
    }
    
    \Log::info('Test login failed', ['credentials' => $credentials]);
    return back()->withErrors(['email' => 'Credenciales incorrectas']);
});

// Ruta de login directo para testing
Route::get('/force-login', function() {
    $user = \App\Models\User::first();
    Auth::login($user);
    
    return response("Usuario logueado: " . auth()->user()->name . "<br>Session ID: " . session()->getId() . "<br><a href='/cookie-test'>Ver cookie test</a>");
});

// Working login system
Route::get('/working-login', function() {
    return view('auth.login-working');
});

// Emergency registration
Route::get('/emergency-register', function() {
    return view('auth.emergency-register');
});

Route::post('/emergency-register', function(\Illuminate\Http\Request $request) {
    try {
        \Log::info('Emergency register attempt', $request->all());
        
        // Validación básica
        if (empty($request->name) || empty($request->email) || empty($request->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Todos los campos son requeridos'
            ]);
        }
        
        if (strlen($request->password) < 6) {
            return response()->json([
                'success' => false,
                'message' => 'La contraseña debe tener al menos 6 caracteres'
            ]);
        }
        
        // Verificar email único
        if (\App\Models\User::where('email', $request->email)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Este email ya está registrado'
            ]);
        }
        
        // Crear usuario
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'email_verified_at' => now()
        ]);
        
        // Iniciar sesión
        \Illuminate\Support\Facades\Auth::login($user);
        
        return response()->json([
            'success' => true,
            'message' => '¡Registro exitoso! Redirigiendo...',
            'user_id' => $user->id
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Emergency register error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
});

Route::post('/working-login-submit', function(\Illuminate\Http\Request $request) {
    $user = \App\Models\User::find($request->user_id);
    if ($user) {
        Auth::login($user);
        session(['manual_auth' => true, 'user_id' => $user->id]);
        return redirect('/working-login')->with('message', '¡Sesión iniciada correctamente!');
    }
    return back()->with('error', 'Usuario no encontrado');
});

Route::get('/working-logout', function() {
    \Log::info('Logout route accessed');
    
    try {
        Auth::logout();
        session()->forget(['manual_auth', 'user_id']);
        session()->invalidate();
        session()->regenerateToken();
        
        \Log::info('Logout successful');
        return redirect('/')->with('message', 'Sesión cerrada exitosamente');
    } catch (\Exception $e) {
        \Log::error('Logout error: ' . $e->getMessage());
        return redirect('/')->with('error', 'Error al cerrar sesión');
    }
})->name('logout.simple');
