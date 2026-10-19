<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscriberController extends Controller
{
    public function index(Request $request): Response
    {
        $q = $request->string('q')->toString();

        $subscribers = Subscriber::query()
            ->when($q, fn ($b) => $b->where('email', 'like', "%{$q}%"))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return Inertia::render('admin/Subscribers', [
            'subscribers' => $subscribers,
            'total' => Subscriber::count(),
            'filters' => ['q' => $q],
        ]);
    }

    public function export(): StreamedResponse
    {
        $filename = 'africanmrr-subscribers-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');

            if ($out === false) {
                return;
            }

            fputcsv($out, ['email', 'source', 'subscribed_at']);

            Subscriber::orderBy('id')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $s) {
                    fputcsv($out, [$s->email, $s->source, $s->created_at?->toDateTimeString()]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
