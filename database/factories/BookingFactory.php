<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use App\Models\TourPackage;
use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'customer_id' => User::factory(),
            'guide_id' => User::factory(),
            'tour_package_id' => TourPackage::factory(),
            'pickup_location' => fake()->address(),
            'dropoff_location' => fake()->address(),
            'custom_destinations' => null,
            'schedule_date' => fake()->dateTimeBetween('now', '+1 month'),
            'pickup_time' => '08:00:00',
            'total_price' => 750000.00,
            'status' => BookingStatus::PENDING_CONFIRMATION,
        ];
    }
}
