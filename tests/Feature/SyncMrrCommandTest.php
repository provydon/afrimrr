<?php

namespace Tests\Feature;

use App\Jobs\SyncStartupMrr;
use App\Models\Connection;
use App\Models\Startup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SyncMrrCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_reports_when_no_startups_have_connections(): void
    {
        Startup::factory()->create(); // no connections

        $this->artisan('mrr:sync')
            ->expectsOutputToContain('No startups with connected providers.')
            ->assertSuccessful();
    }

    public function test_it_queues_a_job_only_for_startups_with_connections(): void
    {
        Queue::fake();

        $withConn = Startup::factory()->create();
        Connection::factory()->provider('paystack')->create(['startup_id' => $withConn->id]);
        Startup::factory()->create(); // without a connection — skipped

        $this->artisan('mrr:sync')->assertSuccessful();

        Queue::assertPushed(SyncStartupMrr::class, 1);
        Queue::assertPushed(fn (SyncStartupMrr $job) => $job->startupId === $withConn->id);
    }

    public function test_now_flag_runs_the_sync_synchronously(): void
    {
        Http::fake([
            'api.paystack.co/subscription*' => Http::response([
                'data' => [['status' => 'active', 'plan' => ['interval' => 'monthly', 'currency' => 'NGN', 'amount' => 500_000]]],
                'meta' => ['pageCount' => 1],
            ]),
        ]);

        $startup = Startup::factory()->create(['mrr_usd' => 0]);
        Connection::factory()->provider('paystack')->create(['startup_id' => $startup->id]);

        $this->artisan('mrr:sync --now')->assertSuccessful();

        $this->assertSame(315, $startup->fresh()->mrr_usd);
    }
}
