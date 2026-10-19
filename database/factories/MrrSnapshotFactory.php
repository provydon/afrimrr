<?php

namespace Database\Factories;

use App\Models\MrrSnapshot;
use App\Models\Startup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MrrSnapshot>
 */
class MrrSnapshotFactory extends Factory
{
    protected $model = MrrSnapshot::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'startup_id' => Startup::factory(),
            'mrr_usd' => fake()->numberBetween(1_00, 500_000_00),
            'mrr_local' => fake()->numberBetween(1_00, 5_000_000_00),
            'display_currency' => 'NGN',
            'active_subscriptions' => fake()->numberBetween(1, 5000),
            'captured_on' => now()->toDateString(),
        ];
    }
}
