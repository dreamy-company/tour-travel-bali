<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\EscrowStatus;
use App\Enums\TariffMode;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Livewire\Customer\BookingForm;
use App\Livewire\Customer\GuideSearch;
use App\Livewire\Customer\GuideShow;
use App\Models\Booking;
use App\Models\EscrowTransaction;
use App\Models\GuideProfile;
use App\Models\Review;
use App\Models\TourPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerFlowTest extends TestCase
{
    use RefreshDatabase;

    private function makeVerifiedGuide(array $overrides = []): array
    {
        // 'name' targets the users table, everything else targets guide_profiles.
        $name = $overrides['name'] ?? null;
        unset($overrides['name']);

        $guide = User::factory()->guide()->create(array_filter([
            'status' => UserStatus::ACTIVE,
            'name' => $name,
        ]));

        $profile = GuideProfile::factory()->create(array_merge([
            'user_id' => $guide->id,
            'communication_style' => 'santai',
            'specializations' => ['nature', 'photography'],
            'tariff_mode' => TariffMode::DAILY,
            'base_rate' => 500000.00,
            'is_verified' => true,
        ], $overrides));

        return [$guide, $profile];
    }

    // ── Page 1: Directory ─────────────────────────────────────────

    public function test_directory_only_lists_verified_active_guides(): void
    {
        [$guide] = $this->makeVerifiedGuide();

        $unverified = User::factory()->guide()->create(['name' => 'Hidden Guide']);
        GuideProfile::factory()->create([
            'user_id' => $unverified->id,
            'is_verified' => false,
        ]);

        $suspended = User::factory()->guide()->create(['name' => 'Suspended Guide', 'status' => UserStatus::SUSPENDED]);
        GuideProfile::factory()->create([
            'user_id' => $suspended->id,
            'is_verified' => true,
        ]);

        Livewire::test(GuideSearch::class)
            ->assertSee($guide->name)
            ->assertDontSee('Hidden Guide')
            ->assertDontSee('Suspended Guide');
    }

    public function test_directory_filters_by_tariff_mode(): void
    {
        [$hourlyGuide] = $this->makeVerifiedGuide([
            'tariff_mode' => TariffMode::HOURLY,
            'base_rate' => 100000.00,
        ]);
        [$dailyGuide] = $this->makeVerifiedGuide(['name' => 'Daily Guide']);

        Livewire::test(GuideSearch::class)
            ->set('tariffMode', 'hourly')
            ->assertSee($hourlyGuide->name)
            ->assertDontSee('Daily Guide');
    }

    public function test_directory_filters_by_communication_style(): void
    {
        [$guide] = $this->makeVerifiedGuide();
        [$other] = $this->makeVerifiedGuide([
            'name' => 'Ekspresif Guide',
            'communication_style' => 'ekspresif',
        ]);

        Livewire::test(GuideSearch::class)
            ->set('communicationStyle', 'santai')
            ->assertSee($guide->name)
            ->assertDontSee('Ekspresif Guide');
    }

    // ── Page 2: Guide Detail ──────────────────────────────────────

    public function test_detail_page_shows_packages_and_reviews(): void
    {
        [$guide, $profile] = $this->makeVerifiedGuide();

        TourPackage::create([
            'guide_id' => $guide->id,
            'title' => 'Ubud Cultural Day Tour',
            'description' => 'Temples, rice terraces and local crafts.',
            'price' => 750000.00,
            'destinations' => ['Monkey Forest', 'Tegalalang'],
            'is_active' => true,
        ]);

        $customer = User::factory()->customer()->create();

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
            'pickup_location' => 'Kuta Hotel',
            'custom_destinations' => ['Ubud'],
            'schedule_date' => now()->addDays(1)->toDateString(),
            'pickup_time' => '09:00',
            'total_price' => 750000.00,
            'status' => BookingStatus::COMPLETED,
        ]);

        Review::create([
            'booking_id' => $booking->id,
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
            'rating' => 5,
            'comment' => 'Amazing guide, highly recommended!',
        ]);

        $this->get(route('guides.show', $profile))
            ->assertOk()
            ->assertSee($guide->name)
            ->assertSee('Ubud Cultural Day Tour')
            ->assertSee('Amazing guide, highly recommended!')
            ->assertSee(route('guides.book', $profile));
    }

    public function test_detail_page_returns_404_for_unverified_guide(): void
    {
        $guide = User::factory()->guide()->create();
        $profile = GuideProfile::factory()->create([
            'user_id' => $guide->id,
            'is_verified' => false,
        ]);

        $this->get(route('guides.show', $profile))->assertNotFound();
    }

    // ── Page 3: Booking ───────────────────────────────────────────

    public function test_booking_creates_pending_confirmation_and_redirects_to_dashboard(): void
    {
        [$guide, $profile] = $this->makeVerifiedGuide();
        $customer = User::factory()->customer()->create();

        Livewire::actingAs($customer)
            ->test(BookingForm::class, ['guideProfile' => $profile])
            ->set('pickupLocation', 'Sheraton Kuta Resort')
            ->set('customDestinations', ['Ubud Monkey Forest'])
            ->set('scheduleDate', now()->addDays(2)->toDateString())
            ->set('pickupTime', '08:00')
            ->call('submitBooking')
            ->assertRedirect(route('customer.trips'));

        $booking = Booking::first();
        $this->assertNotNull($booking);
        $this->assertEquals($customer->id, $booking->customer_id);
        $this->assertEquals($guide->id, $booking->guide_id);
        $this->assertEquals(BookingStatus::PENDING_CONFIRMATION, $booking->status);
        $this->assertEquals(500000.00, (float) $booking->total_price);

        // Escrow created but nothing released/payable yet.
        $escrow = EscrowTransaction::first();
        $this->assertEquals(EscrowStatus::WAITING_PAYMENT, $escrow->status);
    }

    public function test_booking_uses_selected_package_price(): void
    {
        [$guide, $profile] = $this->makeVerifiedGuide();
        $customer = User::factory()->customer()->create();

        $package = TourPackage::create([
            'guide_id' => $guide->id,
            'title' => 'Private Ubud Tour',
            'description' => 'Full day private tour.',
            'price' => 950000.00,
            'destinations' => ['Ubud'],
            'is_active' => true,
        ]);

        Livewire::actingAs($customer)
            ->test(BookingForm::class, ['guideProfile' => $profile])
            ->set('tourPackageId', $package->id)
            ->set('pickupLocation', 'Kuta Hotel')
            ->set('customDestinations', ['Ubud Palace'])
            ->set('scheduleDate', now()->addDays(3)->toDateString())
            ->set('pickupTime', '09:00')
            ->call('submitBooking');

        $booking = Booking::first();
        $this->assertEquals($package->id, $booking->tour_package_id);
        $this->assertEquals(950000.00, (float) $booking->total_price);
    }

    public function test_guests_are_redirected_to_login_from_booking_page(): void
    {
        [$guide, $profile] = $this->makeVerifiedGuide();

        $this->get(route('guides.book', $profile))
            ->assertRedirect(route('login'));
    }
}
