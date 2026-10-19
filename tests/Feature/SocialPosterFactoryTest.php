<?php

namespace Tests\Feature;

use App\Services\Social\Contracts\SocialPoster;
use App\Services\Social\LogPoster;
use App\Services\Social\SocialPosterFactory;
use App\Services\Social\TwitterPoster;
use RuntimeException;
use Tests\TestCase;

class SocialPosterFactoryTest extends TestCase
{
    public function test_log_driver_builds_a_log_poster(): void
    {
        $this->assertInstanceOf(LogPoster::class, SocialPosterFactory::make('log'));
    }

    public function test_twitter_driver_builds_a_twitter_poster_when_configured(): void
    {
        config()->set('social.twitter', [
            'api_key' => 'k', 'api_secret' => 's', 'access_token' => 't', 'access_secret' => 'x',
        ]);

        $this->assertInstanceOf(TwitterPoster::class, SocialPosterFactory::make('twitter'));
    }

    public function test_twitter_driver_without_credentials_throws(): void
    {
        config()->set('social.twitter', ['api_key' => null, 'api_secret' => null, 'access_token' => null, 'access_secret' => null]);

        $this->expectException(RuntimeException::class);
        SocialPosterFactory::make('twitter');
    }

    public function test_unknown_driver_throws(): void
    {
        $this->expectException(RuntimeException::class);
        SocialPosterFactory::make('mastodon');
    }

    public function test_container_resolves_the_configured_driver(): void
    {
        config()->set('social.driver', 'log');

        $this->assertInstanceOf(LogPoster::class, app(SocialPoster::class));
    }
}
