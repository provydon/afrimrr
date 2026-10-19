<?php

namespace Tests\Feature;

use App\Models\Connection;
use App\Models\Startup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ConnectionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_connecting_a_valid_provider_verifies_mrr(): void
    {
        Http::fake([
            'api.paystack.co/subscription*' => Http::response([
                'data' => [['status' => 'active', 'plan' => ['interval' => 'monthly', 'currency' => 'NGN', 'amount' => 500_000]]],
                'meta' => ['pageCount' => 1],
            ]),
        ]);

        $user = User::factory()->create();
        $startup = Startup::factory()->create(['user_id' => $user->id, 'is_verified' => false, 'mrr_usd' => 0]);

        $this->actingAs($user)
            ->from(route('startups.connections', $startup))
            ->post(route('connections.store', $startup), [
                'provider' => 'paystack',
                'secret' => 'sk_test_validkey_123',
            ])
            ->assertRedirect(route('startups.connections', $startup))
            ->assertSessionHas('success');

        $startup->refresh();
        $this->assertTrue($startup->is_verified);
        $this->assertSame(315, $startup->mrr_usd);
        $this->assertDatabaseHas('connections', ['startup_id' => $startup->id, 'provider' => 'paystack', 'status' => 'active']);
    }

    public function test_connecting_with_a_rejected_key_flashes_an_error(): void
    {
        Http::fake(['api.paystack.co/*' => Http::response([], 401)]);

        $user = User::factory()->create();
        $startup = Startup::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->from(route('startups.connections', $startup))
            ->post(route('connections.store', $startup), [
                'provider' => 'paystack',
                'secret' => 'sk_test_badkey_123',
            ])
            ->assertRedirect(route('startups.connections', $startup))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('connections', ['startup_id' => $startup->id, 'provider' => 'paystack', 'status' => 'error']);
    }

    public function test_store_validates_provider_and_secret(): void
    {
        $user = User::factory()->create();
        $startup = Startup::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->from(route('startups.connections', $startup))
            ->post(route('connections.store', $startup), [
                'provider' => 'monnify',   // unsupported
                'secret' => 'short',       // < 10 chars
            ])
            ->assertSessionHasErrors(['provider', 'secret']);
    }

    public function test_disconnecting_last_provider_clears_verification(): void
    {
        $user = User::factory()->create();
        $startup = Startup::factory()->create(['user_id' => $user->id, 'is_verified' => true, 'verification_source' => 'paystack']);
        Connection::factory()->provider('paystack')->create(['startup_id' => $startup->id]);

        $this->actingAs($user)
            ->delete(route('connections.destroy', [$startup, 'paystack']))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('connections', ['startup_id' => $startup->id, 'provider' => 'paystack']);
        $startup->refresh();
        $this->assertFalse($startup->is_verified);
        $this->assertNull($startup->verification_source);
    }

    public function test_non_owner_cannot_manage_connections(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $startup = Startup::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($intruder)->get(route('startups.connections', $startup))->assertForbidden();
        $this->actingAs($intruder)->post(route('connections.store', $startup), [
            'provider' => 'paystack', 'secret' => 'sk_test_validkey_123',
        ])->assertForbidden();
        $this->actingAs($intruder)->post(route('startups.sync', $startup))->assertForbidden();
    }

    public function test_connections_page_lists_providers_and_existing_connections(): void
    {
        $user = User::factory()->create();
        $startup = Startup::factory()->create(['user_id' => $user->id]);
        Connection::factory()->provider('stripe')->active()->create(['startup_id' => $startup->id]);

        $this->actingAs($user)->get(route('startups.connections', $startup))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $p) => $p
                ->component('startups/Connections')
                ->where('startup.slug', $startup->slug)
                ->has('providers', 3) // paystack, flutterwave, stripe
                ->has('connections', 1)
                ->where('connections.0.provider', 'stripe'));
    }

    public function test_sync_action_refreshes_mrr(): void
    {
        Http::fake([
            'api.paystack.co/subscription*' => Http::response([
                'data' => [['status' => 'active', 'plan' => ['interval' => 'monthly', 'currency' => 'NGN', 'amount' => 1_000_000]]],
                'meta' => ['pageCount' => 1],
            ]),
        ]);

        $user = User::factory()->create();
        $startup = Startup::factory()->create(['user_id' => $user->id, 'mrr_usd' => 0]);
        Connection::factory()->provider('paystack')->create(['startup_id' => $startup->id]);

        $this->actingAs($user)->post(route('startups.sync', $startup))->assertSessionHas('success');

        $this->assertSame(630, $startup->fresh()->mrr_usd); // ₦10,000/mo -> $6.30
    }
}
