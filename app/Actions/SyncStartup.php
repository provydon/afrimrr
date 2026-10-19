<?php

namespace App\Actions;

use App\Jobs\AnnounceStartup;
use App\Models\Startup;
use App\Services\Mrr\ConnectorException;
use App\Services\Mrr\ConnectorFactory;
use App\Services\Mrr\MrrResult;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SyncStartup
{
    /**
     * Pull MRR from every active connection on the startup, aggregate, persist,
     * snapshot it, and recompute month-over-month growth.
     *
     * @return array{ok: bool, mrr_usd?: int, errors: array<string, string>}
     */
    public function handle(Startup $startup): array
    {
        $connections = $startup->connections()->get();

        if ($connections->isEmpty()) {
            return ['ok' => false, 'errors' => ['_' => 'No payment providers connected.']];
        }

        $total = new MrrResult;
        $errors = [];
        $verifiedSources = [];

        foreach ($connections as $connection) {
            try {
                $connector = ConnectorFactory::make($connection->provider);
                $result = $connector->fetch($connection->credentials);

                $total->usdCents += $result->usdCents;
                $total->activeSubscriptions += $result->activeSubscriptions;
                foreach ($result->localByCurrency as $currency => $minor) {
                    $total->localByCurrency[$currency] = ($total->localByCurrency[$currency] ?? 0) + $minor;
                }

                $connection->update([
                    'status' => 'active',
                    'last_error' => null,
                    'last_synced_at' => now(),
                ]);

                $verifiedSources[] = $connection->provider;
            } catch (ConnectorException $e) {
                $errors[$connection->provider] = $e->getMessage();
                $connection->update([
                    'status' => 'error',
                    'last_error' => $e->getMessage(),
                ]);
                Log::warning('MRR sync failed', [
                    'startup' => $startup->id,
                    'provider' => $connection->provider,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Nothing synced successfully — leave existing numbers untouched.
        if ($verifiedSources === []) {
            return ['ok' => false, 'errors' => $errors];
        }

        $currency = $total->displayCurrency();
        $localMinor = $total->displayLocalMinor();

        $startup->forceFill([
            'mrr_usd' => $total->usdCents,
            'mrr_local' => $localMinor,
            'display_currency' => $currency,
            'active_subscriptions' => $total->activeSubscriptions,
            'is_verified' => true,
            'verification_source' => count($verifiedSources) === 1 ? $verifiedSources[0] : 'multiple',
            'last_synced_at' => now(),
        ]);

        $this->snapshot($startup, $total->usdCents, $localMinor, $currency, $total->activeSubscriptions);
        $startup->mom_growth = $this->monthOverMonthGrowth($startup);
        $startup->save();

        $this->maybeAnnounce($startup);

        return ['ok' => true, 'mrr_usd' => $total->usdCents, 'errors' => $errors];
    }

    /**
     * Queue the one-time "just joined" announcement once a startup has verified
     * MRR to show. The job re-checks and records announced_at, so repeat syncs
     * never post twice.
     */
    private function maybeAnnounce(Startup $startup): void
    {
        if (! config('social.enabled')) {
            return;
        }

        if ($startup->is_public && $startup->announced_at === null && $startup->mrr_usd > 0) {
            AnnounceStartup::dispatch($startup->id);
        }
    }

    private function snapshot(Startup $startup, int $usdCents, int $localMinor, string $currency, int $subs): void
    {
        $today = Carbon::today()->toDateString();

        $attributes = [
            'mrr_usd' => $usdCents,
            'mrr_local' => $localMinor,
            'display_currency' => $currency,
            'active_subscriptions' => $subs,
        ];

        // whereDate matches regardless of how the driver stores the time component
        // (SQLite keeps 00:00:00, Postgres truncates) so same-day syncs stay idempotent.
        $existing = $startup->snapshots()->whereDate('captured_on', $today)->first();

        if ($existing) {
            $existing->update($attributes);

            return;
        }

        $startup->snapshots()->create($attributes + ['captured_on' => $today]);
    }

    private function monthOverMonthGrowth(Startup $startup): ?float
    {
        $past = $startup->snapshots()
            ->where('captured_on', '<=', Carbon::today()->subDays(28)->toDateString())
            ->orderByDesc('captured_on')
            ->first();

        if (! $past || $past->mrr_usd <= 0) {
            return null;
        }

        return round((($startup->mrr_usd - $past->mrr_usd) / $past->mrr_usd) * 100, 2);
    }
}
