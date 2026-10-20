<?php

namespace App\Services\Mrr;

use App\Services\Mrr\Contracts\ProviderConnector;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Throwable;

class FlutterwaveConnector implements ProviderConnector
{
    public function provider(): string
    {
        return 'flutterwave';
    }

    /**
     * Sum successful transaction volume over the trailing 30 days from the v3
     * /transactions endpoint (amounts in major units), paginating as needed.
     */
    public function fetch(string $secret): MrrResult
    {
        $base = (string) config('mrr.providers.flutterwave.base');
        $from = Carbon::now()->subDays(30)->toDateString();
        $to = Carbon::now()->toDateString();

        /** @var array<string, int> $byCurrency */
        $byCurrency = [];
        $count = 0;
        $page = 1;

        do {
            $body = $this->get($base.'/transactions', $secret, [
                'from' => $from,
                'to' => $to,
                'status' => 'successful',
                'page' => $page,
            ]);

            $rows = $body['data'] ?? [];

            foreach ($rows as $tx) {
                if (strtolower((string) ($tx['status'] ?? '')) !== 'successful') {
                    continue;
                }

                $currency = strtoupper((string) ($tx['currency'] ?? 'NGN'));
                // Flutterwave reports amounts in major units.
                $amountMinor = (int) round(((float) ($tx['amount'] ?? 0)) * 100);

                if ($amountMinor <= 0) {
                    continue;
                }

                $byCurrency[$currency] = ($byCurrency[$currency] ?? 0) + $amountMinor;
                $count++;
            }

            $totalPages = (int) ($body['meta']['page_info']['total_pages'] ?? 1);
            $page++;
        } while ($page <= $totalPages && $rows !== []);

        $result = new MrrResult;

        foreach ($byCurrency as $currency => $minor) {
            $result->add(CurrencyConverter::toUsdCents($minor, $currency), $currency, $minor, 0);
        }

        $result->activeSubscriptions = $count;

        return $result;
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
