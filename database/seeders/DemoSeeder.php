<?php

namespace Database\Seeders;

use App\Models\Startup;
use App\Models\User;
use App\Services\Mrr\CurrencyConverter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    /**
     * Fictional startups — invented names, used to demonstrate the product.
     * Nothing here represents a real company's revenue.
     */
    public function run(): void
    {
        $founders = [
            ['name' => 'Amara Okafor', 'username' => 'amara', 'email' => 'amara@example.com', 'headline' => 'Building fintech infra for Africa'],
            ['name' => 'Kwame Mensah', 'username' => 'kwame', 'email' => 'kwame@example.com', 'headline' => 'Dev tools nerd'],
            ['name' => 'Zuri Wanjiru', 'username' => 'zuri', 'email' => 'zuri@example.com', 'headline' => 'Healthtech founder, Nairobi'],
            ['name' => 'Tobi Adeyemi', 'username' => 'tobi', 'email' => 'tobi@example.com', 'headline' => 'AI + commerce'],
        ];

        $users = [];
        foreach ($founders as $f) {
            $users[$f['username']] = User::firstOrCreate(
                ['email' => $f['email']],
                [
                    'name' => $f['name'],
                    'username' => $f['username'],
                    'headline' => $f['headline'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
            );
        }

        // [name, category, country, currency, mrr_usd (dollars), provider, founder, forSale, askingUsd]
        $startups = [
            ['Paylane', 'fintech', 'NG', 'NGN', 48200, 'paystack', 'amara', false, null],
            ['Nduka AI', 'ai', 'NG', 'USD', 31500, 'stripe', 'tobi', false, null],
            ['Savanna Analytics', 'devtools', 'KE', 'USD', 22400, 'stripe', 'kwame', false, null],
            ['Kudi Ledger', 'fintech', 'NG', 'NGN', 18750, 'paystack', 'amara', true, 420000],
            ['Zuri Health Cloud', 'healthtech', 'KE', 'KES', 14300, 'flutterwave', 'zuri', false, null],
            ['Baobab Billing', 'saas', 'ZA', 'ZAR', 11900, 'stripe', 'kwame', false, null],
            ['Sahara Send', 'fintech', 'EG', 'EGP', 9600, 'paystack', 'amara', false, null],
            ['Lumio Learn', 'edtech', 'GH', 'GHS', 7300, 'flutterwave', 'kwame', true, 180000],
            ['MarketMoto', 'logistics', 'NG', 'NGN', 5400, 'paystack', 'tobi', false, null],
            ['Pesa Pulse', 'fintech', 'KE', 'KES', 4200, 'flutterwave', 'zuri', false, null],
            ['FarmLink Africa', 'agritech', 'UG', 'UGX', 2750, 'flutterwave', 'zuri', true, 60000],
            ['Jollof Jobs', 'marketplace', 'NG', 'NGN', 1450, 'paystack', 'tobi', false, null],
        ];

        foreach ($startups as [$name, $cat, $country, $currency, $mrrUsd, $provider, $founder, $forSale, $asking]) {
            $usdCents = $mrrUsd * 100;
            $rate = CurrencyConverter::rate($currency);
            $localMinor = $rate > 0 ? (int) round($usdCents / $rate) : $usdCents;

            $startup = Startup::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'user_id' => $users[$founder]->id,
                    'name' => $name,
                    'tagline' => $this->tagline($cat),
                    'description' => "{$name} is a demo startup showcasing verified MRR on AfricanMRR.",
                    'website' => 'https://example.com',
                    'category' => $cat === 'marketplace' ? 'other' : $cat,
                    'country' => $country,
                    'founded_year' => random_int(2019, 2024),
                    'is_verified' => true,
                    'verification_source' => $provider,
                    'mrr_usd' => $usdCents,
                    'mrr_local' => $localMinor,
                    'display_currency' => $currency,
                    'active_subscriptions' => (int) round($mrrUsd / random_int(8, 40)),
                    'last_synced_at' => now(),
                    'is_public' => true,
                    'is_for_sale' => $forSale,
                    'asking_price_usd' => $asking ? $asking * 100 : null,
                    'is_demo' => true,
                ],
            );

            $this->seedSnapshots($startup, $usdCents, $localMinor, $currency);
        }
    }

    private function seedSnapshots(Startup $startup, int $usdCents, int $localMinor, string $currency): void
    {
        // Growth curve over the last 90 days (roughly +6% MoM).
        $steps = [90 => 0.84, 60 => 0.90, 30 => 0.945, 0 => 1.0];

        foreach ($steps as $daysAgo => $factor) {
            $startup->snapshots()->updateOrCreate(
                ['captured_on' => Carbon::today()->subDays($daysAgo)->toDateString()],
                [
                    'mrr_usd' => (int) round($usdCents * $factor),
                    'mrr_local' => (int) round($localMinor * $factor),
                    'display_currency' => $currency,
                    'active_subscriptions' => $startup->active_subscriptions,
                ],
            );
        }

        $past = (int) round($usdCents * 0.945);
        $startup->mom_growth = $past > 0 ? round((($usdCents - $past) / $past) * 100, 2) : null;
        $startup->save();
    }

    private function tagline(string $cat): string
    {
        return match ($cat) {
            'fintech' => 'Payments infrastructure for the next billion',
            'ai' => 'AI copilots for African businesses',
            'devtools' => 'Ship faster with observability built for Africa',
            'healthtech' => 'Cloud EHR for clinics across the continent',
            'saas' => 'Subscription billing without the headache',
            'edtech' => 'Upskilling Africa, one cohort at a time',
            'logistics' => 'Last-mile delivery, solved',
            'agritech' => 'Connecting farmers to markets',
            default => 'Building in public across Africa',
        };
    }
}
