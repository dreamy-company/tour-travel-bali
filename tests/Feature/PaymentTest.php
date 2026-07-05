<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\EscrowStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Booking;
use App\Models\EscrowTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.midtrans.server_key' => 'SB-Mid-server-defaultkey']);
    }

    /**
     * Test generating a secure checkout Snap token.
     */
    public function test_customer_can_generate_checkout_token(): void
    {
        Http::fake([
            'https://app.sandbox.midtrans.com/*' => Http::response([
                'token' => 'test-snap-token-123',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/test-snap-token-123',
            ], 200),
        ]);

        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $guide = User::factory()->create(['role' => UserRole::GUIDE]);

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
            'pickup_location' => 'Hotel Kuta',
            'custom_destinations' => ['Ubud'],
            'schedule_date' => now()->addDays(2)->toDateString(),
            'pickup_time' => '09:00',
            'total_price' => 500000.00,
            'status' => BookingStatus::WAITING_PAYMENT,
        ]);

        $this->actingAs($customer);

        $response = $this->postJson(route('bookings.checkout', $booking));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'token' => 'test-snap-token-123',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/test-snap-token-123',
            ]);
    }

    /**
     * Test checkout authorization.
     */
    public function test_unauthorized_user_cannot_checkout(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $otherUser = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $guide = User::factory()->create(['role' => UserRole::GUIDE]);

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
            'pickup_location' => 'Hotel Kuta',
            'custom_destinations' => ['Ubud'],
            'schedule_date' => now()->addDays(2)->toDateString(),
            'pickup_time' => '09:00',
            'total_price' => 500000.00,
            'status' => BookingStatus::WAITING_PAYMENT,
        ]);

        $this->actingAs($otherUser);

        $response = $this->postJson(route('bookings.checkout', $booking));

        $response->assertStatus(403);
    }

    /**
     * Test secure webhook with valid signature and status updates.
     */
    public function test_webhook_confirms_booking_on_settlement(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $guide = User::factory()->create(['role' => UserRole::GUIDE]);

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
            'pickup_location' => 'Hotel Kuta',
            'custom_destinations' => ['Ubud'],
            'schedule_date' => now()->addDays(2)->toDateString(),
            'pickup_time' => '09:00',
            'total_price' => 500000.00,
            'status' => BookingStatus::WAITING_PAYMENT,
        ]);

        $escrow = EscrowTransaction::create([
            'booking_id' => $booking->id,
            'transaction_reference' => 'TXN-REF-123',
            'payment_method' => 'qris',
            'gross_amount' => 500000.00,
            'platform_commission' => 50000.00,
            'guide_net_amount' => 450000.00,
            'status' => EscrowStatus::WAITING_PAYMENT,
        ]);

        $orderId = 'BOOK-' . $booking->id . '-12345';
        $statusCode = '200';
        $grossAmount = '500000';
        $serverKey = 'SB-Mid-server-defaultkey';
        
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $payload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
        ];

        // Send public webhook request (No Auth)
        $response = $this->postJson(route('payment.webhook'), $payload);

        $response->assertOk()
            ->assertJson(['success' => true]);

        $booking->refresh();
        $escrow->refresh();

        $this->assertEquals(BookingStatus::CONFIRMED, $booking->status);
        $this->assertEquals(EscrowStatus::PAID_IN_ESCROW, $escrow->status);
    }

    /**
     * Test webhook rejects invalid signature.
     */
    public function test_webhook_rejects_invalid_signature(): void
    {
        $payload = [
            'order_id' => 'BOOK-1-12345',
            'status_code' => '200',
            'gross_amount' => '500000',
            'signature_key' => 'invalid-signature-key-here',
            'transaction_status' => 'settlement',
        ];

        $response = $this->postJson(route('payment.webhook'), $payload);

        $response->assertStatus(403);
    }
}
