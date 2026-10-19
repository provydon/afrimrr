<?php

namespace App\Services\Social;

use App\Services\Social\Contracts\SocialPoster;
use RuntimeException;

class SocialPosterFactory
{
    public static function make(?string $driver = null): SocialPoster
    {
        $driver ??= (string) config('social.driver', 'log');

        return match ($driver) {
            'log' => new LogPoster,
            'twitter' => self::twitter(),
            default => throw new RuntimeException("Unsupported social driver [{$driver}]."),
        };
    }

    private static function twitter(): TwitterPoster
    {
        $config = (array) config('social.twitter');

        foreach (['api_key', 'api_secret', 'access_token', 'access_secret'] as $key) {
            if (empty($config[$key])) {
                throw new RuntimeException("Missing Twitter credential [{$key}].");
            }
        }

        return new TwitterPoster(
            (string) $config['api_key'],
            (string) $config['api_secret'],
            (string) $config['access_token'],
            (string) $config['access_secret'],
        );
    }
}
