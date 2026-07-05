<?php

namespace App\Livewire\Guide;

use App\Enums\TariffMode;
use App\Models\GuideProfile;
use App\Models\TourPackage;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Manage Services')]
class ServiceManagement extends Component
{
    // Rates configuration
    public string $tariffMode = 'daily';
    public string $baseRate = '';

    // Tour Package CRUD Form state
    public bool $isEditing = false;
    public ?int $packageId = null;
    public string $title = '';
    public string $description = '';
    public string $price = '';
    /** @var array<int, string> */
    public array $destinations = [];
    public string $newDestination = '';
    public bool $is_active = true;

    /**
     * Mount the component and load guide profile rates.
     */
    public function mount(): void
    {
        $profile = Auth::user()->guideProfile;
        if ($profile) {
            $this->tariffMode = $profile->tariff_mode->value === 'package' ? 'daily' : $profile->tariff_mode->value;
            $this->baseRate = (string) $profile->base_rate;
        }
    }

    /**
     * Get guide profile.
     */
    #[Computed]
    public function guideProfile(): ?GuideProfile
    {
        return Auth::user()->guideProfile;
    }

    /**
     * Check if guide is verified.
     */
    #[Computed]
    public function isVerified(): bool
    {
        return (bool) $this->guideProfile()?->is_verified;
    }

    /**
     * Get tour packages created by the guide.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, TourPackage>
     */
    #[Computed]
    public function tourPackages()
    {
        return TourPackage::where('guide_id', Auth::id())
            ->latest()
            ->get();
    }

    /**
     * Update guide pricing rates.
     */
    public function updateRates(): void
    {
        if (! $this->isVerified()) {
            Flux::toast(variant: 'danger', text: __('Your profile is pending verification.'));
            return;
        }

        $this->validate([
            'tariffMode' => ['required', 'string', 'in:hourly,daily'],
            'baseRate' => ['required', 'numeric', 'min:0'],
        ]);

        $profile = $this->guideProfile();
        if ($profile) {
            $profile->update([
                'tariff_mode' => TariffMode::from($this->tariffMode),
                'base_rate' => (float) $this->baseRate,
            ]);
            Flux::toast(variant: 'success', text: __('Pricing rates updated successfully.'));
        }
    }

    /**
     * Open form to create a new package.
     */
    public function createPackage(): void
    {
        if (! $this->isVerified()) {
            Flux::toast(variant: 'danger', text: __('Your profile is pending verification.'));
            return;
        }

        $this->resetPackageForm();
        $this->isEditing = true;
    }

    /**
     * Open form to edit an existing package.
     */
    public function editPackage(int $id): void
    {
        if (! $this->isVerified()) {
            Flux::toast(variant: 'danger', text: __('Your profile is pending verification.'));
            return;
        }

        $package = TourPackage::where('guide_id', Auth::id())->find($id);

        if (! $package) {
            Flux::toast(variant: 'danger', text: __('Package not found.'));
            return;
        }

        $this->packageId = $package->id;
        $this->title = $package->title;
        $this->description = $package->description;
        $this->price = (string) $package->price;
        $this->destinations = $package->destinations;
        $this->is_active = $package->is_active;
        $this->newDestination = '';
        $this->isEditing = true;
    }

    /**
     * Add a destination to the package list.
     */
    public function addDestination(): void
    {
        $this->validate([
            'newDestination' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        $this->destinations[] = trim($this->newDestination);
        $this->newDestination = '';
    }

    /**
     * Remove a destination from the package list.
     */
    public function removeDestination(int $index): void
    {
        if (array_key_exists($index, $this->destinations)) {
            unset($this->destinations[$index]);
            $this->destinations = array_values($this->destinations);
        }
    }

    /**
     * Save/Update tour package.
     */
    public function savePackage(): void
    {
        if (! $this->isVerified()) {
            Flux::toast(variant: 'danger', text: __('Your profile is pending verification.'));
            return;
        }

        $this->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'destinations' => ['required', 'array', 'min:1'],
            'destinations.*' => ['required', 'string', 'min:3'],
            'is_active' => ['required', 'boolean'],
        ]);

        if ($this->packageId) {
            $package = TourPackage::where('guide_id', Auth::id())->find($this->packageId);
            if ($package) {
                $package->update([
                    'title' => $this->title,
                    'description' => $this->description,
                    'price' => (float) $this->price,
                    'destinations' => $this->destinations,
                    'is_active' => $this->is_active,
                ]);
                Flux::toast(variant: 'success', text: __('Tour package updated successfully.'));
            }
        } else {
            TourPackage::create([
                'guide_id' => Auth::id(),
                'title' => $this->title,
                'description' => $this->description,
                'price' => (float) $this->price,
                'destinations' => $this->destinations,
                'is_active' => $this->is_active,
            ]);
            Flux::toast(variant: 'success', text: __('Tour package created successfully.'));
        }

        $this->resetPackageForm();
        $this->isEditing = false;
    }

    /**
     * Delete an existing tour package.
     */
    public function deletePackage(int $id): void
    {
        if (! $this->isVerified()) {
            Flux::toast(variant: 'danger', text: __('Your profile is pending verification.'));
            return;
        }

        $package = TourPackage::where('guide_id', Auth::id())->find($id);

        if ($package) {
            $package->delete();
            Flux::toast(variant: 'success', text: __('Tour package deleted.'));
        }
    }

    /**
     * Cancel editing form.
     */
    public function cancelEdit(): void
    {
        $this->resetPackageForm();
        $this->isEditing = false;
    }

    /**
     * Reset the package editor form variables.
     */
    private function resetPackageForm(): void
    {
        $this->packageId = null;
        $this->title = '';
        $this->description = '';
        $this->price = '';
        $this->destinations = [];
        $this->newDestination = '';
        $this->is_active = true;
    }

    /**
     * Render the component view.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.guide.service-management');
    }
}
