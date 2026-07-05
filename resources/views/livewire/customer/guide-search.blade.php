<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 min-h-[calc(100vh-10rem)]">
    <!-- LEFT PANEL: Search Filters (4 Cols) -->
    <div class="lg:col-span-4 flex flex-col border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs gap-6 h-fit">
        <div>
            <flux:heading size="lg">{{ __('Search Filters') }}</flux:heading>
            <flux:text>{{ __('Filter verified guides based on your travel needs.') }}</flux:text>
        </div>

        <flux:separator />

        <!-- Name Search -->
        <flux:input 
            wire:model.live.debounce.300ms="searchQuery" 
            type="search" 
            placeholder="Search guide name..." 
            icon="magnifying-glass"
        />

        <!-- Pricing Range -->
        <div>
            <flux:label class="mb-2 block">{{ __('Pricing Range (IDR)') }}</flux:label>
            <div class="grid grid-cols-2 gap-2">
                <flux:input wire:model.live.debounce.500ms="minPrice" placeholder="Min Rp" type="number" />
                <flux:input wire:model.live.debounce.500ms="maxPrice" placeholder="Max Rp" type="number" />
            </div>
        </div>

        <!-- Minimum Rating Filter -->
        <flux:select wire:model.live="minRating" label="{{ __('Minimum Rating') }}">
            <flux:option value="">{{ __('Any Rating') }}</flux:option>
            <flux:option value="5">★★★★★ (5.0)</flux:option>
            <flux:option value="4">★★★★☆ (4.0+)</flux:option>
            <flux:option value="3">★★★☆☆ (3.0+)</flux:option>
        </flux:select>

        <!-- Languages -->
        <div>
            <flux:label class="mb-2 block">{{ __('Languages Fluency') }}</flux:label>
            <div class="flex flex-col gap-2">
                <flux:checkbox wire:model.live="selectedLanguages" value="id" label="Indonesian" />
                <flux:checkbox wire:model.live="selectedLanguages" value="en" label="English" />
                <flux:checkbox wire:model.live="selectedLanguages" value="jp" label="Japanese" />
                <flux:checkbox wire:model.live="selectedLanguages" value="fr" label="French" />
                <flux:checkbox wire:model.live="selectedLanguages" value="de" label="German" />
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
                        <flux:avatar 
                            :name="$guide->name" 
                            :initials="$guide->initials()" 
                            size="lg" 
                        />
                        <div>
                            <flux:heading size="lg">{{ __('Configure Itinerary with :name', ['name' => $guide->name]) }}</flux:heading>
                            <flux:text class="text-xs">
                                Tariff: <span class="font-semibold text-zinc-800 dark:text-zinc-200">Rp {{ number_format($guide->guideProfile->base_rate, 0, ',', '.') }} / {{ $guide->guideProfile->tariff_mode->value }}</span>
                            </flux:text>
                        </div>
                    </div>
                    <flux:button wire:click="$set('selectedGuideId', null)" variant="ghost" size="sm" icon="chevron-left">
                        {{ __('Back to Search') }}
                    </flux:button>
                </div>

                <!-- Session Flash Message for Guest -->
                @if (session()->has('warning'))
                    <div class="p-3 text-xs bg-amber-50 border border-amber-200 text-amber-800 rounded-lg dark:bg-amber-950/20 dark:border-amber-900/50 dark:text-amber-400">
                        {{ session('warning') }}
                    </div>
                @endif

                <!-- Form Inputs -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input 
                        wire:model="pickupLocation" 
                        label="{{ __('Pickup Location') }}" 
                        placeholder="Hotel name, airport, or specific coordinates" 
                        required 
                    />
                    <flux:input 
                        wire:model="dropoffLocation" 
                        label="{{ __('Drop-off Location (Optional)') }}" 
                        placeholder="Drop-off point (if different from pickup)" 
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input 
                        wire:model="scheduleDate" 
                        type="date" 
                        label="{{ __('Schedule Date') }}" 
                        required 
                    />
                    <flux:input 
                        wire:model="pickupTime" 
                        type="time" 
                        label="{{ __('Pickup Time') }}" 
                        required 
                    />
                </div>

                <!-- Custom Itinerary destinations tag builder -->
                <div class="space-y-3">
                    <flux:label>{{ __('Custom Route Places') }}</flux:label>
                    <div class="flex gap-2">
                        <flux:input 
                            wire:model="newDestination" 
                            wire:keydown.enter="addDestination"
                            placeholder="Add point of interest (e.g. Uluwatu Temple, Tegalalang)" 
                            class="flex-1"
                        />
                        <flux:button wire:click="addDestination" variant="neutral" icon="plus">
                            {{ __('Add Place') }}
                        </flux:button>
                    </div>
                    <flux:error name="newDestination" />
                    <flux:error name="customDestinations" />

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
                                    <flux:icon.x-mark class="size-3" />
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
                    <flux:icon.map class="size-8 text-zinc-400 dark:text-zinc-600 relative z-10" />
                    <flux:heading size="sm" class="relative z-10">{{ __('Maps API Integration Route Preview') }}</flux:heading>
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
                        <p class="text-lg font-bold text-zinc-850 dark:text-zinc-200 mt-1">
                            {{ number_format($this->calculateItineraryDistance(), 1, '.', ',') }} km
                        </p>
                    </div>
                    <div class="p-3 border rounded-xl dark:border-zinc-800">
                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">{{ __('Estimated Duration') }}</span>
                        <p class="text-lg font-bold text-zinc-850 dark:text-zinc-200 mt-1">
                            {{ number_format($this->calculateItineraryDuration(), 1, '.', ',') }} hrs
                        </p>
                    </div>
                    <div class="p-3 border rounded-xl dark:border-zinc-800 bg-zinc-950 dark:bg-white text-white dark:text-black">
                        <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">{{ __('Estimated Total Price') }}</span>
                        <p class="text-lg font-extrabold mt-1">
                            Rp {{ number_format($this->totalPrice, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <!-- Book action -->
                <div class="flex justify-between items-center gap-4 mt-4">
                    <flux:button wire:click="$set('selectedGuideId', null)" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>
                    @if (! Auth::check())
                        <flux:button href="{{ route('login') }}" variant="primary" class="px-8" wire:navigate>
                            {{ __('Log In to Request Booking') }}
                        </flux:button>
                    @else
                        <flux:button wire:click="book" variant="primary" class="px-8" icon="check">
                            {{ __('Submit Booking Request') }}
                        </flux:button>
                    @endif
                </div>
            </div>
        @else
            <!-- Guides Search Grid -->
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col gap-6">
                <div>
                    <flux:heading size="lg">{{ __('Verified Tour Guides') }}</flux:heading>
                    <flux:text>{{ __('Select a guide to configure a custom travel itinerary around Bali.') }}</flux:text>
                </div>

                <flux:separator class="my-2" />

                <!-- Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    @forelse ($this->guides as $guideItem)
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 flex flex-col justify-between gap-5 bg-zinc-50/20 dark:bg-zinc-900/10 hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors duration-200">
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar 
                                        :name="$guideItem->name" 
                                        :initials="$guideItem->initials()" 
                                        size="md" 
                                    />
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 truncate">{{ $guideItem->name }}</h4>
                                        
                                        <!-- Rating -->
                                        <div class="flex items-center gap-1 mt-0.5">
                                            @if ($guideItem->guide_reviews_avg_rating)
                                                <flux:icon.star class="size-3 text-amber-500 fill-current" />
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
                                        <flux:badge size="sm" variant="neutral" class="text-[9px]">{{ strtoupper($lang) }}</flux:badge>
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

                                <flux:button 
                                    wire:click="selectGuide({{ $guideItem->id }})" 
                                    size="sm" 
                                    variant="primary" 
                                    icon-trailing="chevron-right"
                                >
                                    {{ __('Book Guide') }}
                                </flux:button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-16 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl flex flex-col items-center justify-center">
                            <flux:icon.users class="size-12 text-zinc-300 dark:text-zinc-700 mb-3" />
                            <flux:heading size="md">{{ __('No Guides Found') }}</flux:heading>
                            <flux:text class="max-w-xs mt-1">
                                {{ __('We could not find any verified tour guides matching your active filters. Try widening your criteria.') }}
                            </flux:text>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</div>
