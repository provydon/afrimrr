<?php

namespace App\Http\Controllers;

use App\Actions\SyncStartup;
use App\Models\Startup;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ConnectionController extends Controller
{
    public function index(Request $request, Startup $startup): Response
    {
        $this->authorizeOwner($request, $startup);
        $startup->load('connections');

        return Inertia::render('startups/Connections', [
            'startup' => [
                'id' => $startup->id,
                'name' => $startup->name,
                'slug' => $startup->slug,
                'is_verified' => $startup->is_verified,
                'mrr_usd_display' => Money::usd($startup->mrr_usd),
            ],
            'providers' => collect((array) config('mrr.providers'))->map(fn ($p, $key) => [
                'key' => $key,
                'label' => $p['label'],
                'key_hint' => $p['key_hint'],
            ])->values(),
            'connections' => $startup->connections->map(fn ($c) => [
                'id' => $c->id,
                'provider' => $c->provider,
                'provider_label' => $c->providerLabel(),
                'status' => $c->status,
                'last_error' => $c->last_error,
                'last_synced_at' => $c->last_synced_at?->diffForHumans(),
            ]),
        ]);
    }

    public function store(Request $request, Startup $startup, SyncStartup $sync): RedirectResponse
    {
        $this->authorizeOwner($request, $startup);

        $data = $request->validate([
            'provider' => ['required', Rule::in(['paystack', 'flutterwave', 'stripe'])],
            'secret' => ['required', 'string', 'min:10', 'max:255'],
        ]);

        $startup->connections()->updateOrCreate(
            ['provider' => $data['provider']],
            ['credentials' => trim($data['secret']), 'status' => 'pending', 'last_error' => null],
        );

        $result = $sync->handle($startup->fresh());

        if (! $result['ok']) {
            $message = $result['errors'][$data['provider']] ?? 'Could not verify with that key.';

            return back()->with('error', $message);
        }

        return back()->with('success', ucfirst($data['provider']).' connected — MRR verified.');
    }

    public function sync(Request $request, Startup $startup, SyncStartup $sync): RedirectResponse
    {
        $this->authorizeOwner($request, $startup);

        $result = $sync->handle($startup);

        if (! $result['ok']) {
            return back()->with('error', reset($result['errors']) ?: 'Sync failed.');
        }

        return back()->with('success', 'MRR refreshed.');
    }

    public function destroy(Request $request, Startup $startup, string $provider): RedirectResponse
    {
        $this->authorizeOwner($request, $startup);

        $startup->connections()->where('provider', $provider)->delete();

        if ($startup->connections()->doesntExist()) {
            $startup->update(['is_verified' => false, 'verification_source' => null]);
        }

        return back()->with('success', 'Provider disconnected.');
    }

    private function authorizeOwner(Request $request, Startup $startup): void
    {
        abort_unless($startup->user_id === $request->user()->id, 403);
    }
}
