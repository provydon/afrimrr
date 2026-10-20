<?php

namespace App\Services\Mrr;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

/**
 * Fetches live USD exchange rates and caches them so MRR is converted at the
 * actual market rate. The config('mrr.fx') table is only a fallback for when
 * no live rates have been fetched yet (see CurrencyConverter).
 */
class FxRates
{
    public const CACHE_KEY = 'mrr.fx_live';

    /**
     * Fetch current rates, cache them, and return the USD-per-unit map.
     *
     * @return array<string, float> USD per 1 unit of each supported currency
     */
    public function refresh(): array
    {
        $rates = $this->fetch();

        Cache::put(self::CACHE_KEY, $rates, now()->addHours(25));

        return $rates;
    }

    /**
     * @return array<string, float>
     */
    private function fetch(): array
    {
        $source = (string) config('mrr.fx_source');

        try {
            $response = Http::acceptJson()->timeout(20)->get($source);
        } catch (Throwable $e) {
            throw new RuntimeException('Could not reach the FX source: '.$e->getMessage());
        }

        if (! $response->successful() || $response->json('result') !== 'success') {
            throw new RuntimeException('FX source returned an unexpected response ('.$response->status().').');
        }

        /** @var array<string, float|int> $perUsd units of each currency per 1 USD */
        $perUsd = (array) $response->json('rates', []);

        if ($perUsd === []) {
            throw new RuntimeException('FX source returned no rates.');
        }

        $map = ['USD' => 1.0];

        // Only keep the currencies we actually display/rank in.
        foreach (array_keys((array) config('mrr.fx')) as $currency) {
            $currency = strtoupper((string) $currency);
            $perUnit = $perUsd[$currency] ?? null;

            if ($currency === 'USD' || ! is_numeric($perUnit) || (float) $perUnit <= 0) {
                continue;
            }

            // Rate is units-per-USD; we store USD-per-unit.
            $map[$currency] = 1 / (float) $perUnit;
        }

        return $map;
    }
}
