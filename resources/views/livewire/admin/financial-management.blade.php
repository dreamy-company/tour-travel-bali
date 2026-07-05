<div class="space-y-8">
    <!-- Session Flash Messages -->
    @if (session()->has('success'))
        <div class="p-4 text-sm text-green-800 rounded-xl bg-green-50 dark:bg-green-950/20 dark:text-green-300 border border-green-200 dark:border-green-900/40 flex items-center gap-2.5">
            <svg class="size-5 shrink-0 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Page Header -->
    <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-zinc-950 dark:text-white">{{ __('Financial & Dispute Management') }}</h2>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Process withdrawal requests and resolve disputed bookings.') }}</p>
        </div>
        <!-- Summary pills -->
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/40 text-amber-700 dark:text-amber-300 text-xs font-semibold">
                <span class="size-2 rounded-full bg-amber-500 animate-pulse"></span>
                {{ $pendingWithdrawals->count() }} {{ __('Pending Payouts') }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/40 text-red-700 dark:text-red-300 text-xs font-semibold">
                <span class="size-2 rounded-full bg-red-500 animate-pulse"></span>
                {{ $disputedBookings->count() }} {{ __('Disputes') }}
            </span>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         SECTION 1 — WITHDRAWAL QUEUE
    ════════════════════════════════════════════════════ -->
    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 overflow-hidden shadow-xs">
        <!-- Section Header -->
        <div class="px-6 py-4 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-3">
            <div class="flex items-center justify-center size-8 rounded-lg bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/30">
                <svg class="size-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">{{ __('Withdrawal Queue') }}</h3>
                <p class="text-[10px] text-zinc-500 dark:text-zinc-400">{{ __('Review and process guide payout requests.') }}</p>
            </div>
        </div>

        @if ($pendingWithdrawals->isEmpty())
            <div class="flex flex-col items-center justify-center py-14 text-center">
                <svg class="size-10 text-zinc-300 dark:text-zinc-700 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">{{ __('No pending withdrawals. All caught up!') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-zinc-100 dark:border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-5 text-left">{{ __('Guide') }}</th>
                            <th class="py-3 px-3 text-left">{{ __('Amount') }}</th>
                            <th class="py-3 px-3 text-left">{{ __('Bank Details') }}</th>
                            <th class="py-3 px-3 text-left">{{ __('Requested') }}</th>
                            <th class="py-3 px-5 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800/60">
                        @foreach ($pendingWithdrawals as $w)
                            <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-900/30 transition-colors">
                                <!-- Guide -->
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="size-7 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center font-bold text-[10px] text-zinc-700 dark:text-zinc-300 shrink-0">
                                            {{ $w->guide->initials() }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-zinc-900 dark:text-zinc-100">{{ $w->guide->name }}</p>
                                            <p class="text-[10px] text-zinc-400">{{ $w->guide->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <!-- Amount -->
                                <td class="py-4 px-3">
                                    <p class="font-extrabold text-zinc-900 dark:text-zinc-100 text-sm">
                                        Rp {{ number_format((float) $w->amount, 0, ',', '.') }}
                                    </p>
                                </td>
                                <!-- Bank Details -->
                                <td class="py-4 px-3">
                                    <p class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $w->bank_name }}</p>
                                    <p class="text-[10px] text-zinc-500 font-mono">{{ $w->bank_account_number }}</p>
                                    <p class="text-[10px] text-zinc-400">{{ $w->bank_account_name }}</p>
                                </td>
                                <!-- Timestamp -->
                                <td class="py-4 px-3 whitespace-nowrap">
                                    <p class="text-zinc-600 dark:text-zinc-400">{{ $w->created_at?->format('d M Y') }}</p>
                                    <p class="text-[9px] text-zinc-400">{{ $w->created_at?->format('H:i') }}</p>
                                </td>
                                <!-- Actions with two-step confirm -->
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    @if ($confirmingWithdrawalId === $w->id)
                                        <div class="flex items-center justify-end gap-2">
                                            <span class="text-[10px] text-zinc-500 dark:text-zinc-400">{{ __('Confirm action:') }}</span>
                                            <button
                                                wire:click="approvePayout({{ $w->id }})"
                                                type="button"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[10px] font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-colors"
                                            >
                                                <svg class="size-3 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                                {{ __('Approve') }}
                                            </button>
                                            <button
                                                wire:click="rejectPayout({{ $w->id }})"
                                                type="button"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[10px] font-bold rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors"
                                            >
                                                <svg class="size-3 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                                {{ __('Reject & Refund') }}
                                            </button>
                                            <button
                                                wire:click="$set('confirmingWithdrawalId', null)"
                                                type="button"
                                                class="text-[10px] text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 px-1"
                                            >
                                                {{ __('Cancel') }}
                                            </button>
                                        </div>
                                    @else
                                        <button
                                            wire:click="$set('confirmingWithdrawalId', {{ $w->id }})"
                                            type="button"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg border border-zinc-200 hover:bg-zinc-50 text-zinc-700 dark:border-zinc-700 dark:hover:bg-zinc-800 dark:text-zinc-300 transition-colors"
                                        >
                                            <svg class="size-3.5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z"/></svg>
                                            {{ __('Review') }}
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- ═══════════════════════════════════════════════════
         SECTION 2 — DISPUTE CENTER
    ════════════════════════════════════════════════════ -->
    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 overflow-hidden shadow-xs">
        <!-- Section Header -->
        <div class="px-6 py-4 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-3">
            <div class="flex items-center justify-center size-8 rounded-lg bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/30">
                <svg class="size-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">{{ __('Dispute Center') }}</h3>
                <p class="text-[10px] text-zinc-500 dark:text-zinc-400">{{ __('Administer escrow overrides for disputed tour bookings.') }}</p>
            </div>
        </div>

        @if ($disputedBookings->isEmpty())
            <div class="flex flex-col items-center justify-center py-14 text-center">
                <svg class="size-10 text-zinc-300 dark:text-zinc-700 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">{{ __('No active disputes. Platform is running smoothly.') }}</p>
            </div>
        @else
            <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @foreach ($disputedBookings as $booking)
                    <div class="p-5 sm:p-6 space-y-4">
                        <!-- Booking Meta Row -->
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div class="space-y-2 flex-1">
                                <!-- Booking ID badge + date -->
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-mono text-[10px] font-bold text-zinc-500 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded">
                                        #{{ str_pad((string) $booking->id, 8, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span class="text-[10px] text-zinc-400">
                                        {{ $booking->schedule_date->format('d M Y') }} · {{ $booking->pickup_time }}
                                    </span>
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold ring-1 ring-inset bg-red-50 text-red-700 ring-red-700/10 dark:bg-red-950/20 dark:text-red-400 dark:ring-red-500/20">
                                        {{ __('Disputed') }}
                                    </span>
                                </div>

                                <!-- Customer & Guide Grid -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                    <div class="flex items-center gap-2.5 bg-zinc-50 dark:bg-zinc-900/50 rounded-lg p-3 border border-zinc-100 dark:border-zinc-800">
                                        <div class="size-7 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center font-bold text-[9px] text-indigo-700 dark:text-indigo-300 shrink-0">
                                            {{ $booking->customer->initials() }}
                                        </div>
                                        <div>
                                            <p class="text-[9px] text-zinc-400 uppercase font-semibold tracking-wide">{{ __('Customer') }}</p>
                                            <p class="font-bold text-zinc-800 dark:text-zinc-200">{{ $booking->customer->name }}</p>
                                            <p class="text-[10px] text-zinc-500">{{ $booking->customer->phone_number }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2.5 bg-zinc-50 dark:bg-zinc-900/50 rounded-lg p-3 border border-zinc-100 dark:border-zinc-800">
                                        <div class="size-7 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center font-bold text-[9px] text-emerald-700 dark:text-emerald-300 shrink-0">
                                            {{ $booking->guide->initials() }}
                                        </div>
                                        <div>
                                            <p class="text-[9px] text-zinc-400 uppercase font-semibold tracking-wide">{{ __('Tour Guide') }}</p>
                                            <p class="font-bold text-zinc-800 dark:text-zinc-200">{{ $booking->guide->name }}</p>
                                            <p class="text-[10px] text-zinc-500">{{ $booking->guide->phone_number }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Escrow Summary -->
                            @if ($booking->escrowTransaction)
                                @php $escrow = $booking->escrowTransaction; @endphp
                                <div class="sm:w-52 shrink-0 bg-zinc-950 dark:bg-zinc-900 text-white rounded-xl p-4 space-y-2">
                                    <p class="text-[9px] font-bold uppercase tracking-wider text-zinc-400">{{ __('Escrow Held') }}</p>
                                    <p class="text-2xl font-extrabold">Rp {{ number_format((float) $escrow->gross_amount, 0, ',', '.') }}</p>
                                    <div class="text-[9px] text-zinc-400 space-y-0.5 border-t border-zinc-800 pt-2 mt-1">
                                        <p>{{ __('Ref:') }} <span class="font-mono text-zinc-300">{{ $escrow->transaction_reference }}</span></p>
                                        <p>{{ __('10% fee → Guide net:') }} <span class="font-semibold text-zinc-200">Rp {{ number_format((float) $escrow->gross_amount * 0.90, 0, ',', '.') }}</span></p>
                                    </div>
                                </div>
                            @else
                                <div class="sm:w-52 shrink-0 flex items-center justify-center bg-zinc-50 dark:bg-zinc-900 rounded-xl p-4 text-xs text-zinc-400 text-center">
                                    {{ __('No escrow transaction linked.') }}
                                </div>
                            @endif
                        </div>

                        <!-- Admin Override Controls -->
                        @if ($confirmingDisputeId === $booking->id)
                            <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center p-4 rounded-xl bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800">
                                <svg class="size-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400 flex-1">
                                    <strong>{{ __('Irreversible action.') }}</strong>
                                    {{ __('Select the resolution for Booking #:id:', ['id' => str_pad((string) $booking->id, 8, '0', STR_PAD_LEFT)]) }}
                                </p>
                                <div class="flex flex-wrap items-center gap-2">
                                    <button
                                        wire:click="releaseToGuide({{ $booking->id }})"
                                        wire:loading.attr="disabled"
                                        type="button"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-colors"
                                    >
                                        <svg class="size-3.5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                        {{ __('Release to Guide (90%)') }}
                                    </button>
                                    <button
                                        wire:click="refundCustomer({{ $booking->id }})"
                                        wire:loading.attr="disabled"
                                        type="button"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors"
                                    >
                                        <svg class="size-3.5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/></svg>
                                        {{ __('Full Refund to Customer') }}
                                    </button>
                                    <button
                                        wire:click="$set('confirmingDisputeId', null)"
                                        type="button"
                                        class="text-xs text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 px-1"
                                    >
                                        {{ __('Cancel') }}
                                    </button>
                                </div>
                            </div>
                        @else
                            <button
                                wire:click="$set('confirmingDisputeId', {{ $booking->id }})"
                                type="button"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg border border-red-300 hover:bg-red-50 text-red-700 dark:border-red-800 dark:hover:bg-red-950/20 dark:text-red-400 transition-colors"
                            >
                                <svg class="size-3.5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                {{ __('Resolve Dispute') }}
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
