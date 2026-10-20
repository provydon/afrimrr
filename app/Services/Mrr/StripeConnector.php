<?php

namespace App\Services\Mrr;

use App\Services\Mrr\Contracts\ProviderConnector;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Throwable;

class StripeConnector implements ProviderConnector
{
    public function provider(): string
    {
        return 'stripe';
    }

    /**
     * Sum net successful charge volume (amount captured minus refunds) over the
     * trailing 30 days, paginating the /charges list via starting_after.
     */
    public function fetch(string $secret): MrrResult
    {
        $base = (string) config('mrr.providers.stripe.base');
        $since = Carbon::now()->subDays(30)->timestamp;

        /** @var array<string, int> $byCurrency */
        $byCurrency = [];
        $count = 0;
        $startingAfter = null;

        do {
            $query = ['limit' => 100, 'created' => ['gte' => $since]];

            if ($startingAfter !== null) {
                $query['starting_after'] = $startingAfter;
            }

            try {
                $response = Http::withToken($secret)
                    ->acceptJson()
                    ->timeout(30)
                    ->get($base.'/charges', $query);
            } catch (Throwable $e) {
                throw new ConnectorException('Could not reach Stripe: '.$e->getMessage());
            }

            if ($response->status() === 401) {
                throw new ConnectorException('Stripe rejected the API key (401).');
            }

            if (! $response->successful()) {
                throw new ConnectorException('Stripe error ('.$response->status().'): '.$response->body());
            }

            $body = $response->json();
            $charges = $body['data'] ?? [];

            foreach ($charges as $charge) {
                if (($charge['status'] ?? null) !== 'succeeded' || ! ($charge['paid'] ?? false)) {
                    continue;
                }

                // Stripe amounts are in minor units; net out any refunds.
                $net = (int) ($charge['amount'] ?? 0) - (int) ($charge['amount_refunded'] ?? 0);

                if ($net <= 0) {
                    continue;
                }

                $currency = strtoupper((string) ($charge['currency'] ?? 'USD'));
                $byCurrency[$currency] = ($byCurrency[$currency] ?? 0) + $net;
                $count++;
            }

            $hasMore = (bool) ($body['has_more'] ?? false);
            $startingAfter = $charges !== [] ? (end($charges)['id'] ?? null) : null;
        } while ($hasMore && $startingAfter !== null);

        $result = new MrrResult;

        foreach ($byCurrency as $currency => $minor) {
            $result->add(CurrencyConverter::toUsdCents($minor, $currency), $currency, $minor, 0);
        }

        $result->activeSubscriptions = $count;

        return $result;
    }
}
