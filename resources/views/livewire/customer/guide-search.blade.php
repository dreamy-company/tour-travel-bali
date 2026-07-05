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
        <!-- LEFT PANEL: Search Filters (4 Cols) -->
        <div class="lg:col-span-4 flex flex-col border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs gap-6 h-fit">
        <div>
            <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ __('Search Filters') }}</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Filter verified guides based on your travel needs.') }}</p>
        </div>

        <hr class="border-zinc-200 dark:border-zinc-800" />

        <!-- Name Search -->
        <div class="relative">
            <input 
                wire:model.live.debounce.300ms="searchQuery" 
                type="search" 
                placeholder="Search guide name..." 
                class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
            />
        </div>

        <!-- Pricing Range -->
        <div class="space-y-2">
            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Pricing Range (IDR)') }}</label>
            <div class="grid grid-cols-2 gap-2">
                <input 
                    wire:model.live.debounce.500ms="minPrice" 
                    placeholder="Min Rp" 
                    type="number" 
                    class="w-full text-sm px-3 py-2 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                />
                <input 
                    wire:model.live.debounce.500ms="maxPrice" 
                    placeholder="Max Rp" 
                    type="number" 
                    class="w-full text-sm px-3 py-2 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                />
            </div>
        </div>

        <!-- Minimum Rating Filter -->
        <div class="space-y-2">
            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Minimum Rating') }}</label>
            <select 
                wire:model.live="minRating" 
                class="w-full text-sm px-3 py-2 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
            >
                <option value="">{{ __('Any Rating') }}</option>
                <option value="5">★★★★★ (5.0)</option>
                <option value="4">★★★★☆ (4.0+)</option>
                <option value="3">★★★☆☆ (3.0+)</option>
            </select>
        </div>

        <!-- Languages -->
        <div class="space-y-2">
            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Languages Fluency') }}</label>
            <div class="flex flex-col gap-2">
                <label class="inline-flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400 cursor-pointer">
                    <input 
                        type="checkbox" 
                        wire:model.live="selectedLanguages" 
                        value="id" 
                        class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800"
                    />
                    <span>Indonesian</span>
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400 cursor-pointer">
                    <input 
                        type="checkbox" 
                        wire:model.live="selectedLanguages" 
                        value="en" 
                        class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800"
                    />
                    <span>English</span>
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400 cursor-pointer">
                    <input 
                        type="checkbox" 
                        wire:model.live="selectedLanguages" 
                        value="jp" 
                        class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800"
                    />
                    <span>Japanese</span>
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400 cursor-pointer">
                    <input 
                        type="checkbox" 
                        wire:model.live="selectedLanguages" 
                        value="fr" 
                        class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800"
                    />
                    <span>French</span>
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400 cursor-pointer">
                    <input 
                        type="checkbox" 
                        wire:model.live="selectedLanguages" 
                        value="de" 
                        class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800"
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
            <!-- Custom Itinerary & Booking Pane -->
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col gap-6">
                <!-- Selected Guide summary -->
                <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-4">
                    <div class="flex items-center gap-3.5">
                        <div class="size-12 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-bold text-zinc-850 dark:text-zinc-250">
                            {{ $guide->initials() }}
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ __('Configure Itinerary with :name', ['name' => $guide->name]) }}</h3>
                            <p class="text-xs text-zinc-500 mt-0.5">
                                Tariff: <span class="font-semibold text-zinc-800 dark:text-zinc-200">Rp {{ number_format($guide->guideProfile->base_rate, 0, ',', '.') }} / {{ $guide->guideProfile->tariff_mode->value }}</span>
                            </p>
                        </div>
                    </div>
                    <button 
                        wire:click="$set('selectedGuideId', null)" 
                        type="button"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-650 dark:text-zinc-400 transition-colors"
                    >
                        <svg class="size-3.5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg>
                        {{ __('Back to Search') }}
                    </button>
                </div>

                <!-- Session Flash Message for Guest -->
                @if (session()->has('warning'))
                    <div class="p-3 text-xs bg-amber-50 border border-amber-200 text-amber-800 rounded-lg dark:bg-amber-950/20 dark:border-amber-900/50 dark:text-amber-400">
                        {{ session('warning') }}
                    </div>
                @endif

                <!-- Form Inputs -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Pickup Location') }}</label>
                        <input 
                            wire:model="pickupLocation" 
                            type="text" 
                            placeholder="Hotel name, airport, or specific coordinates" 
                            required 
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('pickupLocation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Drop-off Location (Optional)') }}</label>
                        <input 
                            wire:model="dropoffLocation" 
                            type="text" 
                            placeholder="Drop-off point (if different from pickup)" 
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('dropoffLocation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Schedule Date') }}</label>
                        <input 
                            wire:model="scheduleDate" 
                            type="date" 
                            required 
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('scheduleDate') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Pickup Time') }}</label>
                        <input 
                            wire:model="pickupTime" 
                            type="time" 
                            required 
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('pickupTime') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Custom Itinerary destinations tag builder -->
                <div class="space-y-3">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Custom Route Places') }}</label>
                    <div class="flex gap-2">
                        <input 
                            wire:model="newDestination" 
                            wire:keydown.enter="addDestination"
                            placeholder="Add point of interest (e.g. Uluwatu Temple, Tegalalang)" 
                            class="flex-1 text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        <button 
                            wire:click="addDestination" 
                            type="button"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-lg bg-zinc-100 text-zinc-900 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-250 dark:hover:bg-zinc-700 transition-colors"
                        >
                            {{ __('Add Place') }}
                        </button>
                    </div>
                    @error('newDestination') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    @error('customDestinations') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror

                    <!-- Places tags list -->
                    <div class="flex flex-wrap gap-2 mt-2 p-3 rounded-lg border border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/50 min-h-12">
                        @forelse ($customDestinations as $index => $dest)
                            <div class="inline-flex items-center gap-1 bg-zinc-900 text-white dark:bg-zinc-800 text-[10px] md:text-xs font-semibold px-2.5 py-1 rounded-full">
                                <span>{{ $dest }}</span>
                                <button 
                                    type="button" 
                                    wire:click="removeDestination({{ $index }})" 
                                    class="hover:text-red-400 focus:outline-hidden"
                                >
                                    <svg class="size-3 fill-current" viewBox="0 0 20 20"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
                                </button>
                            </div>
                        @empty
                            <span class="text-xs text-zinc-400 my-auto">{{ __('No destinations added yet. Add at least one.') }}</span>
                        @endforelse
                    </div>
                </div>

                <!-- Maps API Integration placeholder -->
                <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 bg-zinc-100 dark:bg-zinc-900 flex flex-col items-center justify-center text-center gap-2 relative overflow-hidden h-40">
                    <!-- Fake Map grid background -->
                    <div class="absolute inset-0 opacity-15 pointer-events-none bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] dark:bg-[radial-gradient(#374151_1px,transparent_1px)] [background-size:16px_16px]"></div>
                    <svg class="size-8 text-zinc-400 dark:text-zinc-600 relative z-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-12v8.25m.503 3.446 1.202-.721a1.125 1.125 0 0 1 1.28.087l2.216 1.661a.45.45 0 0 1-.224.818l-4.053-.405a1.125 1.125 0 0 0-1.012.336l-2.072 2.072a1.125 1.125 0 0 1-1.502.1l-4.3-.545a.45.45 0 0 1-.294-.741l3.178-3.178A1.125 1.125 0 0 1 10 13.5H9c0-.228-.09-.448-.25-.61L4.603 8.747a1.125 1.125 0 0 1 0-1.59l3.179-3.179A1.125 1.125 0 0 1 9 3.5h.503a1.125 1.125 0 0 1 .721.258l4.9 4.9Z"/></svg>
                    <h4 class="text-sm font-bold text-zinc-950 dark:text-zinc-50 relative z-10">{{ __('Maps API Integration Route Preview') }}</h4>
                    <p class="text-[10px] text-zinc-500 max-w-xs relative z-10">
                        {{ __('Simulated spatial routing calculates distances and travel time for tourist custom destinations.') }}
                    </p>
                    @if (! empty($customDestinations))
                        <div class="absolute bottom-2 left-2 z-10 flex gap-2">
                            <span class="bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-[9px] font-bold px-2 py-0.5 rounded border border-emerald-500/30">
                                {{ __('Route Calculated') }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Estimation breakdown and Pricing -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-t border-zinc-100 dark:border-zinc-800 pt-5 mt-2">
                    <div class="p-3 border rounded-xl dark:border-zinc-800">
                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">{{ __('Estimated Distance') }}</span>
                        <p class="text-lg font-bold text-zinc-800 dark:text-zinc-200 mt-1">
                            {{ number_format($this->calculateItineraryDistance(), 1, '.', ',') }} km
                        </p>
                    </div>
                    <div class="p-3 border rounded-xl dark:border-zinc-800">
                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">{{ __('Estimated Duration') }}</span>
                        <p class="text-lg font-bold text-zinc-800 dark:text-zinc-200 mt-1">
                            {{ number_format($this->calculateItineraryDuration(), 1, '.', ',') }} hrs
                        </p>
                    </div>
                    <div class="p-3 border rounded-xl dark:border-zinc-800 bg-zinc-950 dark:bg-white text-white dark:text-black">
                        <span class="text-[9px] font-bold text-zinc-450 dark:text-zinc-500 uppercase tracking-wider">{{ __('Estimated Total Price') }}</span>
                        <p class="text-lg font-extrabold mt-1">
                            Rp {{ number_format($this->totalPrice, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <!-- Book action -->
                <div class="flex justify-between items-center gap-4 mt-4">
                    <button 
                        wire:click="$set('selectedGuideId', null)" 
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-semibold rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-650 dark:text-zinc-400 transition-colors"
                    >
                        {{ __('Cancel') }}
                    </button>
                    @if (! Auth::check())
                        <a 
                            href="{{ route('login') }}" 
                            wire:navigate
                            class="inline-flex items-center px-6 py-2.5 text-sm font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs"
                        >
                            {{ __('Log In to Request Booking') }}
                        </a>
                    @else
                        <button 
                            wire:click="book" 
                            type="button"
                            class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs"
                        >
                            <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                            {{ __('Submit Booking Request') }}
                        </button>
                    @endif
                </div>
            </div>
        @else
            <!-- Guides Search Grid -->
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col gap-6">
                <div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ __('Verified Tour Guides') }}</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Select a guide to configure a custom travel itinerary around Bali.') }}</p>
                </div>

                <hr class="border-zinc-200 dark:border-zinc-800" />

                <!-- Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    @forelse ($this->guides as $guideItem)
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 flex flex-col justify-between gap-5 bg-zinc-50/20 dark:bg-zinc-900/10 hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors duration-200">
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-bold text-zinc-800 dark:text-zinc-200">
                                        {{ $guideItem->initials() }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 truncate">{{ $guideItem->name }}</h4>
                                        
                                        <!-- Rating -->
                                        <div class="flex items-center gap-1 mt-0.5">
                                            @if ($guideItem->guide_reviews_avg_rating)
                                                <svg class="size-3 text-amber-500 fill-current" viewBox="0 0 20 20"><path d="M10.868 2.784a.75.75 0 0 0-1.736 0l-1.87 3.79-4.183.608a.75.75 0 0 0-.416 1.28l3.028 2.951-.715 4.167a.75.75 0 0 0 1.09.79l3.74-1.966 3.74 1.966a.75.75 0 0 0 1.09-.79l-.715-4.167 3.028-2.951a.75.75 0 0 0-.416-1.28l-4.183-.608-1.87-3.79Z"/></svg>
                                                <span class="text-[10px] font-semibold text-zinc-700 dark:text-zinc-300">
                                                    {{ number_format($guideItem->guide_reviews_avg_rating, 1) }}
                                                </span>
                                            @else
                                                <span class="text-[9px] text-zinc-400 font-medium">{{ __('No reviews yet') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <p class="text-xs text-zinc-500 line-clamp-3 leading-relaxed">{{ $guideItem->guideProfile->bio ?: __('No biography provided.') }}</p>
                                
                                <!-- Languages spoken -->
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($guideItem->guideProfile->languages as $lang)
                                        <span class="inline-flex items-center rounded-md bg-zinc-50 px-2 py-1 text-[9px] font-semibold text-zinc-650 ring-1 ring-inset ring-zinc-500/10 dark:bg-zinc-900 dark:text-zinc-400 dark:ring-zinc-400/20">
                                            {{ strtoupper($lang) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex items-center justify-between border-t border-zinc-100 dark:border-zinc-800 pt-4 mt-2">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider">{{ __('Pricing Rate') }}</span>
                                    <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200 mt-0.5">
                                        Rp {{ number_format($guideItem->guideProfile->base_rate, 0, ',', '.') }} / {{ $guideItem->guideProfile->tariff_mode->value }}
                                    </span>
                                </div>

                                <button 
                                    wire:click="selectGuide({{ $guideItem->id }})" 
                                    type="button"
                                    class="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs"
                                >
                                    {{ __('Book Guide') }}
                                    <svg class="size-3 stroke-current ml-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-16 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl flex flex-col items-center justify-center">
                            <svg class="size-12 text-zinc-300 dark:text-zinc-700 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.978 11.978 0 0 1 12 20.25a11.978 11.978 0 0 1-3-1.013v-.11c0-1.109.326-2.14.887-3M9 19.128c-.89-.13-1.748-.415-2.522-.823a4.122 4.122 0 0 0-4.321 6.326 9.302 9.302 0 0 0 3.738-2.316m3.105-3.32a4.125 4.125 0 0 1 7.533-2.493M9 16.058v-.003c0-1.113.285-2.16.786-3.07M9 16.058A9 9 0 0 0 2.25 15M12 5.25a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm7.5 7.5a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5ZM4.5 12.75a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z"/></svg>
                            <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ __('No Guides Found') }}</h4>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-xs mt-1">
                                {{ __('We could not find any verified tour guides matching your active filters. Try widening your criteria.') }}
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</div>
</div>
