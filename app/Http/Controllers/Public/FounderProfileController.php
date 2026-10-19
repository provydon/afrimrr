<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Startup;
use App\Models\User;
use App\Services\Social\TwitterProfile;
use App\Support\Money;
use App\Support\StartupPresenter;
use Inertia\Inertia;
use Inertia\Response;

class FounderProfileController extends Controller
{
    public function show(string $username, TwitterProfile $twitter): Response
    {
        $user = User::where('username', $username)->firstOrFail();

        $public = $user->startups()->where('is_public', true);

        $startups = (clone $public)
            ->orderByDesc('mrr_usd')
            ->get()
            ->map(fn (Startup $s) => StartupPresenter::card($s));

        $totalMrr = (int) (clone $public)->sum('mrr_usd');
        $totalStartups = (clone $public)->count();
        $verifiedStartups = (clone $public)->where('is_verified', true)->count();

        $followers = $twitter->followers($user->twitter);

        return Inertia::render('public/FounderProfile', [
            'founder' => [
                'name' => $user->name,
                'username' => $user->publicUsername(),
                'headline' => $user->headline,
                'bio' => $user->bio,
                'avatar_url' => $user->avatar_url,
                'twitter' => $user->twitter,
                'twitter_url' => $user->twitter ? 'https://x.com/'.ltrim($user->twitter, '@') : null,
                'website' => $user->website,
            ],
            'startups' => $startups,
            'stats' => [
                'total_mrr_display' => Money::usd($totalMrr),
                'arr_display' => Money::usd($totalMrr * 12),
                'total_startups' => $totalStartups,
                'verified_startups' => $verifiedStartups,
                'followers' => $followers,
                'followers_display' => $followers !== null ? $this->compact($followers) : null,
            ],
        ]);
    }

    /**
     * Compact a follower count, e.g. 411800 -> "411.8k", 5672 -> "5,672".
     */
    private function compact(int $n): string
    {
        if ($n >= 1_000_000) {
            return rtrim(rtrim(number_format($n / 1_000_000, 1), '0'), '.').'M';
        }

        if ($n >= 10_000) {
            return rtrim(rtrim(number_format($n / 1_000, 1), '0'), '.').'k';
        }

        return number_format($n);
    }
}
