<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        {{-- Header --}}
        <div class="space-y-1.5 text-center">
            <h1 class="text-2xl font-semibold tracking-[-0.44px] text-ink">{{ __('Create your account') }}</h1>
            <p class="text-sm text-muted">{{ __('Join BaliGuide and meet guides of the same frequency.') }}</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Name -->
            <div class="space-y-1.5">
                <label for="name" class="block text-sm font-semibold text-ink">{{ __('Full name') }}</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="{{ __('Full name') }}"
                    class="w-full rounded-lg border border-hairline bg-white px-3.5 py-2.5 text-sm text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch"
                />
                @error('name') <span class="mt-1 block text-xs text-error-text">{{ $message }}</span> @enderror
            </div>

            <!-- Email Address -->
            <div class="space-y-1.5">
                <label for="email" class="block text-sm font-semibold text-ink">{{ __('Email address') }}</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    placeholder="email@example.com"
                    class="w-full rounded-lg border border-hairline bg-white px-3.5 py-2.5 text-sm text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch"
                />
                @error('email') <span class="mt-1 block text-xs text-error-text">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <label for="password" class="block text-sm font-semibold text-ink">{{ __('Password') }}</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="{{ __('Password') }}"
                    passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                    class="w-full rounded-lg border border-hairline bg-white px-3.5 py-2.5 text-sm text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch"
                />
                @error('password') <span class="mt-1 block text-xs text-error-text">{{ $message }}</span> @enderror
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-sm font-semibold text-ink">{{ __('Confirm password') }}</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="{{ __('Confirm password') }}"
                    passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                    class="w-full rounded-lg border border-hairline bg-white px-3.5 py-2.5 text-sm text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch"
                />
            </div>

            <button
                type="submit"
                data-test="register-user-button"
                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-rausch px-6 py-3 text-sm font-semibold text-white shadow-airbnb transition-colors hover:bg-rausch-active"
            >
                {{ __('Create account') }}
            </button>
        </form>

        {{-- Alt registration path + login link --}}
        <div class="flex flex-col items-center gap-2 text-center text-sm text-muted">
            <div class="flex items-center gap-2 text-xs text-muted-soft">
                <span class="inline-block h-px w-8 bg-hairline"></span>
                {{ __('Want to earn as a guide?') }}
                <span class="inline-block h-px w-8 bg-hairline"></span>
            </div>
            <a href="{{ url('/register/guide') }}" wire:navigate class="font-semibold text-rausch hover:text-rausch-active transition-colors">
                {{ __('Register as a Tour Guide') }}
            </a>
        </div>

        <p class="text-center text-sm text-muted">
            {{ __('Already have an account?') }}
            <a href="{{ route('login') }}" wire:navigate class="font-semibold text-rausch hover:text-rausch-active transition-colors">{{ __('Log in') }}</a>
        </p>
    </div>
</x-layouts::auth>
