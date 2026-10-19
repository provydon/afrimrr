<?php

namespace App\Services\Social;

use App\Models\Startup;
use App\Services\Social\Contracts\SocialPoster;
use App\Support\Money;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Posts the one-time "just joined" announcement for a startup, the first time
 * it has verified MRR to show.
 */
class StartupAnnouncer
{
    public function __construct(private SocialPoster $poster) {}

    /**
     * @return bool whether an announcement was actually posted
     */
    public function announce(Startup $startup): bool
    {
        if (! $this->shouldAnnounce($startup)) {
            return false;
        }

        try {
            $this->poster->post($this->compose($startup));
        } catch (Throwable $e) {
            Log::warning('Startup announcement failed', [
                'startup' => $startup->id,
                'channel' => $this->poster->channel(),
                'error' => $e->getMessage(),
            ]);

            return false;
        }

        $startup->forceFill(['announced_at' => now()])->save();

        return true;
    }

    public function shouldAnnounce(Startup $startup): bool
    {
        return (bool) config('social.enabled')
            && $startup->is_public
            && $startup->is_verified
            && $startup->announced_at === null
            && $startup->mrr_usd > 0;
    }

    public function compose(Startup $startup): string
    {
        $url = route('startups.show', $startup->slug);
        $mrr = Money::usd($startup->mrr_usd);
        $subs = number_format($startup->active_subscriptions);

        $lines = [
            "🚀 {$startup->name} just joined AfricanMRR",
            '',
            "🔁 MRR: {$mrr}/mo",
            "💳 Active subs: {$subs}",
            "🌍 {$startup->countryLabel()}",
            '',
            $url,
        ];

        return implode(PHP_EOL, $lines);
    }
}
