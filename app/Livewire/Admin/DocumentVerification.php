<?php

namespace App\Livewire\Admin;

use App\Enums\UserStatus;
use App\Models\GuideProfile;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Guide Document Verification')]
class DocumentVerification extends Component
{
    public ?int $selectedProfileId = null;

    public string $rejectionReason = '';

    /**
     * Mount the component and select the first pending profile.
     */
    public function mount(): void
    {
        $firstPending = $this->pendingProfiles()->first();
        if ($firstPending) {
            $this->selectedProfileId = $firstPending->id;
        }
    }

    /**
     * Select a profile to view.
     */
    public function selectProfile(int $id): void
    {
        $this->selectedProfileId = $id;
        $this->rejectionReason = '';
    }

    /**
     * Get all pending profiles.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, GuideProfile>
     */
    #[Computed]
    public function pendingProfiles()
    {
        return GuideProfile::with('user')
            ->where('is_verified', false)
            ->latest()
            ->get();
    }

    /**
     * Get the selected guide profile.
     */
    #[Computed]
    public function selectedProfile(): ?GuideProfile
    {
        if (! $this->selectedProfileId) {
            return null;
        }

        return GuideProfile::with('user')->find($this->selectedProfileId);
    }

    /**
     * Approve the selected guide verification.
     */
    public function approve(): void
    {
        $profile = $this->selectedProfile();

        if (! $profile) {
            Flux::toast(variant: 'danger', text: __('No guide selected.'));
            return;
        }

        DB::transaction(function () use ($profile): void {
            // Update profile
            $profile->update([
                'is_verified' => true,
                'rejection_reason' => null,
            ]);

            // Update user status to active
            $profile->user->update([
                'status' => UserStatus::ACTIVE,
            ]);
        });

        Flux::toast(variant: 'success', text: __('Guide approved successfully.'));

        // Select the next pending profile
        $this->selectedProfileId = null;
        $next = $this->pendingProfiles()->first();
        if ($next) {
            $this->selectedProfileId = $next->id;
        }
    }

    /**
     * Reject the selected guide verification.
     */
    public function reject(): void
    {
        $this->validate([
            'rejectionReason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $profile = $this->selectedProfile();

        if (! $profile) {
            Flux::toast(variant: 'danger', text: __('No guide selected.'));
            return;
        }

        DB::transaction(function () use ($profile): void {
            // Update profile with rejection reason
            $profile->update([
                'is_verified' => false,
                'rejection_reason' => $this->rejectionReason,
            ]);

            // Suspend user until they fix legality details
            $profile->user->update([
                'status' => UserStatus::SUSPENDED,
            ]);
        });

        Flux::toast(variant: 'warning', text: __('Guide verification rejected. Feedback saved.'));

        // Reset inputs and select next profile
        $this->rejectionReason = '';
        $this->selectedProfileId = null;
        $next = $this->pendingProfiles()->first();
        if ($next) {
            $this->selectedProfileId = $next->id;
        }
    }

    /**
     * Render the component view.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.document-verification');
    }
}
