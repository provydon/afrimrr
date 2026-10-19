<?php

namespace Tests\Feature;

use App\Services\Social\TwitterProfile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TwitterProfileTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_it_returns_null_without_a_bearer_token(): void
    {
        config()->set('social.twitter.bearer_token', null);
        Http::fake(); // must not be called

        $this->assertNull((new TwitterProfile)->followers('someone'));
        Http::assertNothingSent();
    }

    public function test_it_returns_null_for_an_empty_handle(): void
    {
        config()->set('social.twitter.bearer_token', 'tok');
        Http::fake();

        $this->assertNull((new TwitterProfile)->followers(null));
        $this->assertNull((new TwitterProfile)->followers('  @  '));
        Http::assertNothingSent();
    }

    public function test_it_reads_the_follower_count(): void
    {
        config()->set('social.twitter.bearer_token', 'tok');
        Http::fake([
            'api.twitter.com/2/users/by/username/*' => Http::response([
                'data' => ['username' => 'ada', 'public_metrics' => ['followers_count' => 411800]],
            ]),
        ]);

        $this->assertSame(411800, (new TwitterProfile)->followers('@ada'));
    }

    public function test_it_fails_soft_on_api_error(): void
    {
        config()->set('social.twitter.bearer_token', 'tok');
        Http::fake(['api.twitter.com/*' => Http::response([], 429)]);

        $this->assertNull((new TwitterProfile)->followers('ada'));
    }

    public function test_it_caches_the_result(): void
    {
        config()->set('social.twitter.bearer_token', 'tok');
        Http::fake([
            'api.twitter.com/*' => Http::response(['data' => ['public_metrics' => ['followers_count' => 100]]]),
        ]);

        $profile = new TwitterProfile;
        $profile->followers('ada');
        $profile->followers('ada');

        Http::assertSentCount(1);
    }
}
