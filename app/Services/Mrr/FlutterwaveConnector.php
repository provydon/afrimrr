<?php

namespace App\Services\Mrr;

use App\Services\Mrr\Contracts\ProviderConnector;
use Illuminate\Support\Facades\Http;
use Throwable;

class FlutterwaveConnector implements ProviderConnector
{
    public function provider(): string
    {
        return 'flutterwave';
    }

    public function fetch(string $secret): MrrResult
    {
        $base = (string) config('mrr.providers.flutterwave.base');
        $plans = $this->loadPlans($base, $secret);

        $result = new MrrResult;
        $page = 1;

        do {
            $body = $this->get($base.'/subscriptions', $secret, [
                'status' => 'active',
                'page' => $page,
            ]);

            $rows = $body['data'] ?? [];

            foreach ($rows as $sub) {
                if (strtolower((string) ($sub['status'] ?? '')) !== 'active') {
                    continue;
                }

                $planId = $sub['plan'] ?? null;
                $plan = $planId !== null ? ($plans[$planId] ?? null) : null;

                $interval = $plan['interval'] ?? 'monthly';
                $currency = strtoupper($plan['currency'] ?? $sub['currency'] ?? 'NGN');
                // Flutterwave reports amounts in major units.
                $amountMajor = (float) ($plan['amount'] ?? $sub['amount'] ?? 0);
                $amountMinor = (int) round($amountMajor * 100);

                $monthlyMinor = IntervalNormalizer::toMonthlyMinor($amountMinor, $interval);
                $usdCents = CurrencyConverter::toUsdCents($monthlyMinor, $currency);

                $result->add($usdCents, $currency, $monthlyMinor);
            }

            $totalPages = (int) ($body['meta']['page_info']['total_pages'] ?? 1);
            $page++;
        } while ($page <= $totalPages && $rows !== []);

        return $result;
    }

    /**
     * @return array<int|string, array<string, mixed>> keyed by plan id
     */
    private function loadPlans(string $base, string $secret): array
    {
        $plans = [];
        $page = 1;

        do {
            $body = $this->get($base.'/payment-plans', $secret, ['page' => $page]);
            $rows = $body['data'] ?? [];

            foreach ($rows as $plan) {
                if (isset($plan['id'])) {
                    $plans[$plan['id']] = $plan;
                }
            }

            $totalPages = (int) ($body['meta']['page_info']['total_pages'] ?? 1);
            $page++;
        } while ($page <= $totalPages && $rows !== []);

        return $plans;
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    private function get(string $url, string $secret, array $query): array
    {
        try {
            $response = Http::withToken($secret)
                ->acceptJson()
                ->timeout(30)
                ->get($url, $query);
        } catch (Throwable $e) {
            throw new ConnectorException('Could not reach Flutterwave: '.$e->getMessage());
        }

        if ($response->status() === 401) {
            throw new ConnectorException('Flutterwave rejected the API key (401).');
        }

        if (! $response->successful()) {
            throw new ConnectorException('Flutterwave error ('.$response->status().'): '.$response->body());
        }

        return (array) $response->json();
    }
}
