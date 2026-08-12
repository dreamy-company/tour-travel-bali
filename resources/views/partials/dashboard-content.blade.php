    <div class="space-y-6">
        <!-- Session Flash Messages -->
        @if (session()->has('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-zinc-900 dark:text-green-400 border border-green-200 dark:border-green-800/30 flex items-center justify-between" role="alert">
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-zinc-900 dark:text-red-400 border border-red-200 dark:border-red-800/30 flex items-center justify-between" role="alert">
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Header Section -->
        <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4">
            <h1 class="text-2xl font-bold text-zinc-950 dark:text-white">{{ __('User Dashboard') }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                {{ __('Logged in as :name (:role)', ['name' => auth()->user()->name, 'role' => ucfirst(auth()->user()->role->value)]) }}
            </p>
        </div>

        <!-- 1. ADMIN DASHBOARD VIEW -->
        @if (auth()->user()->role === \App\Enums\UserRole::ADMIN)
            @livewire('admin.admin-dashboard')

        <!-- 2. TOUR GUIDE DASHBOARD VIEW -->
        @elseif (auth()->user()->role === \App\Enums\UserRole::GUIDE)
            @php
                $profile = auth()->user()->guideProfile;
            @endphp
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Status Card -->
                <div class="lg:col-span-1 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col gap-4">
                    <div>
                        <span class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider block">{{ __('Verification Check') }}</span>
                        @if ($profile && $profile->is_verified)
                            <div class="mt-2 inline-flex items-center rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/10 dark:bg-emerald-950/20 dark:text-emerald-400 dark:ring-emerald-500/20">
                                {{ __('Verified Local Guide') }}
                            </div>
                            <p class="text-xs text-zinc-500 mt-3 leading-relaxed">
                                Your account is verified! You can list packages, manage pricing rates, and receive tourist bookings.
                            </p>
                        @elseif ($profile && $profile->rejection_reason)
                            <div class="mt-2 inline-flex items-center rounded-md bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-600/10 dark:bg-red-950/20 dark:text-red-400 dark:ring-red-500/20">
                                {{ __('Onboarding Rejected') }}
                            </div>
                            <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-800 text-xs dark:bg-red-950/20 dark:border-red-900/50 dark:text-red-400 mt-3 leading-relaxed">
                                <strong>Feedback:</strong> {{ $profile->rejection_reason }}
                            </div>
                        @else
                            <div class="mt-2 inline-flex items-center rounded-md bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/10 dark:bg-amber-950/20 dark:text-amber-400 dark:ring-amber-500/20">
                                {{ __('Verification Pending') }}
                            </div>
                            <p class="text-xs text-zinc-500 mt-3 leading-relaxed">
                                Your credentials are currently being reviewed by administrators. You will be able to customize services once approved.
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Shortcuts Card -->
                <div class="lg:col-span-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col gap-6">
                    <div>
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">{{ __('Guide Control Center') }}</h3>
                        <p class="text-xs text-zinc-500 mt-1">{{ __('Quick links to manage your Balinese marketplace operations.') }}</p>
                    </div>

                    <hr class="border-zinc-200 dark:border-zinc-800" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Services Link -->
                        <a 
                            href="{{ route('guide.services') }}" 
                            class="border border-zinc-100 dark:border-zinc-900 hover:border-zinc-300 dark:hover:border-zinc-700 p-4 rounded-xl flex flex-col justify-between gap-4 group transition-colors bg-zinc-50/30 dark:bg-zinc-900/10"
                        >
                            <div>
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 group-hover:text-zinc-950 dark:group-hover:text-white">{{ __('Manage Packages & Rates') }}</h4>
                                <p class="text-[11px] text-zinc-500 mt-1 leading-relaxed">{{ __('Toggle between daily/hourly rates and publish predefined Balinese tour packages.') }}</p>
                            </div>
                            <span class="text-xs font-semibold text-zinc-900 dark:text-white inline-flex items-center gap-1">
                                {{ __('Configure Services') }}
                                <svg class="size-3 stroke-current group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                            </span>
                        </a>

                        <!-- Orders Link -->
                        <a 
                            href="{{ route('guide.orders') }}" 
                            class="border border-zinc-100 dark:border-zinc-900 hover:border-zinc-300 dark:hover:border-zinc-700 p-4 rounded-xl flex flex-col justify-between gap-4 group transition-colors bg-zinc-50/30 dark:bg-zinc-900/10"
                        >
                            <div>
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 group-hover:text-zinc-950 dark:group-hover:text-white">{{ __('Tour Orders & Bookings') }}</h4>
                                <p class="text-[11px] text-zinc-500 mt-1 leading-relaxed">{{ __('Audit incoming requests, accept custom itineraries, and progress active trip checkpoints.') }}</p>
                            </div>
                            <span class="text-xs font-semibold text-zinc-900 dark:text-white inline-flex items-center gap-1">
                                {{ __('Audit Orders') }}
                                <svg class="size-3 stroke-current group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                            </span>
                        </a>
                    </div>
                </div>
            </div>

        <!-- 3. CUSTOMER DASHBOARD VIEW -->
        @else
            @livewire('customer.customer-dashboard')
        @endif
    </div>
