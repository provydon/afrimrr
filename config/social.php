<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auto-announcements
    |--------------------------------------------------------------------------
    |
    | When enabled, AfricanMRR posts a short "just joined" announcement to a
    | social channel the first time a startup's MRR is verified (mirrors the
    | TrustMRR launch posts). The profile link unfurls into a card showing the
    | startup name + verified MRR. Disabled by default so nothing is posted
    | until real API credentials are configured in production.
    |
    */
    'enabled' => (bool) env('SOCIAL_ANNOUNCE', false),

    // Where announcements go: 'log' (writes to the log, safe default) or 'twitter' (X/Twitter API v2).
    'driver' => env('SOCIAL_DRIVER', 'log'),

    'twitter' => [
        // OAuth 1.0a user-context credentials (from the X developer portal).
        'api_key' => env('TWITTER_API_KEY'),
        'api_secret' => env('TWITTER_API_SECRET'),
        'access_token' => env('TWITTER_ACCESS_TOKEN'),
        'access_secret' => env('TWITTER_ACCESS_SECRET'),

        // App-only bearer token, used to read public follower counts on founder pages.
        'bearer_token' => env('TWITTER_BEARER_TOKEN'),
    ],
];
