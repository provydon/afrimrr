<?php

use App\Http\Controllers\Api\StartupApiController;
use Illuminate\Support\Facades\Route;

Route::get('/startups', [StartupApiController::class, 'index'])->name('api.startups.index');
Route::get('/leaderboard', [StartupApiController::class, 'leaderboard'])->name('api.leaderboard');
Route::get('/startups/{startup}', [StartupApiController::class, 'show'])->name('api.startups.show');
