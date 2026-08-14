<?php

namespace App\Livewire\Customer;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\ZodiacSign;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Page 1 — Guide Search & Directory.
 *
 * Sidebar filters (matching engine, FR-02-01) + verified guide grid.
 */
#[Layout('layouts.customer')]
#[Title('Find Tour Guides')]
class GuideSearch extends Component
{
    // ── Filter state (FR-02-01 matching parameters) ──────────────

    /** @var array<int, string> Activity specializations (same interest) */
    public array $selectedSpecializations = [];

    /** Communication style (same vibe) */
    public string $communicationStyle = '';

    /** Tariff range */
    public string $minPrice = '';

    public string $maxPrice = '';

    /** Tariff mode toggle ('' = any, 'hourly', 'daily') */
    public string $tariffMode = '';

    /** Zodiac sign filter ('' = any) */
    public string $zodiacSign = '';

    /** Rank guides by zodiac compatibility with the logged-in customer */
    public bool $cosmicMatch = false;

    /** @var array<int, string> Language fluency */
    public array $selectedLanguages = [];

    /** @var array<int, int> Guide ids bookmarked by the customer */
    public array $favoriteGuideIds = [];

    /**
     * Mount: apply ?vibe= quick-match pre-filter from the landing page
     * and load the customer's wishlist state.
     */
    public function mount(): void
    {
        $vibe = (string) Request::query('vibe');

        if ($vibe !== '') {
            $this->selectedSpecializations = [$vibe];
        }

        $zodiac = (string) Request::query('zodiac');

        if ($zodiac !== '' && ZodiacSign::tryFrom($zodiac) !== null) {
            $this->zodiacSign = $zodiac;
        }

        if (Auth::check() && Auth::user()->role === UserRole::CUSTOMER) {
            $this->favoriteGuideIds = Auth::user()->favorites()->pluck('guide_id')->map(fn ($id) => (int) $id)->all();
        }
    }

    /**
     * Toggle a guide in the customer's wishlist. Guests are sent to login.
     */
    public function toggleFavorite(int $guideId): void
    {
        if (! Auth::check()) {
            session()->flash('warning', __('Log in to save guides to your favorites.'));
            $this->redirect(route('login'), navigate: true);

            return;
        }

        $guide = User::where('role', UserRole::GUIDE)->find($guideId);

        if (! $guide) {
            return;
        }

        $favorite = Favorite::where('customer_id', Auth::id())->where('guide_id', $guideId)->first();

        if ($favorite) {
            $favorite->delete();
            $this->favoriteGuideIds = array_values(array_diff($this->favoriteGuideIds, [$guideId]));
            session()->flash('success', __('Removed from favorites.'));
        } else {
            Favorite::create(['customer_id' => Auth::id(), 'guide_id' => $guideId]);
            $this->favoriteGuideIds[] = $guideId;
            session()->flash('success', __('Saved to favorites!'));
        }
    }

    /**
     * Reset every filter back to defaults.
     */
    public function resetFilters(): void
    {
        $this->reset([
            'selectedSpecializations',
            'communicationStyle',
            'minPrice',
            'maxPrice',
            'tariffMode',
            'selectedLanguages',
            'zodiacSign',
            'cosmicMatch',
        ]);
    }

    /**
     * The zodiac sign of the authenticated customer, or null when the
     * customer has not set a birth date (or is a guest).
     */
    #[Computed]
    public function customerZodiac(): ?ZodiacSign
    {
        if (! Auth::check()) {
            return null;
        }

        return Auth::user()->zodiac();
    }

    /**
     * Compatibility score between the customer and a guide, or null when
     * either side has no zodiac sign.
     */
    public function zodiacCompatibility(int $guideId): ?int
    {
        $customerSign = $this->customerZodiac;

        if ($customerSign === null) {
            return null;
        }

        $guideSign = $this->guides->firstWhere('id', $guideId)?->zodiac();

        return $guideSign === null ? null : $customerSign->compatibility($guideSign);
    }

    /**
     * Verified, active guides matching the active filters.
     *
     * @return Collection<int, User>
     */
    #[Computed]
    public function guides(): Collection
    {
        $guides = User::query()
            ->where('role', UserRole::GUIDE)
            ->where('status', UserStatus::ACTIVE)
            ->whereHas('guideProfile', function ($query) {
                $query->where('is_verified', true);

                if ($this->minPrice !== '') {
                    $query->where('base_rate', '>=', (float) $this->minPrice);
                }

                if ($this->maxPrice !== '') {
                    $query->where('base_rate', '<=', (float) $this->maxPrice);
                }

                if ($this->tariffMode !== '') {
                    $query->where('tariff_mode', $this->tariffMode);
                }

                if (! empty($this->selectedLanguages)) {
                    $query->where(function ($sub) {
                        foreach ($this->selectedLanguages as $lang) {
                            $sub->orWhereJsonContains('languages', $lang);
                        }
                    });
                }

                // Same interest
                if (! empty($this->selectedSpecializations)) {
                    $query->where(function ($sub) {
                        foreach ($this->selectedSpecializations as $spec) {
                            $sub->orWhereJsonContains('specializations', $spec);
                        }
                    });
                }

                // Same vibe
                if ($this->communicationStyle !== '') {
                    $query->where('communication_style', $this->communicationStyle);
                }
            })
            ->with('guideProfile')
            ->withAvg('guideReviews', 'rating')
            ->latest()
            ->get();

        // Zodiac sign filter — derived in PHP because the sign is computed
        // from birth_date rather than stored as a column.
        if ($this->zodiacSign !== '') {
            $guides = $guides->filter(
                fn (User $guide) => $guide->zodiac()?->value === $this->zodiacSign
            )->values();
        }

        // Cosmic match — rank by compatibility with the customer's sign;
        // guides without a birth date sort last.
        if ($this->cosmicMatch && $this->customerZodiac !== null) {
            $guides = $guides->sortByDesc(
                fn (User $guide) => $guide->zodiac()?->compatibility($this->customerZodiac) ?? -1
            )->values();
        }

        return $guides;
    }

    /**
     * Render the component view.
     */
    public function render(): View
    {
        return view('livewire.customer.guide-search');
    }
}
