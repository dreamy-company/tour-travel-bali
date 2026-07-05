<?php

namespace App\Livewire\Guide;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Flux\Flux;
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

    /**
     * Accept a pending booking request.
     */
    public function acceptBooking(int $bookingId): void
    {
        $booking = Booking::where('guide_id', Auth::id())
            ->where('status', BookingStatus::PENDING_CONFIRMATION)
            ->find($bookingId);

        if (! $booking) {
            Flux::toast(variant: 'danger', text: __('Booking not found.'));
            return;
        }

        $booking->update([
            'status' => BookingStatus::CONFIRMED,
        ]);

        Flux::toast(variant: 'success', text: __('Booking accepted! Customer has been notified.'));
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
            Flux::toast(variant: 'danger', text: __('Booking not found.'));
            return;
        }

        $booking->update([
            'status' => BookingStatus::REJECTED,
        ]);

        Flux::toast(variant: 'warning', text: __('Booking request declined.'));
    }

    /**
     * Advance the booking status to the next sequential state.
     */
    public function advanceStatus(int $bookingId, string $nextState): void
    {
        $booking = Booking::where('guide_id', Auth::id())->find($bookingId);

        if (! $booking) {
            Flux::toast(variant: 'danger', text: __('Booking not found.'));
            return;
        }

        $allowedStates = [
            'heading_to_location' => BookingStatus::HEADING_TO_LOCATION,
            'ongoing' => BookingStatus::ONGOING,
            'completed' => BookingStatus::COMPLETED,
        ];

        if (! array_key_exists($nextState, $allowedStates)) {
            Flux::toast(variant: 'danger', text: __('Invalid state transition.'));
            return;
        }

        $booking->update([
            'status' => $allowedStates[$nextState],
        ]);

        Flux::toast(variant: 'success', text: __('Tour status updated to: :status', [
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
