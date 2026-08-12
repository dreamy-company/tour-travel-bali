<?php

namespace App\Livewire;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Public landing page — trust building + conversion into the directory.
 */
#[Layout('layouts.customer')]
#[Title('BaliGuide — Local Tour Guide Matching')]
class Landing extends Component
{
    /**
     * Top trip vibes surfaced as quick-matching filter cards.
     *
     * @var array<int, array{key: string, label: string, emoji: string, blurb: string}>
     */
    public function vibes(): array
    {
        return [
            ['key' => 'cafe_hopping', 'label' => 'Cafe Hopping', 'emoji' => '☕', 'blurb' => 'Esthetic cafés & local culinary gems'],
            ['key' => 'photography', 'label' => 'Photography', 'emoji' => '📸', 'blurb' => 'Golden-hour spots & scenic viewpoints'],
            ['key' => 'nightlife', 'label' => 'Nightlife', 'emoji' => '🌙', 'blurb' => 'Sunset bars, beach clubs & night vibes'],
            ['key' => 'nature', 'label' => 'Nature', 'emoji' => '🌋', 'blurb' => 'Volcano treks, waterfalls & rice terraces'],
            ['key' => 'healing', 'label' => 'Healing', 'emoji' => '🧘', 'blurb' => 'Wellness retreats, spas & quiet escapes'],
        ];
    }

    /**
     * Top-rated verified guides for the featured section.
     *
     * @return Collection<int, User>
     */
    #[Computed]
    public function featuredGuides(): Collection
    {
        return User::query()
            ->where('role', UserRole::GUIDE)
            ->where('status', UserStatus::ACTIVE)
            ->whereHas('guideProfile', fn ($q) => $q->where('is_verified', true))
            ->with('guideProfile')
            ->withAvg('guideReviews', 'rating')
            ->withCount('guideReviews')
            ->orderByDesc('guide_reviews_avg_rating')
            ->orderByDesc('guide_reviews_count')
            ->take(4)
            ->get();
    }

    /**
     * Render the component view.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.landing');
    }
}
