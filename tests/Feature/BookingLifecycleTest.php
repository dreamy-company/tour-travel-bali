<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\EscrowStatus;
use App\Enums\TariffMode;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Livewire\Customer\GuideSearch;
use App\Livewire\Guide\OrderManagement;
use App\Models\Booking;
use App\Models\EscrowTransaction;
use App\Models\GuideProfile;
use App\Models\GuideWallet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class BookingLifecycleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the complete lifecycle of a booking from creation to completion.
     */
    public function test_complete_booking_lifecycle(): void
    {
        Http::fake([
            'https://app.sandbox.midtrans.com/*' => Http::response([
                'token' => 'dummy-snap-token',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/dummy-snap-token',
            ], 200),
        ]);
        // 1. Create a customer
        $customer = User::factory()->create([
            'role' => UserRole::CUSTOMER,
            'status' => UserStatus::ACTIVE,
        ]);

        // 2. Create a guide
        $guide = User::factory()->create([
            'role' => UserRole::GUIDE,
            'status' => UserStatus::ACTIVE,
        ]);

        // 3. Create guide profile
        GuideProfile::factory()->create([
            'user_id' => $guide->id,
            'base_rate' => 500000.00,
            'tariff_mode' => TariffMode::DAILY,
            'is_verified' => true,
        ]);

        // 4. Create custom itinerary booking (as Customer)
        Livewire::actingAs($customer)
            ->test(GuideSearch::class)
            ->call('selectGuide', $guide->id)
            ->set('pickupLocation', 'Sheraton Kuta Resort')
            ->set('dropoffLocation', 'Sheraton Kuta Resort')
            ->set('customDestinations', ['Ubud Monkey Forest'])
            ->set('scheduleDate', now()->addDays(2)->toDateString())
            ->set('pickupTime', '08:00')
            ->call('book');

        // Verify booking creation in database
        $booking = Booking::first();
        $this->assertNotNull($booking);
        $this->assertEquals($customer->id, $booking->customer_id);
        $this->assertEquals($guide->id, $booking->guide_id);
        $this->assertEquals(BookingStatus::PENDING_CONFIRMATION, $booking->status);
        $this->assertEquals(500000.00, (float) $booking->total_price);

        $escrow = EscrowTransaction::first();
        $this->assertNotNull($escrow);
        $this->assertEquals($booking->id, $escrow->booking_id);
        $this->assertEquals(EscrowStatus::WAITING_PAYMENT, $escrow->status);
        $this->assertEquals(500000.00, (float) $escrow->gross_amount);

        // 5. Accept booking (as Guide)
        Livewire::actingAs($guide)
            ->test(OrderManagement::class)
            ->call('acceptBooking', $booking->id);

        $booking->refresh();
        $this->assertEquals(BookingStatus::WAITING_PAYMENT, $booking->status);

        // Simulate payment webhook to confirm transaction
        $orderId = 'BOOK-' . $booking->id . '-' . time();
        $statusCode = '200';
        $grossAmount = $booking->total_price;
        $serverKey = config('services.midtrans.server_key');
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $this->postJson(route('payment.webhook'), [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
        ])->assertOk();

        $booking->refresh();
        $this->assertEquals(BookingStatus::CONFIRMED, $booking->status);

        // 6. Progress status to heading_to_location
        Livewire::actingAs($guide)
            ->test(OrderManagement::class)
            ->call('advanceStatus', $booking->id, 'heading_to_location');

        $booking->refresh();
        $this->assertEquals(BookingStatus::HEADING_TO_LOCATION, $booking->status);

        // 7. Progress status to ongoing
        Livewire::actingAs($guide)
            ->test(OrderManagement::class)
            ->call('advanceStatus', $booking->id, 'ongoing');

        $booking->refresh();
        $this->assertEquals(BookingStatus::ONGOING, $booking->status);

        // 8. Progress status to completed
        Livewire::actingAs($guide)
            ->test(OrderManagement::class)
            ->call('advanceStatus', $booking->id, 'completed');

        $booking->refresh();
        $this->assertEquals(BookingStatus::COMPLETED, $booking->status);

        // 9. Verify Escrow and Wallet changes
        $escrow->refresh();
        $this->assertEquals(EscrowStatus::RELEASED_TO_GUIDE, $escrow->status);
        $this->assertEquals(50000.00, (float) $escrow->platform_commission);
        $this->assertEquals(450000.00, (float) $escrow->guide_net_amount);

        $wallet = GuideWallet::where('guide_id', $guide->id)->first();
        $this->assertNotNull($wallet);
        $this->assertEquals(450000.00, (float) $wallet->current_balance);
    }
}
