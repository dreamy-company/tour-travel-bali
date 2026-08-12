<?php

namespace App\Livewire\Customer;

use App\Enums\UserRole;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Customer Wishlist (/favorites) — saved guides with quick actions.
 */
#[Layout('layouts.customer')]
#[Title('My Favorites')]
class Wishlist extends Component
{
    /**
     * Guides saved by the logged-in customer.
     *
     * @return Collection<int, User>
     */
    #[Computed]
    public function favoriteGuides(): Collection
    {
        return Auth::user()->favoritedGuides()
            ->where('role', UserRole::GUIDE)
            ->where('status', \App\Enums\UserStatus::ACTIVE)
            ->with('guideProfile')
            ->withAvg('guideReviews', 'rating')
            ->latest('favorites.created_at')
            ->get();
    }

    /**
     * Remove a guide from the wishlist.
     */
    public function removeFavorite(int $guideId): void
    {
        Favorite::where('customer_id', Auth::id())
            ->where('guide_id', $guideId)
            ->delete();

        session()->flash('success', __('Removed from your favorites.'));
    }

    /**
     * Render the component view.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.customer.wishlist');
    }
}
