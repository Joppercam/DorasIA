<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\PlatformController;
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
Route::resource('movies', App\Http\Controllers\MovieController::class);
// Autenticación
Auth::routes();

// Rutas del catálogo
Route::get('/discover', [CatalogController::class, 'discover'])->name('discover');
Route::get('/search', [CatalogController::class, 'search'])->name('search');
Route::get('/trending', [CatalogController::class, 'trending'])->name('trending');
Route::get('/new-releases', [CatalogController::class, 'newReleases'])->name('new-releases');

// Rutas para películas
Route::get('/movies', [CatalogController::class, 'movies'])->name('catalog.movies');
Route::get('/movies/{slug}', [CatalogController::class, 'movieDetail'])->name('catalog.movie-detail');

// Rutas para series/doramas
Route::get('/tv-shows', [CatalogController::class, 'tvShows'])->name('catalog.series');
Route::get('/tv-shows/{slug}', [CatalogController::class, 'tvShowDetail'])->name('catalog.tv-show-detail');
Route::get('/tv-shows/{slug}/season/{season}', [CatalogController::class, 'season'])->name('catalog.season');
Route::get('/tv-shows/{slug}/season/{season}/episode/{episode}', [CatalogController::class, 'episode'])->name('catalog.episode');

// Rutas para categorización
Route::get('/genre/{slug}', [CatalogController::class, 'genre'])->name('catalog.genre');
Route::get('/country/{code}', [CatalogController::class, 'country'])->name('catalog.country');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');

// Rutas para plataformas
Route::get('/platforms/{slug}', [PlatformController::class, 'show'])->name('platforms.show');

// Páginas legales
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/cookies', [HomeController::class, 'cookies'])->name('cookies');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');

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
    Route::get('/watchlists', [WatchlistController::class, 'index'])->name('watchlist.index');
    Route::get('/watchlists/create', [WatchlistController::class, 'create'])->name('watchlist.create');
    Route::post('/watchlists', [WatchlistController::class, 'store'])->name('watchlist.store');
    Route::get('/watchlists/{id}', [WatchlistController::class, 'show'])->name('watchlist.show');
    Route::get('/watchlists/{id}/edit', [WatchlistController::class, 'edit'])->name('watchlist.edit');
    Route::put('/watchlists/{id}', [WatchlistController::class, 'update'])->name('watchlist.update');
    Route::delete('/watchlists/{id}', [WatchlistController::class, 'destroy'])->name('watchlist.destroy');
    
    // Favoritos
    Route::get('/favorites', [UserController::class, 'favorites'])->name('favorites.index');
    
    // Operaciones AJAX para favoritos, valoraciones y listas
    Route::post('/api/favorites/toggle', [UserController::class, 'toggleFavorite'])->name('favorites.toggle');
    Route::post('/api/watchlist/toggle', [WatchlistController::class, 'toggleWatchlist'])->name('watchlist.toggle');
    Route::post('/watchlists/add-item', [WatchlistController::class, 'addItem'])->name('watchlist.add-item');
    Route::delete('/watchlists/remove-item/{id}', [WatchlistController::class, 'removeItem'])->name('watchlist.remove-item');
    Route::post('/ratings/store', [UserController::class, 'storeRating'])->name('ratings.store');
    Route::put('/ratings/{id}', [UserController::class, 'updateRating'])->name('ratings.update');
    Route::delete('/ratings/{id}', [UserController::class, 'deleteRating'])->name('ratings.destroy');
});

// Rutas para administradores
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    // Otras rutas de administración se agregarán más tarde
});