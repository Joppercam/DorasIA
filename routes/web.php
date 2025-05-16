<?php

<<<<<<< HEAD
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileStatisticsController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RomanticDramaController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\TitleController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\WatchHistoryController;
use App\Http\Controllers\WatchlistController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Public routes
Route::get('/', function() {
    // If user is authenticated, check for active profile
    if (Auth::check()) {
        $user = Auth::user();
        $activeProfile = $user->getActiveProfile();
        
        if (!$activeProfile) {
            return redirect()->route('user-profiles.selector');
        }
    }
    
    // Continue to home page
    return app()->make(CatalogController::class)->home();
})->name('home');

// CSRF token route for AJAX requests
Route::get('/csrf-token', function() {
    return csrf_token();
});

// Test watchlist page
Route::get('/test-watchlist', function() {
    return view('test-watchlist');
})->middleware(['auth']);

// Search routes
Route::get('/search/advanced', [App\Http\Controllers\Api\SearchController::class, 'advancedSearch'])->name('search.advanced');

// Catalog routes
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/genre/{slug}', [CatalogController::class, 'genre'])->name('catalog.genre');
Route::get('/category/{slug}', [CatalogController::class, 'category'])->name('catalog.category');

// Romantic Drama routes
Route::prefix('romantic-dramas')->name('romantic-dramas.')->group(function () {
    Route::get('/', [RomanticDramaController::class, 'index'])->name('index');
    Route::get('/subgenre/{subgenre}', [RomanticDramaController::class, 'showSubgenre'])->name('subgenre');
    Route::get('/origin/{origin}', [RomanticDramaController::class, 'showByOrigin'])->name('origin');
    Route::get('/search', [RomanticDramaController::class, 'search'])->name('search');
    Route::get('/recommendations/{title}', [RomanticDramaController::class, 'recommendations'])->name('recommendations');
});

// Card comparison view
Route::get('/card-comparison', function() {
    return view('card-comparison');
})->name('card.comparison');

// Test simple romantic dramas
Route::get('/test-romantic-simple', function() {
    return view('test-romantic-simple');
});

// Test rating system
Route::get('/test-ratings', function() {
    return view('test-ratings');
})->name('test.ratings');

// Test comments system
Route::get('/test-comments', function() {
    return view('test-comments');
})->name('test.comments');

// Debug title components
Route::get('/debug-title-components', function() {
    return view('debug-title-components');
})->name('debug.title.components');

// Test profile edit
Route::get('/test-profile-edit', function() {
    return view('test-profile-edit');
})->name('test.profile.edit')->middleware('auth');

// Test relationships
Route::get('/test-relationships', function() {
    return view('test-relationships');
})->name('test.relationships');

// Title routes (public access)
Route::get('/titles/{slug}', [TitleController::class, 'show'])->name('titles.show');

// Person routes
Route::prefix('people')->name('people.')->group(function () {
    Route::get('/', [PersonController::class, 'index'])->name('index');
    Route::get('/popular', [PersonController::class, 'popular'])->name('popular');
    Route::get('/search', [PersonController::class, 'search'])->name('search');
    Route::get('/{slug}', [PersonController::class, 'show'])->name('show');
});

// News routes
Route::prefix('news')->name('news.')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('index');
    Route::get('/person/{slug}', [NewsController::class, 'personNews'])->name('person');
    Route::get('/{slug}', [NewsController::class, 'show'])->name('show');
});

// Comments section
Route::get('/community/comments', function() {
    return view('comments.index');
})->name('comments.index');

// Ratings section
Route::get('/community/ratings', function() {
    return view('ratings.index');
})->name('community.ratings');

// Dashboard route
Route::get('/dashboard', function () {
    // Check if user has an active profile
    $user = auth()->user();
    if ($user) {
        $activeProfile = $user->getActiveProfile();
        
        if (!$activeProfile) {
            return redirect()->route('user-profiles.create')
                ->with('info', 'Por favor, crea un perfil primero.');
        }
        
        return view('dashboard', [
            'profile' => $activeProfile,
        ]);
    }
    
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // User settings routes (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Dorasia Profiles routes
    Route::get('who-is-watching', [UserProfileController::class, 'selector'])
        ->name('user-profiles.selector');
    Route::resource('user-profiles', UserProfileController::class);
    Route::post('user-profiles/{profile}/set-active', [UserProfileController::class, 'setActive'])
        ->name('user-profiles.set-active');
    Route::post('user-profiles/{profile}/set-active-ajax', [UserProfileController::class, 'setActiveAjax'])
        ->name('user-profiles.set-active-ajax');
});

// Routes that require authentication and active profile
Route::middleware(['auth', 'has.active.profile'])->group(function () {
    // Watchlist routes
    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist.index');
    Route::post('/watchlist', [WatchlistController::class, 'store'])->name('watchlist.store');
    Route::post('/watchlist/toggle', [WatchlistController::class, 'toggle'])->name('watchlist.toggle');
    Route::get('/watchlist/status/{id}', [WatchlistController::class, 'status'])->name('watchlist.status');
    Route::delete('/watchlist/{id}', [WatchlistController::class, 'destroy'])->name('watchlist.destroy');
    Route::post('/watchlist/like/toggle', [WatchlistController::class, 'toggleLike'])->name('watchlist.toggle-like');
    Route::put('/watchlist/notes', [WatchlistController::class, 'updateNotes'])->name('watchlist.update-notes');
    Route::put('/watchlist/category', [WatchlistController::class, 'updateCategory'])->name('watchlist.update-category');
    Route::put('/watchlist/priority', [WatchlistController::class, 'updatePriority'])->name('watchlist.update-priority');
    Route::put('/watchlist/position', [WatchlistController::class, 'updatePosition'])->name('watchlist.update-position');
    Route::post('/watchlist/batch-position', [WatchlistController::class, 'batchUpdatePositions'])->name('watchlist.batch-update-positions');
    
    // Comments routes
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/reply', [CommentController::class, 'reply'])->name('comments.reply');
    
    // Ratings routes
    Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');
    Route::put('/ratings/{rating}', [RatingController::class, 'update'])->name('ratings.update');
    Route::delete('/ratings/{rating}', [RatingController::class, 'destroy'])->name('ratings.destroy');
    
    // AJAX Rating routes
    Route::post('/titles/{title}/rate', [RatingController::class, 'rate'])->name('titles.rate');
    Route::get('/titles/{title}/rating-statistics', [RatingController::class, 'statistics'])->name('titles.rating-statistics');
    Route::delete('/titles/{title}/rating', [RatingController::class, 'destroy'])->name('titles.rating.destroy');
    
    // Profile statistics
    Route::get('/profile/statistics', [ProfileStatisticsController::class, 'index'])->name('profile.statistics');
    Route::get('/profiles/{profile}/statistics', [ProfileStatisticsController::class, 'show'])->name('profiles.statistics');
    
    // Social Profile routes
    Route::get('/profiles/{profile}', [App\Http\Controllers\SocialProfileController::class, 'show'])->name('profiles.show');
    Route::get('/profiles/{profile}/edit', [App\Http\Controllers\SocialProfileController::class, 'edit'])->name('profiles.edit')->middleware(['auth', 'has.active.profile']);
    Route::put('/profiles/{profile}', [App\Http\Controllers\SocialProfileController::class, 'update'])->name('profiles.update')->middleware('can:update,profile');
    Route::get('/profiles/{profile}/followers', [App\Http\Controllers\SocialProfileController::class, 'followers'])->name('profiles.followers');
    Route::get('/profiles/{profile}/following', [App\Http\Controllers\SocialProfileController::class, 'following'])->name('profiles.following');
    Route::post('/profiles/{profile}/follow', [App\Http\Controllers\SocialProfileController::class, 'follow'])->name('profiles.follow');
    Route::post('/profiles/{profile}/unfollow', [App\Http\Controllers\SocialProfileController::class, 'unfollow'])->name('profiles.unfollow');
    
    // Messages routes
    Route::get('/messages', [App\Http\Controllers\SocialProfileController::class, 'messages'])->name('profiles.messages');
    Route::get('/messages/{otherUser}', [App\Http\Controllers\SocialProfileController::class, 'conversation'])->name('profiles.messages.conversation');
    Route::post('/messages', [App\Http\Controllers\SocialProfileController::class, 'sendMessage'])->name('profiles.messages.create');
    
    // Activity feed
    Route::get('/feed', [App\Http\Controllers\SocialProfileController::class, 'feed'])->name('profiles.feed');
    
    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::get('/settings', [App\Http\Controllers\NotificationController::class, 'settings'])->name('settings');
        Route::post('/settings', [App\Http\Controllers\NotificationController::class, 'updateSettings'])->name('settings.update');
    });
    
    // Watch history routes - DISABLED (Portal is information only)
    // Route::get('/watch-history', [WatchHistoryController::class, 'index'])->name('watch-history.index');
    // Route::post('/watch-history', [WatchHistoryController::class, 'store'])->name('watch-history.store');
    // Route::put('/watch-history/{watchHistory}', [WatchHistoryController::class, 'update'])->name('watch-history.update');
    
    // Redirect watch routes to show page (Portal is information only)
    Route::get('/watch/{slug}/{season?}/{episode?}/{startTime?}', function($slug) {
        return redirect()->route('titles.show', $slug);
    })->name('titles.watch');
    
    // Test route - DISABLED
    // Route::get('/test-watch/{slug}', [TitleController::class, 'testWatch'])->name('titles.test-watch');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Title CRUD for admins
    Route::resource('titles', TitleController::class)->except(['show']);
    
    // News admin routes
    Route::get('news', [NewsController::class, 'admin'])->name('news.index');
    Route::resource('news', NewsController::class)->except(['index', 'show']);
    
    // Person admin routes
    Route::resource('people', PersonController::class)->except(['index', 'show']);
});

// Social Login Routes
Route::middleware('guest')->group(function () {
    Route::get('/auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('social.login');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])->name('social.callback');
});

require __DIR__.'/auth.php';

// Test enhanced comments component
Route::get('/test-enhanced-comments', function() {
    $title = \App\Models\Title::first();
    if (!$title) {
        return 'No titles found in database';
    }
    return view('test-enhanced-comments', compact('title'));
});

// Test null title scenario
Route::get('/test-title-null', function() {
    return view('test-title-null');
});
=======
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
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
