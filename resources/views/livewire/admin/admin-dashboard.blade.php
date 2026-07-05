<div wire:poll.10s="refreshDashboard" class="space-y-8">
    <!-- Session Messages -->
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

    <!-- System Statistics Widget -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col justify-between hover:scale-102 transition-transform duration-300">
            <div>
                <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest block">{{ __('Total Customers') }}</span>
                <h3 class="text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-2">{{ $totalCustomers }}</h3>
            </div>
            <p class="text-[10px] text-zinc-500 mt-2">{{ __('Active traveler accounts') }}</p>
        </div>

        <div class="border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col justify-between hover:scale-102 transition-transform duration-300">
            <div>
                <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest block">{{ __('Verified Guides') }}</span>
                <h3 class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-2">{{ $verifiedGuides }}</h3>
            </div>
            <p class="text-[10px] text-zinc-500 mt-2">{{ __('Fully vetted local guides') }}</p>
        </div>

        <div class="border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col justify-between hover:scale-102 transition-transform duration-300">
            <div>
                <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest block">{{ __('Active Escrows') }}</span>
                <h3 class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-2">{{ $activeEscrowBookings }}</h3>
            </div>
            <p class="text-[10px] text-zinc-500 mt-2">{{ __('Trips active or awaiting payment') }}</p>
        </div>

        <div class="border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col justify-between hover:scale-102 transition-transform duration-300 bg-linear-to-br from-white to-zinc-50/50 dark:from-stone-950 dark:to-stone-900/10">
            <div>
                <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest block">{{ __('Escrow Funds Locked') }}</span>
                <h3 class="text-2xl font-black text-zinc-950 dark:text-white mt-2">
                    Rp {{ number_format($totalEscrowFunds, 0, ',', '.') }}
                </h3>
            </div>
            <p class="text-[10px] text-zinc-500 mt-2">{{ __('Protected central platform funds') }}</p>
        </div>
    </div>

    <!-- Main Dynamic Grid split into blocks -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Column Left: KYC & Disputes (7 cols) -->
        <div class="lg:col-span-7 space-y-8">

            <!-- KYC Verification Queue Widget -->
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-stone-950 p-6 shadow-xs space-y-4">
                <div>
                    <h3 class="text-base font-bold text-zinc-950 dark:text-white">{{ __('KYC Verification Queue') }}</h3>
                    <p class="text-xs text-zinc-500 mt-1">{{ __('Approve credentials of tour guides waiting for compliant onboarding audits.') }}</p>
                </div>
                <hr class="border-zinc-100 dark:border-zinc-900" />
                
                <div class="divide-y divide-zinc-100 dark:divide-zinc-900 max-h-[350px] overflow-y-auto space-y-3 pt-1 pr-1">
                    @forelse ($pendingGuides as $pending)
                        <div class="flex items-center justify-between py-3 gap-4">
                            <div class="flex items-center gap-3">
                                <div class="size-9 rounded-full bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center font-bold text-xs text-zinc-800 dark:text-zinc-200">
                                    {{ $pending->user->initials() }}
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-zinc-950 dark:text-white">{{ $pending->user->name }}</h4>
                                    <p class="text-[10px] text-zinc-500">KTP: {{ $pending->ktp_number }}</p>
                                </div>
                            </div>
                            <button wire:click="selectGuideProfile({{ $pending->id }})" type="button" class="inline-flex items-center px-3 py-1.5 text-[10px] font-bold rounded-lg border border-zinc-200 hover:bg-zinc-50 text-zinc-700 dark:border-zinc-800 dark:hover:bg-zinc-900 dark:text-zinc-350 transition-colors">
                                {{ __('Vet Documents') }}
                            </button>
                        </div>
                    @empty
                        <p class="text-xs text-zinc-500 py-6 text-center">{{ __('No pending guide KYC audits.') }}</p>
                    @endforelse
                </div>
            </div>

            <!-- Dispute Center Tracker Widget -->
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-stone-950 p-6 shadow-xs space-y-4">
                <div>
                    <h3 class="text-base font-bold text-zinc-950 dark:text-white">{{ __('Dispute Center Tracker') }}</h3>
                    <p class="text-xs text-zinc-500 mt-1">{{ __('Moderate disputed tour packages and override escrow releases or refunds.') }}</p>
                </div>
                <hr class="border-zinc-100 dark:border-zinc-900" />

                <div class="space-y-4 max-h-[400px] overflow-y-auto pr-1">
                    @forelse ($disputedBookings as $dispute)
                        <div class="p-4 rounded-xl border border-zinc-100 bg-zinc-50/20 dark:border-zinc-900 dark:bg-zinc-900/10 space-y-4">
                            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                                <span class="text-xs font-bold text-zinc-950 dark:text-white">Booking ID: #{{ str_pad((string) $dispute->id, 8, '0', STR_PAD_LEFT) }}</span>
                                <span class="text-[10px] font-extrabold text-amber-600 uppercase tracking-wider">Disputed Status</span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-[10px] text-zinc-500 leading-normal">
                                <div>
                                    <p><strong>Customer:</strong> {{ $dispute->customer->name }}</p>
                                    <p><strong>Guide:</strong> {{ $dispute->guide->name }}</p>
                                </div>
                                <div class="text-right sm:text-left">
                                    <p><strong>Total Price:</strong> Rp {{ number_format($dispute->total_price, 0, ',', '.') }}</p>
                                    <p><strong>Platform Fee:</strong> Rp {{ number_format($dispute->escrowTransaction?->platform_commission ?? 0, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <div class="flex gap-2 justify-end pt-2 border-t border-zinc-100 dark:border-zinc-900">
                                <button wire:click="refundCustomer({{ $dispute->id }})" type="button" class="inline-flex items-center px-3 py-1.5 text-[10px] font-bold rounded-lg border border-red-200 hover:bg-red-50 text-red-650 transition-colors">
                                    {{ __('Refund Customer') }}
                                </button>
                                <button wire:click="releaseToGuide({{ $dispute->id }})" type="button" class="inline-flex items-center px-3 py-1.5 text-[10px] font-bold rounded-lg bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors">
                                    {{ __('Release to Guide') }}
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-zinc-500 py-6 text-center">{{ __('No active booking disputes flagged.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Column Right: Payouts & Sanctions (5 cols) -->
        <div class="lg:col-span-5 space-y-8">

            <!-- Financial Logs & Withdrawal Approvals Widget -->
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-stone-950 p-6 shadow-xs space-y-4">
                <div>
                    <h3 class="text-base font-bold text-zinc-950 dark:text-white">{{ __('Withdrawal Approvals') }}</h3>
                    <p class="text-xs text-zinc-500 mt-1">{{ __('Verify and execute bank payout requests initiated by tour guides.') }}</p>
                </div>
                <hr class="border-zinc-100 dark:border-zinc-900" />

                <div class="space-y-4 max-h-[350px] overflow-y-auto pr-1">
                    @forelse ($pendingWithdrawals as $payout)
                        <div class="p-4 rounded-xl border border-zinc-100 bg-zinc-50/20 dark:border-zinc-900 dark:bg-zinc-900/10 space-y-3">
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-bold text-zinc-950 dark:text-white">{{ $payout->guide->name }}</span>
                                <span class="font-black text-emerald-600 dark:text-emerald-400">Rp {{ number_format($payout->amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-[10px] text-zinc-500 space-y-0.5">
                                <p><strong>Bank Name:</strong> {{ $payout->bank_name }}</p>
                                <p><strong>Account ID:</strong> {{ $payout->bank_account_number }}</p>
                                <p><strong>Account Name:</strong> {{ $payout->bank_account_name }}</p>
                            </div>
                            <div class="flex gap-2 justify-end pt-2 border-t border-zinc-100 dark:border-zinc-900">
                                <button wire:click="rejectPayout({{ $payout->id }})" type="button" class="inline-flex items-center px-3 py-1.5 text-[10px] font-bold rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-900 text-zinc-650 dark:text-zinc-400 transition-colors">
                                    {{ __('Reject') }}
                                </button>
                                <button wire:click="approvePayout({{ $payout->id }})" type="button" class="inline-flex items-center px-4 py-1.5 text-[10px] font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-colors">
                                    {{ __('Approve Transfer') }}
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-zinc-500 py-6 text-center">{{ __('No pending payout requests.') }}</p>
                    @endforelse
                </div>
            </div>

            <!-- Guide Performance & Sanctions Widget -->
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-stone-950 p-6 shadow-xs space-y-4">
                <div>
                    <h3 class="text-base font-bold text-zinc-950 dark:text-white">{{ __('Guide Performance & Sanctions') }}</h3>
                    <p class="text-xs text-zinc-500 mt-1">{{ __('Monitor low traveler ratings (<= 3 Stars) and issue profile compliance strikes or bans.') }}</p>
                </div>
                <hr class="border-zinc-100 dark:border-zinc-900" />

                <div class="space-y-4 max-h-[400px] overflow-y-auto pr-1">
                    @forelse ($lowRatedReviews as $review)
                        <div class="p-4 rounded-xl border border-zinc-100 bg-zinc-50/20 dark:border-zinc-900 dark:bg-zinc-900/10 space-y-3">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-xs font-bold text-zinc-950 dark:text-white">{{ $review->guide->name }}</h4>
                                    <p class="text-[9px] text-zinc-500">Strikes: <span class="font-semibold text-red-500">{{ $review->guide->guideProfile?->strikes ?? 0 }}</span></p>
                                </div>
                                <span class="text-sm font-bold text-amber-500">{{ str_repeat('★', $review->rating) }}</span>
                            </div>
                            
                            <p class="text-[10px] text-zinc-600 dark:text-zinc-400 italic">
                                "{{ $review->comment }}"
                            </p>

                            @if ($review->guide->status !== \App\Enums\UserStatus::BANNED)
                                <div class="flex gap-2 justify-end pt-2 border-t border-zinc-100 dark:border-zinc-900">
                                    <button wire:click="banGuide({{ $review->guide_id }})" type="button" class="inline-flex items-center px-3 py-1.5 text-[10px] font-bold rounded-lg border border-red-200 hover:bg-red-50 text-red-650 transition-colors">
                                        {{ __('Ban Account') }}
                                    </button>
                                    <button wire:click="issueStrike({{ $review->guide_id }})" type="button" class="inline-flex items-center px-3 py-1.5 text-[10px] font-bold rounded-lg bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors">
                                        {{ __('Issue Strike') }}
                                    </button>
                                </div>
                            @else
                                <div class="text-right pt-2 border-t border-zinc-100 dark:border-zinc-900 text-[10px] text-red-600 font-bold uppercase tracking-wider">
                                    {{ __('Permanently Banned') }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-zinc-500 py-6 text-center">{{ __('No low-rating guide reports found.') }}</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    <!-- Detailed Guide Compliance Vetting Modal Overlay -->
    @if ($selectedProfile)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-stone-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-2xl w-full p-6 shadow-xl space-y-6 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-start border-b border-zinc-100 dark:border-zinc-850 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-zinc-950 dark:text-white">{{ __('Vetting Guide Credentials') }}</h3>
                        <p class="text-xs text-zinc-500 mt-1">{{ __('Auditing compliance details for') }} <strong>{{ $selectedProfile->user->name }}</strong></p>
                    </div>
                    <button wire:click="closeVetting" type="button" class="text-zinc-400 hover:text-zinc-500 focus:outline-hidden">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Document Auditing Files Lists -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs leading-relaxed">
                    <div class="space-y-4">
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-4 rounded-xl border dark:border-zinc-850">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest block">{{ __('KTP Identity Detail') }}</span>
                            <p class="mt-1.5"><strong>ID Number:</strong> {{ $selectedProfile->ktp_number }}</p>
                            @if ($selectedProfile->ktp_photo)
                                <div class="mt-3">
                                    <span class="text-[9px] font-bold text-zinc-400 uppercase block mb-1.5">{{ __('KTP Upload Photo') }}</span>
                                    <a href="{{ asset('storage/' . $selectedProfile->ktp_photo) }}" target="_blank" class="inline-flex items-center text-indigo-600 dark:text-indigo-400 font-bold hover:underline gap-1 text-[11px]">
                                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.43 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                        {{ __('View Full KTP Image') }}
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="bg-zinc-50 dark:bg-zinc-950 p-4 rounded-xl border dark:border-zinc-850">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest block">{{ __('Police Certificate (SKCK)') }}</span>
                            <p class="mt-1.5"><strong>Expiry Date:</strong> {{ $selectedProfile->skck_expired_at->format('F d, Y') }}</p>
                            @if ($selectedProfile->skck_file)
                                <a href="{{ asset('storage/' . $selectedProfile->skck_file) }}" target="_blank" class="inline-flex items-center text-indigo-600 dark:text-indigo-400 font-bold hover:underline gap-1 text-[11px] mt-2.5">
                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                    {{ __('Download Official SKCK Doc') }}
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-4 rounded-xl border dark:border-zinc-850">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest block">{{ __('Bali Official Guide License (KTPP)') }}</span>
                            <p class="mt-1.5"><strong>License Number:</strong> {{ $selectedProfile->ktpp_number }}</p>
                            <p><strong>Expiry Date:</strong> {{ $selectedProfile->ktpp_expired_at->format('F d, Y') }}</p>
                            @if ($selectedProfile->ktpp_file)
                                <a href="{{ asset('storage/' . $selectedProfile->ktpp_file) }}" target="_blank" class="inline-flex items-center text-indigo-600 dark:text-indigo-400 font-bold hover:underline gap-1 text-[11px] mt-2.5">
                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                    {{ __('Download Official KTPP Doc') }}
                                </a>
                            @endif
                        </div>

                        <div class="bg-zinc-50 dark:bg-zinc-950 p-4 rounded-xl border dark:border-zinc-850">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest block">{{ __('Medical Fitness Statement') }}</span>
                            @if ($selectedProfile->surat_sehat_file)
                                <a href="{{ asset('storage/' . $selectedProfile->surat_sehat_file) }}" target="_blank" class="inline-flex items-center text-indigo-600 dark:text-indigo-400 font-bold hover:underline gap-1 text-[11px] mt-1.5">
                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                    {{ __('Download Medical Doc') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Rejection Feedback text details -->
                <div class="space-y-2 border-t border-zinc-100 dark:border-zinc-850 pt-4">
                    <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('Compliance Decline Reason (Required only if rejecting Guide)') }}</label>
                    <textarea wire:model="rejectionReason" placeholder="Clearly outline the reason for compliance rejection (e.g. Blurry photo KTP, expired police SKCK certificate...)" rows="3" class="w-full text-xs px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"></textarea>
                    @error('rejectionReason') <span class="text-[10px] text-red-500 block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-2 justify-end border-t border-zinc-100 dark:border-zinc-850 pt-4">
                    <button wire:click="closeVetting" type="button" class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-650 dark:text-zinc-400 transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button wire:click="rejectGuide({{ $selectedProfile->id }})" type="button" class="inline-flex items-center px-4 py-2 text-xs font-bold rounded-lg border border-red-200 hover:bg-red-50 text-red-650 transition-colors">
                        {{ __('Reject Documents') }}
                    </button>
                    <button wire:click="approveGuide({{ $selectedProfile->id }})" type="button" class="inline-flex items-center px-5 py-2 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-colors">
                        {{ __('Approve & Verify Guide') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
