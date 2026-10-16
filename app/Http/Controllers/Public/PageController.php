<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function faq(): Response
    {
        return Inertia::render('public/Faq');
    }

    public function about(): Response
    {
        return Inertia::render('public/About');
    }

    public function terms(): Response
    {
        return Inertia::render('public/Legal', ['kind' => 'terms']);
    }

    public function privacy(): Response
    {
        return Inertia::render('public/Legal', ['kind' => 'privacy']);
    }

    public function badgeStore(): Response
    {
        return Inertia::render('public/BadgeStore');
    }

    public function developers(): Response
    {
        return Inertia::render('public/Developers', [
            'baseUrl' => rtrim((string) config('app.url'), '/'),
        ]);
    }
}
