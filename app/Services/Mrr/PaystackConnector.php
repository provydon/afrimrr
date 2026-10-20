<?php

namespace App\Services\Mrr;

use App\Services\Mrr\Contracts\ProviderConnector;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Throwable;

class PaystackConnector implements ProviderConnector
{
    public function provider(): string
    {
        return 'paystack';
    }

    /**
     * Sum successful transaction volume over the trailing 30 days. Paystack's
     * /transaction/totals endpoint returns the windowed volume per currency in
     * one call (minor units), so no pagination is needed.
     */
    public function fetch(string $secret): MrrResult
    {
        $base = (string) config('mrr.providers.paystack.base');

        try {
            $response = Http::withToken($secret)
                ->acceptJson()
                ->timeout(30)
                ->get($base.'/transaction/totals', [
                    'from' => Carbon::now()->subDays(30)->toIso8601String(),
                    'to' => Carbon::now()->toIso8601String(),
                ]);
        } catch (Throwable $e) {
            throw new ConnectorException('Could not reach Paystack: '.$e->getMessage());
        }

        if ($response->status() === 401) {
            throw new ConnectorException('Paystack rejected the API key (401).');
        }

        if (! $response->successful()) {
            throw new ConnectorException('Paystack error ('.$response->status().'): '.$response->body());
        }

        $data = (array) ($response->json('data') ?? []);
        $result = new MrrResult;

        foreach ((array) ($data['total_volume_by_currency'] ?? []) as $row) {
            $currency = strtoupper((string) ($row['currency'] ?? 'NGN'));
            // Paystack amounts are in minor units (kobo/pesewas/cents).
            $minor = (int) ($row['amount'] ?? 0);

            if ($minor <= 0) {
                continue;
            }

            $usdCents = CurrencyConverter::toUsdCents($minor, $currency);
            $result->add($usdCents, $currency, $minor, 0);
        }

        $result->activeSubscriptions = (int) ($data['total_transactions'] ?? 0);

        return $result;
    }
}
