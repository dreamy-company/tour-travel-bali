<?php

namespace App\Livewire\Customer;

use App\Enums\BookingStatus;
use App\Enums\EscrowStatus;
use App\Models\Booking;
use App\Models\Review;
use App\Services\MidtransService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Customer Trips Hub (/my-bookings).
 *
 * Active trips with live status stepper, escrow payment, embedded chat,
 * and an auto-triggered post-tour review modal. Past orders in a table
 * with inline "Leave Review" actions.
 */
#[Layout('layouts.customer')]
#[Title('My Bookings')]
class CustomerTrips extends Component
{
    public string $activeTab = 'active';

    // ── Review modal state ────────────────────────────────────────
    public ?int $reviewBookingId = null;
    public int $rating = 5;
    public string $comment = '';
    public bool $showReviewModal = false;

    /**
     * Mount and auto-trigger the review modal for completed tours.
     */
    public function mount(): void
    {
        $this->checkForPendingReview();
    }

    /**
     * Polled refresh — keeps the stepper live and opens the review
     * modal the moment a tour reaches completed.
     */
    public function refreshTrips(): void
    {
        $this->checkForPendingReview();
    }

    /**
     * Open the review modal for the latest completed, unreviewed tour.
     */
    protected function checkForPendingReview(): void
    {
        if ($this->showReviewModal) {
            return;
        }

        $completed = Booking::where('customer_id', Auth::id())
            ->where('status', BookingStatus::COMPLETED)
            ->whereDoesntHave('review')
            ->latest()
            ->first();

        if ($completed && ! session()->has('feedback_skipped_' . $completed->id)) {
            $this->reviewBookingId = $completed->id;
            $this->rating = 5;
            $this->comment = '';
            $this->showReviewModal = true;
        }
    }

    /**
     * Open the review modal manually from the past-orders table.
     */
    public function openReview(int $bookingId): void
    {
        $booking = Booking::where('customer_id', Auth::id())
            ->where('status', BookingStatus::COMPLETED)
            ->whereDoesntHave('review')
            ->find($bookingId);

        if (! $booking) {
            session()->flash('error', __('This trip cannot be reviewed.'));
            return;
        }

        $this->reviewBookingId = $bookingId;
        $this->rating = 5;
        $this->comment = '';
        $this->showReviewModal = true;
    }

    /**
     * Skip the review prompt for this session.
     */
    public function skipReview(): void
    {
        if ($this->reviewBookingId) {
            session()->put('feedback_skipped_' . $this->reviewBookingId, true);
        }

        $this->showReviewModal = false;
        $this->reviewBookingId = null;
    }

    /**
     * Persist the 1–5 star rating + comment for a completed booking.
     */
    public function submitReview(): void
    {
        $this->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $booking = Booking::where('customer_id', Auth::id())
            ->where('status', BookingStatus::COMPLETED)
            ->find($this->reviewBookingId);

        if (! $booking) {
            $this->showReviewModal = false;
            session()->flash('error', __('Trip not found.'));
            return;
        }

        if (Review::where('booking_id', $booking->id)->exists()) {
            $this->showReviewModal = false;
            session()->flash('error', __('You have already reviewed this trip.'));
            return;
        }

        Review::create([
            'booking_id' => $booking->id,
            'customer_id' => Auth::id(),
            'guide_id' => $booking->guide_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
        ]);

        $this->showReviewModal = false;
        $this->reviewBookingId = null;

        session()->flash('success', __('Thank you! Your review helps future travelers.'));
    }

    /**
     * Redirect the customer to the escrow payment screen for a
     * waiting_payment booking (Midtrans Snap).
     */
    public function openPayment(int $bookingId, MidtransService $midtrans): void
    {
        $booking = Booking::with('escrowTransaction')
            ->where('customer_id', Auth::id())
            ->where('status', BookingStatus::WAITING_PAYMENT)
            ->find($bookingId);

        if (! $booking) {
            session()->flash('error', __('This booking is not awaiting payment.'));
            return;
        }

        $escrow = $booking->escrowTransaction;

        if ($escrow && $escrow->redirect_url) {
            $this->redirect($escrow->redirect_url);
            return;
        }

        try {
            $transaction = $midtrans->createSnapTransaction($booking);

            if ($escrow) {
                $escrow->update([
                    'snap_token' => $transaction['token'],
                    'redirect_url' => $transaction['redirect_url'],
                ]);
            }

            $this->redirect($transaction['redirect_url']);
        } catch (\Exception $e) {
            logger()->error('Failed to open payment screen: ' . $e->getMessage());
            session()->flash('error', __('Could not open the payment screen right now. Please try again.'));
        }
    }

    /**
     * Active (in-progress) bookings, newest first.
     *
     * @return Collection<int, Booking>
     */
    #[Computed]
    public function activeBookings(): Collection
    {
        return Booking::with(['guide.guideProfile', 'escrowTransaction', 'review'])
            ->where('customer_id', Auth::id())
            ->whereNotIn('status', [
                BookingStatus::COMPLETED,
                BookingStatus::CANCELLED,
                BookingStatus::REJECTED,
            ])
            ->latest()
            ->get();
    }

    /**
     * Past (completed / cancelled / declined) bookings.
     *
     * @return Collection<int, Booking>
     */
    #[Computed]
    public function pastBookings(): Collection
    {
        return Booking::with(['guide', 'review'])
            ->where('customer_id', Auth::id())
            ->whereIn('status', [
                BookingStatus::COMPLETED,
                BookingStatus::CANCELLED,
                BookingStatus::REJECTED,
            ])
            ->latest()
            ->get();
    }

    /**
     * Render the component view.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.customer.customer-trips');
    }
}
