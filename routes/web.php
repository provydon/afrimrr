<?php

use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Mcp\McpController;
use App\Http\Controllers\Public\BadgeController;
use App\Http\Controllers\Public\FounderProfileController;
use App\Http\Controllers\Public\LeaderboardController;
use App\Http\Controllers\Public\MarketplaceController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\SitemapController;
use App\Http\Controllers\Public\StartupDirectoryController;
use App\Http\Controllers\Public\StartupProfileController;
use App\Http\Controllers\StartupController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');
Route::get('/startups', [StartupDirectoryController::class, 'index'])->name('startups.index');
Route::get('/startups/{startup}', [StartupProfileController::class, 'show'])->name('startups.show');
Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace');
Route::get('/founders/{username}', [FounderProfileController::class, 'show'])->name('founders.show');

Route::get('/badge/{startup}.svg', [BadgeController::class, 'svg'])
    ->where('startup', '[a-zA-Z0-9\-]+')
    ->name('badge.svg');

Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/badge-store', [PageController::class, 'badgeStore'])->name('badge-store');
Route::get('/developers', [PageController::class, 'developers'])->name('developers');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// MCP (Model Context Protocol) server — JSON-RPC over HTTP. CSRF-exempt (see bootstrap/app.php).
Route::post('/mcp', [McpController::class, 'handle'])->name('mcp');
Route::get('/mcp', [McpController::class, 'info']);

/*
|--------------------------------------------------------------------------
| Founder dashboard (auth)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('dashboard/startups')->group(function () {
        Route::get('/create', [StartupController::class, 'create'])->name('startups.create');
        Route::post('/', [StartupController::class, 'store'])->name('startups.store');
        Route::get('/{startup}/edit', [StartupController::class, 'edit'])->name('startups.edit');
        Route::put('/{startup}', [StartupController::class, 'update'])->name('startups.update');
        Route::delete('/{startup}', [StartupController::class, 'destroy'])->name('startups.destroy');

        Route::get('/{startup}/connections', [ConnectionController::class, 'index'])->name('startups.connections');
        Route::post('/{startup}/connections', [ConnectionController::class, 'store'])->name('connections.store');
        Route::post('/{startup}/sync', [ConnectionController::class, 'sync'])->name('startups.sync');
        Route::delete('/{startup}/connections/{provider}', [ConnectionController::class, 'destroy'])->name('connections.destroy');
    });
});

require __DIR__.'/settings.php';
