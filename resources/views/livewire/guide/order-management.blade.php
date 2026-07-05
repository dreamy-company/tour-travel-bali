<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4">
        <flux:heading size="xl">{{ __('Tour Orders & Management') }}</flux:heading>
        <flux:text>{{ __('Manage incoming customer itinerary bookings and progress ongoing tours.') }}</flux:text>
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
                <flux:icon.bell class="size-6 text-indigo-600 dark:text-indigo-400 shrink-0 mt-0.5" />
                <div class="space-y-1">
                    <flux:heading size="sm" class="text-indigo-950 dark:text-indigo-150 font-bold">
                        {{ __('Pending Booking Requests (:count)', ['count' => count($this->pendingBookings)]) }}
                    </flux:heading>
                    <flux:text class="text-xs text-indigo-800/90 dark:text-indigo-300/90">
                        {{ __('Customers are waiting for you to review and confirm their custom Bali itineraries.') }}
                    </flux:text>
                </div>
            </div>

            <!-- List inside Notification Alert -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                @foreach ($this->pendingBookings as $pending)
                    <div class="bg-white dark:bg-stone-900 border border-indigo-100 dark:border-indigo-950 p-4 rounded-lg flex flex-col justify-between gap-4">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <flux:avatar :name="$pending->customer->name" :initials="$pending->customer->initials()" size="xs" />
                                <div>
                                    <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-150">{{ $pending->customer->name }}</h4>
                                    <p class="text-[9px] text-zinc-500">{{ $pending->customer->phone_number }}</p>
                                </div>
                            </div>

                            <flux:separator />

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
                            <flux:button 
                                wire:click="rejectBooking({{ $pending->id }})" 
                                size="xs" 
                                variant="ghost" 
                                class="text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20"
                            >
                                {{ __('Reject') }}
                            </flux:button>
                            <flux:button 
                                wire:click="acceptBooking({{ $pending->id }})" 
                                size="xs" 
                                variant="primary"
                            >
                                {{ __('Accept Booking') }}
                            </flux:button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Main List of Ongoing & Processed bookings -->
    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col gap-4">
        <div>
            <flux:heading size="lg">{{ __('Trip Order Records') }}</flux:heading>
            <flux:text>{{ __('Monitor status checkpoints and payouts for confirmed itineraries.') }}</flux:text>
        </div>

        <flux:separator />

        <div class="overflow-x-auto mt-2">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-zinc-100 dark:border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-2">{{ __('Customer') }}</th>
                        <th class="py-3 px-2">{{ __('Schedule Details') }}</th>
                        <th class="py-3 px-2">{{ __('Routing') }}</th>
                        <th class="py-3 px-2">{{ __('Financials & Escrow') }}</th>
                        <th class="py-3 px-2">{{ __('Status Checkpoint') }}</th>
                        <th class="py-3 px-2 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($this->ongoingBookings as $booking)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-900/30">
                            <!-- Customer Details -->
                            <td class="py-4 px-2">
                                <div class="flex items-center gap-2">
                                    <flux:avatar :name="$booking->customer->name" :initials="$booking->customer->initials()" size="xs" />
                                    <div>
                                        <p class="font-bold text-zinc-900 dark:text-zinc-100">{{ $booking->customer->name }}</p>
                                        <p class="text-[10px] text-zinc-500">{{ $booking->customer->phone_number }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Date / Time -->
                            <td class="py-4 px-2 whitespace-nowrap">
                                <p class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $booking->schedule_date->format('M d, Y') }}</p>
                                <p class="text-[10px] text-zinc-500">Time: {{ $booking->pickup_time }}</p>
                            </td>

                            <!-- Route -->
                            <td class="py-4 px-2 max-w-xs">
                                <p class="truncate font-medium text-zinc-700 dark:text-zinc-300"><strong>From:</strong> {{ $booking->pickup_location }}</p>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach ($booking->custom_destinations as $dest)
                                        <flux:badge variant="neutral" size="sm" class="text-[8px]">{{ $dest }}</flux:badge>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Financials & Commission -->
                            <td class="py-4 px-2">
                                <p class="font-bold text-zinc-900 dark:text-zinc-100">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                                @if ($booking->escrowTransaction)
                                    <p class="text-[9px] text-zinc-500">
                                        Escrow: <span class="font-semibold uppercase {{ $booking->escrowTransaction->status->value === 'paid_in_escrow' ? 'text-green-600' : 'text-amber-600' }}">
                                            {{ str_replace('_', ' ', $booking->escrowTransaction->status->value) }}
                                        </span>
                                    </p>
                                @endif
                            </td>

                            <!-- Status checkpoint -->
                            <td class="py-4 px-2 whitespace-nowrap">
                                @php
                                    $color = match ($booking->status) {
                                        App\Enums\BookingStatus::CONFIRMED => 'indigo',
                                        App\Enums\BookingStatus::HEADING_TO_LOCATION => 'amber',
                                        App\Enums\BookingStatus::ONGOING => 'green',
                                        App\Enums\BookingStatus::COMPLETED => 'neutral',
                                        App\Enums\BookingStatus::REJECTED => 'red',
                                        default => 'zinc',
                                    };
                                @endphp
                                <flux:badge size="sm" color="{{ $color }}">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status->value)) }}
                                </flux:badge>
                            </td>

                            <!-- Sequential State Progression buttons -->
                            <td class="py-4 px-2 text-right whitespace-nowrap">
                                @if ($booking->status === App\Enums\BookingStatus::CONFIRMED)
                                    <flux:button 
                                        wire:click="advanceStatus({{ $booking->id }}, 'heading_to_location')" 
                                        size="xs" 
                                        variant="primary" 
                                        icon="arrow-right"
                                    >
                                        {{ __('Heading to Location') }}
                                    </flux:button>
                                @elseif ($booking->status === App\Enums\BookingStatus::HEADING_TO_LOCATION)
                                    <flux:button 
                                        wire:click="advanceStatus({{ $booking->id }}, 'ongoing')" 
                                        size="xs" 
                                        variant="primary" 
                                        icon="play"
                                    >
                                        {{ __('Start Tour') }}
                                    </flux:button>
                                @elseif ($booking->status === App\Enums\BookingStatus::ONGOING)
                                    <flux:button 
                                        wire:click="advanceStatus({{ $booking->id }}, 'completed')" 
                                        size="xs" 
                                        variant="primary" 
                                        icon="stop"
                                    >
                                        {{ __('End Tour') }}
                                    </flux:button>
                                @elseif ($booking->status === App\Enums\BookingStatus::COMPLETED)
                                    <span class="inline-flex items-center gap-1.5 text-xs text-emerald-600 font-semibold px-2 py-1">
                                        <flux:icon.check class="size-4" />
                                        {{ __('Audit Complete') }}
                                    </span>
                                @elseif ($booking->status === App\Enums\BookingStatus::REJECTED)
                                    <span class="inline-flex items-center gap-1.5 text-xs text-red-600 font-semibold px-2 py-1">
                                        <flux:icon.x-mark class="size-4" />
                                        {{ __('Declined') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10">
                                <flux:icon.inbox class="size-12 text-zinc-300 dark:text-zinc-700 mx-auto mb-2" />
                                <p class="text-xs font-semibold text-zinc-650 dark:text-zinc-400">No active or completed orders found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
