<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Startup;
use App\Models\User;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $base = rtrim((string) config('app.url'), '/');

        $urls = [];
        $add = function (string $path, string $priority = '0.6', ?string $lastmod = null) use (&$urls, $base) {
            $urls[] = [
                'loc' => $base.$path,
                'priority' => $priority,
                'lastmod' => $lastmod,
            ];
        };

        // Static pages.
        $add('/', '1.0');
        $add('/leaderboard', '0.9');
        $add('/startups', '0.9');
        $add('/marketplace', '0.8');
        $add('/badge-store', '0.6');
        $add('/developers', '0.6');
        $add('/faq', '0.5');
        $add('/about', '0.5');
        $add('/terms', '0.3');
        $add('/privacy', '0.3');

        // Public startups + their founders (anonymous ones have no profile page).
        Startup::public()->where('is_anonymous', false)->with('owner')->orderByDesc('mrr_usd')->get()
            ->each(function (Startup $s) use ($add) {
                $add('/startups/'.$s->slug, '0.7', $s->updated_at?->toAtomString());
            });

        User::whereHas('startups', fn ($q) => $q->where('is_public', true))
            ->whereNotNull('username')->get()
            ->each(fn (User $u) => $add('/founders/'.$u->username, '0.5'));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $u) {
            $xml .= '  <url><loc>'.htmlspecialchars($u['loc']).'</loc>';
            if ($u['lastmod']) {
                $xml .= '<lastmod>'.$u['lastmod'].'</lastmod>';
            }
            $xml .= '<priority>'.$u['priority'].'</priority></url>'."\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
