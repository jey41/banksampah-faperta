<?php

namespace Database\Factories;

use App\Models\Deposit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deposit>
 */
class DepositFactory extends Factory
{
    protected $model = Deposit::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->nasabah(),
            'total_price' => 0,
            'weight_total' => 0,
            'status' => 'pending',
            'is_donation' => false,
            'donation_category' => 'umum',
            'notes' => null,
            'validated_by' => null,
        ];
    }

    /**
     * Deposit in pending status.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Deposit in approved status.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'validated_by' => User::factory()->petugas(),
            'total_price' => fake()->numberBetween(5000, 100000),
            'weight_total' => fake()->randomFloat(2, 1, 50),
        ]);
    }

    /**
     * Deposit in rejected status.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'validated_by' => User::factory()->petugas(),
        ]);
    }

    /**
     * Deposit as a donation.
     */
    public function donation(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_donation' => true,
            'donation_category' => fake()->randomElement(['umum', 'donasi']),
        ]);
    }

    /**
     * Deposit with specific amount.
     */
    public function withAmount(int $price, float $weight): static
    {
        return $this->state(fn (array $attributes) => [
            'total_price' => $price,
            'weight_total' => $weight,
        ]);
    }
}
