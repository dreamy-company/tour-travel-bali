<div class="space-y-6">
    <!-- Session Flash Messages -->
    @if (session()->has('success'))
        <div class="p-4 text-sm text-green-800 rounded-xl bg-green-50 dark:bg-green-950/20 dark:text-green-300 border border-green-200 dark:border-green-900/40" role="alert">
            <div class="flex items-center gap-2.5">
                <svg class="size-5 shrink-0 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Page Header -->
    <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4">
        <h2 class="text-xl font-bold text-zinc-950 dark:text-white">{{ __('Payout Management') }}</h2>
        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Withdraw your tour earnings to your Indonesian bank account.') }}</p>
    </div>

    <!-- Balance Card + Withdrawal Form (2-col on lg) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- LEFT: Balance Overview & History -->
        <div class="lg:col-span-5 flex flex-col gap-6">

            <!-- Available Balance Card -->
            <div class="relative overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-stone-950 p-6 shadow-xs">
                <!-- Decorative gradient blob -->
                <div class="absolute -top-8 -right-8 size-40 rounded-full bg-emerald-500/10 blur-2xl pointer-events-none"></div>

                <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">
                    {{ __('Available Balance') }}
                </span>

                <p class="text-4xl font-extrabold text-zinc-900 dark:text-white mt-2">
                    @if ($wallet)
                        Rp {{ number_format((float) $wallet->current_balance, 0, ',', '.') }}
                    @else
                        Rp 0
                    @endif
                </p>

                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-2">
                    {{ __('Earnings are released to your wallet after each tour is completed and confirmed by the customer.') }}
                </p>

                <div class="flex items-center gap-1.5 mt-4 text-[10px] text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/40 rounded-lg px-3 py-2 w-fit">
                    <svg class="size-3.5 shrink-0 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>
                    <span class="font-semibold">{{ __('Escrow-secured earnings') }}</span>
                </div>
            </div>

            <!-- Withdrawal History -->
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 overflow-hidden shadow-xs">
                <div class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-800">
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">{{ __('Payout History') }}</h3>
                </div>

                @if ($withdrawals->isEmpty())
                    <div class="flex flex-col items-center justify-center py-10 text-center px-6">
                        <svg class="size-10 text-zinc-300 dark:text-zinc-700 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75"/></svg>
                        <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">{{ __('No payout requests yet.') }}</p>
                    </div>
                @else
                    <ul class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach ($withdrawals as $w)
                            @php
                                $statusColor = match ($w->status) {
                                    \App\Enums\WithdrawalStatus::PENDING => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-950/20 dark:text-amber-400 dark:ring-amber-500/20',
                                    \App\Enums\WithdrawalStatus::PROCESSING => 'bg-indigo-50 text-indigo-700 ring-indigo-700/10 dark:bg-indigo-950/20 dark:text-indigo-400 dark:ring-indigo-500/20',
                                    \App\Enums\WithdrawalStatus::SUCCESS => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-950/20 dark:text-emerald-400 dark:ring-emerald-500/20',
                                    \App\Enums\WithdrawalStatus::FAILED => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-950/20 dark:text-red-400 dark:ring-red-500/20',
                                };
                            @endphp
                            <li class="px-5 py-3.5 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-zinc-900 dark:text-zinc-100">
                                        Rp {{ number_format((float) $w->amount, 0, ',', '.') }}
                                    </p>
                                    <p class="text-[10px] text-zinc-500 truncate mt-0.5">
                                        {{ $w->bank_name }} · {{ $w->bank_account_number }}
                                    </p>
                                    <p class="text-[9px] text-zinc-400 mt-0.5">
                                        {{ $w->created_at?->format('d M Y, H:i') }}
                                    </p>
                                </div>
                                <span class="inline-flex shrink-0 items-center rounded-md px-2 py-0.5 text-[10px] font-semibold ring-1 ring-inset {{ $statusColor }}">
                                    {{ ucfirst($w->status->value) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- RIGHT: Payout Request Form -->
        <div class="lg:col-span-7">
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col gap-5">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">{{ __('Request a Payout') }}</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Funds will be transferred to your bank within 1–2 business days.') }}</p>
                </div>

                <hr class="border-zinc-100 dark:border-zinc-800" />

                <!-- Amount Field -->
                <div class="space-y-1.5">
                    <label for="amount" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">
                        {{ __('Withdrawal Amount') }}
                        <span class="text-red-500 ml-0.5">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-semibold text-zinc-500">Rp</span>
                        <input
                            wire:model="amount"
                            id="amount"
                            type="number"
                            min="1"
                            step="1000"
                            placeholder="0"
                            class="w-full text-sm pl-10 pr-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white @error('amount') border-red-400 dark:border-red-600 @enderror"
                        />
                    </div>
                    @error('amount')
                        <p class="text-xs text-red-500 flex items-center gap-1 mt-0.5">
                            <svg class="size-3.5 shrink-0 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                    @if ($wallet)
                        <p class="text-[10px] text-zinc-400">
                            {{ __('Max: Rp :balance', ['balance' => number_format((float) $wallet->current_balance, 0, ',', '.')]) }}
                        </p>
                    @endif
                </div>

                <!-- Bank Name Dropdown -->
                <div class="space-y-1.5">
                    <label for="bankName" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">
                        {{ __('Bank Name') }}
                        <span class="text-red-500 ml-0.5">*</span>
                    </label>
                    <select
                        wire:model="bankName"
                        id="bankName"
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white @error('bankName') border-red-400 dark:border-red-600 @enderror"
                    >
                        <option value="">{{ __('Select your bank...') }}</option>
                        @foreach ($this->banks() as $bank)
                            <option value="{{ $bank }}">{{ $bank }}</option>
                        @endforeach
                    </select>
                    @error('bankName')
                        <p class="text-xs text-red-500 flex items-center gap-1 mt-0.5">
                            <svg class="size-3.5 shrink-0 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Bank Account Number -->
                <div class="space-y-1.5">
                    <label for="bankAccountNumber" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">
                        {{ __('Bank Account Number') }}
                        <span class="text-red-500 ml-0.5">*</span>
                    </label>
                    <input
                        wire:model="bankAccountNumber"
                        id="bankAccountNumber"
                        type="text"
                        inputmode="numeric"
                        placeholder="e.g. 1234567890"
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white font-mono @error('bankAccountNumber') border-red-400 dark:border-red-600 @enderror"
                    />
                    @error('bankAccountNumber')
                        <p class="text-xs text-red-500 flex items-center gap-1 mt-0.5">
                            <svg class="size-3.5 shrink-0 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Bank Account Name -->
                <div class="space-y-1.5">
                    <label for="bankAccountName" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">
                        {{ __('Account Holder Name') }}
                        <span class="text-red-500 ml-0.5">*</span>
                    </label>
                    <input
                        wire:model="bankAccountName"
                        id="bankAccountName"
                        type="text"
                        placeholder="e.g. Wayan Sudarta"
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white @error('bankAccountName') border-red-400 dark:border-red-600 @enderror"
                    />
                    @error('bankAccountName')
                        <p class="text-xs text-red-500 flex items-center gap-1 mt-0.5">
                            <svg class="size-3.5 shrink-0 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="text-[10px] text-zinc-400">{{ __('Must exactly match your bank account name to avoid transfer failure.') }}</p>
                </div>

                <!-- Disclaimer & Submit -->
                <div class="pt-2 space-y-3 border-t border-zinc-100 dark:border-zinc-800">
                    <div class="flex gap-2.5 p-3 rounded-lg bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/40 text-[10px] text-amber-800 dark:text-amber-300 leading-relaxed">
                        <svg class="size-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                        <span>{{ __('Once submitted, the payout amount will be immediately deducted from your wallet balance. This action cannot be undone.') }}</span>
                    </div>

                    <button
                        wire:click="requestPayout"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-60 cursor-not-allowed"
                        type="button"
                        class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold rounded-xl bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs"
                    >
                        <span wire:loading.remove>
                            <svg class="size-4 inline mr-1 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75"/></svg>
                            {{ __('Submit Payout Request') }}
                        </span>
                        <span wire:loading>{{ __('Processing...') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
