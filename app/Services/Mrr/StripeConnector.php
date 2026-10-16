<?php

namespace App\Services\Mrr;

use App\Services\Mrr\Contracts\ProviderConnector;
use Illuminate\Support\Facades\Http;
use Throwable;

class StripeConnector implements ProviderConnector
{
    public function provider(): string
    {
        return 'stripe';
    }

    public function fetch(string $secret): MrrResult
    {
        $base = (string) config('mrr.providers.stripe.base');
        $result = new MrrResult;
        $startingAfter = null;

        do {
            $query = ['status' => 'active', 'limit' => 100];

            if ($startingAfter !== null) {
                $query['starting_after'] = $startingAfter;
            }

            try {
                $response = Http::withToken($secret)
                    ->acceptJson()
                    ->timeout(30)
                    ->get($base.'/subscriptions', $query);
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
            $subs = $body['data'] ?? [];

            foreach ($subs as $sub) {
                $items = $sub['items']['data'] ?? [];
                $subs_added = false;

                foreach ($items as $item) {
                    $price = $item['price'] ?? [];
                    $recurring = $price['recurring'] ?? [];

                    $unitAmount = (int) ($price['unit_amount'] ?? 0);
                    $quantity = (int) ($item['quantity'] ?? 1);
                    $currency = strtoupper($price['currency'] ?? 'USD');
                    $interval = $recurring['interval'] ?? 'month';
                    $intervalCount = (int) ($recurring['interval_count'] ?? 1);

                    $lineMinor = $unitAmount * max(1, $quantity);
                    $monthlyMinor = IntervalNormalizer::toMonthlyMinor($lineMinor, $interval, $intervalCount);
                    $usdCents = CurrencyConverter::toUsdCents($monthlyMinor, $currency);

                    // Count the subscription once, not per line item.
                    $result->add($usdCents, $currency, $monthlyMinor, $subs_added ? 0 : 1);
                    $subs_added = true;
                }
            }

            $hasMore = (bool) ($body['has_more'] ?? false);
            $startingAfter = $subs !== [] ? end($subs)['id'] ?? null : null;
        } while ($hasMore && $startingAfter !== null);

        return $result;
    }
}
