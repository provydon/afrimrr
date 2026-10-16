<?php

namespace App\Services\Mrr;

use App\Services\Mrr\Contracts\ProviderConnector;
use Illuminate\Support\Facades\Http;
use Throwable;

class PaystackConnector implements ProviderConnector
{
    public function provider(): string
    {
        return 'paystack';
    }

    public function fetch(string $secret): MrrResult
    {
        $base = (string) config('mrr.providers.paystack.base');
        $result = new MrrResult;
        $page = 1;

        do {
            try {
                $response = Http::withToken($secret)
                    ->acceptJson()
                    ->timeout(30)
                    ->get($base.'/subscription', [
                        'perPage' => 100,
                        'page' => $page,
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

            $body = $response->json();
            $rows = $body['data'] ?? [];

            foreach ($rows as $sub) {
                if (($sub['status'] ?? null) !== 'active') {
                    continue;
                }

                $plan = $sub['plan'] ?? [];
                $interval = $plan['interval'] ?? 'monthly';
                $currency = strtoupper($plan['currency'] ?? $sub['currency'] ?? 'NGN');
                // Paystack amounts are in minor units (kobo/pesewas/cents).
                $amountMinor = (int) ($plan['amount'] ?? $sub['amount'] ?? 0);

                $monthlyMinor = IntervalNormalizer::toMonthlyMinor($amountMinor, $interval);
                $usdCents = CurrencyConverter::toUsdCents($monthlyMinor, $currency);

                $result->add($usdCents, $currency, $monthlyMinor);
            }

            $pageCount = (int) ($body['meta']['pageCount'] ?? 1);
            $page++;
        } while ($page <= $pageCount && $rows !== []);

        return $result;
    }
}
