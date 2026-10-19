<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Startup;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StartupController extends Controller
{
    public function index(Request $request): Response
    {
        $q = $request->string('q')->toString();

        $startups = Startup::query()
            ->with('owner')
            ->when($q, fn ($b) => $b->where('name', 'like', "%{$q}%"))
            ->orderByDesc('mrr_usd')
            ->paginate(50)
            ->withQueryString();

        $startups->getCollection()->transform(fn (Startup $s) => [
            'id' => $s->id,
            'name' => $s->name,
            'slug' => $s->slug,
            'mrr_usd_display' => Money::usd($s->mrr_usd),
            'is_verified' => $s->is_verified,
            'is_public' => $s->is_public,
            'is_anonymous' => $s->is_anonymous,
            'is_demo' => $s->is_demo,
            'owner' => $s->owner?->name,
        ]);

        return Inertia::render('admin/Startups', [
            'startups' => $startups,
            'filters' => ['q' => $q],
        ]);
    }

    public function update(Request $request, Startup $startup): RedirectResponse
    {
        $data = $request->validate([
            'is_public' => ['sometimes', 'boolean'],
            'is_anonymous' => ['sometimes', 'boolean'],
        ]);

        $startup->update($data);

        return back()->with('success', 'Startup updated.');
    }

    public function destroy(Startup $startup): RedirectResponse
    {
        $startup->delete();

        return back()->with('success', 'Startup deleted.');
    }
}
