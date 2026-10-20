<?php

namespace App\Services\Newsletter;

use App\Models\Startup;
use App\Support\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Assembles the data shown in the "Spot the Next Big Thing" weekly report
 * email: the biggest verified startups, who just joined, and who is growing
 * fastest. Only public, non-anonymous, non-demo startups with real MRR appear.
 *
 * @phpstan-type ReportRow array{name: string, url: string, mrr_display: string, mrr_compact: string, country_label: string, category_label: string, is_verified: bool, mom_growth: float|null, mom_growth_display: string|null}
 * @phpstan-type Report array{period_label: string, top: array<int, ReportRow>, newcomers: array<int, ReportRow>, growing: array<int, ReportRow>, stats: array{verified_mrr_display: string, verified_count: int, country_count: int}}
 */
class WeeklyReport
{
    /**
     * @return Report
     */
    public function build(): array
    {
        $since = Carbon::now()->subDays(7);

        $top = $this->base()
            ->orderByDesc('mrr_usd')
            ->limit(5)
            ->get()
            ->map(fn (Startup $s) => $this->row($s))
            ->all();

        $newcomers = $this->base()
            ->where('created_at', '>=', $since)
            ->orderByDesc('mrr_usd')
            ->limit(5)
            ->get()
            ->map(fn (Startup $s) => $this->row($s))
            ->all();

        $growing = $this->base()
            ->whereNotNull('mom_growth')
            ->where('mom_growth', '>', 0)
            ->orderByDesc('mom_growth')
            ->limit(5)
            ->get()
            ->map(fn (Startup $s) => $this->row($s))
            ->all();

        $verifiedTotalCents = (int) $this->base()->sum('mrr_usd');

        return [
            'period_label' => Carbon::now()->subDays(7)->format('M j').' – '.Carbon::now()->format('M j, Y'),
            'top' => $top,
            'newcomers' => $newcomers,
            'growing' => $growing,
            'stats' => [
                'verified_mrr_display' => Money::usd($verifiedTotalCents),
                'verified_count' => $this->base()->count(),
                'country_count' => $this->base()->distinct()->count('country'),
            ],
        ];
    }

    /**
     * @param  Report  $report
     */
    public function hasContent(array $report): bool
    {
        return $report['top'] !== [];
    }

    /**
     * Public, non-anonymous, non-demo, revenue-bearing startups.
     *
     * @return Builder<Startup>
     */
    private function base()
    {
        return Startup::query()
            ->public()
            ->real()
            ->where('is_anonymous', false)
            ->where('mrr_usd', '>', 0);
    }

    /**
     * @return ReportRow
     */
    private function row(Startup $startup): array
    {
        return [
            'name' => $startup->name,
            'url' => route('startups.show', $startup->slug),
            'mrr_display' => Money::usd($startup->mrr_usd),
            'mrr_compact' => Money::compactUsd($startup->mrr_usd),
            'country_label' => $startup->countryLabel(),
            'category_label' => $startup->categoryLabel(),
            'is_verified' => $startup->is_verified,
            'mom_growth' => $startup->mom_growth,
            'mom_growth_display' => $startup->mom_growth !== null
                ? sprintf('%+.1f%%', $startup->mom_growth)
                : null,
        ];
    }
}
