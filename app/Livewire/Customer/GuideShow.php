<?php

namespace App\Livewire\Customer;

use App\Enums\UserStatus;
use App\Models\GuideProfile;
use App\Models\TourPackage;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Page 2 — Guide Detail View.
 *
 * Header & bio, services/packages, reviews, and CTAs
 * (pre-booking chat + book now).
 */
#[Layout('layouts.customer')]
#[Title('Guide Profile')]
class GuideShow extends Component
{
    public GuideProfile $profile;

    /**
     * Mount the component with the route-bound guide profile.
     *
     * Only verified guides with active accounts are publicly visible.
     */
    public function mount(GuideProfile $guideProfile): void
    {
        abort_unless(
            $guideProfile->is_verified
            && $guideProfile->user?->status === UserStatus::ACTIVE,
            404,
            'Guide not found.'
        );

        $this->profile = $guideProfile->load('user');
    }

    /**
     * Tour packages published by this guide.
     *
     * @return Collection<int, TourPackage>
     */
    #[Computed]
    public function packages(): Collection
    {
        return $this->profile->user->tourPackages()
            ->where('is_active', true)
            ->latest()
            ->get();
    }

    /**
     * Reviews received by this guide, newest first.
     */
    #[Computed]
    public function reviews()
    {
        return $this->profile->user->guideReviews()
            ->with('customer')
            ->latest()
            ->get();
    }

    /**
     * Average rating across all reviews.
     */
    #[Computed]
    public function averageRating(): float
    {
        return round((float) $this->profile->user->guideReviews()->avg('rating'), 2);
    }

    /**
     * Render the component view.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.customer.guide-show');
    }
}
