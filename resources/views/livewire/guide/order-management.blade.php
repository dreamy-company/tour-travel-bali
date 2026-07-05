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

    <!-- Header -->
    <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4">
        <h2 class="text-xl font-bold text-zinc-950 dark:text-white">{{ __('Tour Orders & Management') }}</h2>
        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Manage incoming customer itinerary bookings and progress ongoing tours.') }}</p>
    </div>

    <!-- Reactive Alerts & Pending Requests Notification -->
    @if (count($this->pendingBookings) > 0)
        <div class="relative overflow-hidden p-5 rounded-xl border border-indigo-200 bg-indigo-50/70 dark:border-indigo-900/50 dark:bg-indigo-950/20 text-indigo-900 dark:text-indigo-200 shadow-sm space-y-4">
            <!-- Pulsing state indicator -->
            <div class="absolute top-4 right-4 flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
            </div>

            <div class="flex gap-3 items-start">
                <svg class="size-6 text-indigo-600 dark:text-indigo-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                <div class="space-y-1">
                    <h4 class="text-indigo-950 dark:text-indigo-150 font-bold text-sm">
                        {{ __('Pending Booking Requests (:count)', ['count' => count($this->pendingBookings)]) }}
                    </h4>
                    <p class="text-xs text-indigo-800/90 dark:text-indigo-300/90">
                        {{ __('Customers are waiting for you to review and confirm their custom Bali itineraries.') }}
                    </p>
                </div>
            </div>

            <!-- List inside Notification Alert -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                @foreach ($this->pendingBookings as $pending)
                    <div class="bg-white dark:bg-stone-900 border border-indigo-100 dark:border-indigo-950 p-4 rounded-lg flex flex-col justify-between gap-4">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <div class="size-6 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-semibold text-[10px] text-zinc-850 dark:text-zinc-250">
                                    {{ $pending->customer->initials() }}
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-150">{{ $pending->customer->name }}</h4>
                                    <p class="text-[9px] text-zinc-500">{{ $pending->customer->phone_number }}</p>
                                </div>
                            </div>

                            <hr class="border-zinc-100 dark:border-zinc-800/50" />

                            <div class="space-y-1 text-[10px] text-zinc-600 dark:text-zinc-400">
                                <p><strong>Date:</strong> {{ $pending->schedule_date->format('F j, Y') }} at {{ $pending->pickup_time }}</p>
                                <p class="truncate"><strong>Pickup:</strong> {{ $pending->pickup_location }}</p>
                                <p class="truncate"><strong>Route:</strong> {{ implode(' ➔ ', $pending->custom_destinations) }}</p>
                            </div>

                            <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200 mt-2">
                                Price: Rp {{ number_format($pending->total_price, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="flex gap-2 justify-end border-t border-zinc-100 dark:border-zinc-800/50 pt-2.5">
                            <button 
                                wire:click="rejectBooking({{ $pending->id }})" 
                                type="button" 
                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg hover:bg-red-50 text-red-650 transition-colors"
                            >
                                {{ __('Reject') }}
                            </button>
                            <button 
                                wire:click="acceptBooking({{ $pending->id }})" 
                                type="button" 
                                class="inline-flex items-center px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs"
                            >
                                {{ __('Accept Booking') }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Main List of Ongoing & Processed bookings -->
    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col gap-4">
        <div>
            <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">{{ __('Trip Order Records') }}</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Monitor status checkpoints and payouts for confirmed itineraries.') }}</p>
        </div>

        <hr class="border-zinc-200 dark:border-zinc-800" />

        <div class="space-y-4 mt-2">
            @forelse ($this->ongoingBookings as $booking)
                <div
                    x-data="{ chatOpen: false }"
                    class="border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50/50 dark:bg-zinc-900/30 overflow-hidden"
                >
                    <!-- Booking summary row -->
                    <div class="p-4 flex flex-col sm:flex-row sm:items-center gap-4 justify-between">
                        <!-- Customer Details -->
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="size-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center font-bold text-[10px] text-zinc-700 dark:text-zinc-300 shrink-0">
                                {{ $booking->customer->initials() }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-zinc-900 dark:text-zinc-100">{{ $booking->customer->name }}</p>
                                <p class="text-[10px] text-zinc-500">{{ $booking->customer->phone_number }}</p>
                                <p class="text-[9px] text-zinc-400 mt-0.5">{{ $booking->schedule_date->format('M d, Y') }} · {{ $booking->pickup_time }}</p>
                            </div>
                        </div>

                        <!-- Route + destinations -->
                        <div class="flex-1 min-w-0 hidden lg:block">
                            <p class="truncate text-[10px] font-medium text-zinc-700 dark:text-zinc-300"><strong>From:</strong> {{ $booking->pickup_location }}</p>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach ($booking->custom_destinations as $dest)
                                    <span class="inline-flex items-center rounded-md bg-white dark:bg-zinc-800 px-2 py-0.5 text-[8px] font-semibold text-zinc-600 ring-1 ring-inset ring-zinc-500/10 dark:text-zinc-400 dark:ring-zinc-400/20">
                                        {{ $dest }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Financials -->
                        <div class="hidden md:block text-xs shrink-0">
                            <p class="font-bold text-zinc-900 dark:text-zinc-100">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                            @if ($booking->escrowTransaction)
                                <p class="text-[9px] text-zinc-500">
                                    Escrow: <span class="font-semibold uppercase {{ $booking->escrowTransaction->status->value === 'paid_in_escrow' ? 'text-green-600' : 'text-amber-600' }}">
                                        {{ str_replace('_', ' ', $booking->escrowTransaction->status->value) }}
                                    </span>
                                </p>
                            @endif
                        </div>

                        <!-- Status badge + action buttons cluster -->
                        <div class="flex items-center gap-2 shrink-0 flex-wrap justify-end">
                            @php
                                $colorClasses = match ($booking->status) {
                                    App\Enums\BookingStatus::CONFIRMED => 'bg-indigo-50 text-indigo-700 ring-indigo-700/10 dark:bg-indigo-950/20 dark:text-indigo-400 dark:ring-indigo-500/20',
                                    App\Enums\BookingStatus::HEADING_TO_LOCATION => 'bg-amber-50 text-amber-700 ring-amber-700/10 dark:bg-amber-950/20 dark:text-amber-400 dark:ring-amber-500/20',
                                    App\Enums\BookingStatus::ONGOING => 'bg-emerald-50 text-emerald-700 ring-emerald-700/10 dark:bg-emerald-950/20 dark:text-emerald-400 dark:ring-emerald-500/20',
                                    App\Enums\BookingStatus::COMPLETED => 'bg-zinc-50 text-zinc-650 ring-zinc-500/10 dark:bg-zinc-900 dark:text-zinc-400 dark:ring-zinc-400/20',
                                    App\Enums\BookingStatus::REJECTED => 'bg-red-50 text-red-700 ring-red-700/10 dark:bg-red-950/20 dark:text-red-400 dark:ring-red-500/20',
                                    default => 'bg-zinc-50 text-zinc-600 dark:bg-zinc-900 dark:text-zinc-400',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $colorClasses }}">
                                {{ ucfirst(str_replace('_', ' ', $booking->status->value)) }}
                            </span>

                            <!-- Sequential State Progression buttons -->
                            @if ($booking->status === App\Enums\BookingStatus::CONFIRMED)
                                <button
                                    wire:click="advanceStatus({{ $booking->id }}, 'heading_to_location')"
                                    type="button"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs"
                                >
                                    {{ __('Heading to Location') }}
                                    <svg class="size-3.5 stroke-current ml-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                                </button>
                            @elseif ($booking->status === App\Enums\BookingStatus::HEADING_TO_LOCATION)
                                <button
                                    wire:click="advanceStatus({{ $booking->id }}, 'ongoing')"
                                    type="button"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs"
                                >
                                    {{ __('Start Tour') }}
                                    <svg class="size-3.5 stroke-current ml-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/></svg>
                                </button>
                            @elseif ($booking->status === App\Enums\BookingStatus::ONGOING)
                                <button
                                    wire:click="advanceStatus({{ $booking->id }}, 'completed')"
                                    type="button"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs"
                                >
                                    {{ __('End Tour') }}
                                    <svg class="size-3.5 stroke-current ml-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z"/></svg>
                                </button>
                            @elseif ($booking->status === App\Enums\BookingStatus::COMPLETED)
                                <span class="inline-flex items-center gap-1 text-xs text-emerald-600 font-semibold px-2.5 py-1">
                                    <svg class="size-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                    {{ __('Audit Complete') }}
                                </span>
                            @elseif ($booking->status === App\Enums\BookingStatus::REJECTED)
                                <span class="inline-flex items-center gap-1 text-xs text-red-500 font-semibold px-2.5 py-1">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                    {{ __('Declined') }}
                                </span>
                            @endif

                            <!-- Chat Toggle Button (only for non-rejected bookings) -->
                            @if ($booking->status !== App\Enums\BookingStatus::REJECTED)
                                <button
                                    type="button"
                                    @click="chatOpen = !chatOpen"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg border border-zinc-200 hover:bg-zinc-100 text-zinc-700 dark:border-zinc-700 dark:hover:bg-zinc-800 dark:text-zinc-300 transition-colors"
                                >
                                    <svg class="size-3.5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375m-13.5 3.01H18A2.25 2.25 0 0 0 20.25 10.5v-3.75A2.25 2.25 0 0 0 18 4.5H6A2.25 2.25 0 0 0 3.75 6.75v3.75a2.25 2.25 0 0 0 1.252 2.022L3 17.25h12.75a2.25 2.25 0 0 0 2.25-2.25v-.375c0-.621-.504-1.125-1.125-1.125H3.75"/></svg>
                                    <span x-text="chatOpen ? '{{ __('Close Chat') }}' : '{{ __('Chat') }}'"></span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Collapsible Chat Drawer -->
                    @if ($booking->status !== App\Enums\BookingStatus::REJECTED)
                        <div
                            x-show="chatOpen"
                            x-collapse
                            class="border-t border-zinc-200 dark:border-zinc-800 p-4 bg-white dark:bg-stone-950"
                        >
                            @livewire('chat.chat-room', ['bookingId' => $booking->id], key('guide-chat-' . $booking->id))
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-14 flex flex-col items-center justify-center">
                    <svg class="size-12 text-zinc-300 dark:text-zinc-700 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.008 1.24l.885 1.77a2.25 2.25 0 0 0 2.007 1.24h1.98a2.25 2.25 0 0 0 2.007-1.24l.885-1.77a2.25 2.25 0 0 1 2.007-1.24h3.86m-18 0h18a2.25 2.25 0 0 0 2.25-2.25V5.25A2.25 2.25 0 0 0 19.5 3h-15a2.25 2.25 0 0 0-2.25 2.25v6a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                    <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">{{ __('No active or completed orders found.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
