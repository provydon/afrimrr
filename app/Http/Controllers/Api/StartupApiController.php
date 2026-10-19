<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Startup;
use App\Support\StartupPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StartupApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $startups = Startup::public()->with('owner')
            ->when($request->boolean('verified'), fn ($q) => $q->verified())
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
            ->orderByDesc('mrr_usd')
            ->paginate(min(100, (int) $request->integer('per_page', 50)));

        return response()->json([
            'data' => $startups->getCollection()->map(fn (Startup $s) => StartupPresenter::card($s)),
            'meta' => [
                'current_page' => $startups->currentPage(),
                'last_page' => $startups->lastPage(),
                'total' => $startups->total(),
            ],
        ]);
    }

    public function show(Startup $startup): JsonResponse
    {
        abort_unless($startup->is_public && ! $startup->is_anonymous, 404);

        $startup->load('owner');

        return response()->json(['data' => StartupPresenter::profile($startup)]);
    }

    public function leaderboard(Request $request): JsonResponse
    {
        $limit = min(100, max(1, (int) $request->integer('limit', 50)));

        $startups = Startup::public()->with('owner')
            ->where('mrr_usd', '>', 0)
            ->orderByDesc('mrr_usd')
            ->limit($limit)
            ->get()
            ->values()
            ->map(fn (Startup $s, int $i) => array_merge(StartupPresenter::card($s), ['rank' => $i + 1]));

        return response()->json(['data' => $startups]);
    }
}
