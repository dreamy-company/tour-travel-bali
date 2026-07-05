<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\GuideProfile;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Customer\CustomerDashboard;
use Tests\TestCase;

class CustomerDashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test active trip view rendering.
     */
    public function test_dashboard_renders_active_trip_view_when_booking_active(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $guide = User::factory()->create(['role' => UserRole::GUIDE]);

        GuideProfile::factory()->create(['user_id' => $guide->id]);

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
            ->test(CustomerDashboard::class)
            ->assertSet('showFeedbackModal', false)
            ->assertSee('Active Tour Tracker')
            ->assertDontSee('Your Order History');
    }

    /**
     * Test history view rendering when no active trip.
     */
    public function test_dashboard_renders_history_view_when_no_active_trip(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $guide = User::factory()->create(['role' => UserRole::GUIDE]);

        GuideProfile::factory()->create(['user_id' => $guide->id]);

        // Create a completed booking
        $booking = Booking::create([
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
            'pickup_location' => 'Hotel Kuta',
            'custom_destinations' => ['Ubud'],
            'schedule_date' => now()->subDays(5)->toDateString(),
            'pickup_time' => '09:00',
            'total_price' => 500000.00,
            'status' => BookingStatus::COMPLETED,
        ]);

        Livewire::actingAs($customer)
            ->test(CustomerDashboard::class)
            ->assertDontSee('Active Tour Tracker')
            ->assertSee('Your Order History')
            ->assertSee('Leave Feedback');
    }

    /**
     * Test feedback form submission from dashboard.
     */
    public function test_can_submit_feedback_via_dashboard(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $guide = User::factory()->create(['role' => UserRole::GUIDE]);

        GuideProfile::factory()->create(['user_id' => $guide->id]);

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
            'pickup_location' => 'Hotel Kuta',
            'custom_destinations' => ['Ubud'],
            'schedule_date' => now()->subDays(5)->toDateString(),
            'pickup_time' => '09:00',
            'total_price' => 500000.00,
            'status' => BookingStatus::COMPLETED,
        ]);

        Livewire::actingAs($customer)
            ->test(CustomerDashboard::class)
            ->call('openFeedback', $booking->id)
            ->assertSet('showFeedbackModal', true)
            ->set('rating', 4)
            ->set('comment', 'Great tour experience!')
            ->call('submitFeedback')
            ->assertSet('showFeedbackModal', false);

        $this->assertTrue(Review::where('booking_id', $booking->id)->exists());
        $this->assertEquals(4, Review::where('booking_id', $booking->id)->first()->rating);
    }
}
