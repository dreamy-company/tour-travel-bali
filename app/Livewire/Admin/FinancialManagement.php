<?php

namespace App\Livewire\Admin;

use App\Enums\BookingStatus;
use App\Enums\EscrowStatus;
use App\Enums\WithdrawalStatus;
use App\Models\Booking;
use App\Models\GuideWallet;
use App\Models\Withdrawal;
use App\Services\EscrowReleaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Financial & Dispute Management')]
class FinancialManagement extends Component
{
    // ── Confirmation Guards ──────────────────────────────────────────────────
    // Stores the ID being confirmed so a second-click confirm UX can be built.
    public ?int $confirmingWithdrawalId = null;
    public ?int $confirmingDisputeId = null;

    // ── Private Data Fetchers (never exposed to Livewire serialization) ──────

    /**
     * All pending withdrawal requests, newest first.
     *
     * @return Collection<int, Withdrawal>
     */
    private function fetchPendingWithdrawals(): Collection
    {
        return Withdrawal::with('guide')
            ->where('status', WithdrawalStatus::PENDING)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * All disputed bookings with their escrow and customer/guide info.
     *
     * @return Collection<int, Booking>
     */
    private function fetchDisputedBookings(): Collection
    {
        return Booking::with(['customer', 'guide', 'escrowTransaction'])
            ->where('status', BookingStatus::DISPUTED)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    // ── Withdrawal Queue Actions ─────────────────────────────────────────────

    /**
     * Approve a pending withdrawal: mark as success.
     */
    public function approvePayout(int $withdrawalId): void
    {
        $withdrawal = Withdrawal::where('id', $withdrawalId)
            ->where('status', WithdrawalStatus::PENDING)
            ->firstOrFail();

        $withdrawal->update(['status' => WithdrawalStatus::SUCCESS]);

        $this->confirmingWithdrawalId = null;
        session()->flash('success', __('Payout approved. Funds will be transferred to :name.', ['name' => $withdrawal->bank_account_name]));
    }

    /**
     * Reject a pending withdrawal: mark as failed and refund the amount
     * back to the guide's wallet atomically.
     */
    public function rejectPayout(int $withdrawalId): void
    {
        $withdrawal = Withdrawal::where('id', $withdrawalId)
            ->where('status', WithdrawalStatus::PENDING)
            ->firstOrFail();

        DB::transaction(function () use ($withdrawal): void {
            // 1. Mark the withdrawal as failed
            $withdrawal->update(['status' => WithdrawalStatus::FAILED]);

            // 2. Refund the amount back to the guide's wallet
            $wallet = GuideWallet::firstOrCreate(
                ['guide_id' => $withdrawal->guide_id],
                ['current_balance' => 0.00]
            );

            $wallet->increment('current_balance', (float) $withdrawal->amount);
        });

        $this->confirmingWithdrawalId = null;
        session()->flash('success', __('Payout rejected and Rp :amount refunded to guide wallet.', [
            'amount' => number_format((float) $withdrawal->amount, 0, ',', '.'),
        ]));
    }

    // ── Dispute Center Actions ───────────────────────────────────────────────

    /**
     * Admin override: release escrow to guide (90% net after 10% commission).
     */
    public function releaseToGuide(int $bookingId, EscrowReleaseService $escrowService): void
    {
        $booking = Booking::with(['escrowTransaction'])
            ->where('id', $bookingId)
            ->where('status', BookingStatus::DISPUTED)
            ->firstOrFail();

        $escrowService->releaseForDispute($booking);

        $this->confirmingDisputeId = null;
        session()->flash('success', __('Escrow released to guide. Booking #:id resolved.', ['id' => $bookingId]));
    }

    /**
     * Admin override: refund 100% of gross amount back to the customer.
     * Updates escrow status to refunded and marks booking as completed.
     */
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

            // Resolve the dispute — mark the booking as completed
            $booking->update(['status' => BookingStatus::COMPLETED]);
        });

        $this->confirmingDisputeId = null;
        session()->flash('success', __('Full refund issued to customer for Booking #:id.', ['id' => $bookingId]));
    }

    /**
     * Render the component — all Eloquent collections are passed through
     * render() to keep them out of Livewire's validation/serialization pipeline.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.financial-management', [
            'pendingWithdrawals' => $this->fetchPendingWithdrawals(),
            'disputedBookings' => $this->fetchDisputedBookings(),
        ]);
    }
}
