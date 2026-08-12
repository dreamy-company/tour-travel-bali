<div wire:poll.5s="refreshDashboard" class="space-y-6">
    <!-- Active Trip View -->
    @if ($activeBooking)
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Panel: Stepper, Payments, and Chat (8 cols) -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Tracker Box -->
                <div class="border border-hairline dark:border-zinc-800 rounded-[14px] bg-white dark:bg-stone-950 p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-hairline-soft dark:border-zinc-900 gap-4">
                        <div>
                            <h2 class="text-[22px] font-medium tracking-[-0.44px] text-ink dark:text-white leading-tight">{{ __('Active Tour Tracker') }}</h2>
                            <p class="text-sm text-muted mt-1">
                                Booking ID: <span class="font-mono font-semibold text-ink dark:text-zinc-200">#{{ str_pad((string) $activeBooking->id, 8, '0', STR_PAD_LEFT) }}</span>
                            </p>
                        </div>
                        <a href="{{ route('bookings.tracker', $activeBooking) }}" wire:navigate class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-full border border-hairline hover:bg-surface-soft text-ink dark:border-zinc-800 dark:hover:bg-zinc-900 dark:text-zinc-300 transition-colors shrink-0">
                            {{ __('Open Tracking Page') }}
                            <svg class="size-3.5 fill-none stroke-current" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                        </a>
                    </div>

                @php
                    $currentStep = match ($activeBooking->status) {
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

                <div class="rounded-[14px] border border-hairline dark:border-zinc-800 bg-white dark:bg-zinc-900 p-8 mt-6">

                    <div class="relative">

                        {{-- Background line --}}
                        <div class="absolute top-5 left-0 right-0 h-1 bg-hairline dark:bg-zinc-700 hidden md:block rounded-full"></div>

                        {{-- Progress --}}
                        <div
                            class="absolute top-5 left-0 h-1 bg-rausch hidden md:block rounded-full transition-all duration-500"
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
                                            bg-ink text-white dark:bg-zinc-800
                                        @elseif($current)
                                            bg-rausch text-white ring-4 ring-rausch/15
                                        @else
                                            bg-white dark:bg-zinc-900 border-2 border-hairline dark:border-zinc-700 text-muted-soft
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

                                        <p class="font-medium text-sm
                                        {{ $current || $completed
                                            ? 'text-ink dark:text-zinc-100'
                                            : 'text-muted-soft'
                                        }}">
                                            {{ __($step['title']) }}
                                        </p>

                                        @if($current)
                                            <span class="text-xs text-rausch font-medium">
                                                Current Step
                                            </span>
                                        @endif

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>

                </div>

                <!-- Payment Checkout Card if waiting payment -->
                @if ($activeBooking->status === \App\Enums\BookingStatus::WAITING_PAYMENT && $activeBooking->escrowTransaction && $activeBooking->escrowTransaction->status === \App\Enums\EscrowStatus::WAITING_PAYMENT)
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border border-hairline dark:border-zinc-800 bg-white dark:bg-stone-950 p-5 rounded-[14px] gap-4 shadow-airbnb">
                        <div class="space-y-1">
                            <h4 class="text-base font-semibold text-ink dark:text-zinc-100">{{ __('Escrow Payment Required') }}</h4>
                            <p class="text-sm text-muted dark:text-zinc-400 leading-relaxed">
                                {{ __('Please complete the payment for your custom tour to confirm your guide.') }}
                            </p>
                        </div>
                        <a href="{{ $activeBooking->escrowTransaction->redirect_url ?: '#' }}" target="_blank" class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg bg-rausch hover:bg-rausch-active text-white transition-colors shrink-0">
                            {{ __('Complete Payment') }}
                        </a>
                    </div>
                @endif

                <!-- Chat Widget natively embedded -->
                <div class="space-y-2">
                    <h3 class="text-sm font-semibold text-muted dark:text-zinc-400">{{ __('Active Communication Channel') }}</h3>
                    @livewire('chat.chat-room', ['bookingId' => $activeBooking->id])
                </div>
            </div>

            <!-- Right Panel: Guide Profile & Escrow Summary (4 cols) -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Guide Summary Card -->
                <div class="border border-hairline dark:border-zinc-800 rounded-[14px] bg-white dark:bg-stone-950 p-6 space-y-4">
                    <h3 class="text-base font-semibold text-ink dark:text-zinc-50">{{ __('Guide Summary') }}</h3>
                    <hr class="border-hairline dark:border-zinc-800" />
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-full bg-surface-soft dark:bg-zinc-900 flex items-center justify-center font-semibold text-ink dark:text-zinc-200">
                            {{ $activeBooking->guide->initials() }}
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-ink dark:text-zinc-100 truncate">{{ $activeBooking->guide->name }}</h4>
                            <p class="text-sm text-muted">{{ $activeBooking->guide->phone_number }}</p>
                        </div>
                    </div>
                    <div class="pt-2 text-sm space-y-1.5 border-t border-hairline-soft dark:border-zinc-900">
                        <p class="text-muted flex justify-between">
                            <span>Base Rate:</span>
                            <span class="font-semibold text-ink dark:text-zinc-200">Rp {{ number_format($activeBooking->guide->guideProfile->base_rate, 0, ',', '.') }} / {{ $activeBooking->guide->guideProfile->tariff_mode->value }}</span>
                        </p>
                        <p class="text-muted flex justify-between">
                            <span>Pickup Address:</span>
                            <span class="font-semibold text-ink dark:text-zinc-200 truncate max-w-xs">{{ $activeBooking->pickup_location }}</span>
                        </p>
                    </div>
                </div>

                <!-- Escrow Security info — Airbnb reservation-card treatment -->
                <div class="border border-hairline dark:border-zinc-800 rounded-[14px] bg-white dark:bg-stone-950 p-6 space-y-3 shadow-airbnb">
                    <span class="text-[11px] font-semibold text-muted-soft uppercase tracking-wider block">{{ __('Escrow Lock') }}</span>
                    <p class="text-3xl font-bold tracking-[-1px] text-ink dark:text-white">
                        Rp {{ number_format($activeBooking->total_price, 0, ',', '.') }}
                    </p>
                    <p class="text-sm text-muted leading-normal border-t border-hairline-soft dark:border-zinc-800 pt-3">
                        {{ __('Funds are securely locked in our central escrow wallet. They are released to the guide only when the tour is fully completed.') }}
                    </p>
                </div>
            </div>
        </div>

    <!-- History View (no active trips exist) -->
    @else
        <div class="space-y-6">
            <!-- Hero band — open, photography-led -->
            <div class="border border-hairline dark:border-zinc-800 rounded-[14px] bg-white dark:bg-stone-950 p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
                <div class="absolute inset-0 opacity-[0.04] pointer-events-none bg-[radial-gradient(#ff385c_1px,transparent_1px)] dark:bg-[radial-gradient(#ff385c_1px,transparent_1px)] [background-size:16px_16px]"></div>
                <div class="space-y-1.5 relative z-10 max-w-xl">
                    <h3 class="text-[22px] font-medium tracking-[-0.44px] text-ink dark:text-zinc-50 leading-tight">{{ __('Design Your Balinese Adventure') }}</h3>
                    <p class="text-sm text-muted leading-relaxed">
                        Connect with verified tour guides registered under local Bali Provincial Government standards. Design custom daily/hourly itineraries and request secure QRIS/Virtual Account payments.
                    </p>
                </div>
                <a href="{{ route('guides.index') }}" class="relative z-10 inline-flex items-center gap-1.5 px-6 py-3 text-sm font-medium rounded-lg bg-rausch hover:bg-rausch-active text-white transition-colors shadow-airbnb shrink-0">
                    {{ __('Find Tour Guides') }}
                    <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </a>
            </div>

            <!-- Order History Table Card -->
            <div class="border border-hairline dark:border-zinc-800 rounded-[14px] bg-white dark:bg-stone-950 p-6 flex flex-col gap-4">
                <div>
                    <h3 class="text-base font-semibold text-ink dark:text-zinc-50">{{ __('Your Order History') }}</h3>
                    <p class="text-sm text-muted mt-1">{{ __('List of your past tour bookings and reviews.') }}</p>
                </div>

                <hr class="border-hairline dark:border-zinc-800" />

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-hairline dark:border-zinc-900 text-muted-soft uppercase tracking-wider font-semibold text-xs">
                                <th class="py-3.5 px-4">Date</th>
                                <th class="py-3.5 px-4">Guide Name</th>
                                <th class="py-3.5 px-4">Total Price</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline-soft dark:divide-zinc-900">
                            @forelse ($pastBookings as $past)
                                <tr class="hover:bg-surface-soft/60 dark:hover:bg-zinc-900/10 text-ink dark:text-zinc-200">
                                    <td class="py-3.5 px-4">{{ $past->schedule_date->format('M d, Y') }}</td>
                                    <td class="py-3.5 px-4 font-medium">{{ $past->guide->name }}</td>
                                    <td class="py-3.5 px-4">Rp {{ number_format($past->total_price, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if ($past->status === \App\Enums\BookingStatus::COMPLETED)
                                            <span class="inline-flex items-center rounded-full bg-surface-soft dark:bg-zinc-900 px-2.5 py-1 text-xs font-semibold text-ink dark:text-zinc-300">
                                                Completed
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-surface-soft dark:bg-zinc-900 px-2.5 py-1 text-xs font-semibold text-muted dark:text-zinc-400">
                                                Declined
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        @if ($past->status === \App\Enums\BookingStatus::COMPLETED)
                                            @if ($past->has_review)
                                                <span class="text-muted-soft dark:text-zinc-500 text-sm font-medium mr-2">{{ __('Reviewed') }}</span>
                                            @else
                                                <button wire:click="openFeedback({{ $past->id }})" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors">
                                                    {{ __('Leave Feedback') }}
                                                </button>
                                            @endif
                                        @else
                                            <span class="text-muted-soft text-sm">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-muted-soft">
                                        {{ __('No past bookings found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Review/Feedback Modal Dialog -->
    @if ($showFeedbackModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 animate-fade-in">
            <div class="bg-white dark:bg-stone-900 border border-hairline dark:border-zinc-800 rounded-[14px] max-w-md w-full p-6 shadow-airbnb space-y-5">
                <div class="text-center space-y-1">
                    <h3 class="text-lg font-medium text-ink dark:text-zinc-50">{{ __('Rate Your Bali Tour Guide') }}</h3>
                    <p class="text-sm text-muted">
                        {{ __('How was your custom tour experience with your guide?') }}
                    </p>
                </div>

                <!-- Stars Selector — ink stars per Airbnb brand rule -->
                <div class="flex flex-col items-center gap-1 bg-surface-soft dark:bg-zinc-950 p-4 rounded-[14px] border dark:border-zinc-800">
                    <span class="text-[11px] font-semibold text-muted-soft uppercase tracking-wider">{{ __('Select Rating') }}</span>
                    <div class="flex gap-2 justify-center mt-1">
                        @foreach (range(1, 5) as $i)
                            <button type="button" wire:click="$set('rating', {{ $i }})" class="text-3xl transition-all hover:scale-110 focus:outline-hidden {{ $rating >= $i ? 'text-star fill-current' : 'text-hairline dark:text-zinc-700' }}">
                                ★
                            </button>
                        @endforeach
                    </div>
                    <span class="text-sm font-semibold text-ink dark:text-zinc-300 mt-2">
                        {{ $rating }} / 5 Stars
                    </span>
                </div>

                <!-- Comment -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-ink dark:text-zinc-300 block">{{ __('Write a Review') }}</label>
                    <textarea wire:model="comment" placeholder="Share details about the destinations, guide's hospitality, vehicle safety..." rows="4" required class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-hairline bg-white text-ink dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-rausch"></textarea>
                    @error('comment') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" wire:click="skipFeedback" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-full hover:bg-surface-soft dark:hover:bg-zinc-800 text-ink dark:text-zinc-300 transition-colors">
                        {{ __('Skip') }}
                    </button>
                    <button wire:click="submitFeedback" type="button" class="inline-flex items-center px-6 py-2.5 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-airbnb">
                        {{ __('Submit Feedback') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
