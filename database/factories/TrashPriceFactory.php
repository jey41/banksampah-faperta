<?php

namespace Database\Factories;

use App\Models\TrashPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrashPrice>
 */
class TrashPriceFactory extends Factory
{
    protected $model = TrashPrice::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Botol Plastik', 'Kardus', 'Kertas HVS', 'Kaleng Aluminium', 'Botol Kaca']),
            'category' => fake()->randomElement(['plastik', 'kertas', 'logam', 'kaca']),
            'category_type' => fake()->randomElement(['umum', 'donasi']),
            'price_buy' => fake()->numberBetween(500, 5000),
            'price_sell' => fn (array $attributes) => $attributes['price_buy'] + fake()->numberBetween(200, 1000),
            'unit' => fake()->randomElement(['kg', 'L', 'pcs']),
            'carbon_factor' => fake()->randomFloat(2, 0.1, 5.0),
        ];
    }
}
