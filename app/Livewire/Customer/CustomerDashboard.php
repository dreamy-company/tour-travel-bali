<?php

namespace App\Livewire\Customer;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CustomerDashboard extends Component
{
    // Feedback Form State
    public ?int $feedbackBookingId = null;
    public int $rating = 5;
    public string $comment = '';
    public bool $showFeedbackModal = false;

    /**
     * Polled refresh to update dashboard status in real-time.
     */
    public function refreshDashboard(): void
    {
        // Polling will naturally refresh the state and re-render.
        // Also check if latest active booking was completed to auto-trigger review feedback modal
        $latestBooking = Booking::where('customer_id', Auth::id())
            ->latest()
            ->first();

        if ($latestBooking && $latestBooking->status === BookingStatus::COMPLETED) {
            $hasReview = Review::where('booking_id', $latestBooking->id)
                ->where('customer_id', Auth::id())
                ->exists();

            if (! $hasReview && ! session()->has('feedback_skipped_' . $latestBooking->id)) {
                $this->feedbackBookingId = $latestBooking->id;
                $this->showFeedbackModal = true;
            }
        }
    }

    /**
     * Open review dialog manually.
     *
     * @param int $bookingId
     */
    public function openFeedback(int $bookingId): void
    {
        $booking = Booking::where('customer_id', Auth::id())->find($bookingId);

        if ($booking && $booking->status === BookingStatus::COMPLETED) {
            $this->feedbackBookingId = $bookingId;
            $this->rating = 5;
            $this->comment = '';
            $this->showFeedbackModal = true;
        }
    }

    /**
     * Skip feedback for this session.
     */
    public function skipFeedback(): void
    {
        if ($this->feedbackBookingId) {
            session()->put('feedback_skipped_' . $this->feedbackBookingId, true);
        }
        $this->showFeedbackModal = false;
        $this->feedbackBookingId = null;
    }

    /**
     * Submit review.
     */
    public function submitFeedback(): void
    {
        $this->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $booking = Booking::where('customer_id', Auth::id())->find($this->feedbackBookingId);

        if (! $booking) {
            $this->showFeedbackModal = false;
            session()->flash('error', __('Booking not found.'));
            return;
        }

        $exists = Review::where('booking_id', $booking->id)->exists();
        if ($exists) {
            $this->showFeedbackModal = false;
            session()->flash('error', __('Feedback already submitted for this booking.'));
            return;
        }

        Review::create([
            'booking_id' => $booking->id,
            'customer_id' => Auth::id(),
            'guide_id' => $booking->guide_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
        ]);

        $this->showFeedbackModal = false;
        $this->feedbackBookingId = null;
        $this->rating = 5;
        $this->comment = '';

        session()->flash('success', __('Thank you for your review!'));
    }

    /**
     * Render Customer Dashboard view.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        $customerId = Auth::id();

        // 1. Get active trip (status is not completed or rejected)
        $activeBooking = Booking::with(['guide.guideProfile', 'escrowTransaction'])
            ->where('customer_id', $customerId)
            ->whereNotIn('status', [BookingStatus::COMPLETED, BookingStatus::REJECTED])
            ->latest()
            ->first();

        // 2. Get past booking history (completed or rejected)
        $pastBookings = Booking::with('guide')
            ->where('customer_id', $customerId)
            ->whereIn('status', [BookingStatus::COMPLETED, BookingStatus::REJECTED])
            ->latest()
            ->get()
            ->map(function ($booking) {
                $booking->has_review = Review::where('booking_id', $booking->id)->exists();
                return $booking;
            });

        return view('livewire.customer.customer-dashboard', [
            'activeBooking' => $activeBooking,
            'pastBookings' => $pastBookings,
        ]);
    }
}
