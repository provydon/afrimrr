<?php

namespace App\Http\Controllers;

use App\Models\Startup;
use App\Services\Mrr\CurrencyConverter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class StartupController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('startups/Create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $startup = new Startup;
        $startup->user_id = $request->user()->id;
        $startup->slug = Startup::uniqueSlug($data['name']);
        $this->fill($startup, $data);
        $startup->save();

        return redirect()
            ->route('startups.connections', $startup)
            ->with('success', 'Startup created. Connect a payment provider to verify your MRR.');
    }

    public function edit(Request $request, Startup $startup): Response
    {
        $this->authorizeOwner($request, $startup);
        $startup->load('connections');

        return Inertia::render('startups/Edit', array_merge($this->formOptions(), [
            'startup' => [
                'id' => $startup->id,
                'name' => $startup->name,
                'slug' => $startup->slug,
                'tagline' => $startup->tagline,
                'description' => $startup->description,
                'website' => $startup->website,
                'logo_url' => $startup->logo_url,
                'category' => $startup->category,
                'country' => $startup->country,
                'founded_year' => $startup->founded_year,
                'is_public' => $startup->is_public,
                'is_anonymous' => $startup->is_anonymous,
                'is_for_sale' => $startup->is_for_sale,
                'asking_price_usd' => $startup->asking_price_usd ? $startup->asking_price_usd / 100 : null,
                'has_connections' => $startup->connections->isNotEmpty(),
            ],
        ]));
    }

    public function update(Request $request, Startup $startup): RedirectResponse
    {
        $this->authorizeOwner($request, $startup);

        $data = $this->validateData($request);
        $this->fill($startup, $data);
        $startup->save();

        return redirect()->route('dashboard')->with('success', 'Startup updated.');
    }

    public function destroy(Request $request, Startup $startup): RedirectResponse
    {
        $this->authorizeOwner($request, $startup);
        $startup->delete();

        return redirect()->route('dashboard')->with('success', 'Startup deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:4000'],
            'website' => ['nullable', 'url', 'max:255'],
            'logo_url' => ['nullable', 'url', 'max:255'],
            'category' => ['required', Rule::in(array_keys(config('mrr.categories')))],
            'country' => ['required', Rule::in(array_keys(config('mrr.countries')))],
            'founded_year' => ['nullable', 'integer', 'min:1990', 'max:'.(date('Y') + 1)],
            'is_public' => ['boolean'],
            'is_anonymous' => ['boolean'],
            'is_for_sale' => ['boolean'],
            'asking_price_usd' => ['nullable', 'numeric', 'min:0', 'max:1000000000'],
            'manual_mrr_amount' => ['nullable', 'numeric', 'min:0'],
            'manual_mrr_currency' => ['nullable', Rule::in(array_keys(config('mrr.fx')))],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function fill(Startup $startup, array $data): void
    {
        $startup->fill([
            'name' => $data['name'],
            'tagline' => $data['tagline'] ?? null,
            'description' => $data['description'] ?? null,
            'website' => $data['website'] ?? null,
            'logo_url' => $data['logo_url'] ?? null,
            'category' => $data['category'],
            'country' => $data['country'],
            'founded_year' => $data['founded_year'] ?? null,
            'is_public' => $data['is_public'] ?? true,
            'is_anonymous' => $data['is_anonymous'] ?? false,
            'is_for_sale' => $data['is_for_sale'] ?? false,
            'asking_price_usd' => isset($data['asking_price_usd']) ? (int) round($data['asking_price_usd'] * 100) : null,
        ]);

        // Manual MRR is only applied when no provider is connected (sync owns it otherwise).
        $hasConnections = $startup->exists && $startup->connections()->exists();

        if (! $hasConnections && ! empty($data['manual_mrr_amount'])) {
            $currency = $data['manual_mrr_currency'] ?? 'USD';
            $localMinor = (int) round(((float) $data['manual_mrr_amount']) * 100);
            $startup->mrr_local = $localMinor;
            $startup->display_currency = $currency;
            $startup->mrr_usd = CurrencyConverter::toUsdCents($localMinor, $currency);
            $startup->verification_source = 'manual';
            $startup->is_verified = false;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'categories' => config('mrr.categories'),
            'countries' => config('mrr.countries'),
            'currencies' => array_keys(config('mrr.fx')),
        ];
    }

    private function authorizeOwner(Request $request, Startup $startup): void
    {
        abort_unless($startup->user_id === $request->user()->id, 403);
    }
}
