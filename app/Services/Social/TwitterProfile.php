<?php

namespace App\Services\Social;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Reads public profile metrics (follower count) from X (Twitter) API v2 using
 * an app-only bearer token. Cached, gated, and fails soft to null so founder
 * pages render fine without credentials.
 */
class TwitterProfile
{
    public function followers(?string $handle): ?int
    {
        $handle = ltrim(trim((string) $handle), '@');

        if ($handle === '') {
            return null;
        }

        $token = (string) config('social.twitter.bearer_token');

        if ($token === '') {
            return null;
        }

        return Cache::remember(
            "x:followers:{$handle}",
            now()->addHours(12),
            fn () => $this->fetch($handle, $token),
        );
    }

    private function fetch(string $handle, string $token): ?int
    {
        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(10)
                ->get("https://api.twitter.com/2/users/by/username/{$handle}", [
                    'user.fields' => 'public_metrics',
                ]);

            if (! $response->successful()) {
                return null;
            }

            $count = $response->json('data.public_metrics.followers_count');

            return $count !== null ? (int) $count : null;
        } catch (Throwable) {
            return null;
        }
    }
}
