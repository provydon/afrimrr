<?php

namespace App\Services\Mrr;

/**
 * Converts a recurring charge of any billing interval into a monthly amount.
 */
class IntervalNormalizer
{
    /** Average days per month used to annualize sub-monthly intervals. */
    private const DAYS_PER_MONTH = 30.4;

    /**
     * Number of months one billing period spans.
     */
    public static function periodInMonths(string $interval, int $count = 1): float
    {
        $count = max(1, $count);

        $base = match (strtolower(trim($interval))) {
            'hourly', 'hour' => 1 / (self::DAYS_PER_MONTH * 24),
            'daily', 'day' => 1 / self::DAYS_PER_MONTH,
            'weekly', 'week' => 7 / self::DAYS_PER_MONTH,
            'biweekly' => 14 / self::DAYS_PER_MONTH,
            'monthly', 'month' => 1.0,
            'quarterly', 'quarter' => 3.0,
            'biannually', 'bi-annually', 'biannual', 'semiannually' => 6.0,
            'annually', 'annual', 'yearly', 'year' => 12.0,
            default => 1.0,
        };

        return $base * $count;
    }

    /**
     * Normalize a per-period amount (in minor units) to a monthly amount (minor units).
     */
    public static function toMonthlyMinor(int $amountMinor, string $interval, int $count = 1): int
    {
        $months = self::periodInMonths($interval, $count);

        if ($months <= 0) {
            return $amountMinor;
        }

        return (int) round($amountMinor / $months);
    }
}
