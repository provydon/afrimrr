<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Startup;
use App\Support\StartupPresenter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeaderboardController extends Controller
{
    public function index(Request $request): Response
    {
        $category = $request->string('category')->toString();

        $startups = Startup::public()->with('owner')
            ->where('mrr_usd', '>', 0)
            ->when($category && $category !== 'all', fn ($q) => $q->where('category', $category))
            ->orderByDesc('mrr_usd')
            ->limit(100)
            ->get()
            ->map(fn (Startup $s, int $i) => array_merge(StartupPresenter::card($s), ['rank' => $i + 1]));

        return Inertia::render('public/Leaderboard', [
            'startups' => $startups,
            'categories' => config('mrr.categories'),
            'activeCategory' => $category ?: 'all',
        ]);
    }
}
