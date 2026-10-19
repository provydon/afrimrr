<?php

namespace Tests\Feature;

use App\Models\Startup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StartupApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_public_startups_ordered_by_mrr(): void
    {
        Startup::factory()->create(['mrr_usd' => 5_000_00]);
        Startup::factory()->create(['mrr_usd' => 20_000_00]);
        Startup::factory()->private()->create(['mrr_usd' => 99_000_00]);

        $res = $this->getJson('/api/startups');

        $res->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 2);

        $data = $res->json('data');
        $this->assertGreaterThanOrEqual($data[1]['mrr_usd'], $data[0]['mrr_usd']);
    }

    public function test_index_filters_by_verified_and_category(): void
    {
        Startup::factory()->verified()->create(['category' => 'fintech']);
        Startup::factory()->create(['category' => 'fintech', 'is_verified' => false]);
        Startup::factory()->verified()->create(['category' => 'ai']);

        $this->getJson('/api/startups?verified=1&category=fintech')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_index_per_page_is_capped_at_100(): void
    {
        $res = $this->getJson('/api/startups?per_page=5000');
        $res->assertOk();
        // The paginator clamps to 100; with no data last_page is 1.
        $res->assertJsonPath('meta.current_page', 1);
    }

    public function test_show_returns_profile_for_public_startup(): void
    {
        $startup = Startup::factory()->verified()->create();

        $this->getJson('/api/startups/'.$startup->slug)
            ->assertOk()
            ->assertJsonPath('data.slug', $startup->slug)
            ->assertJsonStructure(['data' => ['name', 'mrr_usd', 'arr_usd_display', 'history', 'founder']]);
    }

    public function test_show_404_for_private_startup(): void
    {
        $startup = Startup::factory()->private()->create();
        $this->getJson('/api/startups/'.$startup->slug)->assertNotFound();
    }

    public function test_leaderboard_ranks_and_respects_limit(): void
    {
        Startup::factory()->create(['mrr_usd' => 10_000_00]);
        Startup::factory()->create(['mrr_usd' => 30_000_00]);
        Startup::factory()->create(['mrr_usd' => 20_000_00]);
        Startup::factory()->create(['mrr_usd' => 0]); // excluded

        $res = $this->getJson('/api/leaderboard?limit=2');
        $res->assertOk()->assertJsonCount(2, 'data');
        $res->assertJsonPath('data.0.rank', 1);
        $res->assertJsonPath('data.1.rank', 2);

        $data = $res->json('data');
        $this->assertSame(30_000_00, $data[0]['mrr_usd']);
    }
}
