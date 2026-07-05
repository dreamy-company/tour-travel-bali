<?php

namespace Database\Factories;

use App\Models\GuideWallet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GuideWallet>
 */
class GuideWalletFactory extends Factory
{
    protected $model = GuideWallet::class;

    public function definition(): array
    {
        return [
            'guide_id' => User::factory(),
            'current_balance' => 0.00,
        ];
    }
}
