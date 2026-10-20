<?php

namespace Tests\Feature;

use App\Services\Mrr\CurrencyConverter;
use App\Services\Mrr\FxRates;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FxRatesTest extends TestCase
{
    public function test_refresh_caches_live_usd_per_unit_rates(): void
    {
        Http::fake([
            'open.er-api.com/*' => Http::response([
                'result' => 'success',
                'rates' => ['USD' => 1, 'NGN' => 1_000, 'KES' => 100, 'JPY' => 150],
            ]),
        ]);

        $rates = app(FxRates::class)->refresh();

        // ₦1,000 per USD -> 0.001 USD per ₦.
        $this->assertEqualsWithDelta(0.001, $rates['NGN'], 1e-9);
        $this->assertSame(1.0, $rates['USD']);
        // JPY isn't a supported/ranked currency, so it's not stored.
        $this->assertArrayNotHasKey('JPY', $rates);
        $this->assertIsArray(Cache::get(FxRates::CACHE_KEY));
    }

    public function test_converter_uses_live_rates_when_cached(): void
    {
        Cache::put(FxRates::CACHE_KEY, ['USD' => 1.0, 'NGN' => 0.001], now()->addHour());

        $this->assertSame(0.001, CurrencyConverter::rate('NGN'));
        // 500,000 kobo (₦5,000) at 0.001 -> $5.00 = 500 cents.
        $this->assertSame(500, CurrencyConverter::toUsdCents(500_000, 'NGN'));
    }

    public function test_converter_falls_back_to_config_when_no_live_rate(): void
    {
        Cache::forget(FxRates::CACHE_KEY);

        // Config placeholder for NGN is 0.00063.
        $this->assertSame(0.00063, CurrencyConverter::rate('NGN'));
        $this->assertSame(315, CurrencyConverter::toUsdCents(500_000, 'NGN'));
    }

    public function test_command_reports_failure_without_throwing(): void
    {
        Http::fake(['open.er-api.com/*' => Http::response([], 500)]);

        $this->artisan('fx:refresh')->assertFailed();
        $this->assertNull(Cache::get(FxRates::CACHE_KEY));
    }

    public function test_command_succeeds_and_caches_on_good_response(): void
    {
        Http::fake([
            'open.er-api.com/*' => Http::response([
                'result' => 'success',
                'rates' => ['USD' => 1, 'NGN' => 1_500],
            ]),
        ]);

        $this->artisan('fx:refresh')->assertSuccessful();
        $this->assertEqualsWithDelta(1 / 1_500, Cache::get(FxRates::CACHE_KEY)['NGN'], 1e-9);
    }
}
