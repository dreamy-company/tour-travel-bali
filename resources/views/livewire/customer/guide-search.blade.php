<div>
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
        <!-- LEFT PANEL: Search Filters (4 Cols) — Airbnb category-strip style -->
        <div class="lg:col-span-4 flex flex-col border border-hairline rounded-[14px] bg-white dark:bg-stone-950 p-6 gap-6 h-fit">
            <div>
                <h3 class="text-[22px] font-medium tracking-[-0.44px] text-ink dark:text-zinc-100 leading-tight">{{ __('Search Filters') }}</h3>
                <p class="text-sm text-muted dark:text-zinc-400 mt-1">{{ __('Match with verified guides based on your travel needs.') }}</p>
            </div>

            <hr class="border-hairline dark:border-zinc-800" />

            <!-- Name Search -->
            <div class="relative">
                <svg class="size-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-muted-soft pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                <input 
                    wire:model.live.debounce.300ms="searchQuery" 
                    type="search" 
                    placeholder="Search guide name..." 
                    class="w-full text-sm pl-10 pr-3.5 py-2.5 rounded-full border border-hairline bg-white text-ink dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-rausch placeholder:text-muted-soft"
                />
            </div>

            <!-- Pricing Range -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-ink dark:text-zinc-300 block">{{ __('Pricing Range (IDR)') }}</label>
                <div class="grid grid-cols-2 gap-2">
                    <input 
                        wire:model.live.debounce.500ms="minPrice" 
                        placeholder="Min Rp" 
                        type="number" 
                        class="w-full text-sm px-3.5 py-2.5 rounded-full border border-hairline bg-white text-ink dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-rausch"
                    />
                    <input 
                        wire:model.live.debounce.500ms="maxPrice" 
                        placeholder="Max Rp" 
                        type="number" 
                        class="w-full text-sm px-3.5 py-2.5 rounded-full border border-hairline bg-white text-ink dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-rausch"
                    />
                </div>
            </div>

            <!-- Minimum Rating Filter -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-ink dark:text-zinc-300 block">{{ __('Minimum Rating') }}</label>
                <select 
                    wire:model.live="minRating" 
                    class="w-full text-sm px-3.5 py-2.5 rounded-full border border-hairline bg-white text-ink dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-rausch"
                >
                    <option value="">{{ __('Any Rating') }}</option>
                    <option value="5">★★★★★ (5.0)</option>
                    <option value="4">★★★★☆ (4.0+)</option>
                    <option value="3">★★★☆☆ (3.0+)</option>
                </select>
            </div>

            <!-- Activity Specializations (FR-02-01: same interest) -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-ink dark:text-zinc-300 block">{{ __('Activity Specialization') }}</label>
                <div class="flex flex-col gap-2">
                    @foreach (\App\Enums\Specialization::cases() as $spec)
                        <label class="inline-flex items-center gap-2.5 text-sm text-body dark:text-zinc-400 cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model.live="selectedSpecializations"
                                value="{{ $spec->value }}"
                                class="rounded border-hairline text-rausch focus:ring-rausch dark:border-zinc-700 dark:bg-zinc-800"
                            />
                            <span>{{ $spec->label() }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Communication Style (FR-02-01: same vibe) -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-ink dark:text-zinc-300 block">{{ __('Communication Style') }}</label>
                <select
                    wire:model.live="communicationStyle"
                    class="w-full text-sm px-3.5 py-2.5 rounded-full border border-hairline bg-white text-ink dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-rausch"
                >
                    <option value="">{{ __('Any Vibe') }}</option>
                    @foreach (\App\Enums\CommunicationStyle::cases() as $style)
                        <option value="{{ $style->value }}">{{ $style->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Languages -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-ink dark:text-zinc-300 block">{{ __('Languages Fluency') }}</label>
                <div class="flex flex-col gap-2">
                    <label class="inline-flex items-center gap-2.5 text-sm text-body dark:text-zinc-400 cursor-pointer">
                        <input 
                            type="checkbox" 
                            wire:model.live="selectedLanguages" 
                            value="id" 
                            class="rounded border-hairline text-rausch focus:ring-rausch dark:border-zinc-700 dark:bg-zinc-800"
                        />
                        <span>Indonesian</span>
                    </label>
                    <label class="inline-flex items-center gap-2.5 text-sm text-body dark:text-zinc-400 cursor-pointer">
                        <input 
                            type="checkbox" 
                            wire:model.live="selectedLanguages" 
                            value="en" 
                            class="rounded border-hairline text-rausch focus:ring-rausch dark:border-zinc-700 dark:bg-zinc-800"
                        />
                        <span>English</span>
                    </label>
                    <label class="inline-flex items-center gap-2.5 text-sm text-body dark:text-zinc-400 cursor-pointer">
                        <input 
                            type="checkbox" 
                            wire:model.live="selectedLanguages" 
                            value="jp" 
                            class="rounded border-hairline text-rausch focus:ring-rausch dark:border-zinc-700 dark:bg-zinc-800"
                        />
                        <span>Japanese</span>
                    </label>
                    <label class="inline-flex items-center gap-2.5 text-sm text-body dark:text-zinc-400 cursor-pointer">
                        <input 
                            type="checkbox" 
                            wire:model.live="selectedLanguages" 
                            value="fr" 
                            class="rounded border-hairline text-rausch focus:ring-rausch dark:border-zinc-700 dark:bg-zinc-800"
                        />
                        <span>French</span>
                    </label>
                    <label class="inline-flex items-center gap-2.5 text-sm text-body dark:text-zinc-400 cursor-pointer">
                        <input 
                            type="checkbox" 
                            wire:model.live="selectedLanguages" 
                            value="de" 
                            class="rounded border-hairline text-rausch focus:ring-rausch dark:border-zinc-700 dark:bg-zinc-800"
                        />
                        <span>German</span>
                    </label>
                </div>
            </div>
        </div>

    <!-- RIGHT PANEL: Guide List / Booking Form (8 Cols) -->
    <div class="lg:col-span-8 flex flex-col gap-6">
        @if ($selectedGuideId && $this->selectedGuide)
            @php
                $guide = $this->selectedGuide;
            @endphp
            <!-- Custom Itinerary & Booking Pane — Airbnb reservation-card styling -->
            <div class="border border-hairline rounded-[14px] bg-white dark:bg-stone-950 p-6 flex flex-col gap-6 shadow-airbnb">
                <!-- Selected Guide summary -->
                <div class="flex items-center justify-between border-b border-hairline-soft dark:border-zinc-800 pb-4">
                    <div class="flex items-center gap-3.5">
                        <div class="size-12 rounded-full bg-surface-soft dark:bg-zinc-800 flex items-center justify-center font-semibold text-ink dark:text-zinc-200">
                            {{ $guide->initials() }}
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-ink dark:text-zinc-100">{{ __('Configure Itinerary with :name', ['name' => $guide->name]) }}</h3>
                            <p class="text-sm text-muted mt-0.5">
                                Tariff: <span class="font-semibold text-ink dark:text-zinc-200">Rp {{ number_format($guide->guideProfile->base_rate, 0, ',', '.') }} / {{ $guide->guideProfile->tariff_mode->value }}</span>
                            </p>
                        </div>
                    </div>
                    <button 
                        wire:click="$set('selectedGuideId', null)" 
                        type="button"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-full border border-hairline text-ink hover:bg-surface-soft dark:border-zinc-800 dark:hover:bg-zinc-800 dark:text-zinc-300 transition-colors"
                    >
                        <svg class="size-3.5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg>
                        {{ __('Back to Search') }}
                    </button>
                </div>

                <!-- Session Flash Message for Guest -->
                @if (session()->has('warning'))
                    <div class="p-3 text-sm bg-amber-50 border border-amber-200 text-amber-800 rounded-lg dark:bg-amber-950/20 dark:border-amber-900/50 dark:text-amber-400">
                        {{ session('warning') }}
                    </div>
                @endif

                <!-- Form Inputs -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-ink dark:text-zinc-300 block">{{ __('Pickup Location') }}</label>
                        <input 
                            wire:model="pickupLocation" 
                            type="text" 
                            placeholder="Hotel name, airport, or specific coordinates" 
                            required 
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-hairline bg-white text-ink dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-rausch"
                        />
                        @error('pickupLocation') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-ink dark:text-zinc-300 block">{{ __('Drop-off Location (Optional)') }}</label>
                        <input 
                            wire:model="dropoffLocation" 
                            type="text" 
                            placeholder="Drop-off point (if different from pickup)" 
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-hairline bg-white text-ink dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-rausch"
                        />
                        @error('dropoffLocation') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-ink dark:text-zinc-300 block">{{ __('Schedule Date') }}</label>
                        <input 
                            wire:model="scheduleDate" 
                            type="date" 
                            required 
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-hairline bg-white text-ink dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-rausch"
                        />
                        @error('scheduleDate') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-ink dark:text-zinc-300 block">{{ __('Pickup Time') }}</label>
                        <input 
                            wire:model="pickupTime" 
                            type="time" 
                            required 
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-hairline bg-white text-ink dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-rausch"
                        />
                        @error('pickupTime') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Custom Itinerary destinations tag builder -->
                <div class="space-y-3">
                    <label class="text-sm font-medium text-ink dark:text-zinc-300 block">{{ __('Custom Route Places') }}</label>
                    <div class="flex gap-2">
                        <input 
                            wire:model="newDestination" 
                            wire:keydown.enter="addDestination"
                            placeholder="Add point of interest (e.g. Uluwatu Temple, Tegalalang)" 
                            class="flex-1 text-sm px-3.5 py-2.5 rounded-full border border-hairline bg-white text-ink dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-rausch"
                        />
                        <button 
                            wire:click="addDestination" 
                            type="button"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium rounded-full bg-surface-strong text-ink hover:bg-hairline dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700 transition-colors"
                        >
                            {{ __('Add Place') }}
                        </button>
                    </div>
                    @error('newDestination') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                    @error('customDestinations') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror

                    <!-- Places tags list -->
                    <div class="flex flex-wrap gap-2 mt-2 p-3 rounded-lg border border-hairline-soft dark:border-zinc-800 bg-surface-soft dark:bg-zinc-950/50 min-h-12">
                        @forelse ($customDestinations as $index => $dest)
                            <div class="inline-flex items-center gap-1 bg-ink text-white dark:bg-zinc-800 text-xs font-medium px-2.5 py-1 rounded-full">
                                <span>{{ $dest }}</span>
                                <button 
                                    type="button" 
                                    wire:click="removeDestination({{ $index }})" 
                                    class="hover:text-rausch-disabled focus:outline-hidden"
                                >
                                    <svg class="size-3 fill-current" viewBox="0 0 20 20"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
                                </button>
                            </div>
                        @empty
                            <span class="text-sm text-muted-soft my-auto">{{ __('No destinations added yet. Add at least one.') }}</span>
                        @endforelse
                    </div>
                </div>

                <!-- Leaflet Maps Integration -->
                <div wire:ignore class="border border-hairline dark:border-zinc-800 rounded-[14px] overflow-hidden shadow-xs relative z-0">
                    <div id="map" class="h-96 w-full"></div>
                </div>

                <!-- Estimation breakdown and Pricing -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-t border-hairline-soft dark:border-zinc-800 pt-5 mt-2">
                    <div class="p-3 border border-hairline rounded-lg dark:border-zinc-800">
                        <span class="text-[11px] font-semibold text-muted-soft uppercase tracking-wider">{{ __('Estimated Distance') }}</span>
                        <p class="text-lg font-semibold text-ink dark:text-zinc-200 mt-1">
                            {{ number_format($this->calculateItineraryDistance(), 1, '.', ',') }} km
                        </p>
                    </div>
                    <div class="p-3 border border-hairline rounded-lg dark:border-zinc-800">
                        <span class="text-[11px] font-semibold text-muted-soft uppercase tracking-wider">{{ __('Estimated Duration') }}</span>
                        <p class="text-lg font-semibold text-ink dark:text-zinc-200 mt-1">
                            {{ number_format($this->calculateItineraryDuration(), 1, '.', ',') }} hrs
                        </p>
                    </div>
                    <div class="p-3 rounded-lg bg-rausch text-white dark:bg-white dark:text-black">
                        <span class="text-[11px] font-semibold text-white/80 dark:text-zinc-500 uppercase tracking-wider">{{ __('Estimated Total Price') }}</span>
                        <p class="text-lg font-bold mt-1">
                            Rp {{ number_format($this->totalPrice, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <!-- Book action -->
                <div class="flex justify-between items-center gap-4 mt-4">
                    <button 
                        wire:click="$set('selectedGuideId', null)" 
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-full hover:bg-surface-soft dark:hover:bg-zinc-800 text-ink dark:text-zinc-300 transition-colors"
                    >
                        {{ __('Cancel') }}
                    </button>
                    @if (! Auth::check())
                        <a 
                            href="{{ route('login') }}" 
                            wire:navigate
                            class="inline-flex items-center px-6 py-3 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-airbnb"
                        >
                            {{ __('Log In to Request Booking') }}
                        </a>
                    @else
                        <button 
                            wire:click="book" 
                            type="button"
                            class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-airbnb"
                        >
                            <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                            {{ __('Submit Booking Request') }}
                        </button>
                    @endif
                </div>
            </div>
        @else
            <!-- Guides Search Grid — Airbnb property-card style -->
            <div class="flex flex-col gap-6">
                <div class="flex items-end justify-between">
                    <div>
                        <h3 class="text-[22px] font-medium tracking-[-0.44px] text-ink dark:text-zinc-100">{{ __('Verified Tour Guides') }}</h3>
                        <p class="text-sm text-muted dark:text-zinc-400 mt-1">{{ __('Select a guide to configure a custom travel itinerary around Bali.') }}</p>
                    </div>
                </div>

                <!-- Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6">
                    @forelse ($this->guides as $guideItem)
                        <div class="flex flex-col gap-3 group cursor-pointer" wire:click="selectGuide({{ $guideItem->id }})">
                            <!-- Photo plate -->
                            <div class="relative aspect-[4/3] overflow-hidden rounded-[14px] bg-surface-soft dark:bg-zinc-900 border border-hairline dark:border-zinc-800">
                                <!-- Decorative gradient plate -->
                                <div class="absolute inset-0 bg-gradient-to-br from-surface-strong via-surface-soft to-white dark:from-zinc-800 dark:via-zinc-900 dark:to-zinc-950"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="size-20 rounded-full bg-white dark:bg-zinc-800 flex items-center justify-center text-2xl font-semibold text-ink dark:text-zinc-200 shadow-airbnb">
                                        {{ $guideItem->initials() }}
                                    </div>
                                </div>

                                <!-- Guest-favorite style badge -->
                                <div class="absolute top-3 left-3 inline-flex items-center rounded-full bg-white dark:bg-zinc-900 px-3 py-1.5 text-xs font-semibold text-ink dark:text-zinc-200 shadow-airbnb">
                                    <svg class="size-3 text-rausch fill-current mr-1" viewBox="0 0 20 20"><path d="M10.868 2.784a.75.75 0 0 0-1.736 0l-1.87 3.79-4.183.608a.75.75 0 0 0-.416 1.28l3.028 2.951-.715 4.167a.75.75 0 0 0 1.09.79l3.74-1.966 3.74 1.966a.75.75 0 0 0 1.09-.79l-.715-4.167 3.028-2.951a.75.75 0 0 0-.416-1.28l-4.183-.608-1.87-3.79Z"/></svg>
                                    Verified Guide
                                </div>

                                <!-- Heart save icon (Rausch save state) -->
                                <div class="absolute top-3 right-3 size-8 rounded-full bg-white/90 dark:bg-zinc-900/90 flex items-center justify-center text-rausch shadow-airbnb transition-transform group-hover:scale-110">
                                    <svg class="size-4 fill-current" viewBox="0 0 24 24"><path d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z"/></svg>
                                </div>
                            </div>

                            <!-- Meta block -->
                            <div class="flex flex-col gap-1 px-0.5">
                                <div class="flex items-start justify-between gap-3">
                                    <h4 class="text-base font-semibold text-ink dark:text-zinc-100 leading-tight truncate">{{ $guideItem->name }}</h4>
                                    <span class="text-sm font-semibold text-ink dark:text-zinc-200 shrink-0 whitespace-nowrap">
                                        Rp {{ number_format($guideItem->guideProfile->base_rate, 0, ',', '.') }} <span class="text-muted font-normal">/{{ $guideItem->guideProfile->tariff_mode->value }}</span>
                                    </span>
                                </div>

                                <!-- Ink star rating — Airbnb uses ink, never gold -->
                                <div class="flex items-center gap-1">
                                    @if ($guideItem->guide_reviews_avg_rating)
                                        <svg class="size-3.5 text-star fill-current" viewBox="0 0 20 20"><path d="M10.868 2.784a.75.75 0 0 0-1.736 0l-1.87 3.79-4.183.608a.75.75 0 0 0-.416 1.28l3.028 2.951-.715 4.167a.75.75 0 0 0 1.09.79l3.74-1.966 3.74 1.966a.75.75 0 0 0 1.09-.79l-.715-4.167 3.028-2.951a.75.75 0 0 0-.416-1.28l-4.183-.608-1.87-3.79Z"/></svg>
                                        <span class="text-sm font-medium text-ink dark:text-zinc-200">
                                            {{ number_format($guideItem->guide_reviews_avg_rating, 2) }}
                                        </span>
                                    @else
                                        <span class="text-sm text-muted-soft">{{ __('No reviews yet') }}</span>
                                    @endif
                                </div>

                                <p class="text-sm text-muted line-clamp-2 leading-relaxed">{{ $guideItem->guideProfile->bio ?: __('No biography provided.') }}</p>

                                <!-- Specializations + communication style tags -->
                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    @if ($guideItem->guideProfile->communication_style)
                                        <span class="inline-flex items-center rounded-full bg-surface-soft dark:bg-zinc-900 px-2.5 py-1 text-xs font-medium text-ink dark:text-zinc-300">
                                            {{ $guideItem->guideProfile->communication_style->label() }}
                                        </span>
                                    @endif
                                    @if (! empty($guideItem->guideProfile->specializations))
                                        @foreach (\App\Enums\Specialization::cases() as $spec)
                                            @if (in_array($spec->value, $guideItem->guideProfile->specializations, true))
                                                <span class="inline-flex items-center rounded-full bg-surface-soft dark:bg-zinc-900 px-2.5 py-1 text-xs font-medium text-ink dark:text-zinc-300">
                                                    {{ $spec->label() }}
                                                </span>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>

                                <!-- Languages -->
                                @if (! empty($guideItem->guideProfile->languages))
                                    <div class="flex flex-wrap gap-1 pt-0.5">
                                        @foreach ($guideItem->guideProfile->languages as $lang)
                                            <span class="text-xs text-muted-soft">{{ strtoupper($lang) }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Pre-booking chat (FR-02-02) -->
                                @if (Auth::check() && auth()->user()->role === \App\Enums\UserRole::CUSTOMER)
                                    <div class="pt-1.5">
                                        <a
                                            href="{{ route('chat.room', ['receiver' => $guideItem->id]) }}"
                                            wire:navigate
                                            wire:click.stop
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-full border border-hairline text-ink hover:bg-surface-soft dark:border-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-800 transition-colors"
                                        >
                                            <svg class="size-3.5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
                                            {{ __('Chat') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-16 border border-dashed border-hairline rounded-[14px] flex flex-col items-center justify-center">
                            <svg class="size-12 text-muted-soft mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.978 11.978 0 0 1 12 20.25a11.978 11.978 0 0 1-3-1.013v-.11c0-1.109.326-2.14.887-3M9 19.128c-.89-.13-1.748-.415-2.522-.823a4.122 4.122 0 0 0-4.321 6.326 9.302 9.302 0 0 0 3.738-2.316m3.105-3.32a4.125 4.125 0 0 1 7.533-2.493M9 16.058v-.003c0-1.113.285-2.16.786-3.07M9 16.058A9 9 0 0 0 2.25 15M12 5.25a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm7.5 7.5a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5ZM4.5 12.75a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z"/></svg>
                            <h4 class="text-base font-semibold text-ink dark:text-zinc-100">{{ __('No Guides Found') }}</h4>
                            <p class="text-sm text-muted dark:text-zinc-400 max-w-xs mt-1">
                                {{ __('We could not find any verified tour guides matching your active filters. Try widening your criteria.') }}
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .custom-div-icon {
        background: transparent !important;
        border: none !important;
    }
</style>

@assets
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endassets

@script
<script>
    let map = null;
    let markers = [];
    let routeLine = null;

    const placesCoords = {
        'ubud': [-8.5186, 115.2631],
        'monkey forest': [-8.5186, 115.2631],
        'uluwatu': [-8.8291, 115.0849],
        'kuta': [-8.7224, 115.1672],
        'batur': [-8.2417, 115.3781],
        'bedugul': [-8.2752, 115.1652],
        'tanah lot': [-8.6212, 115.0868],
        'penida': [-8.7337, 115.4746],
        'jimbaran': [-8.7844, 115.1611],
        'seminyak': [-8.6913, 115.1682],
        'canggu': [-8.6478, 115.1385],
        'sanur': [-8.6792, 115.2630]
    };

    function initMap() {
        const container = document.getElementById('map');
        if (!container || typeof L === 'undefined') return;

        if (map) {
            map.remove();
        }

        map = L.map('map').setView([-8.409518, 115.188919], 10);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        updateRoute();
    }

    function getStringHash(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }
        return hash;
    }

    function getCoordinatesForPlace(placeName) {
        const lowerName = placeName.toLowerCase();
        for (const [key, coords] of Object.entries(placesCoords)) {
            if (lowerName.includes(key)) {
                return coords;
            }
        }
        const hash = getStringHash(placeName);
        const latOffset = (hash % 100) / 1000;
        const lngOffset = ((hash >> 3) % 100) / 1000;
        return [-8.409518 + latOffset, 115.188919 + lngOffset];
    }

    function updateRoute() {
        if (!map) return;

        markers.forEach(m => map.removeLayer(m));
        markers = [];

        if (routeLine) {
            map.removeLayer(routeLine);
            routeLine = null;
        }

        const destinations = $wire.customDestinations || [];
        const coordinates = [];

        const pickup = $wire.pickupLocation;
        if (pickup && pickup.trim().length >= 5) {
            const coords = getCoordinatesForPlace(pickup);
            coordinates.push(coords);
            const m = L.marker(coords, {
                icon: L.divIcon({
                    html: '<div class="flex items-center justify-center size-6 rounded-full bg-[#ff385c] text-white font-bold border-2 border-white shadow-md text-xs">P</div>',
                    className: 'custom-div-icon',
                    iconSize: [24, 24]
                })
            }).addTo(map).bindPopup('<b>Pickup:</b> ' + pickup);
            markers.push(m);
        }

        destinations.forEach((dest, index) => {
            const coords = getCoordinatesForPlace(dest);
            coordinates.push(coords);
            const m = L.marker(coords, {
                icon: L.divIcon({
                    html: `<div class="flex items-center justify-center size-6 rounded-full bg-[#222222] text-white font-bold border-2 border-white shadow-md text-xs">${index + 1}</div>`,
                    className: 'custom-div-icon',
                    iconSize: [24, 24]
                })
            }).addTo(map).bindPopup(`<b>Stop ${index + 1}:</b> ` + dest);
            markers.push(m);
        });

        const dropoff = $wire.dropoffLocation;
        if (dropoff && dropoff.trim().length > 0) {
            const coords = getCoordinatesForPlace(dropoff);
            coordinates.push(coords);
            const m = L.marker(coords, {
                icon: L.divIcon({
                    html: '<div class="flex items-center justify-center size-6 rounded-full bg-[#222222] text-white font-bold border-2 border-white shadow-md text-xs font-sans">D</div>',
                    className: 'custom-div-icon',
                    iconSize: [24, 24]
                })
            }).addTo(map).bindPopup('<b>Drop-off:</b> ' + dropoff);
            markers.push(m);
        }

        if (coordinates.length > 1) {
            routeLine = L.polyline(coordinates, { color: '#ff385c', weight: 4, dashArray: '5, 10' }).addTo(map);
            map.fitBounds(routeLine.getBounds(), { padding: [50, 50] });
        } else if (coordinates.length === 1) {
            map.setView(coordinates[0], 12);
        }
    }

    document.addEventListener('livewire:navigated', () => {
        initMap();
    });

    $wire.$watch('customDestinations', () => {
        updateRoute();
    });
    $wire.$watch('pickupLocation', () => {
        updateRoute();
    });
    $wire.$watch('dropoffLocation', () => {
        updateRoute();
    });
    $wire.$watch('selectedGuideId', () => {
        setTimeout(initMap, 50);
    });

    setTimeout(initMap, 50);
</script>
@endscript
</div>
