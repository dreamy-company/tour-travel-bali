<?php

namespace Database\Factories;

use App\Models\GuideProfile;
use App\Models\User;
use App\Enums\CommunicationStyle;
use App\Enums\Specialization;
use App\Enums\TariffMode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GuideProfile>
 */
class GuideProfileFactory extends Factory
{
    protected $model = GuideProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'ktp_number' => fake()->numerify('################'),
            'ktp_photo' => 'photos/ktp.jpg',
            'ktpp_number' => fake()->numerify('HPI-#####'),
            'ktpp_file' => 'files/ktpp.pdf',
            'ktpp_expired_at' => fake()->dateTimeBetween('+1 year', '+5 years'),
            'skck_file' => 'files/skck.pdf',
            'skck_expired_at' => fake()->dateTimeBetween('+6 months', '+1 year'),
            'surat_sehat_file' => 'files/sehat.pdf',
            'vehicle_details' => fake()->sentence(),
            'bio' => fake()->paragraph(),
            'communication_style' => fake()->randomElement(CommunicationStyle::cases()),
            'specializations' => fake()->randomElements(array_column(Specialization::cases(), 'value'), 2),
            'languages' => ['id', 'en'],
            'tariff_mode' => TariffMode::DAILY,
            'base_rate' => 500000.00,
            'is_verified' => false,
            'signed_sop_at' => now(),
        ];
    }
}
