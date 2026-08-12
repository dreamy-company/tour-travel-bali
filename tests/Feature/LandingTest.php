<?php

namespace Tests\Feature;

use App\Enums\TariffMode;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Livewire\Customer\GuideSearch;
use App\Models\GuideProfile;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LandingTest extends TestCase
{
    use RefreshDatabase;

    private function makeRatedGuide(string $name, int $rating): User
    {
        $guide = User::factory()->guide()->create(['name' => $name, 'status' => UserStatus::ACTIVE]);
        $profile = GuideProfile::factory()->create([
            'user_id' => $guide->id,
            'is_verified' => true,
            'base_rate' => 500000.00,
            'tariff_mode' => TariffMode::DAILY,
        ]);

        $customer = User::factory()->customer()->create();
        $booking = \App\Models\Booking::create([
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
            'pickup_location' => 'Kuta',
            'custom_destinations' => ['Ubud'],
            'schedule_date' => now()->addDays(1)->toDateString(),
            'pickup_time' => '09:00',
            'total_price' => 500000.00,
            'status' => \App\Enums\BookingStatus::COMPLETED,
        ]);
        Review::create([
            'booking_id' => $booking->id,
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
            'rating' => $rating,
            'comment' => 'Great guide.',
        ]);

        return $guide;
    }

    public function test_landing_page_is_public(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Temukan Pemandu Lokal Bali');
    }

    public function test_landing_shows_vibe_selector_linking_to_directory(): void
    {
        $this->get(route('home'))
            ->assertSee('Cafe Hopping')
            ->assertSee('Photography')
            ->assertSee('Healing');

        // Vibe cards pre-filter the directory via the ?vibe= query param.
        Livewire::test(GuideSearch::class)
            ->set('selectedSpecializations', ['photography'])
            ->assertSet('selectedSpecializations', ['photography']);
    }

    public function test_vibe_query_param_prefilters_guides(): void
    {
        $natureGuide = User::factory()->guide()->create(['name' => 'Nature Guide', 'status' => UserStatus::ACTIVE]);
        GuideProfile::factory()->create([
            'user_id' => $natureGuide->id,
            'specializations' => ['nature'],
            'is_verified' => true,
        ]);

        $photoGuide = User::factory()->guide()->create(['name' => 'Photo Guide', 'status' => UserStatus::ACTIVE]);
        GuideProfile::factory()->create([
            'user_id' => $photoGuide->id,
            'specializations' => ['photography'],
            'is_verified' => true,
        ]);

        $this->get(route('guides.index', ['vibe' => 'photography']))
            ->assertSee('Photo Guide')
            ->assertDontSee('Nature Guide');
    }

    public function test_landing_features_top_rated_verified_guides(): void
    {
        $this->makeRatedGuide('Wayan', 5);
        $this->makeRatedGuide('Made', 3);

        $this->get(route('home'))
            ->assertSee('Wayan')
            ->assertSee('Made')
            ->assertSee('HPI Verified');
    }
}
