<?php

namespace Tests\Unit;

use App\Services\Mrr\CurrencyConverter;
use Tests\TestCase;

class CurrencyConverterTest extends TestCase
{
    public function test_usd_cents_are_unchanged(): void
    {
        $this->assertSame(10_000, CurrencyConverter::toUsdCents(10_000, 'USD'));
    }

    public function test_naira_is_converted_to_usd_cents(): void
    {
        // ₦5,000/mo = 500,000 kobo; at 0.00063 USD/₦ that is $3.15 = 315 cents.
        $this->assertSame(315, CurrencyConverter::toUsdCents(500_000, 'NGN'));
    }

    public function test_conversion_is_case_insensitive(): void
    {
        $this->assertSame(315, CurrencyConverter::toUsdCents(500_000, 'ngn'));
    }

    public function test_unknown_currency_falls_back_to_one_to_one(): void
    {
        $this->assertSame(1_234, CurrencyConverter::toUsdCents(1_234, 'JPY'));
    }

    public function test_rate_reads_from_config(): void
    {
        $this->assertSame(1.0, CurrencyConverter::rate('USD'));
        $this->assertSame(0.00063, CurrencyConverter::rate('NGN'));
        $this->assertSame(1.0, CurrencyConverter::rate('JPY'));
    }

    public function test_is_supported_reflects_configured_currencies(): void
    {
        $this->assertTrue(CurrencyConverter::isSupported('NGN'));
        $this->assertTrue(CurrencyConverter::isSupported('kes'));
        $this->assertFalse(CurrencyConverter::isSupported('JPY'));
    }
}
