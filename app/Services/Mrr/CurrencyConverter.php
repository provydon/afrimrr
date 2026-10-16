<?php

namespace App\Services\Mrr;

/**
 * Converts local minor-unit amounts to USD cents using configured FX rates.
 *
 * Assumes 2-decimal currencies (true for all currencies we support), so a
 * minor-unit -> minor-unit conversion uses the same multiplier as major units.
 */
class CurrencyConverter
{
    public static function toUsdCents(int $localMinor, string $currency): int
    {
        $rate = self::rate($currency);

        return (int) round($localMinor * $rate);
    }

    /**
     * USD per 1 unit of the given currency. Unknown currencies fall back to 1.0.
     */
    public static function rate(string $currency): float
    {
        return (float) config('mrr.fx.'.strtoupper($currency), 1.0);
    }

    public static function isSupported(string $currency): bool
    {
        return config('mrr.fx.'.strtoupper($currency)) !== null;
    }
}
