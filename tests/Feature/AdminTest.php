<?php

namespace Tests\Feature;

use App\Models\Startup;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private array $adminRoutes = ['/admin', '/admin/subscribers', '/admin/startups'];

    public function test_guests_are_redirected_from_admin(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_non_admins_are_forbidden(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        foreach ($this->adminRoutes as $route) {
            $this->actingAs($user)->get($route)->assertForbidden();
        }
    }

    public function test_admin_overview_shows_platform_stats(): void
    {
        $admin = User::factory()->admin()->create();
        Subscriber::factory()->count(3)->create();
        Startup::factory()->verified()->create(['mrr_usd' => 10_000_00]);
        Startup::factory()->anonymous()->create();

        $this->actingAs($admin)->get('/admin')
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p
                ->component('admin/Overview')
                ->where('stats.subscribers', 3)
                ->where('stats.startups', 2)
                ->where('stats.verified', 1)
                ->where('stats.anonymous', 1));
    }

    public function test_admin_can_list_and_search_subscribers(): void
    {
        $admin = User::factory()->admin()->create();
        Subscriber::factory()->create(['email' => 'alice@example.com']);
        Subscriber::factory()->create(['email' => 'bob@example.com']);

        $this->actingAs($admin)->get('/admin/subscribers?q=alice')
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p
                ->component('admin/Subscribers')
                ->where('total', 2)
                ->has('subscribers.data', 1)
                ->where('subscribers.data.0.email', 'alice@example.com'));
    }

    public function test_admin_can_export_subscribers_as_csv(): void
    {
        $admin = User::factory()->admin()->create();
        Subscriber::factory()->create(['email' => 'csv@example.com', 'source' => 'popup']);

        $res = $this->actingAs($admin)->get('/admin/subscribers/export');

        $res->assertOk();
        $res->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $content = $res->streamedContent();
        $this->assertStringContainsString('email,source,subscribed_at', $content);
        $this->assertStringContainsString('csv@example.com', $content);
    }

    public function test_non_admin_cannot_export(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/admin/subscribers/export')->assertForbidden();
    }

    public function test_admin_can_toggle_startup_visibility(): void
    {
        $admin = User::factory()->admin()->create();
        $startup = Startup::factory()->create(['is_public' => true]);

        $this->actingAs($admin)->put('/admin/startups/'.$startup->id, ['is_public' => false])
            ->assertSessionHas('success');

        $this->assertFalse($startup->fresh()->is_public);
    }

    public function test_admin_can_delete_a_startup(): void
    {
        $admin = User::factory()->admin()->create();
        $startup = Startup::factory()->create();

        $this->actingAs($admin)->delete('/admin/startups/'.$startup->id)
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('startups', ['id' => $startup->id]);
    }

    public function test_non_admin_cannot_mutate_startups(): void
    {
        $user = User::factory()->create();
        $startup = Startup::factory()->create();

        $this->actingAs($user)->put('/admin/startups/'.$startup->id, ['is_public' => false])->assertForbidden();
        $this->actingAs($user)->delete('/admin/startups/'.$startup->id)->assertForbidden();
    }
}
