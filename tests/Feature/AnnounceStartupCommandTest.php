<?php

namespace Tests\Feature;

use App\Models\Startup;
use App\Services\Social\Contracts\SocialPoster;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\FakePoster;
use Tests\TestCase;

class AnnounceStartupCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fails_for_an_unknown_slug(): void
    {
        $this->artisan('startups:announce missing')
            ->expectsOutputToContain('Startup not found.')
            ->assertFailed();
    }

    public function test_it_warns_and_posts_nothing_when_disabled(): void
    {
        config()->set('social.enabled', false);
        $poster = new FakePoster;
        $this->app->instance(SocialPoster::class, $poster);
        $startup = Startup::factory()->verified()->create(['mrr_usd' => 1_000_00]);

        $this->artisan('startups:announce '.$startup->slug)
            ->expectsOutputToContain('disabled')
            ->assertSuccessful();

        $this->assertSame([], $poster->posts);
    }

    public function test_it_posts_when_enabled(): void
    {
        config()->set('social.enabled', true);
        $poster = new FakePoster;
        $this->app->instance(SocialPoster::class, $poster);
        $startup = Startup::factory()->verified()->create(['mrr_usd' => 1_000_00, 'announced_at' => null]);

        $this->artisan('startups:announce '.$startup->slug)
            ->expectsOutputToContain('Announced')
            ->assertSuccessful();

        $this->assertCount(1, $poster->posts);
    }

    public function test_force_re_announces_an_already_posted_startup(): void
    {
        config()->set('social.enabled', true);
        $poster = new FakePoster;
        $this->app->instance(SocialPoster::class, $poster);
        $startup = Startup::factory()->verified()->create(['mrr_usd' => 1_000_00, 'announced_at' => now()]);

        $this->artisan('startups:announce '.$startup->slug.' --force')
            ->assertSuccessful();

        $this->assertCount(1, $poster->posts);
    }
}
