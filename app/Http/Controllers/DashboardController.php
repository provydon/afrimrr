<?php

namespace App\Http\Controllers;

use App\Models\Startup;
use App\Support\Money;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $startups = $request->user()->startups()
            ->with('connections')
            ->orderByDesc('mrr_usd')
            ->get()
            ->map(fn (Startup $s) => [
                'id' => $s->id,
                'name' => $s->name,
                'slug' => $s->slug,
                'category_label' => $s->categoryLabel(),
                'is_verified' => $s->is_verified,
                'verification_source' => $s->verification_source,
                'mrr_usd_display' => Money::usd($s->mrr_usd),
                'mrr_local_display' => Money::format($s->mrr_local, $s->display_currency),
                'display_currency' => $s->display_currency,
                'mom_growth' => $s->mom_growth,
                'is_public' => $s->is_public,
                'is_for_sale' => $s->is_for_sale,
                'last_synced_at' => $s->last_synced_at?->diffForHumans(),
                'connections' => $s->connections->map(fn ($c) => [
                    'provider' => $c->provider,
                    'provider_label' => $c->providerLabel(),
                    'status' => $c->status,
                    'last_error' => $c->last_error,
                    'last_synced_at' => $c->last_synced_at?->diffForHumans(),
                ]),
            ]);

        $totalMrr = (int) $request->user()->startups()->sum('mrr_usd');

        return Inertia::render('Dashboard', [
            'startups' => $startups,
            'totalMrrDisplay' => Money::usd($totalMrr),
        ]);
    }
}
