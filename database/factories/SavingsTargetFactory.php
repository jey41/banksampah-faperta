<?php

namespace Database\Factories;

use App\Models\SavingsTarget;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SavingsTarget>
 */
class SavingsTargetFactory extends Factory
{
    protected $model = SavingsTarget::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->nasabah(),
            'title' => fake()->randomElement([
                'Tabungan Emas',
                'Dana Darurat',
                'Tabungan Pendidikan',
                'Donasi Lingkungan',
                'Investasi Hijau',
            ]),
            'target_amount' => fake()->numberBetween(50000, 1000000),
            'is_achieved' => false,
        ];
    }

    /**
     * Target that has been achieved.
     */
    public function achieved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_achieved' => true,
        ]);
    }

    /**
     * Target with specific amount.
     */
    public function withTarget(int $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'target_amount' => $amount,
        ]);
    }
}
