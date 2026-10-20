<?php

namespace Tests\Feature;

use App\Actions\SyncStartup;
use App\Models\Connection;
use App\Models\MrrSnapshot;
use App\Models\Startup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SyncStartupTest extends TestCase
{
    use RefreshDatabase;

    private function fakePaystack(int $amountMinor = 500_000): void
    {
        Http::fake([
            'api.paystack.co/transaction/totals*' => Http::response([
                'data' => [
                    'total_transactions' => 1,
                    'total_volume' => $amountMinor,
                    'total_volume_by_currency' => [['currency' => 'NGN', 'amount' => $amountMinor]],
                ],
            ]),
        ]);
    }

    public function test_sync_with_no_connections_returns_error(): void
    {
        $startup = Startup::factory()->create();

        $result = (new SyncStartup)->handle($startup);

        $this->assertFalse($result['ok']);
        $this->assertArrayHasKey('_', $result['errors']);
    }

    public function test_successful_sync_updates_startup_and_snapshots(): void
    {
        $this->fakePaystack(); // ₦5,000/mo -> $3.15
        $startup = Startup::factory()->create(['is_verified' => false, 'mrr_usd' => 0]);
        Connection::factory()->provider('paystack')->create(['startup_id' => $startup->id]);

        $result = (new SyncStartup)->handle($startup);

        $this->assertTrue($result['ok']);
        $this->assertSame(315, $result['mrr_usd']);

        $startup->refresh();
        $this->assertTrue($startup->is_verified);
        $this->assertSame(315, $startup->mrr_usd);
        $this->assertSame('NGN', $startup->display_currency);
        $this->assertSame(500_000, $startup->mrr_local);
        $this->assertSame('paystack', $startup->verification_source);
        $this->assertSame(1, $startup->active_subscriptions);
        $this->assertNotNull($startup->last_synced_at);

        $snapshot = MrrSnapshot::where('startup_id', $startup->id)->sole();
        $this->assertSame(315, $snapshot->mrr_usd);
        $this->assertSame(Carbon::today()->toDateString(), $snapshot->captured_on->toDateString());

        // Connection marked active.
        $this->assertDatabaseHas('connections', [
            'startup_id' => $startup->id,
            'provider' => 'paystack',
            'status' => 'active',
        ]);
    }

    public function test_sync_is_idempotent_for_same_day_snapshot(): void
    {
        $this->fakePaystack();
        $startup = Startup::factory()->create();
        Connection::factory()->provider('paystack')->create(['startup_id' => $startup->id]);

        (new SyncStartup)->handle($startup);
        (new SyncStartup)->handle($startup->fresh());

        $this->assertSame(1, MrrSnapshot::where('startup_id', $startup->id)->count());
    }

    public function test_multiple_providers_mark_source_as_multiple(): void
    {
        Http::fake([
            'api.paystack.co/transaction/totals*' => Http::response([
                'data' => [
                    'total_transactions' => 1,
                    'total_volume_by_currency' => [['currency' => 'NGN', 'amount' => 500_000]],
                ],
            ]),
            'api.stripe.com/v1/charges*' => Http::response([
                'data' => [
                    ['id' => 'ch_1', 'status' => 'succeeded', 'paid' => true, 'amount' => 1_000, 'amount_refunded' => 0, 'currency' => 'usd'],
                ],
                'has_more' => false,
            ]),
        ]);

        $startup = Startup::factory()->create();
        Connection::factory()->provider('paystack')->create(['startup_id' => $startup->id]);
        Connection::factory()->provider('stripe')->create(['startup_id' => $startup->id]);

        $result = (new SyncStartup)->handle($startup);

        $this->assertTrue($result['ok']);
        $this->assertSame(315 + 1_000, $result['mrr_usd']); // $3.15 + $10
        $this->assertSame('multiple', $startup->fresh()->verification_source);
        $this->assertSame(2, $startup->fresh()->active_subscriptions);
    }

    public function test_failed_provider_records_error_and_leaves_numbers_untouched(): void
    {
        Http::fake(['api.paystack.co/*' => Http::response([], 401)]);
        $startup = Startup::factory()->create(['mrr_usd' => 9_999, 'is_verified' => true]);
        Connection::factory()->provider('paystack')->create(['startup_id' => $startup->id]);

        $result = (new SyncStartup)->handle($startup);

        $this->assertFalse($result['ok']);
        $this->assertArrayHasKey('paystack', $result['errors']);
        $this->assertSame(9_999, $startup->fresh()->mrr_usd); // untouched

        $this->assertDatabaseHas('connections', [
            'startup_id' => $startup->id,
            'provider' => 'paystack',
            'status' => 'error',
        ]);
        $this->assertNotNull($startup->connections()->first()->last_error);
    }

    public function test_month_over_month_growth_is_computed_from_past_snapshot(): void
    {
        $this->fakePaystack(1_000_000); // ₦10,000/mo -> $6.30 = 630 cents
        $startup = Startup::factory()->create();
        Connection::factory()->provider('paystack')->create(['startup_id' => $startup->id]);

        // A month-old snapshot at half the new value -> +100% growth.
        MrrSnapshot::factory()->create([
            'startup_id' => $startup->id,
            'mrr_usd' => 315,
            'captured_on' => Carbon::today()->subDays(30)->toDateString(),
        ]);

        (new SyncStartup)->handle($startup);

        $this->assertSame(100.0, (float) $startup->fresh()->mom_growth);
    }
}
