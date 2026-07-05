<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4">
        <flux:heading size="xl">{{ __('Manage Tour Services') }}</flux:heading>
        <flux:text>{{ __('Configure your base rates and manage your tour packages.') }}</flux:text>
    </div>

    <!-- Verification Pending Alert Banner -->
    @if (! $this->isVerified)
        <div class="flex gap-4 p-5 rounded-xl border border-amber-200 bg-amber-50 dark:border-amber-900/50 dark:bg-amber-950/20 text-amber-800 dark:text-amber-300">
            <flux:icon.clock class="size-6 shrink-0 mt-0.5 text-amber-600 dark:text-amber-400" />
            <div class="space-y-1">
                <flux:heading size="sm" class="text-amber-900 dark:text-amber-200 font-semibold">{{ __('Profile Verification Pending') }}</flux:heading>
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
                <flux:heading size="lg">{{ __('Base Pricing Rates') }}</flux:heading>
                <flux:text>{{ __('Set your default charging model for custom itineraries.') }}</flux:text>
            </div>

            <flux:separator />

            <!-- Tariff Mode Selection -->
            <div>
                <flux:label class="mb-2 block">{{ __('Tariff Mode') }}</flux:label>
                <div class="grid grid-cols-2 gap-3">
                    <button 
                        wire:click="$set('tariffMode', 'daily')"
                        type="button" 
                        class="p-3 rounded-lg border text-xs font-semibold flex flex-col items-center justify-center gap-1.5 transition-all duration-200 {{ $tariffMode === 'daily' ? 'bg-zinc-950 border-zinc-950 text-white dark:bg-white dark:border-white dark:text-black' : 'bg-transparent border-zinc-200 text-zinc-600 hover:bg-zinc-50 dark:border-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-900' }}"
                    >
                        <flux:icon.calendar class="size-4" />
                        {{ __('Daily Rate') }}
                    </button>
                    <button 
                        wire:click="$set('tariffMode', 'hourly')"
                        type="button" 
                        class="p-3 rounded-lg border text-xs font-semibold flex flex-col items-center justify-center gap-1.5 transition-all duration-200 {{ $tariffMode === 'hourly' ? 'bg-zinc-950 border-zinc-950 text-white dark:bg-white dark:border-white dark:text-black' : 'bg-transparent border-zinc-200 text-zinc-600 hover:bg-zinc-50 dark:border-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-900' }}"
                    >
                        <flux:icon.clock class="size-4" />
                        {{ __('Hourly Rate') }}
                    </button>
                </div>
                <flux:error name="tariffMode" />
            </div>

            <!-- Base Rate Input -->
            <flux:input 
                wire:model.blur="baseRate" 
                type="number" 
                label="{{ __('Base Rate (IDR)') }}" 
                placeholder="e.g. 500000" 
                prefix="Rp" 
                suffix="{{ $tariffMode === 'daily' ? '/ day' : '/ hour' }}"
                required 
            />

            <flux:button 
                wire:click="updateRates" 
                variant="primary" 
                icon="check" 
                class="w-full"
            >
                {{ __('Update Rates') }}
            </flux:button>
        </div>

        <!-- RIGHT COLUMN: Packages Management (8 Cols) -->
        <div class="lg:col-span-8 flex flex-col gap-6">
            
            <!-- CRUD Form view -->
            @if ($isEditing)
                <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col gap-6">
                    <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                        <flux:heading size="lg">
                            {{ $packageId ? __('Edit Tour Package') : __('Create Tour Package') }}
                        </flux:heading>
                        <flux:button wire:click="cancelEdit" variant="ghost" size="sm">{{ __('Cancel') }}</flux:button>
                    </div>

                    <!-- Title & Price -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input 
                            wire:model.blur="title" 
                            label="{{ __('Package Title') }}" 
                            placeholder="e.g. Classic Ubud Day Tour" 
                            required 
                        />

                        <flux:input 
                            wire:model.blur="price" 
                            type="number" 
                            label="{{ __('Package Price (IDR)') }}" 
                            placeholder="e.g. 750000" 
                            prefix="Rp" 
                            required 
                        />
                    </div>

                    <!-- Description -->
                    <flux:textarea 
                        wire:model.blur="description" 
                        label="{{ __('Tour Description') }}" 
                        placeholder="Detail the itinerary, timings, inclusions, and experiences..." 
                        rows="4" 
                        required 
                    />

                    <!-- Dynamic Destinations tags manager -->
                    <div class="space-y-3">
                        <flux:label>{{ __('Tour Destinations') }}</flux:label>
                        
                        <div class="flex gap-2">
                            <flux:input 
                                wire:model="newDestination" 
                                wire:keydown.enter="addDestination"
                                placeholder="Add place (e.g. Ubud Monkey Forest)" 
                                class="flex-1"
                            />
                            <flux:button wire:click="addDestination" variant="neutral" icon="plus">
                                {{ __('Add') }}
                            </flux:button>
                        </div>
                        <flux:error name="newDestination" />
                        <flux:error name="destinations" />

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
                                        <flux:icon.x-mark class="size-3" />
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
                            <flux:heading size="sm">{{ __('Publish Package') }}</flux:heading>
                            <flux:text class="text-xs">{{ __('Tourists can view and book this package immediately.') }}</flux:text>
                        </div>
                        <flux:checkbox wire:model="is_active" label="{{ __('Active') }}" />
                    </div>

                    <div class="flex gap-3 justify-end mt-4">
                        <flux:button wire:click="cancelEdit" variant="ghost">
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button wire:click="savePackage" variant="primary" icon="check" class="px-6">
                            {{ $packageId ? __('Save Changes') : __('Create Package') }}
                        </flux:button>
                    </div>
                </div>
            @else
                <!-- Packages List View -->
                <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col gap-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:heading size="lg">{{ __('My Tour Packages') }}</flux:heading>
                            <flux:text>{{ __('Configure pre-defined itineraries for quick booking.') }}</flux:text>
                        </div>
                        <flux:button 
                            wire:click="createPackage" 
                            variant="primary" 
                            icon="plus"
                            size="sm"
                        >
                            {{ __('Add Package') }}
                        </flux:button>
                    </div>

                    <flux:separator />

                    <!-- Packages Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                        @forelse ($this->tourPackages as $package)
                            <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 flex flex-col justify-between gap-4 bg-zinc-50/20 dark:bg-zinc-900/10 hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors duration-200">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <flux:badge size="sm" color="{{ $package->is_active ? 'green' : 'neutral' }}">
                                            {{ $package->is_active ? __('Active') : __('Inactive') }}
                                        </flux:badge>
                                        <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                            Rp {{ number_format($package->price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $package->title }}</h4>
                                    <p class="text-xs text-zinc-500 line-clamp-3 leading-relaxed">{{ $package->description }}</p>
                                    
                                    <!-- Destinations list -->
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @foreach ($package->destinations as $dest)
                                            <flux:badge variant="neutral" size="sm" class="text-[9px]">{{ $dest }}</flux:badge>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="flex justify-end gap-2 border-t border-zinc-100 dark:border-zinc-800 pt-3 mt-1">
                                    <flux:button 
                                        wire:click="editPackage({{ $package->id }})" 
                                        size="xs" 
                                        variant="ghost" 
                                        icon="pencil-square"
                                    >
                                        {{ __('Edit') }}
                                    </flux:button>
                                    <flux:button 
                                        wire:click="deletePackage({{ $package->id }})" 
                                        size="xs" 
                                        variant="ghost" 
                                        icon="trash"
                                        class="hover:text-red-600 dark:hover:text-red-400"
                                    >
                                        {{ __('Delete') }}
                                    </flux:button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-16 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl flex flex-col items-center justify-center">
                                <flux:icon.briefcase class="size-12 text-zinc-300 dark:text-zinc-700 mb-3" />
                                <flux:heading size="md">{{ __('No Tour Packages') }}</flux:heading>
                                <flux:text class="max-w-xs mt-1">
                                    {{ __('Create your first package to list standard Bali tour itineraries.') }}
                                </flux:text>
                                <flux:button 
                                    wire:click="createPackage" 
                                    variant="neutral" 
                                    icon="plus" 
                                    size="sm" 
                                    class="mt-4"
                                >
                                    {{ __('Create Package') }}
                                </flux:button>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
