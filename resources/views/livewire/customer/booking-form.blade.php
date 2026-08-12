<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- LEFT: Booking form -->
    <div class="lg:col-span-8 min-w-0">
        {{-- Breadcrumb --}}
        <a href="{{ route('guides.show', $profile) }}" wire:navigate class="inline-flex items-center gap-1.5 text-sm font-medium text-muted hover:text-ink transition-colors w-fit mb-6">
            <svg class="size-4 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg>
            {{ __('Back to :name', ['name' => $profile->user->name]) }}
        </a>

        <form wire:submit="submitBooking" class="border border-hairline rounded-[14px] bg-white p-6 sm:p-8 flex flex-col gap-7">
            {{-- Guide summary --}}
            <div class="flex items-center gap-4 border-b border-hairline pb-6">
                @if ($profile->headshot)
                    <img src="{{ route('guides.photo', $profile) }}" alt="{{ $profile->user->name }}" class="size-14 rounded-full object-cover border-2 border-white shadow-airbnb" />
                @else
                    <div class="flex size-14 items-center justify-center rounded-full bg-surface-soft border border-hairline text-lg font-semibold text-ink">{{ $profile->user->initials() }}</div>
                @endif
                <div>
                    <h1 class="text-xl font-semibold tracking-[-0.3px] text-ink">{{ __('Book with :name', ['name' => $profile->user->name]) }}</h1>
                    <p class="text-sm text-muted mt-0.5">
                        {{ $profile->communication_style?->label() }} · {{ $profile->tariff_mode->value === 'hourly' ? 'Rp ' . number_format($profile->base_rate, 0, ',', '.') . ' / hour' : 'Rp ' . number_format($profile->base_rate, 0, ',', '.') . ' / day' }}
                    </p>
                </div>
            </div>

            {{-- Schedule --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-ink block" for="schedule-date">{{ __('Booking Date') }}</label>
                    <input
                        id="schedule-date"
                        wire:model="scheduleDate"
                        type="date"
                        min="{{ now()->addDay()->toDateString() }}"
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-hairline bg-white text-ink focus:outline-hidden focus:ring-2 focus:ring-rausch"
                    />
                    @error('scheduleDate') <span class="text-xs text-error-text block mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-ink block" for="pickup-time">{{ __('Pickup Time') }}</label>
                    <input
                        id="pickup-time"
                        wire:model="pickupTime"
                        type="time"
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-hairline bg-white text-ink focus:outline-hidden focus:ring-2 focus:ring-rausch"
                    />
                    @error('pickupTime') <span class="text-xs text-error-text block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Pickup location --}}
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-ink block" for="pickup-location">{{ __('Pickup Location') }}</label>
                <input
                    id="pickup-location"
                    wire:model="pickupLocation"
                    type="text"
                    placeholder="Hotel name, villa, airport, or specific coordinates"
                    class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-hairline bg-white text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch"
                />
                @error('pickupLocation') <span class="text-xs text-error-text block mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Package selection (optional) --}}
            <div class="space-y-2">
                <label class="text-sm font-semibold text-ink block" for="package">{{ __('Tour Package') }} <span class="text-xs font-normal text-muted-soft">({{ __('optional') }})</span></label>
                <select
                    id="package"
                    wire:model.live="tourPackageId"
                    class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-hairline bg-white text-ink focus:outline-hidden focus:ring-2 focus:ring-rausch"
                >
                    <option value="">{{ __('Custom itinerary (no package)') }}</option>
                    @foreach ($this->packages as $package)
                        <option value="{{ $package->id }}">{{ $package->title }} — Rp {{ number_format($package->price, 0, ',', '.') }}</option>
                    @endforeach
                </select>
                @error('tourPackageId') <span class="text-xs text-error-text block mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Custom destinations --}}
            <div class="space-y-3">
                <label class="text-sm font-semibold text-ink block">{{ __('Custom Itinerary Destinations') }}</label>
                <div class="flex gap-2">
                    <input
                        wire:model="newDestination"
                        wire:keydown.enter="addDestination"
                        type="text"
                        placeholder="Add a point of interest (e.g. Uluwatu Temple)"
                        class="flex-1 text-sm px-3.5 py-2.5 rounded-full border border-hairline bg-white text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch"
                    />
                    <button
                        type="button"
                        wire:click="addDestination"
                        class="inline-flex items-center px-5 py-2.5 text-sm font-medium rounded-full bg-surface-strong text-ink hover:bg-hairline transition-colors"
                    >
                        {{ __('Add') }}
                    </button>
                </div>
                @error('newDestination') <span class="text-xs text-error-text block mt-1">{{ $message }}</span> @enderror
                @error('customDestinations') <span class="text-xs text-error-text block mt-1">{{ $message }}</span> @enderror

                <div class="flex flex-wrap gap-2 p-3 rounded-lg border border-hairline-soft bg-surface-soft min-h-12">
                    @forelse ($customDestinations as $index => $dest)
                        <span class="inline-flex items-center gap-1.5 bg-ink text-white text-sm font-medium px-3 py-1.5 rounded-full">
                            <span class="text-[10px] text-white/60 font-bold bg-white/15 size-4 flex items-center justify-center rounded-full">{{ $index + 1 }}</span>
                            {{ $dest }}
                            <button type="button" wire:click="removeDestination({{ $index }})" class="hover:text-rausch-disabled focus:outline-hidden" aria-label="Remove {{ $dest }}">
                                <svg class="size-3 fill-current" viewBox="0 0 20 20"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
                            </button>
                        </span>
                    @empty
                        <span class="text-sm text-muted-soft my-auto">{{ __('Add at least one destination.') }}</span>
                    @endforelse
                </div>
            </div>

            {{-- Submission note --}}
            <div class="rounded-[14px] bg-surface-soft border border-hairline p-4 flex gap-3">
                <svg class="size-5 text-rausch shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                <p class="text-sm text-body leading-relaxed">
                    {{ __('Confirmation-first: your booking is sent to the guide for approval. The payment screen only appears after the guide accepts your request.') }}
                </p>
            </div>

            <button
                type="submit"
                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active transition-colors shadow-airbnb"
            >
                {{ __('Submit Booking') }}
            </button>
        </form>
    </div>

    <!-- RIGHT: Cost summary -->
    <aside class="lg:col-span-4">
        <div class="lg:sticky lg:top-24 border border-hairline rounded-[14px] bg-white p-6 shadow-airbnb space-y-4">
            <h3 class="text-base font-semibold text-ink">{{ __('Price Summary') }}</h3>
            <hr class="border-hairline" />

            <div class="space-y-2.5 text-sm">
                @if ($this->selectedPackage)
                    <div class="flex justify-between text-body">
                        <span>{{ $this->selectedPackage->title }}</span>
                        <span class="font-semibold text-ink">Rp {{ number_format($this->selectedPackage->price, 0, ',', '.') }}</span>
                    </div>
                @else
                    <div class="flex justify-between text-body">
                        <span>{{ $profile->tariff_mode->value === 'hourly' ? 'Hourly rate' : 'Daily rate' }}</span>
                        <span class="font-semibold text-ink">Rp {{ number_format($profile->base_rate, 0, ',', '.') }}</span>
                    </div>
                    @if ($profile->tariff_mode->value === 'hourly')
                        <div class="flex justify-between text-body">
                            <span>{{ __('Estimated duration') }}</span>
                            <span class="font-semibold text-ink">{{ number_format($this->estimatedHours(), 1) }} hrs</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-body">
                        <span>{{ __('Destinations') }}</span>
                        <span class="font-semibold text-ink">{{ count($customDestinations) }}</span>
                    </div>
                @endif
            </div>

            <hr class="border-hairline" />

            <div class="flex items-end justify-between">
                <span class="text-sm font-semibold text-ink">{{ __('Total') }}</span>
                <span class="text-2xl font-bold tracking-[-0.5px] text-ink">Rp {{ number_format($this->totalPrice, 0, ',', '.') }}</span>
            </div>

            <p class="text-xs text-muted leading-relaxed pt-1 border-t border-hairline-soft">
                {{ __('No payment is collected now — the amount is locked into escrow only after the guide confirms your booking.') }}
            </p>
        </div>
    </aside>
</div>
