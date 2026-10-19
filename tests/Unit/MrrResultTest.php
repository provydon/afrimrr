<?php

namespace Tests\Unit;

use App\Services\Mrr\MrrResult;
use PHPUnit\Framework\TestCase;

class MrrResultTest extends TestCase
{
    public function test_empty_result_defaults_to_usd(): void
    {
        $result = new MrrResult;

        $this->assertSame(0, $result->usdCents);
        $this->assertSame(0, $result->activeSubscriptions);
        $this->assertSame('USD', $result->displayCurrency());
        $this->assertSame(0, $result->displayLocalMinor());
    }

    public function test_add_accumulates_totals_and_subscriptions(): void
    {
        $result = new MrrResult;
        $result->add(315, 'NGN', 500_000);
        $result->add(315, 'NGN', 500_000);

        $this->assertSame(630, $result->usdCents);
        $this->assertSame(2, $result->activeSubscriptions);
        $this->assertSame(1_000_000, $result->displayLocalMinor());
    }

    public function test_add_can_skip_counting_a_subscription(): void
    {
        $result = new MrrResult;
        $result->add(100, 'USD', 100, 1);
        $result->add(100, 'USD', 100, 0); // second line item of same sub

        $this->assertSame(1, $result->activeSubscriptions);
        $this->assertSame(200, $result->usdCents);
    }

    public function test_display_currency_is_the_largest_local_share(): void
    {
        $result = new MrrResult;
        $result->add(10, 'KES', 1_000);
        $result->add(500, 'NGN', 9_000_000);

        $this->assertSame('NGN', $result->displayCurrency());
        $this->assertSame(9_000_000, $result->displayLocalMinor());
    }
}
