<?php

namespace Tests\Feature;

use App\Models\MrrSnapshot;
use App\Models\Startup;
use App\Support\StartupPresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StartupPresenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_card_exposes_display_and_marketplace_fields(): void
    {
        $startup = Startup::factory()->forSale(360_000_00)->verified()->create([
            'mrr_usd' => 10_000_00,
            'display_currency' => 'NGN',
            'mrr_local' => 1_500_000_000,
        ]);

        $card = StartupPresenter::card($startup);

        $this->assertSame('$10,000', $card['mrr_usd_display']);
        $this->assertSame('$10K', $card['mrr_usd_compact']);
        $this->assertSame('$360,000', $card['asking_price_display']);
        $this->assertSame(3.0, $card['revenue_multiple']); // 360k / (10k*12)
        $this->assertSame($startup->owner->publicUsername(), $card['founder']['username']);
    }

    public function test_profile_includes_arr_and_ordered_history(): void
    {
        $startup = Startup::factory()->create(['mrr_usd' => 2_000_00]);
        MrrSnapshot::factory()->create(['startup_id' => $startup->id, 'mrr_usd' => 100, 'captured_on' => '2026-01-02']);
        MrrSnapshot::factory()->create(['startup_id' => $startup->id, 'mrr_usd' => 200, 'captured_on' => '2026-01-01']);

        $profile = StartupPresenter::profile($startup);

        $this->assertSame('$24,000', $profile['arr_usd_display']); // mrr * 12
        $this->assertCount(2, $profile['history']);
        $this->assertSame('2026-01-01', $profile['history'][0]['date']); // chronological
        $this->assertSame('2026-01-02', $profile['history'][1]['date']);
    }
}
