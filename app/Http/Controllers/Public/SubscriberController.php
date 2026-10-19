<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'source' => ['nullable', 'string', 'max:40'],
        ]);

        // Idempotent — signing up again is not an error.
        Subscriber::firstOrCreate(
            ['email' => mb_strtolower(trim($data['email']))],
            ['source' => $data['source'] ?? 'popup'],
        );

        return back()->with('success', "You're on the list — the next report is on its way.");
    }
}
