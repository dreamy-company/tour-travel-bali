<?php

namespace App\Livewire\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Services\GuideAdminService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Admin user management: search & filter every customer and guide,
 * edit account details (name, email, phone, birth date, status, role),
 * and for guides also delete their service repository (tour packages),
 * KYC documents, ban/unban, or permanently delete the account.
 */
#[Layout('layouts.app')]
#[Title('User Management')]
class UserManagement extends Component
{
    public string $search = '';

    /** @var string '' = customers + guides, otherwise a UserRole value */
    public string $roleFilter = '';

    /** @var string '' = all, otherwise a UserStatus value */
    public string $statusFilter = '';

    public ?int $selectedUserId = null;

    // ── Edit modal state ──────────────────────────────────────────
    public bool $showEditModal = false;

    public ?int $editingUserId = null;

    public string $editName = '';

    public string $editEmail = '';

    public string $editPhone = '';

    public string $editBirthDate = '';

    public string $editStatus = '';

    public string $editRole = '';

    /**
     * Select a user to inspect in the detail pane.
     */
    public function selectUser(int $userId): void
    {
        $this->selectedUserId = $userId;
    }

    // ── Edit user ─────────────────────────────────────────────────

    /**
     * Open the edit modal pre-filled with the user's account details.
     */
    public function openEdit(int $userId): void
    {
        $user = $this->findUser($userId);

        if (! $user) {
            return;
        }

        $this->editingUserId = $user->id;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editPhone = $user->phone_number ?? '';
        $this->editBirthDate = $user->birth_date?->format('Y-m-d') ?? '';
        $this->editStatus = $user->status->value;
        $this->editRole = $user->role->value;
        $this->showEditModal = true;
    }

    /**
     * Close the edit modal without saving.
     */
    public function closeEdit(): void
    {
        $this->showEditModal = false;
        $this->editingUserId = null;
    }

    /**
     * Persist the edited account details.
     */
    public function saveUser(): void
    {
        $user = $this->findUser($this->editingUserId);

        if (! $user) {
            return;
        }

        // A guide with an active profile cannot be demoted to customer —
        // their profile, packages, wallet, and bookings assume the guide role.
        if ($user->role === UserRole::GUIDE
            && $this->editRole === UserRole::CUSTOMER->value
            && $user->guideProfile !== null) {
            session()->flash('error', __('Cannot change :name to Customer — the guide profile must be deleted first (see the guide danger zone).', ['name' => $user->name]));

            return;
        }

        $validated = $this->validate([
            'editName' => ['required', 'string', 'max:255'],
            'editEmail' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'editPhone' => ['nullable', 'string', 'min:10', 'max:20'],
            'editBirthDate' => ['nullable', 'date', 'before_or_equal:today', 'after_or_equal:1900-01-01'],
            'editStatus' => ['required', Rule::enum(UserStatus::class)],
            'editRole' => ['required', Rule::in([UserRole::CUSTOMER->value, UserRole::GUIDE->value])],
        ]);

        $user->update([
            'name' => $validated['editName'],
            'email' => $validated['editEmail'],
            'phone_number' => $validated['editPhone'] ?: null,
            'birth_date' => $validated['editBirthDate'] ?: null,
            'status' => $validated['editStatus'],
            'role' => $validated['editRole'],
        ]);

        $this->closeEdit();
        session()->flash('success', __('Account details for :name have been updated.', ['name' => $user->name]));
    }

    // ── Guide-specific actions (guides only) ──────────────────────

    /**
     * Delete all tour packages (service repository) for the selected guide.
     */
    public function deleteRepository(GuideAdminService $service): void
    {
        $guide = $this->selectedGuide();

        if (! $guide) {
            return;
        }

        $count = $service->deleteRepository($guide);

        session()->flash('success', __('Deleted :count tour package(s) from :name.', [
            'count' => $count,
            'name' => $guide->name,
        ]));
    }

    /**
     * Delete all KYC documents for the selected guide and revoke verification.
     */
    public function deleteDocuments(GuideAdminService $service): void
    {
        $guide = $this->selectedGuide();

        if (! $guide) {
            return;
        }

        $count = $service->deleteDocuments($guide);

        session()->flash('success', __('Deleted :count document file(s) from :name. Verification revoked until documents are re-submitted.', [
            'count' => $count,
            'name' => $guide->name,
        ]));
    }

    /**
     * Permanently delete the selected guide account.
     */
    public function deleteGuide(GuideAdminService $service): void
    {
        $guide = $this->selectedGuide();

        if (! $guide) {
            return;
        }

        if ($service->hasActiveFunds($guide)) {
            session()->flash('error', __('Cannot delete :name — escrow funds are still held for active bookings. Resolve or release those bookings first.', ['name' => $guide->name]));

            return;
        }

        $service->deleteGuide($guide);

        $this->selectedUserId = null;
        session()->flash('success', __('Guide :name and all associated data have been permanently deleted.', ['name' => $guide->name]));
    }

    /**
     * Ban an active/suspended guide, or unban a banned guide.
     */
    public function toggleBan(): void
    {
        $guide = $this->selectedGuide();

        if (! $guide) {
            return;
        }

        if ($guide->status === UserStatus::BANNED) {
            $guide->update(['status' => UserStatus::ACTIVE]);
            session()->flash('success', __('Guide :name has been unbanned and reactivated.', ['name' => $guide->name]));
        } else {
            $guide->update(['status' => UserStatus::BANNED]);
            session()->flash('success', __('Guide :name has been banned from the platform.', ['name' => $guide->name]));
        }
    }

    // ── Queries ───────────────────────────────────────────────────

    /**
     * All customers and guides matching the active filters.
     *
     * @return Collection<int, User>
     */
    #[Computed]
    public function users(): Collection
    {
        return User::query()
            ->whereIn('role', [UserRole::CUSTOMER, UserRole::GUIDE])
            ->when($this->roleFilter !== '', fn ($query) => $query->where('role', $this->roleFilter))
            ->when($this->search !== '', function ($query) {
                $query->where(function ($sub) {
                    $term = '%'.$this->search.'%';
                    $sub->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone_number', 'like', $term);
                });
            })
            ->when($this->statusFilter !== '', fn ($query) => $query->where('status', $this->statusFilter))
            ->with('guideProfile')
            ->withCount('tourPackages')
            ->withCount('guideBookings')
            ->withCount('customerBookings')
            ->withCount('favorites')
            ->withAvg('guideReviews', 'rating')
            ->latest()
            ->get();
    }

    /**
     * The user currently selected in the detail pane.
     */
    #[Computed]
    public function selectedUser(): ?User
    {
        if (! $this->selectedUserId) {
            return null;
        }

        return User::whereIn('role', [UserRole::CUSTOMER, UserRole::GUIDE])
            ->with(['guideProfile', 'guideWallet'])
            ->withCount('tourPackages')
            ->withCount('guideBookings')
            ->withCount('customerBookings')
            ->withCount('favorites')
            ->withAvg('guideReviews', 'rating')
            ->find($this->selectedUserId);
    }

    /**
     * Resolve a manageable user by id.
     */
    private function findUser(?int $userId): ?User
    {
        if (! $userId) {
            return null;
        }

        return User::whereIn('role', [UserRole::CUSTOMER, UserRole::GUIDE])
            ->with('guideProfile')
            ->find($userId);
    }

    /**
     * Alias kept for the guide-specific actions.
     */
    private function selectedGuide(): ?User
    {
        $user = $this->selectedUser();

        return $user?->role === UserRole::GUIDE ? $user : null;
    }

    /**
     * Render the component view.
     */
    public function render(): View
    {
        return view('livewire.admin.user-management');
    }
}
