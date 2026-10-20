<?php

namespace App\Support;

use App\Models\Startup;

class StartupPresenter
{
    /**
     * Compact shape for leaderboard rows / directory cards.
     *
     * @return array<string, mixed>
     */
    public static function card(Startup $startup): array
    {
        // Anonymous startups are ranked on MRR but keep their identity hidden —
        // name/slug/logo/tagline never leave the server, so there is nothing to
        // un-blur in the browser.
        $anonymous = $startup->is_anonymous;

        return [
            'name' => $anonymous ? 'Anonymous Startup' : $startup->name,
            'slug' => $anonymous ? null : $startup->slug,
            'tagline' => $anonymous ? null : $startup->tagline,
            'logo_url' => $anonymous ? null : $startup->logo_url,
            'is_anonymous' => $anonymous,
            'category' => $startup->category,
            'category_label' => $startup->categoryLabel(),
            'country' => $startup->country,
            'country_label' => $startup->countryLabel(),
            'is_verified' => $startup->is_verified,
            'verification_source' => $startup->verification_source,
            'mrr_usd' => $startup->mrr_usd,
            'mrr_usd_display' => Money::usd($startup->mrr_usd),
            'mrr_usd_compact' => Money::compactUsd($startup->mrr_usd),
            'mrr_local_display' => Money::format($startup->mrr_local, $startup->display_currency),
            'display_currency' => $startup->display_currency,
            'mom_growth' => $startup->mom_growth,
            'is_for_sale' => $startup->is_for_sale,
            'asking_price_display' => $startup->asking_price_usd ? Money::usd($startup->asking_price_usd) : null,
            'revenue_multiple' => $startup->revenueMultiple(),
            'founder' => [
                'name' => $startup->owner?->name,
                'username' => $startup->owner?->publicUsername(),
                'avatar_url' => $startup->owner?->avatar_url,
            ],
        ];
    }

    /**
     * Full shape for a public startup profile page.
     *
     * @return array<string, mixed>
     */
    public static function profile(Startup $startup): array
    {
        $snapshots = $startup->snapshots()
            ->orderBy('captured_on')
            ->get(['captured_on', 'mrr_usd'])
            ->map(fn ($s) => [
                'date' => $s->captured_on->toDateString(),
                'mrr_usd' => $s->mrr_usd,
            ]);

        return array_merge(self::card($startup), [
            'description' => $startup->is_anonymous ? null : $startup->description,
            'website' => $startup->is_anonymous ? null : $startup->website,
            'founded_year' => $startup->founded_year,
            'active_subscriptions' => $startup->active_subscriptions,
            'last_synced_at' => $startup->last_synced_at?->diffForHumans(),
            'arr_usd_display' => Money::usd($startup->mrr_usd * 12),
            'arr_local_display' => Money::format($startup->mrr_local * 12, $startup->display_currency),
            'history' => $snapshots,
            'founder' => [
                'name' => $startup->owner?->name,
                'username' => $startup->owner?->publicUsername(),
                'avatar_url' => $startup->owner?->avatar_url,
                'headline' => $startup->owner?->headline,
                'twitter' => $startup->owner?->twitter,
            ],
        ]);
    }
}
