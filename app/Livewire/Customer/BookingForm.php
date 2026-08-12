<?php

namespace App\Livewire\Customer;

use App\Enums\BookingStatus;
use App\Enums\EscrowStatus;
use App\Enums\PaymentMethod;
use App\Enums\UserStatus;
use App\Models\Booking;
use App\Models\EscrowTransaction;
use App\Models\GuideProfile;
use App\Models\TourPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Page 3 — Dedicated Booking Page.
 *
 * Confirmation-first submission: creates a booking with status
 * pending_confirmation and redirects to the customer dashboard.
 * No payment screen is shown at this stage (SRS FR-02-03 step 1).
 */
#[Layout('layouts.customer')]
#[Title('Book a Guide')]
class BookingForm extends Component
{
    public GuideProfile $profile;

    // ── Form state ────────────────────────────────────────────────
    public string $scheduleDate = '';
    public string $pickupTime = '';
    public string $pickupLocation = '';
    public ?int $tourPackageId = null;
    /** @var array<int, string> */
    public array $customDestinations = [];
    public string $newDestination = '';

    /**
     * Mount the booking form for a verified, active guide.
     */
    public function mount(GuideProfile $guideProfile): void
    {
        abort_unless(
            $guideProfile->is_verified
            && $guideProfile->user?->status === UserStatus::ACTIVE,
            404,
            'Guide not found.'
        );

        if (! Auth::check()) {
            session()->flash('warning', __('Please log in to book a guide.'));
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $this->profile = $guideProfile->load('user');

        // Optional package pre-selection via ?package= query param.
        if ($packageId = (int) Request::query('package')) {
            $package = $this->profile->user->tourPackages()->find($packageId);
            if ($package && $package->is_active) {
                $this->tourPackageId = $package->id;
            }
        }
    }

    /**
     * Guide's active tour packages (optional selection).
     */
    #[Computed]
    public function packages()
    {
        return $this->profile->user->tourPackages()
            ->where('is_active', true)
            ->latest()
            ->get();
    }

    /**
     * The currently selected package, if any.
     */
    #[Computed]
    public function selectedPackage(): ?TourPackage
    {
        if (! $this->tourPackageId) {
            return null;
        }

        return $this->profile->user->tourPackages()->find($this->tourPackageId);
    }

    /**
     * Estimated tour duration in hours (placeholder estimate).
     */
    public function estimatedHours(): float
    {
        $num = count($this->customDestinations);
        return $num > 0 ? (1.5 * $num + 1.0) : 1.0;
    }

    /**
     * Calculated total price.
     *
     * Package selected  -> package price.
     * Hourly rate       -> base_rate × estimated hours.
     * Daily rate        -> base_rate (flat).
     */
    #[Computed]
    public function totalPrice(): float
    {
        if ($package = $this->selectedPackage()) {
            return (float) $package->price;
        }

        $baseRate = (float) $this->profile->base_rate;

        if ($this->profile->tariff_mode->value === 'hourly') {
            return $baseRate * $this->estimatedHours();
        }

        return $baseRate;
    }

    /**
     * Add a custom destination to the itinerary.
     */
    public function addDestination(): void
    {
        $this->validate([
            'newDestination' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        $this->customDestinations[] = trim($this->newDestination);
        $this->newDestination = '';
    }

    /**
     * Remove a custom destination from the itinerary.
     */
    public function removeDestination(int $index): void
    {
        if (array_key_exists($index, $this->customDestinations)) {
            unset($this->customDestinations[$index]);
            $this->customDestinations = array_values($this->customDestinations);
        }
    }

    /**
     * Confirmation-first submission:
     * create the booking as pending_confirmation, never show payment.
     */
    public function submitBooking(): void
    {
        $this->validate([
            'scheduleDate' => ['required', 'date', 'after:today'],
            'pickupTime' => ['required', 'string'],
            'pickupLocation' => ['required', 'string', 'min:5', 'max:255'],
            'tourPackageId' => ['nullable', 'integer', 'exists:tour_packages,id'],
            'customDestinations' => ['required', 'array', 'min:1'],
            'customDestinations.*' => ['required', 'string', 'min:3'],
        ]);

        try {
            DB::transaction(function (): void {
                $totalPrice = $this->totalPrice();

                $booking = Booking::create([
                    'customer_id' => Auth::id(),
                    'guide_id' => $this->profile->user_id,
                    'tour_package_id' => $this->tourPackageId,
                    'pickup_location' => $this->pickupLocation,
                    'dropoff_location' => null,
                    'custom_destinations' => $this->customDestinations,
                    'schedule_date' => $this->scheduleDate,
                    'pickup_time' => $this->pickupTime,
                    'total_price' => $totalPrice,
                    'status' => BookingStatus::PENDING_CONFIRMATION,
                ]);

                EscrowTransaction::create([
                    'booking_id' => $booking->id,
                    'transaction_reference' => 'TXN-' . str_pad((string) $booking->id, 8, '0', STR_PAD_LEFT) . '-' . strtoupper(bin2hex(random_bytes(4))),
                    'payment_method' => PaymentMethod::QRIS,
                    'gross_amount' => $totalPrice,
                    'platform_commission' => $totalPrice * 0.10,
                    'guide_net_amount' => $totalPrice * 0.90,
                    'status' => EscrowStatus::WAITING_PAYMENT,
                ]);
            });

            session()->flash(
                'success',
                __('Booking request sent to :guide. It is now waiting for guide approval — you will be able to pay once the guide confirms.', [
                    'guide' => $this->profile->user->name,
                ])
            );

            $this->redirect(route('dashboard'), navigate: true);
        } catch (\Exception $e) {
            logger()->error('Booking creation failed: ' . $e->getMessage());

            session()->flash('error', __('Booking request could not be processed. Please try again.'));
            $this->redirect(route('dashboard'), navigate: true);
        }
    }

    /**
     * Render the component view.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.customer.booking-form');
    }
}
