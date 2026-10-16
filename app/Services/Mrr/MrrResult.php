<?php

namespace App\Services\Mrr;

/**
 * Normalized recurring-revenue snapshot pulled from a provider.
 */
class MrrResult
{
    public function __construct(
        public int $usdCents = 0,
        public int $activeSubscriptions = 0,
        /** @var array<string, int> local minor units keyed by currency */
        public array $localByCurrency = [],
    ) {}

    public function add(int $usdCents, string $currency, int $localMinor, int $subs = 1): void
    {
        $this->usdCents += $usdCents;
        $this->activeSubscriptions += $subs;
        $this->localByCurrency[$currency] = ($this->localByCurrency[$currency] ?? 0) + $localMinor;
    }

    /**
     * The currency holding the largest share of local revenue — used for display.
     */
    public function displayCurrency(): string
    {
        if ($this->localByCurrency === []) {
            return 'USD';
        }

        arsort($this->localByCurrency);

        return (string) array_key_first($this->localByCurrency);
    }

    public function displayLocalMinor(): int
    {
        return $this->localByCurrency[$this->displayCurrency()] ?? 0;
    }
}
