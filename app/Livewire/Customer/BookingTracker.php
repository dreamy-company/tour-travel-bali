<?php

namespace App\Livewire\Customer;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.customer')]
#[Title('Booking Tracker')]
class BookingTracker extends Component
{
    public ?Booking $booking = null;

    // Review Form State
    public int $rating = 5;
    public string $comment = '';
    public bool $showReviewModal = false;

    /**
     * Mount the component and authorize user access.
     */
    public function mount(?Booking $booking = null): void
    {
        if ($booking && $booking->exists) {
            if ($booking->customer_id !== Auth::id()) {
                abort(403, 'Unauthorized access to this booking tracker.');
            }
            $this->booking = $booking;
        } else {
            // Find latest active booking where status is NOT completed or rejected
            $activeBooking = Booking::where('customer_id', Auth::id())
                ->whereNotIn('status', [BookingStatus::COMPLETED, BookingStatus::REJECTED])
                ->latest()
                ->first();

            if ($activeBooking) {
                $this->booking = $activeBooking;
            }
        }

        if ($this->booking) {
            $this->checkReviewStatus();
        }
    }

    /**
     * Refresh booking instance (polled dynamically in the view).
     */
    public function refreshBooking(): void
    {
        if ($this->booking) {
            $this->booking->refresh();
            $this->checkReviewStatus();
        } else {
            $activeBooking = Booking::where('customer_id', Auth::id())
                ->whereNotIn('status', [BookingStatus::COMPLETED, BookingStatus::REJECTED])
                ->latest()
                ->first();

            if ($activeBooking) {
                $this->booking = $activeBooking;
                $this->checkReviewStatus();
            }
        }
    }

    /**
     * Check if the tour is completed and if the customer has not left a review yet.
     */
    protected function checkReviewStatus(): void
    {
        if ($this->booking->status === BookingStatus::COMPLETED) {
            $hasReview = Review::where('booking_id', $this->booking->id)
                ->where('customer_id', Auth::id())
                ->exists();

            if (! $hasReview) {
                $this->showReviewModal = true;
            } else {
                $this->showReviewModal = false;
            }
        }
    }

    /**
     * Submit rating and comment feedback.
     */
    public function submitReview(): void
    {
        $this->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        // Double check review existence
        $exists = Review::where('booking_id', $this->booking->id)->exists();
        if ($exists) {
            $this->showReviewModal = false;
            session()->flash('error', __('You have already submitted a review for this booking.'));
            return;
        }

        Review::create([
            'booking_id' => $this->booking->id,
            'customer_id' => Auth::id(),
            'guide_id' => $this->booking->guide_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
        ]);

        $this->showReviewModal = false;
        session()->flash('success', __('Thank you! Your review has been submitted successfully.'));
    }

    /**
     * Render the component view.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.customer.booking-tracker');
    }
}
