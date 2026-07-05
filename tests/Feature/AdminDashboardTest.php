<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\EscrowStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\WithdrawalStatus;
use App\Models\Booking;
use App\Models\EscrowTransaction;
use App\Models\GuideProfile;
use App\Models\GuideWallet;
use App\Models\Review;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Admin\AdminDashboard;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guide;
    protected GuideProfile $guideProfile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->guide = User::factory()->create([
            'role' => UserRole::GUIDE,
            'status' => UserStatus::SUSPENDED,
        ]);

        $this->guideProfile = GuideProfile::factory()->create([
            'user_id' => $this->guide->id,
            'is_verified' => false,
            'rejection_reason' => null,
            'strikes' => 0,
        ]);
    }

    /**
     * Test admin can load dashboard and see widgets.
     */
    public function test_admin_can_load_dashboard_and_see_widgets(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class)
            ->assertSee('KYC Verification Queue')
            ->assertSee('Withdrawal Approvals')
            ->assertSee('Dispute Center Tracker')
            ->assertSee('Guide Performance & Sanctions');
    }

    /**
     * Test KYC Approval flow.
     */
    public function test_admin_can_approve_guide_kyc(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class)
            ->call('approveGuide', $this->guideProfile->id);

        $this->guideProfile->refresh();
        $this->guide->refresh();

        $this->assertTrue($this->guideProfile->is_verified);
        $this->assertEquals(UserStatus::ACTIVE, $this->guide->status);
    }

    /**
     * Test KYC Rejection flow.
     */
    public function test_admin_can_reject_guide_kyc(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class)
            ->set('rejectionReason', 'KTP document is blurry and unreadable.')
            ->call('rejectGuide', $this->guideProfile->id);

        $this->guideProfile->refresh();
        $this->guide->refresh();

        $this->assertFalse($this->guideProfile->is_verified);
        $this->assertEquals('KTP document is blurry and unreadable.', $this->guideProfile->rejection_reason);
        $this->assertEquals(UserStatus::SUSPENDED, $this->guide->status);
    }

    /**
     * Test Payout approvals.
     */
    public function test_admin_can_approve_withdrawal_payout(): void
    {
        $withdrawal = Withdrawal::create([
            'guide_id' => $this->guide->id,
            'amount' => 300000.00,
            'bank_name' => 'BCA',
            'bank_account_number' => '12345678',
            'bank_account_name' => 'Wayan Bali',
            'status' => WithdrawalStatus::PENDING,
        ]);

        Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class)
            ->call('approvePayout', $withdrawal->id);

        $withdrawal->refresh();
        $this->assertEquals(WithdrawalStatus::SUCCESS, $withdrawal->status);
    }

    /**
     * Test Payout rejection and refund.
     */
    public function test_admin_can_reject_withdrawal_payout_and_refund_wallet(): void
    {
        $withdrawal = Withdrawal::create([
            'guide_id' => $this->guide->id,
            'amount' => 300000.00,
            'bank_name' => 'BCA',
            'bank_account_number' => '12345678',
            'bank_account_name' => 'Wayan Bali',
            'status' => WithdrawalStatus::PENDING,
        ]);

        $wallet = GuideWallet::create([
            'guide_id' => $this->guide->id,
            'current_balance' => 100000.00,
        ]);

        Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class)
            ->call('rejectPayout', $withdrawal->id);

        $withdrawal->refresh();
        $wallet->refresh();

        $this->assertEquals(WithdrawalStatus::FAILED, $withdrawal->status);
        $this->assertEquals(400000.00, (float) $wallet->current_balance);
    }

    /**
     * Test dispute centers refund to customer.
     */
    public function test_admin_can_refund_customer_for_disputed_booking(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'guide_id' => $this->guide->id,
            'pickup_location' => 'Hotel Ubud',
            'custom_destinations' => ['Kintamani'],
            'schedule_date' => now()->toDateString(),
            'pickup_time' => '10:00',
            'total_price' => 500000.00,
            'status' => BookingStatus::DISPUTED,
        ]);

        $escrow = EscrowTransaction::create([
            'booking_id' => $booking->id,
            'transaction_reference' => 'TXN-DISPUTE-1',
            'payment_method' => 'qris',
            'gross_amount' => 500000.00,
            'platform_commission' => 50000.00,
            'guide_net_amount' => 450000.00,
            'status' => EscrowStatus::PAID_IN_ESCROW,
        ]);

        Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class)
            ->call('refundCustomer', $booking->id);

        $booking->refresh();
        $escrow->refresh();

        $this->assertEquals(BookingStatus::COMPLETED, $booking->status);
        $this->assertEquals(EscrowStatus::REFUNDED, $escrow->status);
    }

    /**
     * Test strikes and bans.
     */
    public function test_admin_can_issue_strike_and_ban_guide(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class)
            ->call('issueStrike', $this->guide->id)
            ->call('banGuide', $this->guide->id);

        $this->guideProfile->refresh();
        $this->guide->refresh();

        $this->assertEquals(1, $this->guideProfile->strikes);
        $this->assertEquals(UserStatus::BANNED, $this->guide->status);
    }
}
