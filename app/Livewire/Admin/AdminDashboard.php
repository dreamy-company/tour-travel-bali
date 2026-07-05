<?php

namespace App\Livewire\Admin;

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
use App\Services\EscrowReleaseService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Super Admin Dashboard')]
class AdminDashboard extends Component
{
    // Vetting Detailed View Modal State
    public ?int $selectedGuideProfileId = null;
    public string $rejectionReason = '';

    // Sanction Modal State
    public ?int $sanctionGuideId = null;
    public string $sanctionGuideName = '';

    // Polled dashboard refresh
    public function refreshDashboard(): void
    {
        // Polling will naturally trigger re-render of dynamic metrics and records
    }

    // ── 1. KYC / Document Vetting actions ──

    public function selectGuideProfile(int $profileId): void
    {
        $this->selectedGuideProfileId = $profileId;
        $this->rejectionReason = '';
    }

    public function closeVetting(): void
    {
        $this->selectedGuideProfileId = null;
        $this->rejectionReason = '';
    }

    public function approveGuide(int $profileId): void
    {
        $profile = GuideProfile::findOrFail($profileId);

        DB::transaction(function () use ($profile): void {
            $profile->update([
                'is_verified' => true,
                'rejection_reason' => null,
            ]);

            $profile->user->update([
                'status' => UserStatus::ACTIVE,
            ]);
        });

        $this->closeVetting();
        session()->flash('success', __('Guide profile and documents approved successfully.'));
    }

    public function rejectGuide(int $profileId): void
    {
        $this->validate([
            'rejectionReason' => ['required', 'string', 'min:5', 'max:255'],
        ]);

        $profile = GuideProfile::findOrFail($profileId);

        DB::transaction(function () use ($profile): void {
            $profile->update([
                'is_verified' => false,
                'rejection_reason' => $this->rejectionReason,
            ]);

            $profile->user->update([
                'status' => UserStatus::SUSPENDED,
            ]);
        });

        $this->closeVetting();
        session()->flash('success', __('Guide profile rejected. Notification sent with reason.'));
    }

    // ── 2. Withdrawal Approvals ──

    public function approvePayout(int $withdrawalId): void
    {
        $withdrawal = Withdrawal::where('id', $withdrawalId)
            ->where('status', WithdrawalStatus::PENDING)
            ->firstOrFail();

        $withdrawal->update(['status' => WithdrawalStatus::SUCCESS]);

        session()->flash('success', __('Payout approved. Transferred to :name.', ['name' => $withdrawal->bank_account_name]));
    }

    public function rejectPayout(int $withdrawalId): void
    {
        $withdrawal = Withdrawal::where('id', $withdrawalId)
            ->where('status', WithdrawalStatus::PENDING)
            ->firstOrFail();

        DB::transaction(function () use ($withdrawal): void {
            $withdrawal->update(['status' => WithdrawalStatus::FAILED]);

            $wallet = GuideWallet::firstOrCreate(
                ['guide_id' => $withdrawal->guide_id],
                ['current_balance' => 0.00]
            );

            $wallet->increment('current_balance', (float) $withdrawal->amount);
        });

        session()->flash('success', __('Payout rejected and Rp :amount refunded to guide wallet.', [
            'amount' => number_format((float) $withdrawal->amount, 0, ',', '.'),
        ]));
    }

    // ── 3. Dispute Override options ──

    public function releaseToGuide(int $bookingId, EscrowReleaseService $escrowService): void
    {
        $booking = Booking::with(['escrowTransaction'])
            ->where('id', $bookingId)
            ->where('status', BookingStatus::DISPUTED)
            ->firstOrFail();

        $escrowService->releaseForDispute($booking);

        session()->flash('success', __('Escrow released to guide. Booking resolved.'));
    }

    public function refundCustomer(int $bookingId): void
    {
        $booking = Booking::with(['escrowTransaction'])
            ->where('id', $bookingId)
            ->where('status', BookingStatus::DISPUTED)
            ->firstOrFail();

        DB::transaction(function () use ($booking): void {
            $escrow = $booking->escrowTransaction;

            if ($escrow) {
                $escrow->update(['status' => EscrowStatus::REFUNDED]);
            }

            $booking->update(['status' => BookingStatus::COMPLETED]);
        });

        session()->flash('success', __('Full refund issued to customer. Booking resolved.'));
    }

    // ── 4. Guide Sanctions & Strike actions ──

    public function issueStrike(int $guideId): void
    {
        $guide = User::where('role', UserRole::GUIDE)->findOrFail($guideId);
        $profile = $guide->guideProfile;

        if ($profile) {
            $profile->increment('strikes');
            session()->flash('success', __('Issued 1 compliance strike to :name (Total: :strikes).', ['name' => $guide->name, 'strikes' => $profile->strikes]));
        } else {
            session()->flash('error', __('Guide profile not found.'));
        }
    }

    public function banGuide(int $guideId): void
    {
        $guide = User::where('role', UserRole::GUIDE)->findOrFail($guideId);

        $guide->update([
            'status' => UserStatus::BANNED,
        ]);

        session()->flash('success', __('Guide :name has been permanently banned from the platform.', ['name' => $guide->name]));
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        // 1. Stats Metrics
        $totalCustomers = User::where('role', UserRole::CUSTOMER)->where('status', UserStatus::ACTIVE)->count();
        $verifiedGuides = User::where('role', UserRole::GUIDE)->where('status', UserStatus::ACTIVE)->whereHas('guideProfile', fn($q) => $q->where('is_verified', true))->count();
        $activeEscrowBookings = Booking::whereNotIn('status', [BookingStatus::COMPLETED, BookingStatus::REJECTED, BookingStatus::DISPUTED])->count();
        $totalEscrowFunds = EscrowTransaction::where('status', EscrowStatus::PAID_IN_ESCROW)->sum('gross_amount');

        // 2. KYC verification queue
        $pendingGuides = GuideProfile::with('user')
            ->where('is_verified', false)
            ->whereNull('rejection_reason')
            ->latest()
            ->get();

        // 3. Pending withdrawals
        $pendingWithdrawals = Withdrawal::with('guide')
            ->where('status', WithdrawalStatus::PENDING)
            ->latest()
            ->get();

        // 4. Disputed bookings
        $disputedBookings = Booking::with(['customer', 'guide', 'escrowTransaction'])
            ->where('status', BookingStatus::DISPUTED)
            ->latest()
            ->get();

        // 5. Low-rated reviews (3 stars or less)
        $lowRatedReviews = Review::with(['booking', 'customer', 'guide.guideProfile'])
            ->where('rating', '<=', 3)
            ->latest()
            ->get();

        // Detailed guide profile selection
        $selectedProfile = $this->selectedGuideProfileId 
            ? GuideProfile::with('user')->find($this->selectedGuideProfileId) 
            : null;

        return view('livewire.admin.admin-dashboard', [
            'totalCustomers' => $totalCustomers,
            'verifiedGuides' => $verifiedGuides,
            'activeEscrowBookings' => $activeEscrowBookings,
            'totalEscrowFunds' => $totalEscrowFunds,
            'pendingGuides' => $pendingGuides,
            'pendingWithdrawals' => $pendingWithdrawals,
            'disputedBookings' => $disputedBookings,
            'lowRatedReviews' => $lowRatedReviews,
            'selectedProfile' => $selectedProfile,
        ]);
    }
}
