<?php

namespace Tests\Feature;

use App\Models\Startup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Filtering and sorting behaviour of the directory, marketplace and leaderboard.
 */
class PublicListingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_directory_filters_by_category_and_country(): void
    {
        Startup::factory()->create(['category' => 'fintech', 'country' => 'NG']);
        Startup::factory()->create(['category' => 'fintech', 'country' => 'KE']);
        Startup::factory()->create(['category' => 'ai', 'country' => 'NG']);

        $this->get('/startups?category=fintech&country=NG')
            ->assertInertia(fn (Assert $p) => $p->has('startups.data', 1)
                ->where('filters.category', 'fintech')
                ->where('filters.country', 'NG'));
    }

    public function test_directory_sorts_by_recent_and_growth(): void
    {
        $old = Startup::factory()->create(['mrr_usd' => 9_000_00, 'mom_growth' => 1]);
        $old->forceFill(['created_at' => now()->subYear()])->save();
        $new = Startup::factory()->create(['mrr_usd' => 1_000_00, 'mom_growth' => 50]);

        $this->get('/startups?sort=recent')
            ->assertInertia(fn (Assert $p) => $p->where('startups.data.0.slug', $new->slug));

        $this->get('/startups?sort=growth')
            ->assertInertia(fn (Assert $p) => $p->where('startups.data.0.slug', $new->slug));
    }

    public function test_directory_excludes_private_startups(): void
    {
        Startup::factory()->create();
        Startup::factory()->private()->create();

        $this->get('/startups')
            ->assertInertia(fn (Assert $p) => $p->has('startups.data', 1));
    }

    public function test_marketplace_sorts_by_price_and_mrr(): void
    {
        $cheapHighMrr = Startup::factory()->forSale(10_000_00)->create(['mrr_usd' => 50_000_00]);
        $priceyLowMrr = Startup::factory()->forSale(900_000_00)->create(['mrr_usd' => 1_000_00]);

        $this->get('/marketplace?sort=price')
            ->assertInertia(fn (Assert $p) => $p->where('listings.0.slug', $priceyLowMrr->slug));

        $this->get('/marketplace?sort=mrr')
            ->assertInertia(fn (Assert $p) => $p->where('listings.0.slug', $cheapHighMrr->slug));
    }

    public function test_leaderboard_all_category_shows_everything(): void
    {
        Startup::factory()->create(['mrr_usd' => 5_000_00, 'category' => 'fintech']);
        Startup::factory()->create(['mrr_usd' => 3_000_00, 'category' => 'ai']);

        $this->get('/leaderboard?category=all')
            ->assertInertia(fn (Assert $p) => $p->has('startups', 2)->where('activeCategory', 'all'));
    }
}
