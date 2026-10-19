<?php

namespace Tests\Unit;

use App\Services\Mrr\IntervalNormalizer;
use PHPUnit\Framework\TestCase;

class IntervalNormalizerTest extends TestCase
{
    public function test_period_in_months_for_named_intervals(): void
    {
        $this->assertSame(1.0, IntervalNormalizer::periodInMonths('monthly'));
        $this->assertSame(1.0, IntervalNormalizer::periodInMonths('month'));
        $this->assertSame(3.0, IntervalNormalizer::periodInMonths('quarterly'));
        $this->assertSame(6.0, IntervalNormalizer::periodInMonths('biannually'));
        $this->assertSame(12.0, IntervalNormalizer::periodInMonths('annually'));
        $this->assertSame(12.0, IntervalNormalizer::periodInMonths('yearly'));
    }

    public function test_period_in_months_is_case_and_whitespace_insensitive(): void
    {
        $this->assertSame(12.0, IntervalNormalizer::periodInMonths('  ANNUAL  '));
        $this->assertSame(3.0, IntervalNormalizer::periodInMonths('Quarter'));
    }

    public function test_unknown_interval_defaults_to_monthly(): void
    {
        $this->assertSame(1.0, IntervalNormalizer::periodInMonths('fortnightly-ish'));
    }

    public function test_sub_monthly_intervals_annualize_from_days(): void
    {
        $this->assertEqualsWithDelta(1 / 30.4, IntervalNormalizer::periodInMonths('daily'), 1e-9);
        $this->assertEqualsWithDelta(7 / 30.4, IntervalNormalizer::periodInMonths('weekly'), 1e-9);
        $this->assertEqualsWithDelta(14 / 30.4, IntervalNormalizer::periodInMonths('biweekly'), 1e-9);
    }

    public function test_interval_count_multiplies_the_period(): void
    {
        // Billed every 3 months.
        $this->assertSame(3.0, IntervalNormalizer::periodInMonths('month', 3));
        // Billed every 2 years.
        $this->assertSame(24.0, IntervalNormalizer::periodInMonths('year', 2));
    }

    public function test_count_is_floored_at_one(): void
    {
        $this->assertSame(1.0, IntervalNormalizer::periodInMonths('month', 0));
        $this->assertSame(1.0, IntervalNormalizer::periodInMonths('month', -5));
    }

    public function test_monthly_amount_is_unchanged(): void
    {
        $this->assertSame(500_000, IntervalNormalizer::toMonthlyMinor(500_000, 'monthly'));
    }

    public function test_annual_amount_is_divided_by_twelve(): void
    {
        // ₦120,000/year -> ₦10,000/month (in kobo).
        $this->assertSame(1_000_000, IntervalNormalizer::toMonthlyMinor(12_000_000, 'annually'));
    }

    public function test_quarterly_amount_is_divided_by_three(): void
    {
        $this->assertSame(1_000, IntervalNormalizer::toMonthlyMinor(3_000, 'quarterly'));
    }

    public function test_interval_count_applies_to_amount(): void
    {
        // $300 billed once every 3 months -> $100/month.
        $this->assertSame(10_000, IntervalNormalizer::toMonthlyMinor(30_000, 'month', 3));
    }
}
