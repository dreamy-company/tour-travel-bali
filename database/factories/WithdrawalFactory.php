<?php

namespace Database\Factories;

use App\Models\Withdrawal;
use App\Models\User;
use App\Enums\WithdrawalStatus;
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
            'guide_id' => User::factory(),
            'amount' => 500000.00,
            'bank_name' => 'Bank Mandiri',
            'bank_account_number' => fake()->numerify('##########'),
            'bank_account_name' => fake()->name(),
            'status' => WithdrawalStatus::PENDING,
        ];
    }
}
