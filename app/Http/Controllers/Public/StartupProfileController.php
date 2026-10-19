<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Startup;
use App\Support\Money;
use App\Support\StartupPresenter;
use Inertia\Inertia;
use Inertia\Response;

class StartupProfileController extends Controller
{
    public function show(Startup $startup): Response
    {
        abort_unless($startup->is_public && ! $startup->is_anonymous, 404);

        $startup->load('owner');

        $badgeUrl = route('badge.svg', $startup->slug);
        $profileUrl = route('startups.show', $startup->slug);

        // Per-startup social unfurl card + meta (read by crawlers from the
        // server-rendered root view). Falls through to sensible defaults.
        $verb = $startup->is_verified ? 'Verified' : 'Listed';
        view()->share([
            'ogImage' => route('og.startup', $startup->slug),
            'ogTitle' => sprintf('%s — %s MRR on AfricanMRR', $startup->name, $verb),
            'ogDescription' => sprintf(
                '%s MRR: %s/mo · %s. Real recurring revenue, verified from Paystack, Flutterwave and Stripe.',
                $verb,
                Money::usd($startup->mrr_usd),
                $startup->countryLabel(),
            ),
        ]);

        $embedCode = sprintf(
            '<a href="%s" target="_blank" rel="noopener"><img src="%s" alt="%s verified MRR" height="56"></a>',
            $profileUrl,
            $badgeUrl,
            e($startup->name)
        );

        return Inertia::render('public/StartupProfile', [
            'startup' => StartupPresenter::profile($startup),
            'badgeUrl' => $badgeUrl,
            'embedCode' => $embedCode,
        ]);
    }
}
