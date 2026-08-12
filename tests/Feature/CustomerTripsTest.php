<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\EscrowStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\EscrowTransaction;
use App\Models\GuideProfile;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Customer\CustomerTrips;
use Tests\TestCase;

class CustomerTripsTest extends TestCase
{
    use RefreshDatabase;

    private function makeCustomerAndBooking(BookingStatus $status): array
    {
        $customer = User::factory()->customer()->create();
        $guide = User::factory()->guide()->create();
        GuideProfile::factory()->create(['user_id' => $guide->id, 'is_verified' => true]);

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
            'pickup_location' => 'Kuta Hotel',
            'custom_destinations' => ['Ubud'],
            'schedule_date' => now()->addDays(2)->toDateString(),
            'pickup_time' => '09:00',
            'total_price' => 500000.00,
            'status' => $status,
        ]);

        return [$customer, $guide, $booking];
    }

    public function test_active_trips_are_listed_with_stepper(): void
    {
        [$customer, $guide, $booking] = $this->makeCustomerAndBooking(BookingStatus::PENDING_CONFIRMATION);

        Livewire::actingAs($customer)
            ->test(CustomerTrips::class)
            ->assertSee('Request Sent')
            ->assertSee($guide->name)
            ->assertSee('Waiting for guide approval');
    }

    public function test_waiting_payment_booking_shows_escrow_payment_button(): void
    {
        [$customer, , $booking] = $this->makeCustomerAndBooking(BookingStatus::WAITING_PAYMENT);

        EscrowTransaction::create([
            'booking_id' => $booking->id,
            'transaction_reference' => 'TXN-00000001-ABCD',
            'payment_method' => 'qris',
            'gross_amount' => 500000.00,
            'platform_commission' => 50000.00,
            'guide_net_amount' => 450000.00,
            'status' => EscrowStatus::WAITING_PAYMENT,
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/dummy-token',
        ]);

        Livewire::actingAs($customer)
            ->test(CustomerTrips::class)
            ->assertSee('Pay via Escrow');
    }

    public function test_completed_booking_auto_triggers_review_modal(): void
    {
        [$customer, $guide, $booking] = $this->makeCustomerAndBooking(BookingStatus::COMPLETED);

        Livewire::actingAs($customer)
            ->test(CustomerTrips::class)
            ->assertSet('showReviewModal', true)
            ->assertSet('reviewBookingId', $booking->id);
    }

    public function test_review_can_be_submitted_from_modal(): void
    {
        [$customer, $guide, $booking] = $this->makeCustomerAndBooking(BookingStatus::COMPLETED);

        Livewire::actingAs($customer)
            ->test(CustomerTrips::class)
            ->assertSet('showReviewModal', true)
            ->set('rating', 5)
            ->set('comment', 'An unforgettable experience with a great local guide!')
            ->call('submitReview')
            ->assertSet('showReviewModal', false);

        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
            'rating' => 5,
        ]);
    }

    public function test_past_orders_tab_lists_completed_with_review_action(): void
    {
        [$customer, $guide, $booking] = $this->makeCustomerAndBooking(BookingStatus::COMPLETED);

        Livewire::actingAs($customer)
            ->test(CustomerTrips::class)
            ->set('activeTab', 'past')
            ->assertSee($guide->name)
            ->assertSee('Leave Review');
    }

    public function test_already_reviewed_trip_shows_rating_not_button(): void
    {
        [$customer, $guide, $booking] = $this->makeCustomerAndBooking(BookingStatus::COMPLETED);

        Review::create([
            'booking_id' => $booking->id,
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
            'rating' => 4,
            'comment' => 'Great trip!',
        ]);

        Livewire::actingAs($customer)
            ->test(CustomerTrips::class)
            ->set('activeTab', 'past')
            ->assertDontSee('Leave Review')
            ->assertSee('4');
    }

    public function test_guest_cannot_access_trips_hub(): void
    {
        $this->get(route('customer.trips'))->assertRedirect(route('login'));
    }
}
