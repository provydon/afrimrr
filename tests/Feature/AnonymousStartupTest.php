<?php

namespace Tests\Feature;

use App\Models\Startup;
use App\Models\User;
use App\Support\StartupPresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AnonymousStartupTest extends TestCase
{
    use RefreshDatabase;

    public function test_presenter_redacts_identity(): void
    {
        $startup = Startup::factory()->anonymous()->verified()->create([
            'name' => 'Secret Co',
            'tagline' => 'hush',
            'logo_url' => 'https://example.com/logo.png',
            'mrr_usd' => 10_000_00,
        ]);

        $card = StartupPresenter::card($startup);

        $this->assertTrue($card['is_anonymous']);
        $this->assertSame('Anonymous Startup', $card['name']);
        $this->assertNull($card['slug']);
        $this->assertNull($card['tagline']);
        $this->assertNull($card['logo_url']);
        // MRR + ranking data is kept.
        $this->assertSame(10_000_00, $card['mrr_usd']);
        // The real name must not appear anywhere in the payload.
        $this->assertStringNotContainsString('Secret Co', json_encode($card));
    }

    public function test_anonymous_startup_is_ranked_on_the_leaderboard_but_redacted(): void
    {
        Startup::factory()->anonymous()->create(['name' => 'Hidden', 'mrr_usd' => 50_000_00]);
        Startup::factory()->create(['name' => 'Public Co', 'mrr_usd' => 10_000_00]);

        $res = $this->get('/leaderboard');
        $res->assertOk();
        $res->assertInertia(fn (Assert $p) => $p
            ->where('startups.0.is_anonymous', true)
            ->where('startups.0.name', 'Anonymous Startup')
            ->where('startups.0.slug', null)
            ->where('startups.1.name', 'Public Co'));
        $this->assertStringNotContainsString('Hidden', $res->getContent());
    }

    public function test_anonymous_profile_page_is_not_found(): void
    {
        $startup = Startup::factory()->anonymous()->create();

        $this->get('/startups/'.$startup->slug)->assertNotFound();
    }

    public function test_api_includes_anonymous_in_index_but_hides_the_profile(): void
    {
        $startup = Startup::factory()->anonymous()->create(['name' => 'Hidden', 'mrr_usd' => 10_000_00]);

        $this->getJson('/api/startups')
            ->assertOk()
            ->assertJsonPath('data.0.is_anonymous', true)
            ->assertJsonPath('data.0.name', 'Anonymous Startup');

        $this->getJson('/api/startups/'.$startup->slug)->assertNotFound();
    }

    public function test_mcp_hides_anonymous_profile_but_lists_it_redacted(): void
    {
        $startup = Startup::factory()->anonymous()->create(['name' => 'Hidden', 'mrr_usd' => 10_000_00]);

        $get = $this->postJson('/mcp', [
            'jsonrpc' => '2.0', 'id' => 1, 'method' => 'tools/call',
            'params' => ['name' => 'get_startup', 'arguments' => ['slug' => $startup->slug]],
        ]);
        $this->assertSame('not found', json_decode($get->json('result.content.0.text'), true)['error']);

        $board = $this->postJson('/mcp', [
            'jsonrpc' => '2.0', 'id' => 2, 'method' => 'tools/call',
            'params' => ['name' => 'leaderboard', 'arguments' => []],
        ]);
        $payload = json_decode($board->json('result.content.0.text'), true);
        $this->assertSame('Anonymous Startup', $payload['startups'][0]['name']);
    }

    public function test_sitemap_excludes_anonymous_startups(): void
    {
        $anon = Startup::factory()->anonymous()->create();
        $public = Startup::factory()->create();

        $body = $this->get('/sitemap.xml')->getContent();

        $this->assertStringContainsString('/startups/'.$public->slug, $body);
        $this->assertStringNotContainsString('/startups/'.$anon->slug, $body);
    }

    public function test_owner_can_mark_a_startup_anonymous(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('startups.store'), [
            'name' => 'Stealth',
            'category' => 'fintech',
            'country' => 'NG',
            'is_public' => true,
            'is_anonymous' => true,
        ]);

        $this->assertDatabaseHas('startups', ['name' => 'Stealth', 'is_anonymous' => true]);
    }
}
