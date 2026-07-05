<?php

namespace App\Livewire\Customer;

use App\Enums\BookingStatus;
use App\Enums\EscrowStatus;
use App\Enums\PaymentMethod;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\EscrowTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Search Tour Guides')]
class GuideSearch extends Component
{
    // Search Filters
    public string $searchQuery = '';
    public string $minPrice = '';
    public string $maxPrice = '';
    public string $minRating = '';
    /** @var array<int, string> */
    public array $selectedLanguages = [];

    // Booking / Itinerary Form State
    public ?int $selectedGuideId = null;
    public string $pickupLocation = '';
    public string $dropoffLocation = '';
    /** @var array<int, string> */
    public array $customDestinations = [];
    public string $newDestination = '';
    public string $scheduleDate = '';
    public string $pickupTime = '';

    /**
     * Select a guide to open the custom itinerary booking pane.
     */
    public function selectGuide(int $id): void
    {
        $this->selectedGuideId = $id;
        $this->pickupLocation = '';
        $this->dropoffLocation = '';
        $this->customDestinations = [];
        $this->newDestination = '';
        $this->scheduleDate = '';
        $this->pickupTime = '';
    }

    /**
     * Get list of verified guides matching active filters.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    #[Computed]
    public function guides()
    {
        return User::query()
            ->where('role', UserRole::GUIDE)
            ->whereHas('guideProfile', function ($query) {
                $query->where('is_verified', true);

                if ($this->minPrice !== '') {
                    $query->where('base_rate', '>=', (float) $this->minPrice);
                }

                if ($this->maxPrice !== '') {
                    $query->where('base_rate', '<=', (float) $this->maxPrice);
                }

                if (! empty($this->selectedLanguages)) {
                    $query->where(function ($sub) {
                        foreach ($this->selectedLanguages as $lang) {
                            $sub->orWhereJsonContains('languages', $lang);
                        }
                    });
                }
            })
            ->with('guideProfile')
            ->withAvg('guideReviews', 'rating')
            ->when($this->searchQuery !== '', function ($query) {
                $query->where('name', 'like', '%' . $this->searchQuery . '%');
            })
            ->when($this->minRating !== '', function ($query) {
                $query->having('guide_reviews_avg_rating', '>=', (float) $this->minRating);
            })
            ->latest()
            ->get();
    }

    /**
     * Get currently selected guide.
     */
    #[Computed]
    public function selectedGuide(): ?User
    {
        if (! $this->selectedGuideId) {
            return null;
        }

        return User::with('guideProfile')
            ->where('role', UserRole::GUIDE)
            ->find($this->selectedGuideId);
    }

    /**
     * Placeholder method to calculate estimated driving distance in kilometers.
     */
    public function calculateItineraryDistance(): float
    {
        $num = count($this->customDestinations);
        return $num > 0 ? (12.5 * $num) : 0;
    }

    /**
     * Placeholder method to calculate estimated travel duration in hours.
     */
    public function calculateItineraryDuration(): float
    {
        $num = count($this->customDestinations);
        return $num > 0 ? (1.5 * $num + 1.0) : 0;
    }

    /**
     * Get computed total price dynamically based on guide base rate and estimates.
     */
    #[Computed]
    public function totalPrice(): float
    {
        $guide = $this->selectedGuide();
        if (! $guide || ! $guide->guideProfile) {
            return 0;
        }

        $baseRate = (float) $guide->guideProfile->base_rate;
        $mode = $guide->guideProfile->tariff_mode->value;

        if ($mode === 'hourly') {
            $hours = $this->calculateItineraryDuration();
            return $baseRate * $hours;
        }

        // Daily rate flat charges
        return $baseRate;
    }

    /**
     * Add custom destination to itinerary list.
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
     * Remove custom destination from itinerary list.
     */
    public function removeDestination(int $index): void
    {
        if (array_key_exists($index, $this->customDestinations)) {
            unset($this->customDestinations[$index]);
            $this->customDestinations = array_values($this->customDestinations);
        }
    }

    /**
     * Request booking for selected guide.
     */
    public function book(): void
    {
        if (! Auth::check()) {
            session()->flash('warning', __('Please log in to submit a booking request.'));
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $this->validate([
            'pickupLocation' => ['required', 'string', 'min:5', 'max:255'],
            'dropoffLocation' => ['nullable', 'string', 'max:255'],
            'customDestinations' => ['required', 'array', 'min:1'],
            'customDestinations.*' => ['required', 'string', 'min:3'],
            'scheduleDate' => ['required', 'date', 'after:today'],
            'pickupTime' => ['required', 'string'],
        ]);

        $booking = null;
        $redirectUrl = null;

        try {
            DB::transaction(function () use (&$booking): void {
                $totalPrice = $this->totalPrice();

                $booking = Booking::create([
                    'customer_id' => Auth::id(),
                    'guide_id' => $this->selectedGuideId,
                    'tour_package_id' => null,
                    'pickup_location' => $this->pickupLocation,
                    'dropoff_location' => $this->dropoffLocation ?: null,
                    'custom_destinations' => $this->customDestinations,
                    'schedule_date' => $this->scheduleDate,
                    'pickup_time' => $this->pickupTime,
                    'total_price' => $totalPrice,
                    'status' => BookingStatus::PENDING_CONFIRMATION,
                ]);

                $commission = $totalPrice * 0.10;
                $netAmount = $totalPrice - $commission;

                EscrowTransaction::create([
                    'booking_id' => $booking->id,
                    'transaction_reference' => 'TXN-' . str_pad((string) $booking->id, 8, '0', STR_PAD_LEFT) . '-' . strtoupper(bin2hex(random_bytes(4))),
                    'payment_method' => PaymentMethod::QRIS,
                    'gross_amount' => $totalPrice,
                    'platform_commission' => $commission,
                    'guide_net_amount' => $netAmount,
                    'status' => EscrowStatus::WAITING_PAYMENT,
                ]);
            });

            session()->flash('success', __('Booking request submitted successfully! Awaiting guide confirmation.'));

            $this->selectedGuideId = null;
            $this->redirect(route('dashboard'), navigate: true);
        } catch (\Exception $e) {
            logger()->error('Booking creation failed: ' . $e->getMessage());

            session()->flash('error', __('Booking request could not be processed. Please try again.'));

            $this->selectedGuideId = null;
            $this->redirect(route('dashboard'), navigate: true);
        }
    }

    /**
     * Render the component view.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.customer.guide-search');
    }
}
