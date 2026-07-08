<?php

namespace Database\Factories;

use App\Models\DepositItem;
use App\Models\TrashPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DepositItem>
 */
class DepositItemFactory extends Factory
{
    protected $model = DepositItem::class;

    public function definition(): array
    {
        $weight = fake()->randomFloat(2, 0.5, 20);

        return [
            'trash_price_id' => TrashPrice::factory(),
            'weight' => $weight,
            'price_per_unit' => fake()->numberBetween(500, 5000),
            'total_price' => 0, // Will be calculated
            'total_carbon' => 0, // Will be calculated
        ];
    }

    /**
     * Set specific weight and calculate totals.
     */
    public function withWeight(float $weight, int $pricePerUnit, float $carbonFactor = 0): static
    {
        return $this->state(fn (array $attributes) => [
            'weight' => $weight,
            'price_per_unit' => $pricePerUnit,
            'total_price' => (int) ($weight * $pricePerUnit),
            'total_carbon' => $weight * $carbonFactor,
        ]);
    }
}
