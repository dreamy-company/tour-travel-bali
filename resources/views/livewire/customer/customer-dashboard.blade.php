<div wire:poll.5s="refreshDashboard" class="space-y-6">
    <!-- Active Trip View -->
    @if ($activeBooking)
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Panel: Stepper, Payments, and Chat (8 cols) -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Tracker Box -->
                <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs">
                    <div class="flex items-center justify-between pb-4 border-b border-zinc-100 dark:border-zinc-900 gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-zinc-950 dark:text-white">{{ __('Active Tour Tracker') }}</h2>
                            <p class="text-xs text-zinc-500 mt-1">
                                Booking ID: <span class="font-mono font-semibold">#{{ str_pad((string) $activeBooking->id, 8, '0', STR_PAD_LEFT) }}</span>
                            </p>
                        </div>
                        <a href="{{ route('bookings.tracker', $activeBooking) }}" wire:navigate class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg border border-zinc-200 hover:bg-zinc-50 text-zinc-700 dark:border-zinc-800 dark:hover:bg-zinc-900 dark:text-zinc-300 transition-colors shrink-0">
                            {{ __('Open Tracking Page') }}
                            <svg class="size-3.5 fill-none stroke-current" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                        </a>
                    </div>

                    @php
                        $currentStep = match ($activeBooking->status) {
                            \App\Enums\BookingStatus::PENDING_CONFIRMATION => 1,
                            \App\Enums\BookingStatus::CONFIRMED => 2,
                            \App\Enums\BookingStatus::HEADING_TO_LOCATION => 3,
                            \App\Enums\BookingStatus::ONGOING => 4,
                            \App\Enums\BookingStatus::COMPLETED => 5,
                            default => 1,
                        };
                    @endphp

                    <!-- Stepper -->
                    <div class="relative flex flex-col md:flex-row justify-between items-center md:items-start gap-8 md:gap-4 my-8">
                        <div class="absolute left-1/2 md:left-0 top-0 md:top-4 h-full md:h-0.5 w-0.5 md:w-full -translate-x-1/2 md:translate-x-0 bg-zinc-100 dark:bg-zinc-800 z-0"></div>
                        <div class="absolute left-1/2 md:left-0 top-0 md:top-4 h-full md:h-0.5 w-0.5 md:w-full -translate-x-1/2 md:translate-x-0 bg-emerald-600 dark:bg-emerald-500 transition-all duration-300 z-0" style="height: {{ $currentStep > 1 && $currentStep < 5 ? (($currentStep - 1) / 4) * 100 : '0' }}%; @media (min-width: 768px) { width: {{ (($currentStep - 1) / 4) * 100 }}%; height: 0.125rem; }"></div>

                        <!-- Step 1: Request Sent -->
                        <div class="relative z-10 flex flex-col items-center text-center max-w-xs gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full border text-xs font-semibold {{ $currentStep >= 1 ? 'bg-emerald-600 text-white border-emerald-600 dark:bg-emerald-500 dark:text-white dark:border-emerald-500' : 'bg-white text-zinc-500 border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800' }}">1</div>
                            <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ __('Request Sent') }}</span>
                        </div>

                        <!-- Step 2: Confirmed -->
                        <div class="relative z-10 flex flex-col items-center text-center max-w-xs gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full border text-xs font-semibold {{ $currentStep >= 2 ? 'bg-emerald-600 text-white border-emerald-600 dark:bg-emerald-500 dark:text-white dark:border-emerald-500' : 'bg-white text-zinc-500 border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800' }}">2</div>
                            <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ __('Confirmed') }}</span>
                        </div>

                        <!-- Step 3: En Route -->
                        <div class="relative z-10 flex flex-col items-center text-center max-w-xs gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full border text-xs font-semibold {{ $currentStep >= 3 ? 'bg-emerald-600 text-white border-emerald-600 dark:bg-emerald-500 dark:text-white dark:border-emerald-500' : 'bg-white text-zinc-500 border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800' }}">3</div>
                            <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ __('Guide En Route') }}</span>
                        </div>

                        <!-- Step 4: Ongoing -->
                        <div class="relative z-10 flex flex-col items-center text-center max-w-xs gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full border text-xs font-semibold {{ $currentStep >= 4 ? 'bg-emerald-600 text-white border-emerald-600 dark:bg-emerald-500 dark:text-white dark:border-emerald-500' : 'bg-white text-zinc-500 border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800' }}">4</div>
                            <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ __('Tour Ongoing') }}</span>
                        </div>

                        <!-- Step 5: Completed -->
                        <div class="relative z-10 flex flex-col items-center text-center max-w-xs gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full border text-xs font-semibold {{ $currentStep >= 5 ? 'bg-emerald-650 border-emerald-650 text-white dark:bg-emerald-500 dark:text-white' : 'bg-white text-zinc-500 border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800' }}">5</div>
                            <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ __('Completed') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Checkout Card if waiting payment -->
                @if ($activeBooking->escrowTransaction && $activeBooking->escrowTransaction->status === \App\Enums\EscrowStatus::WAITING_PAYMENT)
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border border-amber-200 bg-amber-50/50 dark:border-amber-900/30 dark:bg-amber-950/10 p-5 rounded-xl gap-4">
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-amber-900 dark:text-amber-300">{{ __('Escrow Payment Required') }}</h4>
                            <p class="text-xs text-amber-800/80 dark:text-amber-400/80 leading-relaxed">
                                {{ __('Please complete the payment for your custom tour to confirm your guide.') }}
                            </p>
                        </div>
                        <a href="{{ $activeBooking->escrowTransaction->redirect_url ?: '#' }}" target="_blank" class="inline-flex items-center px-4 py-2 text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors shrink-0">
                            {{ __('Complete Payment') }}
                        </a>
                    </div>
                @endif

                <!-- Chat Widget natively embedded -->
                <div class="space-y-2">
                    <h3 class="text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Active Communication Channel') }}</h3>
                    @livewire('chat.chat-room', ['bookingId' => $activeBooking->id])
                </div>
            </div>

            <!-- Right Panel: Guide Profile & Escrow Summary (4 cols) -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Guide Summary Card -->
                <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs space-y-4">
                    <h3 class="text-md font-bold text-zinc-900 dark:text-zinc-50">{{ __('Guide Summary') }}</h3>
                    <hr class="border-zinc-200 dark:border-zinc-800" />
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-full bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center font-bold text-zinc-800 dark:text-zinc-200">
                            {{ $activeBooking->guide->initials() }}
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 truncate">{{ $activeBooking->guide->name }}</h4>
                            <p class="text-xs text-zinc-500">{{ $activeBooking->guide->phone_number }}</p>
                        </div>
                    </div>
                    <div class="pt-2 text-xs space-y-1.5 border-t border-zinc-100 dark:border-zinc-900">
                        <p class="text-zinc-500 flex justify-between">
                            <span>Base Rate:</span>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-200">Rp {{ number_format($activeBooking->guide->guideProfile->base_rate, 0, ',', '.') }} / {{ $activeBooking->guide->guideProfile->tariff_mode->value }}</span>
                        </p>
                        <p class="text-zinc-500 flex justify-between">
                            <span>Pickup Address:</span>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-200 truncate max-w-xs">{{ $activeBooking->pickup_location }}</span>
                        </p>
                    </div>
                </div>

                <!-- Escrow Security info -->
                <div class="bg-zinc-50 dark:bg-zinc-900 p-5 rounded-xl border dark:border-zinc-850 space-y-3">
                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider block">{{ __('Escrow Lock') }}</span>
                    <p class="text-2xl font-extrabold text-zinc-900 dark:text-white">
                        Rp {{ number_format($activeBooking->total_price, 0, ',', '.') }}
                    </p>
                    <p class="text-[10px] text-zinc-500 leading-normal border-t border-zinc-200 dark:border-zinc-850 pt-2.5">
                        {{ __('Funds are securely locked in our central escrow wallet. They are released to the guide only when the tour is fully completed.') }}
                    </p>
                </div>
            </div>
        </div>

    <!-- History View (no active trips exist) -->
    @else
        <div class="space-y-6">
            <!-- Balinese travel design accent banner -->
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10 pointer-events-none bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] dark:bg-[radial-gradient(#374151_1px,transparent_1px)] [background-size:16px_16px]"></div>
                <div class="space-y-1.5 relative z-10 max-w-xl">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">{{ __('Design Your Balinese Adventure') }}</h3>
                    <p class="text-xs text-zinc-500 leading-relaxed">
                        Connect with verified tour guides registered under local Bali Provincial Government standards. Design custom daily/hourly itineraries and request secure QRIS/Virtual Account payments.
                    </p>
                </div>
                <a href="{{ route('guides.index') }}" class="relative z-10 inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs shrink-0">
                    {{ __('Find Tour Guides') }}
                    <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </a>
            </div>

            <!-- Order History Table Card -->
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col gap-4">
                <div>
                    <h3 class="text-md font-bold text-zinc-900 dark:text-zinc-50">{{ __('Your Order History') }}</h3>
                    <p class="text-xs text-zinc-500 mt-1">{{ __('List of your past tour bookings and reviews.') }}</p>
                </div>

                <hr class="border-zinc-200 dark:border-zinc-800" />

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-zinc-100 dark:border-zinc-900 text-zinc-400 uppercase tracking-wider font-semibold">
                                <th class="py-3.5 px-4">Date</th>
                                <th class="py-3.5 px-4">Guide Name</th>
                                <th class="py-3.5 px-4">Total Price</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-900">
                            @forelse ($pastBookings as $past)
                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-900/10 text-zinc-800 dark:text-zinc-200">
                                    <td class="py-3.5 px-4">{{ $past->schedule_date->format('M d, Y') }}</td>
                                    <td class="py-3.5 px-4 font-medium">{{ $past->guide->name }}</td>
                                    <td class="py-3.5 px-4">Rp {{ number_format($past->total_price, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if ($past->status === \App\Enums\BookingStatus::COMPLETED)
                                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-[10px] font-semibold text-green-700 ring-1 ring-inset ring-green-600/10 dark:bg-green-950/20 dark:text-green-400 dark:ring-green-500/20">
                                                Completed
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-md bg-zinc-50 px-2 py-1 text-[10px] font-semibold text-zinc-700 ring-1 ring-inset ring-zinc-600/10 dark:bg-zinc-900 dark:text-zinc-400 dark:ring-zinc-450">
                                                Declined
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        @if ($past->status === \App\Enums\BookingStatus::COMPLETED)
                                            @if ($past->has_review)
                                                <span class="text-zinc-400 dark:text-zinc-550 text-[11px] font-medium mr-2">{{ __('Reviewed') }}</span>
                                            @else
                                                <button wire:click="openFeedback({{ $past->id }})" type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors">
                                                    {{ __('Leave Feedback') }}
                                                </button>
                                            @endif
                                        @else
                                            <span class="text-zinc-400 text-[11px]">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-zinc-400">
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
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs animate-fade-in">
            <div class="bg-white dark:bg-stone-900 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-md w-full p-6 shadow-xl space-y-5">
                <div class="text-center space-y-1">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">{{ __('Rate Your Bali Tour Guide') }}</h3>
                    <p class="text-xs text-zinc-500">
                        {{ __('How was your custom tour experience with your guide?') }}
                    </p>
                </div>

                <!-- Stars Selector -->
                <div class="flex flex-col items-center gap-1 bg-zinc-50 dark:bg-zinc-950 p-4 rounded-xl border dark:border-zinc-850">
                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">{{ __('Select Rating') }}</span>
                    <div class="flex gap-2 justify-center mt-1">
                        @foreach (range(1, 5) as $i)
                            <button type="button" wire:click="$set('rating', {{ $i }})" class="text-3xl transition-colors hover:scale-110 focus:outline-hidden {{ $rating >= $i ? 'text-amber-500 fill-current' : 'text-zinc-300 dark:text-zinc-700' }}">
                                ★
                            </button>
                        @endforeach
                    </div>
                    <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 mt-2">
                        {{ $rating }} / 5 Stars
                    </span>
                </div>

                <!-- Comment -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('Write a Review') }}</label>
                    <textarea wire:model="comment" placeholder="Share details about the destinations, guide's hospitality, vehicle safety..." rows="4" required class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"></textarea>
                    @error('comment') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" wire:click="skipFeedback" class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-650 dark:text-zinc-400 transition-colors">
                        {{ __('Skip') }}
                    </button>
                    <button wire:click="submitFeedback" type="button" class="inline-flex items-center px-6 py-2 text-xs font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs">
                        {{ __('Submit Feedback') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
