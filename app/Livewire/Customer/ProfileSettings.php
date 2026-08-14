<?php

namespace App\Livewire\Customer;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Customer profile & traveler persona settings (/profile).
 */
#[Layout('layouts.customer')]
#[Title('Profile & Preferences')]
class ProfileSettings extends Component
{
    // ── Profile form ──────────────────────────────────────────────
    public string $name = '';

    public string $email = '';

    public string $phone_number = '';

    public string $birth_date = '';

    // ── Password form ─────────────────────────────────────────────
    public string $current_password = '';

    public string $new_password = '';

    public string $new_password_confirmation = '';

    // ── Traveler persona preferences ──────────────────────────────
    /** @var array<int, string> */
    public array $traveler_preferences = [];

    /**
     * Persona options shown as checkboxes.
     *
     * @return array<string, string>
     */
    public function personaOptions(): array
    {
        return [
            'introvert' => 'Introvert',
            'cafe_hopper' => 'Cafe Hopper',
            'photography_enthusiast' => 'Photography Enthusiast',
            'adventurer' => 'Adventurer',
            'culture_lover' => 'Culture Lover',
            'night_owl' => 'Night Owl',
            'foodie' => 'Foodie',
            'wellness_seeker' => 'Wellness Seeker',
        ];
    }

    /**
     * Mount with the current customer data.
     */
    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number ?? '';
        $this->birth_date = $user->birth_date?->format('Y-m-d') ?? '';
        $this->traveler_preferences = $user->traveler_preferences ?? [];
    }

    /**
     * Update name, email, and phone number.
     */
    public function updateProfile(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'min:10', 'max:20'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today', 'after_or_equal:1900-01-01'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?: null,
            'birth_date' => $validated['birth_date'] ?: null,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        session()->flash('success', __('Profile updated successfully.'));
    }

    /**
     * Update the account password.
     */
    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'new_password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset('current_password', 'new_password', 'new_password_confirmation');

        session()->flash('success', __('Password updated successfully.'));
    }

    /**
     * Save the traveler persona preferences used for matching.
     */
    public function updatePreferences(): void
    {
        $options = array_keys($this->personaOptions());

        $this->validate([
            'traveler_preferences' => ['nullable', 'array'],
            'traveler_preferences.*' => ['string', 'in:'.implode(',', $options)],
        ]);

        Auth::user()->update([
            'traveler_preferences' => $this->traveler_preferences,
        ]);

        session()->flash('success', __('Traveler preferences saved — matching will be more accurate.'));
    }

    /**
     * Render the component view.
     */
    public function render(): View
    {
        return view('livewire.customer.profile-settings');
    }
}
