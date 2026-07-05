<div wire:poll.5s="refreshBooking" class="space-y-6">
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

    @if ($booking)
        <!-- Header Section -->
        <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-zinc-950 dark:text-white">{{ __('Active Tour Tracker') }}</h2>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                Booking ID: <span class="font-mono font-semibold">#{{ str_pad((string) $booking->id, 8, '0', STR_PAD_LEFT) }}</span>
            </p>
        </div>
        <a 
            href="{{ route('dashboard') }}" 
            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg border border-zinc-200 hover:bg-zinc-50 text-zinc-700 dark:border-zinc-800 dark:hover:bg-zinc-900 dark:text-zinc-350 transition-colors self-start"
        >
            <svg class="size-3.5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg>
            {{ __('Back to Dashboard') }}
        </a>
    </div>

    @php
        $currentStep = match ($booking->status) {
            \App\Enums\BookingStatus::PENDING_CONFIRMATION => 1,
            \App\Enums\BookingStatus::WAITING_PAYMENT => 1,
            \App\Enums\BookingStatus::CONFIRMED => 2,
            \App\Enums\BookingStatus::HEADING_TO_LOCATION => 3,
            \App\Enums\BookingStatus::ONGOING => 4,
            \App\Enums\BookingStatus::COMPLETED => 5,
            default => 1,
        };

        $steps = [
            ['title' => 'Request Sent'],
            ['title' => 'Confirmed'],
            ['title' => 'Guide En Route'],
            ['title' => 'Tour Ongoing'],
            ['title' => 'Completed'],
        ];
    @endphp

    <!-- Timeline Stepper -->
    @if ($booking->status !== \App\Enums\BookingStatus::REJECTED)
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-8">

                <div class="relative">

                    {{-- Background line --}}
                    <div class="absolute top-5 left-0 right-0 h-1 bg-zinc-200 dark:bg-zinc-700 hidden md:block rounded-full"></div>

                    {{-- Progress --}}
                    <div
                        class="absolute top-5 left-0 h-1 bg-emerald-500 hidden md:block rounded-full transition-all duration-500"
                        style="width: {{ (($currentStep-1)/4)*100 }}%">
                    </div>

                    <div class="grid grid-cols-5 relative">

                        @foreach($steps as $index => $step)

                            @php
                                $number = $index + 1;

                                $completed = $number < $currentStep;
                                $current = $number == $currentStep;
                            @endphp

                            <div class="flex flex-col items-center">

                                {{-- Circle --}}
                                <div
                                    class="
                                    relative z-10
                                    flex items-center justify-center
                                    w-10 h-10 rounded-full
                                    transition-all duration-300

                                    @if($completed)
                                        bg-emerald-500 text-white
                                    @elseif($current)
                                        bg-emerald-500 text-white ring-4 ring-emerald-100 dark:ring-emerald-900
                                    @else
                                        bg-white dark:bg-zinc-900 border-2 border-zinc-300 dark:border-zinc-700 text-zinc-400
                                    @endif
                                ">

                                    @if($completed)

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="w-5 h-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="3"
                                                d="M5 13l4 4L19 7"/>
                                        </svg>

                                    @else

                                        {{ $number }}

                                    @endif

                                </div>

                                {{-- Text --}}
                                <div class="mt-3 text-center">

                                    <p class="font-semibold text-sm
                                    {{ $current || $completed
                                        ? 'text-emerald-600 dark:text-emerald-400'
                                        : 'text-zinc-400'
                                    }}">
                                        {{ __($step['title']) }}
                                    </p>

                                    @if($current)
                                        <span class="text-xs text-zinc-500">
                                            Current Step
                                        </span>
                                    @endif

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            </div>
    @else
        <!-- Rejection Banner -->
        <div class="flex gap-4 p-5 rounded-xl border border-red-200 bg-red-50 dark:border-red-900/50 dark:bg-red-950/20 text-red-800 dark:text-red-300">
            <svg class="size-6 shrink-0 mt-0.5 text-red-650" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
            <div class="space-y-1">
                <h3 class="font-bold text-sm text-red-900 dark:text-red-200">{{ __('Booking Request Declined') }}</h3>
                <p class="text-xs leading-relaxed text-red-800/90 dark:text-red-300/90">
                    Your booking request was declined by the guide. You can search for other active verified tour guides.
                </p>
                <div class="pt-2">
                    <a 
                        href="{{ route('guides.index') }}" 
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-[11px] font-bold rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors"
                    >
                        {{ __('Find Another Guide') }}
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Booking Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left details pane (8 cols) -->
        <div class="lg:col-span-8 flex flex-col border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs gap-6">
            <div>
                <h3 class="text-md font-bold text-zinc-900 dark:text-zinc-50">{{ __('Custom Itinerary Route') }}</h3>
                <p class="text-xs text-zinc-500 mt-1">{{ __('Schedule checkpoints and pickup address info.') }}</p>
            </div>

            <hr class="border-zinc-200 dark:border-zinc-800" />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                <div class="space-y-2">
                    <p class="text-zinc-500"><strong>Pickup Location:</strong></p>
                    <p class="text-zinc-800 dark:text-zinc-200 font-semibold bg-zinc-50 dark:bg-zinc-900 p-3 rounded-lg border dark:border-zinc-800">{{ $booking->pickup_location }}</p>
                </div>
                <div class="space-y-2">
                    <p class="text-zinc-500"><strong>Drop-off Location:</strong></p>
                    <p class="text-zinc-800 dark:text-zinc-200 font-semibold bg-zinc-50 dark:bg-zinc-900 p-3 rounded-lg border dark:border-zinc-800">{{ $booking->dropoff_location ?: __('Same as pickup') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                <div class="space-y-1">
                    <p class="text-zinc-500"><strong>Scheduled Date & Time:</strong></p>
                    <p class="text-zinc-800 dark:text-zinc-200 font-semibold">{{ $booking->schedule_date->format('F j, Y') }} at {{ $booking->pickup_time }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-zinc-500"><strong>Escrow Transaction Reference:</strong></p>
                    <p class="text-zinc-800 dark:text-zinc-200 font-mono font-semibold">{{ $booking->escrowTransaction?->transaction_reference ?: '-' }}</p>
                </div>
            </div>

            <div class="space-y-2">
                <p class="text-xs text-zinc-500"><strong>Custom Route Places:</strong></p>
                <div class="flex flex-wrap gap-2 p-3 rounded-lg border border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50">
                    @foreach ($booking->custom_destinations as $index => $dest)
                        <span class="inline-flex items-center gap-1.5 bg-zinc-900 text-white dark:bg-zinc-850 text-xs font-semibold px-3 py-1.5 rounded-full">
                            <span class="text-[9px] text-zinc-400 font-bold bg-zinc-800 dark:bg-zinc-900 size-4 flex items-center justify-center rounded-full">{{ $index + 1 }}</span>
                            <span>{{ $dest }}</span>
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Summary pane (4 cols) -->
        <div class="lg:col-span-4 flex flex-col border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs justify-between gap-6 h-fit">
            <div class="space-y-4">
                <div>
                    <h3 class="text-md font-bold text-zinc-900 dark:text-zinc-50">{{ __('Guide Summary') }}</h3>
                </div>

                <hr class="border-zinc-200 dark:border-zinc-800" />

                <!-- Guide profile card -->
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-bold text-zinc-800 dark:text-zinc-200">
                        {{ $booking->guide->initials() }}
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $booking->guide->name }}</h4>
                        <p class="text-xs text-zinc-500">{{ $booking->guide->phone_number }}</p>
                    </div>
                </div>

                <div class="pt-2 text-xs space-y-1.5">
                    <p class="text-zinc-500 flex justify-between">
                        <span>Base Rate:</span>
                        <span class="font-semibold text-zinc-800 dark:text-zinc-200">Rp {{ number_format($booking->guide->guideProfile->base_rate, 0, ',', '.') }} / {{ $booking->guide->guideProfile->tariff_mode->value }}</span>
                    </p>
                    <p class="text-zinc-500 flex justify-between">
                        <span>Tariff Model:</span>
                        <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ ucfirst($booking->guide->guideProfile->tariff_mode->value) }}</span>
                    </p>
                </div>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-xl border dark:border-zinc-800 space-y-2">
                <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider block">{{ __('Itinerary Price') }}</span>
                <p class="text-xl font-extrabold text-zinc-900 dark:text-white">
                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                </p>
                <div class="flex items-center gap-1.5 text-[10px] text-zinc-500 leading-normal border-t border-zinc-200 dark:border-zinc-800 pt-2 mt-1">
                    <svg class="size-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z"/></svg>
                    <span>{{ __('Escrow payment locked') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- In-App Chat Panel -->
    @if ($booking->status !== \App\Enums\BookingStatus::REJECTED)
        <div class="space-y-2">
            <h3 class="text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Communication Channel') }}</h3>
            @livewire('chat.chat-room', ['bookingId' => $booking->id])
        </div>
    @endif

    <!-- Rating & Review Modal Overlay -->
    @if ($showReviewModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-stone-900 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-md w-full p-6 shadow-xl space-y-5">
                <div class="text-center space-y-1">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">{{ __('Rate Your Bali Tour Guide') }}</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ __('How was your custom tour experience with :name?', ['name' => $booking->guide->name]) }}
                    </p>
                </div>

                <!-- Star Rating Selector -->
                <div class="flex flex-col items-center gap-1 bg-zinc-50 dark:bg-zinc-950 p-4 rounded-xl border dark:border-zinc-850">
                    <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">{{ __('Select Rating') }}</span>
                    <div class="flex gap-2 justify-center mt-1">
                        @foreach (range(1, 5) as $i)
                            <button 
                                type="button" 
                                wire:click="$set('rating', {{ $i }})"
                                class="text-3xl transition-colors hover:scale-115 focus:outline-hidden {{ $rating >= $i ? 'text-amber-500 fill-current font-bold' : 'text-zinc-300 dark:text-zinc-700' }}"
                            >
                                ★
                            </button>
                        @endforeach
                    </div>
                    <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 mt-2">
                        {{ $rating }} / 5 Stars
                    </span>
                </div>

                <!-- Comment Input -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('Write a Review') }}</label>
                    <textarea 
                        wire:model="comment" 
                        placeholder="Share details about the destinations, guide's hospitality, vehicle safety..." 
                        rows="4" 
                        required 
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                    ></textarea>
                    @error('comment') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-2 justify-end pt-2">
                    <button 
                        type="button" 
                        wire:click="$set('showReviewModal', false)" 
                        class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-650 dark:text-zinc-400 transition-colors"
                    >
                        {{ __('Skip') }}
                    </button>
                    <button 
                        wire:click="submitReview" 
                        type="button" 
                        class="inline-flex items-center px-6 py-2 text-xs font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs"
                    >
                        {{ __('Submit Feedback') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
    @else
        <!-- No Active Trips placeholder -->
        <div class="flex flex-col items-center justify-center text-center py-20 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-8 shadow-xs gap-4 my-6">
             <div class="size-16 rounded-full bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center text-zinc-400 dark:text-zinc-600">
                 <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
             </div>
             <div class="space-y-1">
                 <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ __('No Active Trips') }}</h3>
                 <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-sm">
                     {{ __('You do not have any active tour or itinerary bookings at the moment. Search for tour guides to plan your next journey.') }}
                 </p>
             </div>
             <div class="pt-2">
                 <a 
                     href="{{ route('guides.index') }}" 
                     wire:navigate
                     class="inline-flex items-center gap-1.5 px-5 py-2.5 text-xs font-bold rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-black transition-colors shadow-xs"
                 >
                     {{ __('Find a Tour Guide') }}
                 </a>
             </div>
         </div>
    @endif
</div>
