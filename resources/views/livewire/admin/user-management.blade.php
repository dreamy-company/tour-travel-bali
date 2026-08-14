<div class="space-y-4 w-full">
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

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 min-h-[calc(100vh-10rem)]">
        <!-- LEFT COLUMN: User list (master pane) -->
        <div class="lg:col-span-4 flex flex-col border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-4 shadow-xs">
            <div class="mb-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ __('User Management') }}</h3>
                    <span class="inline-flex items-center rounded-md bg-zinc-50 px-2 py-0.5 text-xs font-semibold text-zinc-600 ring-1 ring-inset ring-zinc-500/10 dark:bg-zinc-900 dark:text-zinc-400 dark:ring-zinc-400/20">
                        {{ $this->users->count() }}
                    </span>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Manage customers, guides, and their account details') }}</p>
            </div>

            {{-- Search --}}
            <input
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('Search by name, email, or phone') }}"
                class="w-full text-xs px-3 py-2 rounded-lg border border-zinc-200 bg-white text-zinc-900 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white placeholder:text-zinc-400 dark:placeholder:text-zinc-500 focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
            />

            {{-- Role filter --}}
            <div class="flex flex-wrap gap-1.5 mt-3">
                @foreach ([
                    '' => __('All'),
                    'customer' => __('Customers'),
                    'guide' => __('Guides'),
                ] as $value => $label)
                    <button
                        type="button"
                        wire:click="$set('roleFilter', '{{ $value }}')"
                        class="px-2.5 py-1 text-[11px] font-medium rounded-full border transition-colors {{ $roleFilter === $value ? 'bg-zinc-900 text-white border-zinc-900 dark:bg-white dark:text-zinc-900 dark:border-white' : 'bg-white text-zinc-600 border-zinc-200 hover:bg-zinc-50 dark:bg-zinc-900 dark:text-zinc-400 dark:border-zinc-700 dark:hover:bg-zinc-800' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            {{-- Status filter --}}
            <div class="flex flex-wrap gap-1.5 mt-2">
                @foreach ([
                    '' => __('All status'),
                    'active' => __('Active'),
                    'suspended' => __('Suspended'),
                    'banned' => __('Banned'),
                    'pending_verification' => __('Pending'),
                ] as $value => $label)
                    <button
                        type="button"
                        wire:click="$set('statusFilter', '{{ $value }}')"
                        class="px-2.5 py-1 text-[11px] font-medium rounded-full border transition-colors {{ $statusFilter === $value ? 'bg-zinc-900 text-white border-zinc-900 dark:bg-white dark:text-zinc-900 dark:border-white' : 'bg-white text-zinc-600 border-zinc-200 hover:bg-zinc-50 dark:bg-zinc-900 dark:text-zinc-400 dark:border-zinc-700 dark:hover:bg-zinc-800' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <hr class="border-zinc-200 dark:border-zinc-800 my-3" />

            {{-- User rows --}}
            <div class="flex-1 overflow-y-auto space-y-2 max-h-[30rem] lg:max-h-[42rem] pr-1">
                @forelse ($this->users as $user)
                    <button
                        type="button"
                        wire:click="selectUser({{ $user->id }})"
                        class="w-full text-left p-3 rounded-lg border transition-all duration-200 flex gap-3 items-start {{ $selectedUserId === $user->id ? 'bg-zinc-50 border-zinc-950 dark:bg-zinc-900 dark:border-white' : 'bg-transparent border-zinc-100 hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-900/50' }}"
                    >
                        <div class="size-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-semibold text-xs text-zinc-700 dark:text-zinc-300 shrink-0">
                            {{ $user->initials() }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 truncate">{{ $user->name }}</h4>
                                @php
                                    $statusBadge = match ($user->status->value) {
                                        'active' => ['bg-emerald-50 text-emerald-700 ring-emerald-600/10', __('Active')],
                                        'suspended' => ['bg-amber-50 text-amber-700 ring-amber-600/10', __('Suspended')],
                                        'banned' => ['bg-red-50 text-red-700 ring-red-600/10', __('Banned')],
                                        default => ['bg-zinc-50 text-zinc-600 ring-zinc-500/10', __('Pending')],
                                    };
                                @endphp
                                <span class="inline-flex shrink-0 items-center rounded-full px-2 py-0.5 text-[9px] font-semibold ring-1 ring-inset {{ $statusBadge[0] }}">
                                    {{ $statusBadge[1] }}
                                </span>
                            </div>
                            <p class="text-[10px] text-zinc-500 truncate mt-0.5">{{ $user->email }}</p>
                            <div class="flex items-center gap-2 mt-1 text-[9px] text-zinc-400">
                                <span class="inline-flex items-center rounded-full px-1.5 py-0.5 {{ $user->role->value === 'guide' ? 'bg-violet-50 text-violet-700 dark:bg-violet-950/50 dark:text-violet-400' : 'bg-sky-50 text-sky-700 dark:bg-sky-950/50 dark:text-sky-400' }}">
                                    {{ $user->role->value === 'guide' ? __('Guide') : __('Customer') }}
                                </span>
                                @if ($user->role->value === 'guide')
                                    <span>{{ $user->tour_packages_count }} {{ __('packages') }}</span>
                                    @if ($user->guide_reviews_avg_rating)
                                        <span>·</span>
                                        <span>{{ number_format($user->guide_reviews_avg_rating, 1) }} ★</span>
                                    @endif
                                @else
                                    <span>{{ $user->customer_bookings_count }} {{ __('bookings') }}</span>
                                @endif
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="text-center py-10 flex flex-col items-center justify-center">
                        <svg class="size-12 text-zinc-300 dark:text-zinc-700 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.978 11.978 0 0 1 12 20.25a11.978 11.978 0 0 1-3-1.013v-.11c0-1.109.326-2.14.887-3M9 19.128c-.89-.13-1.748-.415-2.522-.823a4.122 4.122 0 0 0-4.321 6.326 9.302 9.302 0 0 0 3.738-2.316m3.105-3.32a4.125 4.125 0 0 1 7.533-2.493M9 16.058v-.003c0-1.113.285-2.16.786-3.07M9 16.058A9 9 0 0 0 2.25 15M12 5.25a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm7.5 7.5a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5ZM4.5 12.75a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z"/></svg>
                        <p class="text-xs font-medium text-zinc-600 dark:text-zinc-400">{{ __('No users found') }}</p>
                        <p class="text-[10px] text-zinc-500 mt-1">{{ __('Try widening your search or filters.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- RIGHT COLUMN: User detail pane -->
        <div class="lg:col-span-8 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col">
            @if ($this->selectedUser)
                @php $user = $this->selectedUser; @endphp
                @php
                    $statusBadge = match ($user->status->value) {
                        'active' => ['bg-emerald-50 text-emerald-700 ring-emerald-600/10', __('Active')],
                        'suspended' => ['bg-amber-50 text-amber-700 ring-amber-600/10', __('Suspended')],
                        'banned' => ['bg-red-50 text-red-700 ring-red-600/10', __('Banned')],
                        default => ['bg-zinc-50 text-zinc-600 ring-zinc-500/10', __('Pending')],
                    };
                    $isGuide = $user->role->value === 'guide';
                @endphp
                <div class="space-y-6">
                    {{-- Header --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-100 dark:border-zinc-800 pb-4">
                        <div class="flex items-center gap-3.5">
                            <div class="size-12 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-bold text-zinc-700 dark:text-zinc-300">
                                {{ $user->initials() }}
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ $user->name }}</h2>
                                <p class="text-xs text-zinc-500">{{ $user->email }} · {{ $user->phone_number ?? '—' }}</p>
                                <p class="text-[10px] text-zinc-400 mt-0.5">{{ __('Joined') }} {{ $user->created_at?->format('F j, Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $isGuide ? 'bg-violet-50 text-violet-700 ring-violet-600/10 dark:bg-violet-950/50 dark:text-violet-400' : 'bg-sky-50 text-sky-700 ring-sky-600/10 dark:bg-sky-950/50 dark:text-sky-400' }}">
                                {{ $isGuide ? __('Guide') : __('Customer') }}
                            </span>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusBadge[0] }}">
                                {{ $statusBadge[1] }}
                            </span>
                            <button
                                type="button"
                                wire:click="openEdit({{ $user->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border border-zinc-300 text-zinc-700 hover:bg-zinc-50 transition-colors dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
                            >
                                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>
                                {{ __('Edit User') }}
                            </button>
                        </div>
                    </div>

                    {{-- Stats --}}
                    @if ($isGuide)
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach ([
                                ['label' => __('Packages'), 'value' => (string) $user->tour_packages_count],
                                ['label' => __('Bookings'), 'value' => (string) $user->guide_bookings_count],
                                ['label' => __('Rating'), 'value' => $user->guide_reviews_avg_rating ? number_format($user->guide_reviews_avg_rating, 1) : '—'],
                                ['label' => __('Strikes'), 'value' => (string) ($user->guideProfile?->strikes ?? 0)],
                            ] as $stat)
                                <div class="rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 p-3">
                                    <p class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ $stat['value'] }}</p>
                                    <p class="text-[10px] font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $stat['label'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach ([
                                ['label' => __('Bookings'), 'value' => (string) $user->customer_bookings_count],
                                ['label' => __('Favorites'), 'value' => (string) $user->favorites_count],
                                ['label' => __('Zodiac'), 'value' => $user->zodiac() ? $user->zodiac()->symbol().' '.$user->zodiac()->label() : '—'],
                                ['label' => __('Status'), 'value' => $statusBadge[1]],
                            ] as $stat)
                                <div class="rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 p-3">
                                    <p class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ $stat['value'] }}</p>
                                    <p class="text-[10px] font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $stat['label'] }}</p>
                                </div>
                            @endforeach
                        </div>

                        {{-- Traveler persona --}}
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4">
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ __('Traveler Persona') }}</h3>
                            <p class="text-[10px] text-zinc-500 mt-0.5">{{ __('Personality preferences used for matching') }}</p>
                            <div class="flex flex-wrap gap-1.5 mt-3">
                                @php
                                    $personaLabels = [
                                        'introvert' => 'Introvert', 'cafe_hopper' => 'Cafe Hopper', 'photography_enthusiast' => 'Photography Enthusiast',
                                        'adventurer' => 'Adventurer', 'culture_lover' => 'Culture Lover', 'night_owl' => 'Night Owl',
                                        'foodie' => 'Foodie', 'wellness_seeker' => 'Wellness Seeker',
                                    ];
                                @endphp
                                @forelse ($user->traveler_preferences ?? [] as $pref)
                                    <span class="inline-flex items-center rounded-full bg-zinc-50 px-2.5 py-1 text-[10px] font-medium text-zinc-600 ring-1 ring-inset ring-zinc-500/10 dark:bg-zinc-900 dark:text-zinc-300 dark:ring-zinc-400/20">
                                        {{ $personaLabels[$pref] ?? $pref }}
                                    </span>
                                @empty
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('No traveler preferences set.') }}</p>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    @if ($isGuide && $user->guideProfile)
                        @php $profile = $user->guideProfile; @endphp

                        {{-- Service repository --}}
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ __('Service Repository') }}</h3>
                                    <p class="text-[10px] text-zinc-500 mt-0.5">{{ __('Tour packages offered by this guide') }}</p>
                                </div>
                                @if ($user->tour_packages_count > 0)
                                    <button
                                        type="button"
                                        wire:click="deleteRepository"
                                        wire:confirm="{{ __('Delete all tour packages for this guide? This action cannot be undone.') }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-[11px] font-semibold rounded-lg border border-red-200 text-red-700 hover:bg-red-50 transition-colors dark:border-red-800/40 dark:text-red-400 dark:hover:bg-red-950/40"
                                    >
                                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                        {{ __('Delete Repository') }}
                                    </button>
                                @endif
                            </div>
                            @forelse ($user->tourPackages as $package)
                                <div class="flex items-center justify-between gap-3 py-2 border-b border-zinc-100 last:border-0 dark:border-zinc-800">
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 truncate">{{ $package->title }}</p>
                                        <p class="text-[10px] text-zinc-500">{{ __('Rp') }} {{ number_format((float) $package->price, 0, ',', '.') }} · {{ implode(', ', $package->destinations ?? []) }}</p>
                                    </div>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold {{ $package->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-zinc-100 text-zinc-500' }}">{{ $package->is_active ? __('Active') : __('Inactive') }}</span>
                                </div>
                            @empty
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('No tour packages — guide offers custom itineraries only.') }}</p>
                            @endforelse
                        </div>

                        {{-- Documents --}}
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ __('KYC Documents') }}</h3>
                                    <p class="text-[10px] text-zinc-500 mt-0.5">{{ __('Uploaded identity and legality files') }}</p>
                                </div>
                                @if ($profile->ktp_photo || $profile->headshot || $profile->ktpp_file || $profile->skck_file || $profile->surat_sehat_file)
                                    <button
                                        type="button"
                                        wire:click="deleteDocuments"
                                        wire:confirm="{{ __('Delete all uploaded documents for this guide? Verification will be revoked.') }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-[11px] font-semibold rounded-lg border border-red-200 text-red-700 hover:bg-red-50 transition-colors dark:border-red-800/40 dark:text-red-400 dark:hover:bg-red-950/40"
                                    >
                                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                        {{ __('Delete Documents') }}
                                    </button>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach ([
                                    ['key' => 'ktp_photo', 'label' => 'KTP Photo'],
                                    ['key' => 'headshot', 'label' => 'Headshot'],
                                    ['key' => 'ktpp_file', 'label' => 'KTPP License'],
                                    ['key' => 'skck_file', 'label' => 'Police Clearance'],
                                    ['key' => 'surat_sehat_file', 'label' => 'Health Certificate'],
                                ] as $doc)
                                    @php $present = $profile->getAttribute($doc['key']); @endphp
                                    @if ($present)
                                        <a
                                            href="{{ route('admin.documents.show', [$profile, $doc['key']]) }}"
                                            target="_blank"
                                            class="flex items-center gap-2 rounded-lg border border-zinc-200 dark:border-zinc-800 px-3 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-900/50 transition-colors"
                                        >
                                            <svg class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                            <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">{{ $doc['label'] }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        {{-- Wallet --}}
                        @if ($user->guideWallet)
                            <div class="flex items-center justify-between rounded-xl border border-zinc-200 dark:border-zinc-800 p-4">
                                <div>
                                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ __('Wallet Balance') }}</h3>
                                    <p class="text-[10px] text-zinc-500 mt-0.5">{{ __('Escrow & payout balance held for this guide') }}</p>
                                </div>
                                <span class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Rp {{ number_format((float) $user->guideWallet->current_balance, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        {{-- Danger zone --}}
                        <div class="border border-red-200 dark:border-red-900/40 rounded-xl p-4 space-y-3">
                            <div>
                                <h3 class="text-sm font-bold text-red-700 dark:text-red-400">{{ __('Danger Zone') }}</h3>
                                <p class="text-[10px] text-zinc-500 mt-0.5">{{ __('Irreversible account actions') }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    type="button"
                                    wire:click="toggleBan"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg border {{ $user->status->value === 'banned' ? 'border-emerald-300 text-emerald-700 hover:bg-emerald-50 dark:border-emerald-700/40 dark:text-emerald-400 dark:hover:bg-emerald-950/40' : 'border-amber-300 text-amber-700 hover:bg-amber-50 dark:border-amber-700/40 dark:text-amber-400 dark:hover:bg-amber-950/40' }} transition-colors"
                                >
                                    @if ($user->status->value === 'banned')
                                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                        {{ __('Unban Guide') }}
                                    @else
                                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        {{ __('Ban Guide') }}
                                    @endif
                                </button>
                                <button
                                    type="button"
                                    wire:click="deleteGuide"
                                    wire:confirm="{{ __('Permanently delete this guide and ALL associated data? This cannot be undone.') }}"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg border border-red-300 text-red-700 hover:bg-red-50 dark:border-red-700/40 dark:text-red-400 dark:hover:bg-red-950/40 transition-colors"
                                >
                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                    {{ __('Delete Guide') }}
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center text-center py-20">
                    <svg class="size-14 text-zinc-300 dark:text-zinc-700 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Select a user to manage') }}</p>
                    <p class="text-xs text-zinc-500 mt-1">{{ __('Choose a customer or guide to inspect and edit their account.') }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Edit user modal --}}
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data @keydown.escape.window="$wire.closeEdit()">
            <div class="absolute inset-0 bg-black/50" wire:click="closeEdit"></div>
            <div class="relative w-full max-w-lg rounded-xl bg-white dark:bg-stone-950 border border-zinc-200 dark:border-zinc-800 shadow-xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-200 dark:border-zinc-800">
                    <div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">{{ __('Edit User') }}</h3>
                        <p class="text-xs text-zinc-500">{{ __('Update account details for this user') }}</p>
                    </div>
                    <button type="button" wire:click="closeEdit" class="flex size-8 items-center justify-center rounded-full text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-800 dark:hover:text-zinc-300 transition-colors">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="saveUser" class="px-6 py-5 space-y-4">
                    <div class="space-y-1.5">
                        <label for="edit-name" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('Full Name') }}</label>
                        <input id="edit-name" wire:model="editName" type="text" class="w-full text-xs px-3 py-2 rounded-lg border border-zinc-200 bg-white text-zinc-900 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white" />
                        @error('editName') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="edit-email" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('Email Address') }}</label>
                        <input id="edit-email" wire:model="editEmail" type="email" class="w-full text-xs px-3 py-2 rounded-lg border border-zinc-200 bg-white text-zinc-900 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white" />
                        @error('editEmail') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="edit-phone" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('Phone Number') }}</label>
                            <input id="edit-phone" wire:model="editPhone" type="tel" placeholder="08xxxxxxxxxx" class="w-full text-xs px-3 py-2 rounded-lg border border-zinc-200 bg-white text-zinc-900 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white placeholder:text-zinc-400 focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white" />
                            @error('editPhone') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label for="edit-birth-date" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('Birth Date') }}</label>
                            <input id="edit-birth-date" wire:model="editBirthDate" type="date" max="{{ now()->toDateString() }}" class="w-full text-xs px-3 py-2 rounded-lg border border-zinc-200 bg-white text-zinc-900 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white" />
                            @error('editBirthDate') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            @php $editSign = \App\Services\ZodiacService::fromDate($editBirthDate); @endphp
                            @if ($editSign !== null)
                                <p class="text-[10px] text-zinc-500 mt-1">{{ $editSign->symbol() }} {{ $editSign->label() }} · {{ ucfirst($editSign->element()) }} element</p>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="edit-status" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('Status') }}</label>
                            <select id="edit-status" wire:model="editStatus" class="w-full text-xs px-3 py-2 rounded-lg border border-zinc-200 bg-white text-zinc-900 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white">
                                @foreach (\App\Enums\UserStatus::cases() as $status)
                                    <option value="{{ $status->value }}">{{ ucfirst($status->value) }}</option>
                                @endforeach
                            </select>
                            @error('editStatus') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label for="edit-role" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('Role') }}</label>
                            <select id="edit-role" wire:model="editRole" class="w-full text-xs px-3 py-2 rounded-lg border border-zinc-200 bg-white text-zinc-900 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white">
                                <option value="customer">{{ __('Customer') }}</option>
                                <option value="guide">{{ __('Guide') }}</option>
                            </select>
                            @error('editRole') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    @if ($editingUserId && ($this->selectedUser?->id === $editingUserId) && $this->selectedUser?->role === \App\Enums\UserRole::GUIDE && $this->selectedUser->guideProfile)
                        <p class="text-[10px] text-amber-600 dark:text-amber-500">
                            {{ __('Note: this guide has an active profile — changing the role to Customer is blocked until the guide profile is deleted.') }}
                        </p>
                    @endif

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeEdit" class="px-4 py-2 text-xs font-semibold rounded-lg border border-zinc-300 text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800 transition-colors">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 transition-colors">
                            {{ __('Save Changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
