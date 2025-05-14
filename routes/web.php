<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProfileController;
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
    
    // Watch history routes
    Route::get('/watch-history', [WatchHistoryController::class, 'index'])->name('watch-history.index');
    Route::post('/watch-history', [WatchHistoryController::class, 'store'])->name('watch-history.store');
    Route::put('/watch-history/{watchHistory}', [WatchHistoryController::class, 'update'])->name('watch-history.update');
    
    // Player/Watch route
    Route::get('/watch/{slug}/{season?}/{episode?}/{startTime?}', [TitleController::class, 'watch'])->name('titles.watch');
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
Route::get('/auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('social.login');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])->name('social.callback');

require __DIR__.'/auth.php';
