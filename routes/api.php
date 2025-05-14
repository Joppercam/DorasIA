<?php

use App\Http\Controllers\WatchlistController;
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