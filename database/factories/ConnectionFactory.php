<?php

namespace Database\Factories;

use App\Models\Connection;
use App\Models\Startup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Connection>
 */
class ConnectionFactory extends Factory
{
    protected $model = Connection::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'startup_id' => Startup::factory(),
            'provider' => 'paystack',
            'credentials' => 'sk_test_'.fake()->sha1(),
            'status' => 'pending',
            'last_error' => null,
            'last_synced_at' => null,
        ];
    }

    public function provider(string $provider): static
    {
        return $this->state(fn () => ['provider' => $provider]);
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => 'active',
            'last_synced_at' => now(),
        ]);
    }
}
