<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Customer\BookingTracker;
use Tests\TestCase;

class BookingTrackerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test tracker displays the active booking when visited without parameters.
     */
    public function test_tracker_displays_latest_active_booking(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $guide = User::factory()->create(['role' => UserRole::GUIDE]);

        \App\Models\GuideProfile::factory()->create([
            'user_id' => $guide->id,
        ]);

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
            'pickup_location' => 'Hotel Kuta',
            'custom_destinations' => ['Ubud'],
            'schedule_date' => now()->addDays(2)->toDateString(),
            'pickup_time' => '09:00',
            'total_price' => 500000.00,
            'status' => BookingStatus::CONFIRMED,
        ]);

        Livewire::actingAs($customer)
            ->test(BookingTracker::class)
            ->assertSet('booking.id', $booking->id)
            ->assertDontSee('No Active Trips');
    }

    /**
     * Test tracker displays placeholder when there are no active bookings.
     */
    public function test_tracker_displays_placeholder_for_no_active_bookings(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);

        Livewire::actingAs($customer)
            ->test(BookingTracker::class)
            ->assertSet('booking', null)
            ->assertSee('No Active Trips');
    }
}
