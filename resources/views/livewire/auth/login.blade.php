<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6 py-12">
        {{-- Header --}}
        <div class="space-y-1.5 text-center">
            <h1 class="text-2xl font-semibold tracking-[-0.44px] text-ink">{{ __('Welcome back') }}</h1>
            <p class="text-sm text-muted">{{ __('Log in to continue your Bali adventure.') }}</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />


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
            <div class="space-y-1.5" x-data="{ show: false }">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-semibold text-ink">{{ __('Password') }}</label>
                    <div class="flex items-center gap-3">
                        <button 
                            type="button" 
                            @click="show = !show" 
                            class="text-xs font-medium text-muted hover:text-ink transition-colors select-none flex items-center gap-1 cursor-pointer"
                            tabindex="-1"
                        >
                            <span x-show="!show" class="flex items-center gap-1">
                                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                <span>{{ __('Show') }}</span>
                            </span>
                            <span x-show="show" x-cloak class="flex items-center gap-1">
                                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                <span>{{ __('Hide') }}</span>
                            </span>
                        </button>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" wire:navigate class="text-xs font-medium text-rausch hover:text-rausch-active transition-colors">
                                {{ __('Forgot password?') }}
                            </a>
                        @endif
                    </div>
                </div>
                <div class="relative">
                    <input
                        id="password"
                        name="password"
                        :type="show ? 'text' : 'password'"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="{{ __('Password') }}"
                        class="w-full rounded-lg border border-hairline bg-white px-3.5 py-2.5 text-sm text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch pr-10"
                    />
                    <button 
                        type="button" 
                        @click="show = !show" 
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-muted-soft hover:text-ink cursor-pointer transition-colors"
                        tabindex="-1"
                        aria-label="{{ __('Toggle password visibility') }}"
                    >
                        <svg x-show="!show" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                        <svg x-show="show" x-cloak class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                    </button>
                </div>
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
