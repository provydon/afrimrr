<?php

namespace Tests\Feature;

use App\Models\Startup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_static_pages_load(): void
    {
        foreach (['/faq', '/about', '/terms', '/privacy', '/badge-store', '/developers'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_home_uses_live_non_demo_figures_only(): void
    {
        Startup::factory()->verified()->create(['mrr_usd' => 10_000_00, 'is_demo' => false, 'country' => 'NG']);
        Startup::factory()->verified()->create(['mrr_usd' => 5_000_00, 'is_demo' => false, 'country' => 'KE']);
        // Demo + private are excluded from the headline numbers.
        Startup::factory()->verified()->demo()->create(['mrr_usd' => 99_000_00, 'country' => 'ZA']);
        Startup::factory()->verified()->private()->create(['mrr_usd' => 99_000_00, 'country' => 'GH']);

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p
                ->component('Welcome')
                ->where('stats.verified_count', 2)
                ->where('stats.country_count', 2)
                ->has('topStartups', 2)
                ->where('topStartups.0.rank', 1));
    }

    public function test_leaderboard_ranks_public_revenue_bearing_startups(): void
    {
        $big = Startup::factory()->verified()->create(['mrr_usd' => 20_000_00, 'category' => 'fintech']);
        $small = Startup::factory()->verified()->create(['mrr_usd' => 5_000_00, 'category' => 'ai']);
        Startup::factory()->create(['mrr_usd' => 0]); // excluded (no revenue)
        Startup::factory()->private()->create(['mrr_usd' => 50_000_00]); // excluded (private)

        $this->get('/leaderboard')
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p
                ->component('public/Leaderboard')
                ->has('startups', 2)
                ->where('startups.0.slug', $big->slug)
                ->where('startups.0.rank', 1)
                ->where('startups.1.slug', $small->slug));
    }

    public function test_leaderboard_filters_by_category(): void
    {
        Startup::factory()->verified()->create(['mrr_usd' => 20_000_00, 'category' => 'fintech']);
        Startup::factory()->verified()->create(['mrr_usd' => 5_000_00, 'category' => 'ai']);

        $this->get('/leaderboard?category=fintech')
            ->assertInertia(fn (Assert $p) => $p->has('startups', 1)
                ->where('activeCategory', 'fintech'));
    }

    public function test_directory_search_and_sort(): void
    {
        Startup::factory()->create(['name' => 'Zebra Pay', 'mrr_usd' => 1_000_00]);
        Startup::factory()->create(['name' => 'Alpha Bank', 'mrr_usd' => 9_000_00]);

        $this->get('/startups?q=zebra')
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p
                ->component('public/Startups')
                ->has('startups.data', 1)
                ->where('startups.data.0.name', 'Zebra Pay'));
    }

    public function test_startup_profile_shows_public_startup_with_embed(): void
    {
        $startup = Startup::factory()->verified()->create();

        $this->get('/startups/'.$startup->slug)
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p
                ->component('public/StartupProfile')
                ->where('startup.slug', $startup->slug)
                ->has('badgeUrl')
                ->has('embedCode'));
    }

    public function test_private_startup_profile_is_not_found(): void
    {
        $startup = Startup::factory()->private()->create();

        $this->get('/startups/'.$startup->slug)->assertNotFound();
    }

    public function test_marketplace_lists_only_priced_for_sale_startups(): void
    {
        $listed = Startup::factory()->forSale(50_000_00)->create();
        Startup::factory()->create(['is_for_sale' => false]);
        Startup::factory()->create(['is_for_sale' => true, 'asking_price_usd' => null]);

        $this->get('/marketplace')
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p
                ->component('public/Marketplace')
                ->has('listings', 1)
                ->where('listings.0.slug', $listed->slug));
    }

    public function test_founder_profile_resolves_by_username_with_stats(): void
    {
        $user = User::factory()->create(['username' => 'ada', 'name' => 'Ada L', 'twitter' => 'ada']);
        Startup::factory()->verified()->create(['user_id' => $user->id, 'is_public' => true, 'mrr_usd' => 10_000_00]);
        Startup::factory()->create(['user_id' => $user->id, 'is_public' => true, 'is_verified' => false, 'mrr_usd' => 5_000_00]);
        // Private startup is excluded from the totals.
        Startup::factory()->private()->create(['user_id' => $user->id, 'mrr_usd' => 99_000_00]);

        $this->get('/founders/ada')
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p
                ->component('public/FounderProfile')
                ->where('founder.username', 'ada')
                ->where('founder.twitter_url', 'https://x.com/ada')
                ->has('startups', 2)
                ->where('stats.total_mrr_display', '$15,000')
                ->where('stats.arr_display', '$180,000')
                ->where('stats.total_startups', 2)
                ->where('stats.verified_startups', 1)
                ->where('stats.followers', null));

        $this->get('/founders/nobody')->assertNotFound();
    }

    public function test_badge_svg_renders_for_public_startup(): void
    {
        $verified = Startup::factory()->verified()->create(['mrr_usd' => 12_500_00]);

        $res = $this->get('/badge/'.$verified->slug.'.svg');
        $res->assertOk();
        $res->assertHeader('Content-Type', 'image/svg+xml');
        $this->assertStringContainsString('VERIFIED MRR', $res->getContent());
        $this->assertStringContainsString('$12.5K', $res->getContent());
    }

    public function test_badge_svg_shows_listed_for_unverified(): void
    {
        $unverified = Startup::factory()->create(['is_verified' => false, 'mrr_usd' => 1_000_00]);

        $res = $this->get('/badge/'.$unverified->slug.'.svg');
        $res->assertOk();
        $this->assertStringContainsString('LISTED MRR', $res->getContent());
    }

    public function test_badge_svg_404_for_private(): void
    {
        $startup = Startup::factory()->private()->create();
        $this->get('/badge/'.$startup->slug.'.svg')->assertNotFound();
    }

    public function test_sitemap_lists_pages_startups_and_founders(): void
    {
        $user = User::factory()->create(['username' => 'ada']);
        $startup = Startup::factory()->create(['user_id' => $user->id, 'is_public' => true]);

        $res = $this->get('/sitemap.xml');
        $res->assertOk();
        $res->assertHeader('Content-Type', 'application/xml');
        $body = $res->getContent();
        $this->assertStringContainsString('<loc>', $body);
        $this->assertStringContainsString('/startups/'.$startup->slug, $body);
        $this->assertStringContainsString('/founders/ada', $body);
    }
}
