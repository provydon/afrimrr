<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Startup;
use App\Support\StartupPresenter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StartupDirectoryController extends Controller
{
    public function index(Request $request): Response
    {
        $q = $request->string('q')->toString();
        $category = $request->string('category')->toString();
        $country = $request->string('country')->toString();
        $sort = $request->string('sort', 'mrr')->toString();

        $query = Startup::public()->with('owner')
            ->when($q, fn ($b) => $b->where(fn ($w) => $w
                ->where('name', 'like', "%{$q}%")
                ->orWhere('tagline', 'like', "%{$q}%")))
            ->when($category && $category !== 'all', fn ($b) => $b->where('category', $category))
            ->when($country && $country !== 'all', fn ($b) => $b->where('country', $country));

        $query = match ($sort) {
            'recent' => $query->orderByDesc('created_at'),
            'growth' => $query->orderByDesc('mom_growth'),
            default => $query->orderByDesc('mrr_usd'),
        };

        $startups = $query->paginate(24)->withQueryString();
        $startups->getCollection()->transform(fn (Startup $s) => StartupPresenter::card($s));

        return Inertia::render('public/Startups', [
            'startups' => $startups,
            'filters' => compact('q', 'category', 'country', 'sort'),
            'categories' => config('mrr.categories'),
            'countries' => config('mrr.countries'),
        ]);
    }
}
