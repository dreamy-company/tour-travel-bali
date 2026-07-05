<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4">
        <h2 class="text-xl font-bold text-zinc-950 dark:text-white">{{ __('Manage Tour Services') }}</h2>
        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Configure your base rates and manage your tour packages.') }}</p>
    </div>

    <!-- Verification Pending Alert Banner -->
    @if (! $this->isVerified)
        <div class="flex gap-4 p-5 rounded-xl border border-amber-200 bg-amber-50 dark:border-amber-900/50 dark:bg-amber-950/20 text-amber-800 dark:text-amber-300">
            <svg class="size-6 shrink-0 mt-0.5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            <div class="space-y-1">
                <h3 class="text-amber-900 dark:text-amber-200 font-semibold text-sm">{{ __('Profile Verification Pending') }}</h3>
                <p class="text-xs leading-relaxed text-amber-800/90 dark:text-amber-300/90">
                    Your onboarding compliance documents (National ID KTP, KTPP License, Police Clearance SKCK, and Medical Certificate) are currently being audited by our Super Admin team. 
                </p>
                <p class="text-[10px] text-amber-700 dark:text-amber-400 mt-2 font-medium">
                    {{ __('You will be able to customize your pricing rates and create tour packages once your verification is approved.') }}
                </p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 {{ ! $this->isVerified ? 'opacity-50 pointer-events-none select-none' : '' }}">
        <!-- LEFT COLUMN: Rates Configuration (4 Cols) -->
        <div class="lg:col-span-4 flex flex-col border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs gap-6 h-fit">
            <div>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">{{ __('Base Pricing Rates') }}</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Set your default charging model for custom itineraries.') }}</p>
            </div>

            <hr class="border-zinc-200 dark:border-zinc-800" />

            <!-- Tariff Mode Selection -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Tariff Mode') }}</label>
                <div class="grid grid-cols-2 gap-3">
                    <button 
                        wire:click="$set('tariffMode', 'daily')"
                        type="button" 
                        class="p-3 rounded-lg border text-xs font-semibold flex flex-col items-center justify-center gap-1.5 transition-all duration-200 {{ $tariffMode === 'daily' ? 'bg-zinc-950 border-zinc-950 text-white dark:bg-white dark:border-white dark:text-black' : 'bg-transparent border-zinc-200 text-zinc-650 hover:bg-zinc-50 dark:border-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-900' }}"
                    >
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"/></svg>
                        {{ __('Daily Rate') }}
                    </button>
                    <button 
                        wire:click="$set('tariffMode', 'hourly')"
                        type="button" 
                        class="p-3 rounded-lg border text-xs font-semibold flex flex-col items-center justify-center gap-1.5 transition-all duration-200 {{ $tariffMode === 'hourly' ? 'bg-zinc-950 border-zinc-950 text-white dark:bg-white dark:border-white dark:text-black' : 'bg-transparent border-zinc-200 text-zinc-650 hover:bg-zinc-50 dark:border-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-900' }}"
                    >
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        {{ __('Hourly Rate') }}
                    </button>
                </div>
                @error('tariffMode') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Base Rate Input -->
            <div class="space-y-1.5">
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Base Rate (IDR)') }}</label>
                <div class="relative flex items-center">
                    <span class="absolute left-3.5 text-zinc-400 text-sm">Rp</span>
                    <input 
                        wire:model.blur="baseRate" 
                        type="number" 
                        placeholder="e.g. 500000" 
                        required
                        class="w-full text-sm pl-9 pr-14 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                    />
                    <span class="absolute right-3.5 text-zinc-400 text-xs">
                        {{ $tariffMode === 'daily' ? '/ day' : '/ hour' }}
                    </span>
                </div>
                @error('baseRate') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <button 
                wire:click="updateRates" 
                type="button"
                class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs"
            >
                <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                {{ __('Update Rates') }}
            </button>
        </div>

        <!-- RIGHT COLUMN: Packages Management (8 Cols) -->
        <div class="lg:col-span-8 flex flex-col gap-6">
            
            <!-- CRUD Form view -->
            @if ($isEditing)
                <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col gap-6">
                    <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">
                            {{ $packageId ? __('Edit Tour Package') : __('Create Tour Package') }}
                        </h3>
                        <button 
                            wire:click="cancelEdit" 
                            type="button" 
                            class="text-xs font-semibold px-2.5 py-1.5 text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-350"
                        >
                            {{ __('Cancel') }}
                        </button>
                    </div>

                    <!-- Title & Price -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Package Title') }}</label>
                            <input 
                                wire:model.blur="title" 
                                type="text" 
                                placeholder="e.g. Classic Ubud Day Tour" 
                                required 
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                            />
                            @error('title') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Package Price (IDR)') }}</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-zinc-400 text-sm">Rp</span>
                                <input 
                                    wire:model.blur="price" 
                                    type="number" 
                                    placeholder="e.g. 750000" 
                                    required 
                                    class="w-full text-sm pl-9 pr-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                                />
                            </div>
                            @error('price') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Tour Description') }}</label>
                        <textarea 
                            wire:model.blur="description" 
                            placeholder="Detail the itinerary, timings, inclusions, and experiences..." 
                            rows="4" 
                            required 
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        ></textarea>
                        @error('description') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Dynamic Destinations tags manager -->
                    <div class="space-y-3">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Tour Destinations') }}</label>
                        
                        <div class="flex gap-2">
                            <input 
                                wire:model="newDestination" 
                                wire:keydown.enter="addDestination"
                                placeholder="Add place (e.g. Ubud Monkey Forest)" 
                                class="flex-1 text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                            />
                            <button 
                                wire:click="addDestination" 
                                type="button"
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-lg bg-zinc-100 text-zinc-900 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-250 dark:hover:bg-zinc-700 transition-colors"
                            >
                                {{ __('Add') }}
                            </button>
                        </div>
                        @error('newDestination') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        @error('destinations') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror

                        <!-- Tags Container -->
                        <div class="flex flex-wrap gap-2 mt-2 p-3 rounded-lg border border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/50 min-h-12">
                            @forelse ($destinations as $index => $dest)
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

                    <!-- Toggle Status -->
                    <div class="flex items-center justify-between border-t border-zinc-100 dark:border-zinc-800 pt-4 mt-2">
                        <div class="space-y-0.5">
                            <h4 class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ __('Publish Package') }}</h4>
                            <p class="text-xs text-zinc-500">{{ __('Tourists can view and book this package immediately.') }}</p>
                        </div>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input 
                                type="checkbox" 
                                wire:model="is_active" 
                                class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800"
                            />
                            <span class="text-sm font-medium text-zinc-750 dark:text-zinc-300">{{ __('Active') }}</span>
                        </label>
                    </div>

                    <div class="flex gap-3 justify-end mt-4">
                        <button 
                            wire:click="cancelEdit" 
                            type="button"
                            class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-650 dark:text-zinc-400 transition-colors"
                        >
                            {{ __('Cancel') }}
                        </button>
                        <button 
                            wire:click="savePackage" 
                            type="button"
                            class="inline-flex items-center gap-1.5 px-6 py-2.5 text-sm font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs"
                        >
                            <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                            {{ $packageId ? __('Save Changes') : __('Create Package') }}
                        </button>
                    </div>
                </div>
            @else
                <!-- Packages List View -->
                <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col gap-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">{{ __('My Tour Packages') }}</h3>
                            <p class="text-xs text-zinc-500 mt-1">{{ __('Configure pre-defined itineraries for quick booking.') }}</p>
                        </div>
                        <button 
                            wire:click="createPackage" 
                            type="button"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs"
                        >
                            <svg class="size-3.5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            {{ __('Add Package') }}
                        </button>
                    </div>

                    <hr class="border-zinc-200 dark:border-zinc-800" />

                    <!-- Packages Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                        @forelse ($this->tourPackages as $package)
                            <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 flex flex-col justify-between gap-4 bg-zinc-50/20 dark:bg-zinc-900/10 hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors duration-200">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        @if ($package->is_active)
                                            <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/10 dark:bg-emerald-950/20 dark:text-emerald-400 dark:ring-emerald-500/20">
                                                {{ __('Active') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-md bg-zinc-50 px-2 py-0.5 text-[10px] font-semibold text-zinc-650 ring-1 ring-inset ring-zinc-500/10 dark:bg-zinc-900 dark:text-zinc-400 dark:ring-zinc-400/20">
                                                {{ __('Inactive') }}
                                            </span>
                                        @endif
                                        <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                            Rp {{ number_format($package->price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $package->title }}</h4>
                                    <p class="text-xs text-zinc-500 line-clamp-3 leading-relaxed">{{ $package->description }}</p>
                                    
                                    <!-- Destinations list -->
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @foreach ($package->destinations as $dest)
                                            <span class="inline-flex items-center rounded-md bg-zinc-50 px-2 py-0.5 text-[9px] font-semibold text-zinc-650 ring-1 ring-inset ring-zinc-500/10 dark:bg-zinc-900 dark:text-zinc-400 dark:ring-zinc-400/20">
                                                {{ $dest }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="flex justify-end gap-2 border-t border-zinc-100 dark:border-zinc-800 pt-3 mt-1">
                                    <button 
                                        wire:click="editPackage({{ $package->id }})" 
                                        type="button"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400 transition-colors"
                                    >
                                        {{ __('Edit') }}
                                    </button>
                                    <button 
                                        wire:click="deletePackage({{ $package->id }})" 
                                        type="button"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg hover:bg-red-50 text-red-650 dark:hover:bg-red-950/20 transition-colors"
                                    >
                                        {{ __('Delete') }}
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-16 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl flex flex-col items-center justify-center">
                                <svg class="size-12 text-zinc-300 dark:text-zinc-700 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 .621-.504 1.125-1.125 1.125H4.875A1.125 1.125 0 0 1 3.75 18.4v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8c-.06.27-.196.52-.387.712M3.75 14.15a2.18 2.18 0 0 1-.75-1.661V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.818m0 0a48.11 48.11 0 0 1 6 0m-6 0a48.064 48.064 0 0 1 3.82.215m-1.161-1.033A2.25 2.25 0 0 1 12 3.75h3.621a2.25 2.25 0 0 1 1.662.756l1.321 1.484a2.25 2.25 0 0 1 .536 1.442v4.87c0 .621-.504 1.125-1.125 1.125H4.875A1.125 1.125 0 0 1 3.75 12.38v-4.87a2.25 2.25 0 0 1 .536-1.442l1.32-1.484a2.25 2.25 0 0 1 1.663-.756H9"/></svg>
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ __('No Tour Packages') }}</h4>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-xs mt-1">
                                    {{ __('Create your first package to list standard Bali tour itineraries.') }}
                                </p>
                                <button 
                                    wire:click="createPackage" 
                                    type="button" 
                                    class="mt-4 inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs"
                                >
                                    <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                    {{ __('Create Package') }}
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
