<?php

namespace Tests\Feature;

use App\Models\Startup;
use App\Services\Social\StartupAnnouncer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\FakePoster;
use Tests\TestCase;

class StartupAnnouncerTest extends TestCase
{
    use RefreshDatabase;

    private function announcer(FakePoster $poster): StartupAnnouncer
    {
        return new StartupAnnouncer($poster);
    }

    public function test_it_announces_a_newly_verified_startup_once(): void
    {
        config()->set('social.enabled', true);
        $poster = new FakePoster;
        $startup = Startup::factory()->verified()->create([
            'name' => 'Kudi',
            'mrr_usd' => 144_00,
            'active_subscriptions' => 16,
            'country' => 'NG',
            'announced_at' => null,
        ]);

        $posted = $this->announcer($poster)->announce($startup);

        $this->assertTrue($posted);
        $this->assertCount(1, $poster->posts);
        $this->assertNotNull($startup->fresh()->announced_at);

        $text = $poster->posts[0];
        $this->assertStringContainsString('Kudi just joined AfricanMRR', $text);
        $this->assertStringContainsString('MRR: $144/mo', $text);
        $this->assertStringContainsString('Transactions (30d): 16', $text);
        $this->assertStringContainsString('Nigeria', $text);
        $this->assertStringContainsString('/startups/'.$startup->slug, $text);
    }

    public function test_it_does_not_announce_when_disabled(): void
    {
        config()->set('social.enabled', false);
        $poster = new FakePoster;
        $startup = Startup::factory()->verified()->create(['mrr_usd' => 1_000_00, 'announced_at' => null]);

        $this->assertFalse($this->announcer($poster)->announce($startup));
        $this->assertSame([], $poster->posts);
        $this->assertNull($startup->fresh()->announced_at);
    }

    public function test_it_does_not_announce_twice(): void
    {
        config()->set('social.enabled', true);
        $poster = new FakePoster;
        $startup = Startup::factory()->verified()->create(['mrr_usd' => 1_000_00, 'announced_at' => now()]);

        $this->assertFalse($this->announcer($poster)->announce($startup));
        $this->assertSame([], $poster->posts);
    }

    public function test_it_does_not_announce_unverified_or_zero_mrr_or_private(): void
    {
        config()->set('social.enabled', true);
        $poster = new FakePoster;

        $unverified = Startup::factory()->create(['is_verified' => false, 'mrr_usd' => 1_000_00, 'announced_at' => null]);
        $zero = Startup::factory()->verified()->create(['mrr_usd' => 0, 'announced_at' => null]);
        $private = Startup::factory()->verified()->private()->create(['mrr_usd' => 1_000_00, 'announced_at' => null]);

        $this->assertFalse($this->announcer($poster)->announce($unverified));
        $this->assertFalse($this->announcer($poster)->announce($zero));
        $this->assertFalse($this->announcer($poster)->announce($private));
        $this->assertSame([], $poster->posts);
    }

    public function test_a_posting_failure_leaves_announced_at_null(): void
    {
        config()->set('social.enabled', true);
        $poster = new FakePoster(shouldFail: true);
        $startup = Startup::factory()->verified()->create(['mrr_usd' => 1_000_00, 'announced_at' => null]);

        $this->assertFalse($this->announcer($poster)->announce($startup));
        $this->assertNull($startup->fresh()->announced_at); // so a later sync can retry
    }
}
