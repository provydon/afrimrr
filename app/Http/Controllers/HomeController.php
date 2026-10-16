<?php

namespace App\Http\Controllers;

use App\Models\Startup;
use App\Support\Money;
use App\Support\StartupPresenter;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $top = Startup::public()->with('owner')
            ->where('mrr_usd', '>', 0)
            ->orderByDesc('mrr_usd')
            ->limit(6)
            ->get()
            ->map(fn (Startup $s, int $i) => array_merge(StartupPresenter::card($s), ['rank' => $i + 1]));

        $totalMrr = (int) Startup::public()->verified()->sum('mrr_usd');

        return Inertia::render('Welcome', [
            'topStartups' => $top,
            'stats' => [
                'verified_count' => Startup::public()->verified()->count(),
                'total_mrr_display' => Money::compactUsd($totalMrr),
                'country_count' => Startup::public()->distinct('country')->count('country'),
            ],
        ]);
    }
}
