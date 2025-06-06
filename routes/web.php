<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SeriesController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/series/{id}', [HomeController::class, 'series'])->name('series.show');