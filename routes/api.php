<?php

use App\Http\Controllers\Api\StartupApiController;
use Illuminate\Support\Facades\Route;

Route::get('/startups', [StartupApiController::class, 'index'])->name('api.startups.index');
Route::get('/startups/{startup}', [StartupApiController::class, 'show'])->name('api.startups.show');
