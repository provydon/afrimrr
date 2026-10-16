<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Startup;
use App\Support\StartupPresenter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarketplaceController extends Controller
{
    public function index(Request $request): Response
    {
        $sort = $request->string('sort', 'recent')->toString();

        $query = Startup::public()->with('owner')
            ->where('is_for_sale', true)
            ->whereNotNull('asking_price_usd');

        $query = match ($sort) {
            'price' => $query->orderByDesc('asking_price_usd'),
            'mrr' => $query->orderByDesc('mrr_usd'),
            default => $query->orderByDesc('updated_at'),
        };

        $listings = $query->get()->map(fn (Startup $s) => StartupPresenter::card($s));

        return Inertia::render('public/Marketplace', [
            'listings' => $listings,
            'sort' => $sort,
        ]);
    }
}
