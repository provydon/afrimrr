<?php

namespace Tests\Feature;

use App\Models\Startup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class StartupManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_reach_startup_management(): void
    {
        $this->get(route('startups.create'))->assertRedirect(route('login'));
    }

    public function test_owner_can_create_a_startup_and_is_sent_to_connections(): void
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)->post(route('startups.store'), [
            'name' => 'Paystackers',
            'category' => 'fintech',
            'country' => 'NG',
        ]);

        $startup = Startup::firstWhere('name', 'Paystackers');
        $this->assertNotNull($startup);
        $this->assertSame($user->id, $startup->user_id);
        $this->assertSame('paystackers', $startup->slug);
        $res->assertRedirect(route('startups.connections', $startup));
        $res->assertSessionHas('success');
    }

    public function test_store_validates_category_and_country(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('startups.create'))
            ->post(route('startups.store'), [
                'name' => 'Bad Co',
                'category' => 'not-a-category',
                'country' => 'ZZ',
            ])
            ->assertSessionHasErrors(['category', 'country']);
    }

    public function test_manual_mrr_is_applied_when_no_provider_is_connected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('startups.store'), [
            'name' => 'Manual Co',
            'category' => 'saas',
            'country' => 'NG',
            'manual_mrr_amount' => 5000,        // ₦5,000
            'manual_mrr_currency' => 'NGN',
        ]);

        $startup = Startup::firstWhere('name', 'Manual Co');
        $this->assertSame(500_000, $startup->mrr_local);       // kobo
        $this->assertSame('NGN', $startup->display_currency);
        $this->assertSame(315, $startup->mrr_usd);              // $3.15
        $this->assertSame('manual', $startup->verification_source);
        $this->assertFalse($startup->is_verified);
    }

    public function test_owner_can_update_their_startup(): void
    {
        $user = User::factory()->create();
        $startup = Startup::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->put(route('startups.update', $startup), [
            'name' => 'Renamed',
            'category' => 'ai',
            'country' => 'KE',
        ])->assertRedirect(route('dashboard'));

        $this->assertSame('Renamed', $startup->fresh()->name);
        $this->assertSame('ai', $startup->fresh()->category);
    }

    public function test_non_owner_cannot_edit_update_or_delete(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $startup = Startup::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($intruder)->get(route('startups.edit', $startup))->assertForbidden();
        $this->actingAs($intruder)->put(route('startups.update', $startup), [
            'name' => 'Hijack', 'category' => 'ai', 'country' => 'KE',
        ])->assertForbidden();
        $this->actingAs($intruder)->delete(route('startups.destroy', $startup))->assertForbidden();

        $this->assertDatabaseHas('startups', ['id' => $startup->id, 'name' => $startup->name]);
    }

    public function test_owner_can_view_the_edit_form_with_asking_price_in_dollars(): void
    {
        $user = User::factory()->create();
        $startup = Startup::factory()->forSale(250_000_00)->create([
            'user_id' => $user->id,
            'name' => 'Editable',
            'category' => 'saas',
        ]);

        $this->actingAs($user)->get(route('startups.edit', $startup))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $p) => $p
                ->component('startups/Edit')
                ->where('startup.name', 'Editable')
                ->where('startup.asking_price_usd', 250000) // cents -> dollars
                ->where('startup.has_connections', false)
                ->has('categories')
                ->has('countries')
                ->has('currencies'));
    }

    public function test_update_stores_asking_price_as_cents(): void
    {
        $user = User::factory()->create();
        $startup = Startup::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->put(route('startups.update', $startup), [
            'name' => 'Priced',
            'category' => 'saas',
            'country' => 'NG',
            'is_for_sale' => true,
            'asking_price_usd' => 1500, // dollars
        ])->assertRedirect(route('dashboard'));

        $this->assertSame(150_000, $startup->fresh()->asking_price_usd); // cents
    }

    public function test_owner_can_delete_their_startup(): void
    {
        $user = User::factory()->create();
        $startup = Startup::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->delete(route('startups.destroy', $startup))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('startups', ['id' => $startup->id]);
    }
}
