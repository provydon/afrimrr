<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Startup;
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
