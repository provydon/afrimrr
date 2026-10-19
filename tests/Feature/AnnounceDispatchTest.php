<?php

namespace Tests\Feature;

use App\Actions\SyncStartup;
use App\Jobs\AnnounceStartup;
use App\Models\Connection;
use App\Models\Startup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AnnounceDispatchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'api.paystack.co/subscription*' => Http::response([
                'data' => [['status' => 'active', 'plan' => ['interval' => 'monthly', 'currency' => 'NGN', 'amount' => 500_000]]],
                'meta' => ['pageCount' => 1],
            ]),
        ]);
    }

    private function startupWithConnection(array $attributes = []): Startup
    {
        $startup = Startup::factory()->create(array_merge(['mrr_usd' => 0, 'announced_at' => null], $attributes));
        Connection::factory()->provider('paystack')->create(['startup_id' => $startup->id]);

        return $startup;
    }

    public function test_first_verified_sync_queues_an_announcement_when_enabled(): void
    {
        config()->set('social.enabled', true);
        Queue::fake();
        $startup = $this->startupWithConnection();

        (new SyncStartup)->handle($startup);

        Queue::assertPushed(AnnounceStartup::class, 1);
        Queue::assertPushed(fn (AnnounceStartup $job) => $job->startupId === $startup->id);
    }

    public function test_no_announcement_is_queued_when_disabled(): void
    {
        config()->set('social.enabled', false);
        Queue::fake();

        (new SyncStartup)->handle($this->startupWithConnection());

        Queue::assertNotPushed(AnnounceStartup::class);
    }

    public function test_already_announced_startup_does_not_queue_again(): void
    {
        config()->set('social.enabled', true);
        Queue::fake();

        (new SyncStartup)->handle($this->startupWithConnection(['announced_at' => now()]));

        Queue::assertNotPushed(AnnounceStartup::class);
    }
}
