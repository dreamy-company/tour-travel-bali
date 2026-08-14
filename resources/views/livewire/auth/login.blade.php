<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        {{-- Header --}}
        <div class="space-y-1.5 text-center">
            <h1 class="text-2xl font-semibold tracking-[-0.44px] text-ink">{{ __('Welcome back') }}</h1>
            <p class="text-sm text-muted">{{ __('Log in to continue your Bali adventure.') }}</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <x-passkey-verify />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Email Address -->
            <div class="space-y-1.5">
                <label for="email" class="block text-sm font-semibold text-ink">{{ __('Email address') }}</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="email@example.com"
                    class="w-full rounded-lg border border-hairline bg-white px-3.5 py-2.5 text-sm text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch"
                />
                @error('email') <span class="mt-1 block text-xs text-error-text">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-semibold text-ink">{{ __('Password') }}</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" wire:navigate class="text-xs font-medium text-rausch hover:text-rausch-active transition-colors">
                            {{ __('Forgot password?') }}
                        </a>
                    @endif
                </div>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="{{ __('Password') }}"
                    class="w-full rounded-lg border border-hairline bg-white px-3.5 py-2.5 text-sm text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch"
                />
                @error('password') <span class="mt-1 block text-xs text-error-text">{{ $message }}</span> @enderror
            </div>

            <!-- Remember Me -->
            <label class="flex cursor-pointer select-none items-center gap-2.5 text-sm text-body">
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} class="size-4 rounded border-hairline text-rausch focus:ring-rausch focus:ring-2" />
                <span>{{ __('Remember me') }}</span>
            </label>

            <button
                type="submit"
                data-test="login-button"
                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-rausch px-6 py-3 text-sm font-semibold text-white shadow-airbnb transition-colors hover:bg-rausch-active"
            >
                {{ __('Log in') }}
            </button>
        </form>

        {{-- Divider --}}
        <div class="relative text-center">
            <div class="absolute inset-x-0 top-1/2 border-t border-hairline-soft"></div>
            <span class="relative inline-block bg-white px-3 text-xs font-medium uppercase tracking-wider text-muted-soft">{{ __('New to BaliGuide?') }}</span>
        </div>

        {{-- Sign-up role cards --}}
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ url('/register') }}" wire:navigate class="flex flex-col items-center gap-1.5 rounded-[14px] border border-hairline px-4 py-4 text-center transition-all hover:border-rausch hover:shadow-airbnb">
                <span class="text-2xl leading-none">🧳</span>
                <span class="text-sm font-semibold text-ink">{{ __('Create account') }}</span>
                <span class="text-xs text-muted">{{ __('Book a local guide') }}</span>
            </a>
            <a href="{{ url('/register/guide') }}" wire:navigate class="flex flex-col items-center gap-1.5 rounded-[14px] border border-hairline px-4 py-4 text-center transition-all hover:border-rausch hover:shadow-airbnb">
                <span class="text-2xl leading-none">🗺️</span>
                <span class="text-sm font-semibold text-ink">{{ __('Become a guide') }}</span>
                <span class="text-xs text-muted">{{ __('Earn as a local pro') }}</span>
            </a>
        </div>
    </div>
</x-layouts::auth>
