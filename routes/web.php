<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rutas públicas
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Autenticación
Auth::routes();

// Rutas del catálogo
Route::get('/discover', [CatalogController::class, 'discover'])->name('discover');
Route::get('/search', [CatalogController::class, 'search'])->name('search');

// Rutas para películas
Route::get('/movies', [CatalogController::class, 'movies'])->name('movies.index');
Route::get('/movies/{slug}', [CatalogController::class, 'movieDetail'])->name('movies.show');

// Rutas para series/doramas
Route::get('/tv-shows', [CatalogController::class, 'tvShows'])->name('tv-shows.index');
Route::get('/tv-shows/{slug}', [CatalogController::class, 'tvShowDetail'])->name('tv-shows.show');
Route::get('/tv-shows/{slug}/season/{season}', [CatalogController::class, 'season'])->name('tv-shows.season');
Route::get('/tv-shows/{slug}/season/{season}/episode/{episode}', [CatalogController::class, 'episode'])->name('tv-shows.episode');

// Rutas para categorización
Route::get('/genre/{slug}', [CatalogController::class, 'genre'])->name('catalog.genre');
Route::get('/country/{code}', [CatalogController::class, 'country'])->name('catalog.country');

// API para autocompletado de búsqueda
Route::get('/api/search', [CatalogController::class, 'apiSearch'])->name('api.search');

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/preferences', [ProfileController::class, 'preferences'])->name('profile.preferences');
    Route::put('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences.update');
    
    // Listas de visualización
    Route::get('/watchlists', [WatchlistController::class, 'index'])->name('watchlists.index');
    Route::get('/watchlists/create', [WatchlistController::class, 'create'])->name('watchlists.create');
    Route::post('/watchlists', [WatchlistController::class, 'store'])->name('watchlists.store');
    Route::get('/watchlists/{id}', [WatchlistController::class, 'show'])->name('watchlists.show');
    Route::get('/watchlists/{id}/edit', [WatchlistController::class, 'edit'])->name('watchlists.edit');
    Route::put('/watchlists/{id}', [WatchlistController::class, 'update'])->name('watchlists.update');
    Route::delete('/watchlists/{id}', [WatchlistController::class, 'destroy'])->name('watchlists.destroy');
    
    // Operaciones AJAX para favoritos, valoraciones y listas
    Route::post('/api/favorites/toggle', [UserController::class, 'toggleFavorite'])->name('favorites.toggle');
    Route::post('/api/watchlist/toggle', [WatchlistController::class, 'toggleWatchlist'])->name('watchlist.toggle');
    Route::post('/watchlists/add-item', [WatchlistController::class, 'addItem'])->name('watchlists.add-item');
    Route::delete('/watchlists/remove-item/{id}', [WatchlistController::class, 'removeItem'])->name('watchlists.remove-item');
    Route::post('/ratings/rate', [UserController::class, 'rate'])->name('ratings.rate');
});

// Rutas para administradores
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    // Otras rutas de administración se agregarán más tarde
});