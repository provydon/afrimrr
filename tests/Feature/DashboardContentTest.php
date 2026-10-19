<?php

namespace Tests\Feature;

use App\Models\Connection;
use App\Models\Startup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_lists_the_owners_startups_and_total(): void
    {
        $user = User::factory()->create();
        $a = Startup::factory()->create(['user_id' => $user->id, 'mrr_usd' => 10_000_00]);
        Startup::factory()->create(['user_id' => $user->id, 'mrr_usd' => 5_000_00]);
        Connection::factory()->provider('paystack')->active()->create(['startup_id' => $a->id]);

        // Another user's startup must not appear.
        Startup::factory()->create(['mrr_usd' => 99_000_00]);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p
                ->component('Dashboard')
                ->has('startups', 2)
                ->where('startups.0.mrr_usd_display', '$10,000') // ordered by mrr desc
                ->has('startups.0.connections', 1)
                ->where('totalMrrDisplay', '$15,000'));
    }

    public function test_dashboard_includes_private_startups_for_the_owner(): void
    {
        $user = User::factory()->create();
        Startup::factory()->private()->create(['user_id' => $user->id, 'mrr_usd' => 1_000_00]);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertInertia(fn (Assert $p) => $p->has('startups', 1));
    }
}
