<?php

namespace Tests\Unit;

use App\Support\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_known_currency_symbols(): void
    {
        $this->assertSame('$', Money::symbol('USD'));
        $this->assertSame('₦', Money::symbol('NGN'));
        $this->assertSame('KSh', Money::symbol('kes'));
    }

    public function test_unknown_currency_symbol_falls_back_to_code(): void
    {
        $this->assertSame('JPY ', Money::symbol('jpy'));
    }

    public function test_format_shows_cents_only_for_small_fractional_amounts(): void
    {
        $this->assertSame('$3.15', Money::format(315, 'USD'));   // < 100, fractional
        $this->assertSame('$100', Money::format(10_000, 'USD')); // >= 100, no decimals
        $this->assertSame('$5,000', Money::format(500_000, 'USD'));
        $this->assertSame('₦5,000', Money::format(500_000, 'NGN'));
    }

    public function test_format_zero(): void
    {
        $this->assertSame('$0', Money::format(0, 'USD'));
    }

    public function test_usd_helper(): void
    {
        $this->assertSame('$3.15', Money::usd(315));
    }

    public function test_compact_usd_thousands_and_millions(): void
    {
        $this->assertSame('$315', Money::compactUsd(31_500));     // $315
        $this->assertSame('$12.5K', Money::compactUsd(1_250_000)); // $12,500
        $this->assertSame('$3K', Money::compactUsd(300_000));      // $3,000 -> trimmed
        $this->assertSame('$3.2M', Money::compactUsd(320_000_000)); // $3,200,000
        $this->assertSame('$5M', Money::compactUsd(500_000_000));   // $5,000,000 -> trimmed
    }

    public function test_compact_usd_small_values(): void
    {
        $this->assertSame('$0', Money::compactUsd(0));
        $this->assertSame('$3', Money::compactUsd(315)); // $3.15 -> $3
    }
}
