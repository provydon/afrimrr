<?php

namespace Database\Factories;

use App\Models\Startup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Startup>
 */
class StartupFactory extends Factory
{
    protected $model = Startup::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 1_000_000),
            'tagline' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'website' => 'https://'.fake()->domainName(),
            'logo_url' => null,
            'category' => fake()->randomElement(array_keys(config('mrr.categories'))),
            'country' => fake()->randomElement(array_keys(config('mrr.countries'))),
            'founded_year' => fake()->numberBetween(2015, (int) date('Y')),
            'is_verified' => false,
            'verification_source' => null,
            'mrr_usd' => fake()->numberBetween(1_00, 500_000_00),
            'mrr_local' => fake()->numberBetween(1_00, 5_000_000_00),
            'display_currency' => 'NGN',
            'active_subscriptions' => fake()->numberBetween(1, 5000),
            'mom_growth' => null,
            'last_synced_at' => null,
            'is_public' => true,
            'is_for_sale' => false,
            'asking_price_usd' => null,
            'is_demo' => false,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn () => [
            'is_verified' => true,
            'verification_source' => 'paystack',
            'last_synced_at' => now(),
        ]);
    }

    public function demo(): static
    {
        return $this->state(fn () => ['is_demo' => true]);
    }

    public function private(): static
    {
        return $this->state(fn () => ['is_public' => false]);
    }

    public function forSale(int $askingPriceUsdCents = 50_000_00): static
    {
        return $this->state(fn () => [
            'is_for_sale' => true,
            'asking_price_usd' => $askingPriceUsdCents,
        ]);
    }

    public function withMrr(int $usdCents): static
    {
        return $this->state(fn () => ['mrr_usd' => $usdCents]);
    }
}
