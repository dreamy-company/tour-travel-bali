<?php

namespace Database\Factories;

use App\Models\TourPackage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TourPackage>
 */
class TourPackageFactory extends Factory
{
    protected $model = TourPackage::class;

    public function definition(): array
    {
        return [
            'guide_id' => User::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'price' => 750000.00,
            'destinations' => ['Ubud Monkey Forest', 'Tegallalang Rice Terrace'],
            'is_active' => true,
        ];
    }
}
