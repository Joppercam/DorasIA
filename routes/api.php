<?php

use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\Api\SocialController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Watchlist API routes
Route::middleware(['auth', 'has.active.profile'])->group(function () {
    Route::get('/watchlist/status/{id}', [WatchlistController::class, 'status'])->name('api.watchlist.status');
});

// API de búsqueda (pública)
Route::get('/search', [\App\Http\Controllers\Api\SearchController::class, 'search'])->name('api.search');

// Comments API routes (public read, auth required for write)
Route::get('/titles/{title}/comments', [\App\Http\Controllers\Api\CommentController::class, 'index']);
Route::get('/comments/{comment}/replies', [\App\Http\Controllers\Api\CommentController::class, 'getReplies']);

Route::middleware(['auth', 'has.active.profile'])->group(function () {
    Route::post('/titles/{title}/comments', [\App\Http\Controllers\Api\CommentController::class, 'store']);
    Route::post('/comments/{comment}/like', [\App\Http\Controllers\Api\CommentController::class, 'toggleLike']);
    Route::post('/comments/{comment}/replies', [\App\Http\Controllers\Api\CommentController::class, 'storeReply']);
    Route::delete('/comments/{comment}', [\App\Http\Controllers\Api\CommentController::class, 'destroy']);
});

// User search for mentions and messages
Route::middleware(['auth', 'has.active.profile'])->group(function () {
    Route::get('/users/search', [SocialController::class, 'searchUsers']);
});

// Popular searches
Route::get('/search/popular', [\App\Http\Controllers\Api\SearchController::class, 'popular']);

// Social API endpoints
Route::middleware(['auth', 'has.active.profile'])->group(function () {
    Route::prefix('profiles')->group(function () {
        // Follow/Unfollow
        Route::post('/{profile}/follow', [SocialController::class, 'follow']);
        Route::post('/{profile}/unfollow', [SocialController::class, 'unfollow']);
        
        // Followers/Following lists
        Route::get('/{profile}/followers', [SocialController::class, 'followers']);
        Route::get('/{profile}/following', [SocialController::class, 'following']);
        
        // Activity feed
        Route::get('/feed', [SocialController::class, 'getFeed']);
        
        // Profile statistics
        Route::get('/{profile}/stats', [SocialController::class, 'getProfileStats']);
        
        // Suggested profiles
        Route::get('/suggested', [SocialController::class, 'getSuggestedProfiles']);
    });
    
    // Messages API
    Route::prefix('messages')->group(function () {
        Route::post('/', [SocialController::class, 'sendMessage']);
        Route::get('/{userId}', [SocialController::class, 'getMessages']);
        Route::post('/{userId}/read', [SocialController::class, 'markMessagesAsRead']);
        Route::delete('/{userId}', [SocialController::class, 'deleteConversation']);
    });
    
    // Notifications API
    Route::prefix('notifications')->name('api.notifications.')->group(function () {
        Route::get('/recent', [\App\Http\Controllers\NotificationController::class, 'recent']);
        Route::post('/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead']);
        Route::delete('/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy']);
        Route::delete('/', [\App\Http\Controllers\NotificationController::class, 'destroyAll']);
    });
});