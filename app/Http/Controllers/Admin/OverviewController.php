<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Startup;
use App\Models\Subscriber;
use App\Support\Money;
use Inertia\Inertia;
use Inertia\Response;

class OverviewController extends Controller
{
    public function index(): Response
    {
        $totalMrr = (int) Startup::real()->verified()->sum('mrr_usd');

        return Inertia::render('admin/Overview', [
            'stats' => [
                'subscribers' => Subscriber::count(),
                'startups' => Startup::count(),
                'verified' => Startup::verified()->count(),
                'anonymous' => Startup::where('is_anonymous', true)->count(),
                'for_sale' => Startup::where('is_for_sale', true)->count(),
                'total_mrr_display' => Money::usd($totalMrr),
            ],
            'recentSubscribers' => Subscriber::latest()->limit(8)->get(['email', 'source', 'created_at'])
                ->map(fn (Subscriber $s) => [
                    'email' => $s->email,
                    'source' => $s->source,
                    'created_at' => $s->created_at?->diffForHumans(),
                ]),
            'recentStartups' => Startup::latest()->with('owner')->limit(8)->get()
                ->map(fn (Startup $s) => [
                    'name' => $s->is_anonymous ? 'Anonymous Startup' : $s->name,
                    'mrr_usd_display' => Money::usd($s->mrr_usd),
                    'is_verified' => $s->is_verified,
                    'owner' => $s->owner?->name,
                    'created_at' => $s->created_at?->diffForHumans(),
                ]),
        ]);
    }
}
