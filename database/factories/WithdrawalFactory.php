<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Withdrawal>
 */
class WithdrawalFactory extends Factory
{
    protected $model = Withdrawal::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->nasabah(),
            'amount' => fake()->numberBetween(10000, 500000),
            'withdrawal_method' => fake()->randomElement(['tunai', 'transfer_bank']),
            'bank_name' => fn (array $attributes) => $attributes['withdrawal_method'] === 'tunai' ? 'Tunai' : fake()->randomElement(['BTN', 'BCA', 'Mandiri', 'BRI']),
            'bank_type' => fn (array $attributes) => $attributes['withdrawal_method'] === 'tunai' ? null : fake()->randomElement(['btn', 'lainnya']),
            'account_number' => fn (array $attributes) => $attributes['withdrawal_method'] === 'tunai' ? '-' : fake()->numerify('##########'),
            'account_name' => fn (array $attributes) => $attributes['withdrawal_method'] === 'tunai' ? '' : fake()->name(),
            'admin_fee' => 0,
            'status' => 'pending',
            'validated_by' => null,
            'notes' => null,
        ];
    }

    /**
     * Withdrawal in pending status.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Withdrawal in approved status.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'validated_by' => User::factory()->petugas(),
            'admin_fee' => $attributes['bank_type'] === 'lainnya' ? 2500 : 0,
        ]);
    }

    /**
     * Withdrawal in rejected status.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'validated_by' => User::factory()->petugas(),
        ]);
    }

    /**
     * Withdrawal via tunai (cash).
     */
    public function tunai(): static
    {
        return $this->state(fn (array $attributes) => [
            'withdrawal_method' => 'tunai',
            'bank_name' => 'Tunai',
            'bank_type' => null,
            'account_number' => '-',
            'account_name' => '',
        ]);
    }

    /**
     * Withdrawal via transfer to non-BTN bank.
     */
    public function transferNonBtn(): static
    {
        return $this->state(fn (array $attributes) => [
            'withdrawal_method' => 'transfer_bank',
            'bank_name' => 'BCA',
            'bank_type' => 'lainnya',
            'account_number' => fake()->numerify('##########'),
            'account_name' => fake()->name(),
        ]);
    }

    /**
     * Withdrawal via transfer to BTN bank.
     */
    public function transferBtn(): static
    {
        return $this->state(fn (array $attributes) => [
            'withdrawal_method' => 'transfer_bank',
            'bank_name' => 'BTN',
            'bank_type' => 'btn',
            'account_number' => fake()->numerify('##########'),
            'account_name' => fake()->name(),
        ]);
    }
}
