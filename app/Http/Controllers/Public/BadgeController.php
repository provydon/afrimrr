<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Startup;
use App\Support\Money;
use Illuminate\Http\Response;

class BadgeController extends Controller
{
    public function svg(Startup $startup): Response
    {
        abort_unless($startup->is_public, 404);

        $mrr = Money::compactUsd($startup->mrr_usd);
        $verified = $startup->is_verified;
        $label = $verified ? 'VERIFIED MRR' : 'LISTED MRR';
        $accent = $verified ? '#16a34a' : '#64748b';

        // Width scales a little with the value length.
        $valueWidth = max(104, 54 + strlen($mrr) * 13);
        $labelWidth = 150;
        $width = $labelWidth + $valueWidth;

        $check = $verified
            ? '<path d="M20 20.5l3 3 6-6.5" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>'
            : '';

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="56" viewBox="0 0 {$width} 56" role="img" aria-label="{$label}: {$mrr}">
  <rect width="{$width}" height="56" rx="8" fill="#0f172a"/>
  <rect x="{$labelWidth}" width="{$valueWidth}" height="56" rx="8" fill="{$accent}"/>
  <rect x="{$labelWidth}" width="12" height="56" fill="{$accent}"/>
  <g font-family="'Segoe UI',system-ui,-apple-system,sans-serif">
    <circle cx="24" cy="21" r="12" fill="{$accent}"/>
    {$check}
    <text x="44" y="18" fill="#94a3b8" font-size="10" font-weight="600" letter-spacing="1.2">{$label}</text>
    <text x="44" y="40" fill="#f8fafc" font-size="17" font-weight="700">afrimrr.com</text>
    <text x="{$labelWidth}" y="33" dx="{$valueWidth}" text-anchor="end" transform="translate(-16,0)" fill="#ffffff" font-size="22" font-weight="800">{$mrr}</text>
  </g>
</svg>
SVG;

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
