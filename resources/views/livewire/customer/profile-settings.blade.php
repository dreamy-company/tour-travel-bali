<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- LEFT: Profile + Password -->
    <div class="lg:col-span-7 min-w-0 flex flex-col gap-6">
        @if (session()->has('success'))
            <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 flex items-center justify-between" role="alert">
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 flex items-center justify-between" role="alert">
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Account info --}}
        <section class="border border-hairline rounded-[14px] bg-white p-6 sm:p-8 space-y-6">
            <div>
                <h2 class="text-lg font-semibold tracking-[-0.3px] text-ink">{{ __('Account Information') }}</h2>
                <p class="text-sm text-muted mt-1">{{ __('Update your name, email, contact number, and birth date.') }}</p>
            </div>
            <hr class="border-hairline" />

            <form wire:submit="updateProfile" class="space-y-5">
                <div class="space-y-1.5">
                    <label for="profile-name" class="text-sm font-semibold text-ink block">{{ __('Full Name') }}</label>
                    <input id="profile-name" wire:model="name" type="text" class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-hairline bg-white text-ink focus:outline-hidden focus:ring-2 focus:ring-rausch" />
                    @error('name') <span class="text-xs text-error-text block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="profile-email" class="text-sm font-semibold text-ink block">{{ __('Email Address') }}</label>
                    <input id="profile-email" wire:model="email" type="email" class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-hairline bg-white text-ink focus:outline-hidden focus:ring-2 focus:ring-rausch" />
                    @error('email') <span class="text-xs text-error-text block mt-1">{{ $message }}</span> @enderror
                    @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                        <p class="text-xs text-amber-700 mt-1">{{ __('Your email address is unverified.') }}</p>
                    @endif
                </div>

                <div class="space-y-1.5">
                    <label for="profile-phone" class="text-sm font-semibold text-ink block">{{ __('Phone Number') }}</label>
                    <input id="profile-phone" wire:model="phone_number" type="tel" placeholder="08xxxxxxxxxx" class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-hairline bg-white text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch" />
                    @error('phone_number') <span class="text-xs text-error-text block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="profile-birth-date" class="text-sm font-semibold text-ink block">{{ __('Birth Date') }}</label>
                    <input id="profile-birth-date" wire:model="birth_date" type="date" max="{{ now()->toDateString() }}" class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-hairline bg-white text-ink focus:outline-hidden focus:ring-2 focus:ring-rausch" />
                    @error('birth_date') <span class="text-xs text-error-text block mt-1">{{ $message }}</span> @enderror
                    @php $previewSign = \App\Services\ZodiacService::fromDate($birth_date); @endphp
                    @if ($previewSign !== null)
                        <p class="text-xs text-muted flex items-center gap-1.5 mt-1">
                            <span class="text-sm leading-none">{{ $previewSign->symbol() }}</span>
                            <span>{{ $previewSign->label() }}</span>
                            <span>{{ $previewSign->elementEmoji() }}</span>
                            <span>· {{ __('used for zodiac matching') }}</span>
                        </p>
                    @elseif ($birth_date !== '')
                        <p class="text-xs text-error-text mt-1">{{ __('This date is not valid for zodiac matching.') }}</p>
                    @endif
                </div>

                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active transition-colors shadow-airbnb">
                    {{ __('Save Profile') }}
                </button>
            </form>
        </section>

        {{-- Password --}}
        <section class="border border-hairline rounded-[14px] bg-white p-6 sm:p-8 space-y-6">
            <div>
                <h2 class="text-lg font-semibold tracking-[-0.3px] text-ink">{{ __('Change Password') }}</h2>
                <p class="text-sm text-muted mt-1">{{ __('Use a strong password you do not use elsewhere.') }}</p>
            </div>
            <hr class="border-hairline" />

            <form wire:submit="updatePassword" class="space-y-5">
                <div class="space-y-1.5">
                    <label for="current-password" class="text-sm font-semibold text-ink block">{{ __('Current Password') }}</label>
                    <input id="current-password" wire:model="current_password" type="password" autocomplete="current-password" class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-hairline bg-white text-ink focus:outline-hidden focus:ring-2 focus:ring-rausch" />
                    @error('current_password') <span class="text-xs text-error-text block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label for="new-password" class="text-sm font-semibold text-ink block">{{ __('New Password') }}</label>
                        <input id="new-password" wire:model="new_password" type="password" autocomplete="new-password" class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-hairline bg-white text-ink focus:outline-hidden focus:ring-2 focus:ring-rausch" />
                        @error('new_password') <span class="text-xs text-error-text block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label for="new-password-confirmation" class="text-sm font-semibold text-ink block">{{ __('Confirm New Password') }}</label>
                        <input id="new-password-confirmation" wire:model="new_password_confirmation" type="password" autocomplete="new-password" class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-hairline bg-white text-ink focus:outline-hidden focus:ring-2 focus:ring-rausch" />
                        @error('new_password_confirmation') <span class="text-xs text-error-text block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium rounded-lg border border-hairline text-ink hover:bg-surface-soft transition-colors">
                    {{ __('Update Password') }}
                </button>
            </form>
        </section>
    </div>

    <!-- RIGHT: Traveler persona preferences -->
    <aside class="lg:col-span-5">
        <div class="lg:sticky lg:top-24 border border-hairline rounded-[14px] bg-white p-6 sm:p-8 space-y-6">
            <div>
                <h2 class="text-lg font-semibold tracking-[-0.3px] text-ink">{{ __('Traveler Persona') }}</h2>
                <p class="text-sm text-muted mt-1">{{ __('Set your travel personality so we can match you with guides of the same frequency.') }}</p>
            </div>
            <hr class="border-hairline" />

            <form wire:submit="updatePreferences" class="space-y-3">
                @foreach ($this->personaOptions() as $value => $label)
                    <label class="flex items-center gap-3 rounded-[14px] border border-hairline px-4 py-3 cursor-pointer hover:bg-surface-soft transition-colors {{ in_array($value, $traveler_preferences, true) ? 'border-rausch bg-rausch/5' : '' }}">
                        <input
                            type="checkbox"
                            wire:model="traveler_preferences"
                            value="{{ $value }}"
                            class="size-4 rounded border-hairline text-rausch focus:ring-rausch focus:ring-2"
                        />
                        <span class="text-sm font-medium text-ink">{{ $label }}</span>
                    </label>
                @endforeach
                @error('traveler_preferences') <span class="text-xs text-error-text block">{{ $message }}</span> @enderror

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active transition-colors shadow-airbnb mt-2">
                    {{ __('Save Preferences') }}
                </button>
            </form>
        </div>
    </aside>
</div>
