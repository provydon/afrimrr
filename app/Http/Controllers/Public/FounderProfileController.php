<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Startup;
use App\Models\User;
use App\Support\Money;
use App\Support\StartupPresenter;
use Inertia\Inertia;
use Inertia\Response;

class FounderProfileController extends Controller
{
    public function show(string $username): Response
    {
        $user = User::where('username', $username)->firstOrFail();

        $startups = $user->startups()
            ->where('is_public', true)
            ->orderByDesc('mrr_usd')
            ->get()
            ->map(fn (Startup $s) => StartupPresenter::card($s));

        $totalMrr = (int) $user->startups()->where('is_public', true)->sum('mrr_usd');

        return Inertia::render('public/FounderProfile', [
            'founder' => [
                'name' => $user->name,
                'username' => $user->publicUsername(),
                'headline' => $user->headline,
                'bio' => $user->bio,
                'avatar_url' => $user->avatar_url,
                'twitter' => $user->twitter,
                'website' => $user->website,
            ],
            'startups' => $startups,
            'totalMrrDisplay' => Money::usd($totalMrr),
        ]);
    }
}
