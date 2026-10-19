<?php

namespace Tests\Feature;

use App\Models\Startup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StartupModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_unique_slug_appends_a_suffix_on_collision(): void
    {
        Startup::factory()->create(['slug' => 'acme']);

        $this->assertSame('acme-1', Startup::uniqueSlug('Acme'));

        Startup::factory()->create(['slug' => 'acme-1']);
        $this->assertSame('acme-2', Startup::uniqueSlug('Acme'));
    }

    public function test_unique_slug_falls_back_when_name_has_no_slug_characters(): void
    {
        $this->assertSame('startup', Startup::uniqueSlug('!!!'));
    }

    public function test_scope_public_excludes_private(): void
    {
        Startup::factory()->create();
        Startup::factory()->private()->create();

        $this->assertSame(1, Startup::public()->count());
    }

    public function test_scope_verified_and_real(): void
    {
        Startup::factory()->verified()->create();
        Startup::factory()->create(['is_verified' => false]);
        Startup::factory()->demo()->verified()->create();

        $this->assertSame(2, Startup::verified()->count());
        $this->assertSame(2, Startup::real()->count());
        $this->assertSame(1, Startup::real()->verified()->count());
    }

    public function test_category_and_country_labels(): void
    {
        $startup = Startup::factory()->make(['category' => 'fintech', 'country' => 'NG']);
        $this->assertSame('Fintech', $startup->categoryLabel());
        $this->assertSame('Nigeria', $startup->countryLabel());

        $unknown = Startup::factory()->make(['category' => 'nope', 'country' => 'ZZ']);
        $this->assertSame('Other', $unknown->categoryLabel());
        $this->assertSame('ZZ', $unknown->countryLabel());
    }

    public function test_revenue_multiple_is_asking_price_over_annual_run_rate(): void
    {
        // MRR $10,000/mo -> ARR $120,000; asking $360,000 -> 3.0x.
        $startup = Startup::factory()->forSale(360_000_00)->make(['mrr_usd' => 10_000_00]);

        $this->assertSame(3.0, $startup->revenueMultiple());
    }

    public function test_revenue_multiple_is_null_when_not_for_sale_or_no_mrr(): void
    {
        $notForSale = Startup::factory()->make(['is_for_sale' => false, 'mrr_usd' => 10_000_00]);
        $this->assertNull($notForSale->revenueMultiple());

        $noMrr = Startup::factory()->forSale()->make(['mrr_usd' => 0]);
        $this->assertNull($noMrr->revenueMultiple());
    }
}
