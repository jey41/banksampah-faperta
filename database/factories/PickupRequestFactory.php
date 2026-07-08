<?php

namespace Database\Factories;

use App\Models\PickupRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PickupRequest>
 */
class PickupRequestFactory extends Factory
{
    protected $model = PickupRequest::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->nasabah(),
            'pickup_address' => fake()->address(),
            'pickup_phone' => fake()->phoneNumber(),
            'pickup_date' => fake()->dateTimeBetween('now', '+7 days'),
            'pickup_time' => fake()->randomElement(['08:00-10:00', '10:00-12:00', '13:00-15:00']),
            'latitude' => fake()->latitude(-1, 1),
            'longitude' => fake()->longitude(116, 118),
            'estimated_distance' => fake()->randomFloat(2, 0.1, 2.0),
            'notes' => fake()->optional()->sentence(),
            'status' => 'pending',
            'assigned_to' => null,
            'deposit_id' => null,
        ];
    }

    /**
     * Pickup request in pending status.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Pickup request in assigned status.
     */
    public function assigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'assigned',
            'assigned_to' => User::factory()->petugas(),
        ]);
    }

    /**
     * Pickup request in completed status.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'assigned_to' => User::factory()->petugas(),
        ]);
    }
}
