<?php

namespace App\Services\Mrr;

use Illuminate\Support\Facades\Cache;

/**
 * Converts local minor-unit amounts to USD cents using live FX rates (fetched
 * by FxRates and cached), falling back to the config('mrr.fx') placeholders
 * when no live rates have been fetched yet.
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
     * USD per 1 unit of the given currency. Prefers the live cached rate, then
     * the config placeholder, then 1.0 for unknown currencies.
     */
    public static function rate(string $currency): float
    {
        $currency = strtoupper($currency);

        $live = Cache::get(FxRates::CACHE_KEY);

        if (is_array($live) && isset($live[$currency]) && is_numeric($live[$currency])) {
            return (float) $live[$currency];
        }

        return (float) config('mrr.fx.'.$currency, 1.0);
    }

    public static function isSupported(string $currency): bool
    {
        return config('mrr.fx.'.strtoupper($currency)) !== null;
    }
}
