<?php

namespace App\Livewire\Customer;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
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

    /** @var array<int, string> Language fluency */
    public array $selectedLanguages = [];

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
        ]);
    }

    /**
     * Verified, active guides matching the active filters.
     *
     * @return Collection<int, User>
     */
    #[Computed]
    public function guides(): Collection
    {
        return User::query()
            ->where('role', UserRole::GUIDE)
            ->where('status', \App\Enums\UserStatus::ACTIVE)
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
    }

    /**
     * Render the component view.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.customer.guide-search');
    }
}
