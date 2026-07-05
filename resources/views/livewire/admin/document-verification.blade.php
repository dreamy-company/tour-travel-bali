<div x-data="{ showRejectModal: false }" class="space-y-4 w-full">
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

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 min-h-[calc(100vh-10rem)]">
        <!-- LEFT COLUMN: Pending Guides List (Master Pane) -->
        <div class="lg:col-span-4 flex flex-col border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-4 shadow-xs">
        <div class="mb-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ __('Pending Verifications') }}</h3>
                <span class="inline-flex items-center rounded-md bg-zinc-50 px-2 py-0.5 text-xs font-semibold text-zinc-650 ring-1 ring-inset ring-zinc-500/10 dark:bg-zinc-900 dark:text-zinc-400 dark:ring-zinc-400/20">
                    {{ count($this->pendingProfiles) }}
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Audit compliance uploads for applicant tour guides') }}</p>
        </div>

        <hr class="border-zinc-200 dark:border-zinc-800 my-2" />

        <div class="flex-1 overflow-y-auto space-y-2 max-h-[30rem] lg:max-h-[40rem] mt-2 pr-1">
            @forelse ($this->pendingProfiles as $profile)
                <button 
                    wire:click="selectProfile({{ $profile->id }})"
                    class="w-full text-left p-3 rounded-lg border transition-all duration-200 flex gap-3 items-start {{ $selectedProfileId === $profile->id ? 'bg-zinc-50 border-zinc-950 dark:bg-zinc-900 dark:border-white' : 'bg-transparent border-zinc-100 hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-900/50' }}"
                >
                    <div class="size-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-semibold text-xs text-zinc-850 dark:text-zinc-250">
                        {{ $profile->user->name ? $profile->user->initials() : '' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 truncate">
                                {{ $profile->user->name }}
                            </h4>
                            <span class="text-[9px] text-zinc-500 whitespace-nowrap">
                                {{ $profile->created_at?->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-[10px] text-zinc-500 truncate mt-0.5">{{ $profile->user->email }}</p>
                        <p class="text-[10px] text-zinc-500 mt-0.5">{{ $profile->user->phone_number }}</p>
                    </div>
                </button>
            @empty
                <div class="text-center py-10 flex flex-col items-center justify-center">
                    <svg class="size-12 text-emerald-500 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z"/></svg>
                    <p class="text-xs font-medium text-zinc-600 dark:text-zinc-400">All caught up!</p>
                    <p class="text-[10px] text-zinc-500 mt-1">No pending guide profiles to verify.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- RIGHT COLUMN: Guide Profile & Documents Details (Detail Pane) -->
    <div class="lg:col-span-8 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-6 shadow-xs flex flex-col justify-between">
        @if ($this->selectedProfile)
            @php
                $profile = $this->selectedProfile;
            @endphp
            <div class="space-y-6">
                <!-- Header details -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-100 dark:border-zinc-800 pb-4">
                    <div class="flex items-center gap-3.5">
                        <div class="size-12 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-bold text-zinc-850 dark:text-zinc-250">
                            {{ $profile->user->name ? $profile->user->initials() : '' }}
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ $profile->user->name }}</h2>
                            <p class="text-xs text-zinc-500">
                                Applied: {{ $profile->created_at?->format('F j, Y, g:i a') }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <span class="inline-flex items-center rounded-md bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/10 dark:bg-amber-950/20 dark:text-amber-400 dark:ring-amber-500/20">
                            {{ __('Pending Manual Audit') }}
                        </span>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <span class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider block">{{ __('Pricing & Mode') }}</span>
                        <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                            Rp {{ number_format($profile->base_rate, 0, ',', '.') }} / {{ ucfirst($profile->tariff_mode->value) }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider block">{{ __('Languages Spoken') }}</span>
                        <div class="flex flex-wrap gap-1 mt-0.5">
                            @foreach ($profile->languages as $lang)
                                <span class="inline-flex items-center rounded-md bg-zinc-50 px-2 py-0.5 text-xs font-semibold text-zinc-650 ring-1 ring-inset ring-zinc-500/10 dark:bg-zinc-900 dark:text-zinc-400 dark:ring-zinc-400/20">
                                    {{ strtoupper($lang) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider block">{{ __('KTP ID Number') }}</span>
                        <p class="text-sm font-mono font-medium text-zinc-800 dark:text-zinc-200">
                            {{ $profile->ktp_number }}
                        </p>
                    </div>
                </div>

                <!-- Bio & Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-zinc-50 dark:bg-zinc-950 p-4 rounded-xl border dark:border-zinc-800">
                    <div>
                        <span class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider block">{{ __('Biography') }}</span>
                        <p class="text-xs text-zinc-700 dark:text-zinc-300 mt-1 leading-relaxed whitespace-pre-line">
                            {{ $profile->bio ?: __('No biography provided.') }}
                        </p>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider block">{{ __('Vehicle Details') }}</span>
                        <p class="text-xs text-zinc-700 dark:text-zinc-300 mt-1 leading-relaxed whitespace-pre-line">
                            {{ $profile->vehicle_details ?: __('No vehicle details registered.') }}
                        </p>
                    </div>
                </div>

                <!-- Document Audit Section -->
                <div>
                    <h3 class="text-md font-bold text-zinc-900 dark:text-zinc-100 mb-3">{{ __('Compliance Documents') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- KTP Photo card -->
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 flex flex-col justify-between gap-3 bg-zinc-50/50 dark:bg-zinc-900/30">
                            <div>
                                <h4 class="text-sm font-semibold text-zinc-800 dark:text-zinc-200 flex items-center gap-1.5">
                                    <svg class="size-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z"/></svg>
                                    {{ __('National ID (KTP) Photo') }}
                                </h4>
                                <p class="text-[10px] text-zinc-500 mt-1">{{ __('Used to verify legal identity and citizenship.') }}</p>
                            </div>
                            
                            @if (in_array(strtolower(pathinfo($profile->ktp_photo, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
                                <img src="{{ route('admin.documents.show', [$profile->id, 'ktp_photo']) }}" class="max-h-28 object-cover rounded-lg border dark:border-zinc-800">
                            @endif

                            <a 
                                href="{{ route('admin.documents.show', [$profile->id, 'ktp_photo']) }}" 
                                target="_blank" 
                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg border border-zinc-200 hover:bg-zinc-50 text-zinc-700 dark:border-zinc-800 dark:hover:bg-zinc-900 dark:text-zinc-350 transition-colors"
                            >
                                <span>{{ __('Open Secure Document') }}</span>
                                <svg class="size-3 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                            </a>
                        </div>

                        <!-- KTPP License card -->
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 flex flex-col justify-between gap-3 bg-zinc-50/50 dark:bg-zinc-900/30">
                            <div>
                                <h4 class="text-sm font-semibold text-zinc-800 dark:text-zinc-200 flex items-center gap-1.5">
                                    <svg class="size-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.75a1.125 1.125 0 0 1-1.125-1.125V11.25M9 3.75h6m-6 0a1.5 1.5 0 0 1 3-3h3a1.5 1.5 0 0 1 3 3m-9 0V9m9-5.25V9"/></svg>
                                    {{ __('KTPP HPI License') }}
                                </h4>
                                <p class="text-[10px] text-zinc-500 mt-1">
                                    License: <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $profile->ktpp_number }}</span>
                                </p>
                                <p class="text-[10px] text-zinc-500">
                                    Expires: <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $profile->ktpp_expired_at->format('F j, Y') }}</span>
                                </p>
                            </div>

                            @if (in_array(strtolower(pathinfo($profile->ktpp_file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
                                <img src="{{ route('admin.documents.show', [$profile->id, 'ktpp_file']) }}" class="max-h-28 object-cover rounded-lg border dark:border-zinc-800">
                            @endif

                            <a 
                                href="{{ route('admin.documents.show', [$profile->id, 'ktpp_file']) }}" 
                                target="_blank" 
                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg border border-zinc-200 hover:bg-zinc-50 text-zinc-700 dark:border-zinc-800 dark:hover:bg-zinc-900 dark:text-zinc-350 transition-colors"
                            >
                                <span>{{ __('Open Secure Document') }}</span>
                                <svg class="size-3 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                            </a>
                        </div>

                        <!-- SKCK Police Clearance card -->
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 flex flex-col justify-between gap-3 bg-zinc-50/50 dark:bg-zinc-900/30">
                            <div>
                                <h4 class="text-sm font-semibold text-zinc-800 dark:text-zinc-200 flex items-center gap-1.5">
                                    <svg class="size-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z"/></svg>
                                    {{ __('Police Record Check (SKCK)') }}
                                </h4>
                                <p class="text-[10px] text-zinc-500 mt-1">
                                    Expires: <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $profile->skck_expired_at->format('F j, Y') }}</span>
                                </p>
                            </div>

                            @if (in_array(strtolower(pathinfo($profile->skck_file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
                                <img src="{{ route('admin.documents.show', [$profile->id, 'skck_file']) }}" class="max-h-28 object-cover rounded-lg border dark:border-zinc-800">
                            @endif

                            <a 
                                href="{{ route('admin.documents.show', [$profile->id, 'skck_file']) }}" 
                                target="_blank" 
                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg border border-zinc-200 hover:bg-zinc-50 text-zinc-700 dark:border-zinc-800 dark:hover:bg-zinc-900 dark:text-zinc-350 transition-colors"
                            >
                                <span>{{ __('Open Secure Document') }}</span>
                                <svg class="size-3 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                            </a>
                        </div>

                        <!-- Medical Certificate Card -->
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 flex flex-col justify-between gap-3 bg-zinc-50/50 dark:bg-zinc-900/30">
                            <div>
                                <h4 class="text-sm font-semibold text-zinc-800 dark:text-zinc-200 flex items-center gap-1.5">
                                    <svg class="size-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg>
                                    {{ __('Medical Certificate') }}
                                </h4>
                                <p class="text-[10px] text-zinc-500 mt-1">{{ __('Signed health letter stating fitness for duty.') }}</p>
                            </div>

                            @if (in_array(strtolower(pathinfo($profile->surat_sehat_file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
                                <img src="{{ route('admin.documents.show', [$profile->id, 'surat_sehat_file']) }}" class="max-h-28 object-cover rounded-lg border dark:border-zinc-800">
                            @endif

                            <a 
                                href="{{ route('admin.documents.show', [$profile->id, 'surat_sehat_file']) }}" 
                                target="_blank" 
                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg border border-zinc-200 hover:bg-zinc-50 text-zinc-700 dark:border-zinc-800 dark:hover:bg-zinc-900 dark:text-zinc-355 transition-colors"
                            >
                                <span>{{ __('Open Secure Document') }}</span>
                                <svg class="size-3 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                            </a>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Footer approval actions -->
            <div class="border-t border-zinc-100 dark:border-zinc-800 pt-6 mt-6 flex flex-col gap-4">
                <div class="flex gap-3 justify-end">
                    <button 
                        @click="showRejectModal = true"
                        type="button" 
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg bg-red-600 text-white hover:bg-red-750 transition-colors shadow-xs"
                    >
                        <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        {{ __('Reject Verification') }}
                    </button>

                    <button 
                        wire:click="approve" 
                        type="button" 
                        class="inline-flex items-center gap-1.5 px-6 py-2 text-sm font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs"
                    >
                        <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        {{ __('Approve Verification') }}
                    </button>
                </div>

                <!-- Rejection Reason Modal Backdrop & Card -->
                <div 
                    x-show="showRejectModal" 
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/55 backdrop-blur-xs"
                    x-transition
                    x-cloak
                >
                    <div 
                        @click.away="showRejectModal = false" 
                        class="bg-white dark:bg-stone-900 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-md w-full p-6 shadow-xl space-y-5"
                    >
                        <div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">{{ __('Reject Guide Verification') }}</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Specify feedback explaining why this guides documents were rejected. They will be notified to upload corrections.') }}</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Rejection Feedback') }}</label>
                            <textarea 
                                wire:model="rejectionReason" 
                                placeholder="Provide details (e.g. SKCK has expired, photo is blurry...)" 
                                rows="4" 
                                required 
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                            ></textarea>
                            @error('rejectionReason') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex gap-2 justify-end pt-2">
                            <button 
                                type="button" 
                                @click="showRejectModal = false" 
                                class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-650 dark:text-zinc-400 transition-colors"
                            >
                                {{ __('Cancel') }}
                            </button>
                            <button 
                                wire:click="reject" 
                                @click="if ($wire.rejectionReason && $wire.rejectionReason.length >= 5) { showRejectModal = false; }"
                                type="button" 
                                class="inline-flex items-center px-5 py-2 text-xs font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors"
                            >
                                {{ __('Confirm Rejection') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Placeholder state -->
            <div class="flex flex-col items-center justify-center text-center py-20 my-auto">
                <svg class="size-16 text-zinc-300 dark:text-zinc-700 mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.179 18.549a8.25 8.25 0 1 1-13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7m3.188 13.585a1.608 1.608 0 1 1-2.275-2.275 1.608 1.608 0 0 1 2.275 2.275Z"/></svg>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ __('Guide Verification Audit') }}</h3>
                <p class="text-xs text-zinc-500 max-w-xs mt-1">
                    {{ __('Select a pending tour guide applicant from the sidebar list to inspect their private files and credentials.') }}
                </p>
            </div>
        @endif
    </div>
</div>
</div>
