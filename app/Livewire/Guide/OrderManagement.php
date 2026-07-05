<?php

namespace App\Livewire\Guide;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('My Orders')]
class OrderManagement extends Component
{
    /**
     * Get pending booking requests for the guide.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Booking>
     */
    #[Computed]
    public function pendingBookings()
    {
        return Booking::with('customer')
            ->where('guide_id', Auth::id())
            ->where('status', BookingStatus::PENDING_CONFIRMATION)
            ->latest()
            ->get();
    }

    /**
     * Get accepted/processed bookings for the guide.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Booking>
     */
    #[Computed]
    public function ongoingBookings()
    {
        return Booking::with(['customer', 'escrowTransaction'])
            ->where('guide_id', Auth::id())
            ->where('status', '!=', BookingStatus::PENDING_CONFIRMATION)
            ->latest()
            ->get();
    }

    public function acceptBooking(int $bookingId): void
    {
        $booking = Booking::where('guide_id', Auth::id())
            ->where('status', BookingStatus::PENDING_CONFIRMATION)
            ->find($bookingId);

        if (! $booking) {
            session()->flash('error', __('Booking not found.'));
            return;
        }

        try {
            // 1. Generate the Midtrans checkout snap token and redirect URL
            $midtrans = app(\App\Services\MidtransService::class);
            $transaction = $midtrans->createSnapTransaction($booking);

            // 2. Update booking and escrow transaction inside a DB transaction
            \Illuminate\Support\Facades\DB::transaction(function () use ($booking, $transaction): void {
                $booking->update([
                    'status' => BookingStatus::WAITING_PAYMENT,
                ]);

                if ($booking->escrowTransaction) {
                    $booking->escrowTransaction->update([
                        'snap_token' => $transaction['token'],
                        'redirect_url' => $transaction['redirect_url'],
                    ]);
                }
            });

            session()->flash('success', __('Booking accepted! Awaiting customer payment.'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to generate Midtrans snap token on accept: ' . $e->getMessage());
            session()->flash('error', __('Failed to accept booking because payment checkout could not be generated. Please try again.'));
        }
    }

    /**
     * Reject a pending booking request.
     */
    public function rejectBooking(int $bookingId): void
    {
        $booking = Booking::where('guide_id', Auth::id())
            ->where('status', BookingStatus::PENDING_CONFIRMATION)
            ->find($bookingId);

        if (! $booking) {
            session()->flash('error', __('Booking not found.'));
            return;
        }

        $booking->update([
            'status' => BookingStatus::REJECTED,
        ]);

        session()->flash('success', __('Booking request declined.'));
    }

    /**
     * Advance the booking status to the next sequential state.
     */
    public function advanceStatus(int $bookingId, string $nextState, \App\Services\EscrowReleaseService $releaseService): void
    {
        $booking = Booking::where('guide_id', Auth::id())->find($bookingId);

        if (! $booking) {
            session()->flash('error', __('Booking not found.'));
            return;
        }

        $allowedStates = [
            'heading_to_location' => BookingStatus::HEADING_TO_LOCATION,
            'ongoing' => BookingStatus::ONGOING,
            'completed' => BookingStatus::COMPLETED,
        ];

        if (! array_key_exists($nextState, $allowedStates)) {
            session()->flash('error', __('Invalid state transition.'));
            return;
        }

        // Run updates inside transaction
        try {
            $booking->update([
                'status' => $allowedStates[$nextState],
            ]);

            if ($nextState === 'completed') {
                $releaseService->release($booking);
            }
        } catch (\Exception $e) {
            session()->flash('error', __('Failed to update tour: :error', ['error' => $e->getMessage()]));
            return;
        }

        session()->flash('success', __('Tour status updated to: :status', [
            'status' => ucfirst(str_replace('_', ' ', $nextState)),
        ]));
    }

    /**
     * Render the component view.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.guide.order-management');
    }
}
