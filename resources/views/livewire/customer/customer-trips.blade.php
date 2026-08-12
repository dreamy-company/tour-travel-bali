<div wire:poll.5s="refreshTrips" class="space-y-6">
    {{-- Flash messages --}}
    @if (session()->has('success'))
        <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 flex items-center justify-between" role="alert">
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 flex items-center justify-between" role="alert">
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-medium tracking-[-0.44px] text-ink">{{ __('My Trips') }}</h1>
            <p class="text-sm text-muted mt-1">{{ __('Track active tours, pay escrow, and review past trips.') }}</p>
        </div>
        <a href="{{ route('guides.index') }}" wire:navigate class="inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active transition-colors self-start sm:self-auto">
            <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            {{ __('New Booking') }}
        </a>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 border-b border-hairline">
        <button
            type="button"
            wire:click="$set('activeTab', 'active')"
            class="px-5 py-3 text-sm font-semibold border-b-2 -mb-px transition-colors {{ $activeTab === 'active' ? 'border-rausch text-ink' : 'border-transparent text-muted hover:text-ink' }}"
        >
            {{ __('Active Trips') }}
            @if ($this->activeBookings->count())
                <span class="ml-1.5 inline-flex items-center rounded-full bg-surface-soft px-2 py-0.5 text-xs font-semibold text-ink">{{ $this->activeBookings->count() }}</span>
            @endif
        </button>
        <button
            type="button"
            wire:click="$set('activeTab', 'past')"
            class="px-5 py-3 text-sm font-semibold border-b-2 -mb-px transition-colors {{ $activeTab === 'past' ? 'border-rausch text-ink' : 'border-transparent text-muted hover:text-ink' }}"
        >
            {{ __('Past Orders') }}
            @if ($this->pastBookings->count())
                <span class="ml-1.5 inline-flex items-center rounded-full bg-surface-soft px-2 py-0.5 text-xs font-semibold text-ink">{{ $this->pastBookings->count() }}</span>
            @endif
        </button>
    </div>

    {{-- ── ACTIVE TRIPS TAB ─────────────────────────────────────── --}}
    @if ($activeTab === 'active')
        @forelse ($this->activeBookings as $booking)
            <div class="border border-hairline rounded-[14px] bg-white p-6 space-y-6">
                {{-- Booking header --}}
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-hairline-soft pb-5">
                    <div class="flex items-center gap-3">
                        <div class="flex size-11 items-center justify-center rounded-full bg-surface-soft text-sm font-semibold text-ink">{{ $booking->guide->initials() }}</div>
                        <div>
                            <h3 class="text-base font-semibold text-ink">{{ $booking->guide->name }}</h3>
                            <p class="text-sm text-muted">
                                {{ $booking->schedule_date->format('D, M j, Y') }} · {{ $booking->pickup_time }} · {{ $booking->pickup_location }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-muted">{{ __('Booking #:id', ['id' => str_pad((string) $booking->id, 8, '0', STR_PAD_LEFT)]) }}</p>
                        <p class="text-lg font-bold text-ink">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                    </div>
                </div>

                {{-- Status stepper --}}
                @php
                    $stepMap = [
                        'pending_confirmation' => 1,
                        'waiting_payment' => 2,
                        'confirmed' => 3,
                        'heading_to_location' => 4,
                        'ongoing' => 5,
                        'completed' => 6,
                    ];
                    $currentStep = $stepMap[$booking->status->value] ?? 1;
                    $steps = ['Request Sent', 'Awaiting Payment', 'Confirmed', 'Guide En Route', 'Tour Ongoing', 'Completed'];
                @endphp
                <div class="pt-1">
                    <div class="relative">
                        <div class="absolute top-4 left-0 right-0 h-1 bg-hairline rounded-full hidden sm:block"></div>
                        <div class="absolute top-4 left-0 h-1 bg-rausch rounded-full transition-all duration-500 hidden sm:block" style="width: {{ (($currentStep - 1) / 5) * 100 }}%"></div>
                        <div class="grid grid-cols-6 relative">
                            @foreach ($steps as $index => $step)
                                @php
                                    $number = $index + 1;
                                    $completed = $number < $currentStep;
                                    $current = $number === $currentStep;
                                @endphp
                                <div class="flex flex-col items-center">
                                    <div class="relative z-10 flex items-center justify-center size-8 rounded-full transition-all duration-300 {{ $completed ? 'bg-ink text-white' : ($current ? 'bg-rausch text-white ring-4 ring-rausch/15' : 'bg-white border-2 border-hairline text-muted-soft') }}">
                                        @if ($completed)
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        @else
                                            <span class="text-xs font-semibold">{{ $number }}</span>
                                        @endif
                                    </div>
                                    <p class="mt-2 text-[11px] text-center leading-tight {{ $current || $completed ? 'text-ink font-semibold' : 'text-muted-soft' }}">{{ __($step) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Status actions --}}
                <div class="flex flex-wrap items-center gap-3 border-t border-hairline-soft pt-5">
                    @if ($booking->status === \App\Enums\BookingStatus::PENDING_CONFIRMATION)
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/10">
                            {{ __('Waiting for guide approval…') }}
                        </span>
                    @endif

                    @if ($booking->status === \App\Enums\BookingStatus::WAITING_PAYMENT && $booking->escrowTransaction?->status === \App\Enums\EscrowStatus::WAITING_PAYMENT)
                        <button
                            type="button"
                            wire:click="openPayment({{ $booking->id }})"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold rounded-lg bg-rausch text-white hover:bg-rausch-active transition-colors shadow-airbnb"
                        >
                            <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/></svg>
                            {{ __('Pay via Escrow') }}
                        </button>
                        <p class="text-xs text-muted">Rp {{ number_format($booking->escrowTransaction->gross_amount, 0, ',', '.') }} — {{ __('funds are held until the tour completes.') }}</p>
                    @endif

                    @if ($booking->status === \App\Enums\BookingStatus::CONFIRMED)
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/10">
                            {{ __('Paid & confirmed — guide will meet you on tour day.') }}
                        </span>
                    @endif
                </div>

                {{-- Embedded reactive chat with the guide --}}
                <div class="space-y-2">
                    <h4 class="text-sm font-semibold text-muted">{{ __('Chat with :name', ['name' => $booking->guide->name]) }}</h4>
                    @livewire('chat.chat-room', ['bookingId' => $booking->id], key('trips-chat-' . $booking->id))
                </div>
            </div>
        @empty
            <div class="border border-dashed border-hairline rounded-[14px] p-16 text-center flex flex-col items-center gap-4">
                <div class="flex size-16 items-center justify-center rounded-full bg-surface-soft text-muted">
                    <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 0 1 3.75-.615A2.993 2.993 0 0 1 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 1 2.25 1.016c.496.573 1.156.95 1.875 1.15m-16.5 2.4h16.5"/></svg>
                </div>
                <div class="space-y-1">
                    <h3 class="text-lg font-medium text-ink">{{ __('No Active Trips') }}</h3>
                    <p class="text-sm text-muted max-w-sm">{{ __('You have no active tours right now. Find a verified guide and plan your next Balinese adventure.') }}</p>
                </div>
                <a href="{{ route('guides.index') }}" wire:navigate class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active transition-colors">
                    {{ __('Search Guides') }}
                </a>
            </div>
        @endforelse
    @endif

    {{-- ── PAST ORDERS TAB ──────────────────────────────────────── --}}
    @if ($activeTab === 'past')
        <div class="border border-hairline rounded-[14px] bg-white overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-hairline text-xs text-muted-soft uppercase tracking-wider">
                            <th class="py-3.5 px-5 font-semibold">{{ __('Date') }}</th>
                            <th class="py-3.5 px-5 font-semibold">{{ __('Guide') }}</th>
                            <th class="py-3.5 px-5 font-semibold">{{ __('Total') }}</th>
                            <th class="py-3.5 px-5 font-semibold">{{ __('Status') }}</th>
                            <th class="py-3.5 px-5 text-right font-semibold">{{ __('Review') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @forelse ($this->pastBookings as $past)
                            <tr class="hover:bg-surface-soft/60 transition-colors">
                                <td class="py-4 px-5 text-ink">{{ $past->schedule_date->format('M d, Y') }}</td>
                                <td class="py-4 px-5 font-medium text-ink">{{ $past->guide->name }}</td>
                                <td class="py-4 px-5 text-ink">Rp {{ number_format($past->total_price, 0, ',', '.') }}</td>
                                <td class="py-4 px-5">
                                    @if ($past->status === \App\Enums\BookingStatus::COMPLETED)
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">{{ __('Completed') }}</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-surface-soft px-2.5 py-1 text-xs font-semibold text-muted">{{ $past->status->value === 'cancelled' ? __('Cancelled') : __('Declined') }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-right">
                                    @if ($past->status === \App\Enums\BookingStatus::COMPLETED)
                                        @if ($past->review)
                                            <span class="inline-flex items-center gap-1 text-sm text-muted">
                                                <svg class="size-3.5 fill-current text-star" viewBox="0 0 20 20"><path d="M10.868 2.784a.75.75 0 0 0-1.736 0l-1.87 3.79-4.183.608a.75.75 0 0 0-.416 1.28l3.028 2.951-.715 4.167a.75.75 0 0 0 1.09.79l3.74-1.966 3.74 1.966a.75.75 0 0 0 1.09-.79l-.715-4.167 3.028-2.951a.75.75 0 0 0-.416-1.28l-4.183-.608-1.87-3.79Z"/></svg>
                                                {{ $past->review->rating }} ★
                                            </span>
                                        @else
                                            <button type="button" wire:click="openReview({{ $past->id }})" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active transition-colors">
                                                {{ __('Leave Review') }}
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-muted-soft">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-14 text-center text-muted-soft">{{ __('No past trips yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Review modal --}}
    @if ($showReviewModal && $reviewBookingId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:key="review-modal">
            <div class="bg-white border border-hairline rounded-[14px] max-w-md w-full p-6 shadow-airbnb space-y-5" @keydown.escape.window="document.getElementById('skip-review')?.click()">
                <div class="text-center space-y-1">
                    <h3 class="text-lg font-medium text-ink">{{ __('Rate Your Bali Tour Guide') }}</h3>
                    <p class="text-sm text-muted">{{ __('How was your custom tour experience?') }}</p>
                </div>

                <div class="flex flex-col items-center gap-1 bg-surface-soft p-4 rounded-[14px] border border-hairline">
                    <span class="text-[11px] font-semibold text-muted-soft uppercase tracking-wider">{{ __('Select Rating') }}</span>
                    <div class="flex gap-2 justify-center mt-1">
                        @foreach (range(1, 5) as $i)
                            <button type="button" wire:click="$set('rating', {{ $i }})" class="text-3xl transition-all hover:scale-110 focus:outline-hidden {{ $rating >= $i ? 'text-star fill-current' : 'text-hairline' }}">★</button>
                        @endforeach
                    </div>
                    <span class="text-sm font-semibold text-ink mt-2">{{ $rating }} / 5 Stars</span>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-ink block">{{ __('Write a Review') }}</label>
                    <textarea wire:model="comment" placeholder="Share details about the destinations, the guide's hospitality, vehicle safety…" rows="4" class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-hairline bg-white text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch"></textarea>
                    @error('comment') <span class="text-xs text-error-text block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-2 justify-end pt-1">
                    <button id="skip-review" type="button" wire:click="skipReview" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-full hover:bg-surface-soft text-ink transition-colors">{{ __('Skip') }}</button>
                    <button type="button" wire:click="submitReview" class="inline-flex items-center px-6 py-2.5 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active transition-colors shadow-airbnb">{{ __('Submit Feedback') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
