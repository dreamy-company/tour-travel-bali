<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 min-h-[calc(100vh-10rem)]">
    <!-- LEFT COLUMN: Pending Guides List (Master Pane) -->
    <div class="lg:col-span-4 flex flex-col border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-stone-950 p-4 shadow-xs">
        <div class="mb-4">
            <flux:heading size="lg" class="flex items-center justify-between">
                <span>{{ __('Pending Verifications') }}</span>
                <flux:badge variant="neutral" size="sm">
                    {{ count($this->pendingProfiles) }}
                </flux:badge>
            </flux:heading>
            <flux:text>{{ __('Audit compliance uploads for applicant tour guides') }}</flux:text>
        </div>

        <flux:separator class="my-2" />

        <div class="flex-1 overflow-y-auto space-y-2 max-h-[30rem] lg:max-h-[40rem] mt-2 pr-1">
            @forelse ($this->pendingProfiles as $profile)
                <button 
                    wire:click="selectProfile({{ $profile->id }})"
                    class="w-full text-left p-3 rounded-lg border transition-all duration-200 flex gap-3 items-start {{ $selectedProfileId === $profile->id ? 'bg-zinc-50 border-zinc-950 dark:bg-zinc-900 dark:border-white' : 'bg-transparent border-zinc-100 hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-900/50' }}"
                >
                    <flux:avatar 
                        :name="$profile->user->name" 
                        :initials="$profile->user->initials()" 
                        size="sm" 
                    />
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
                    <flux:icon.shield-check class="size-12 text-emerald-500 mb-2" />
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
                        <flux:avatar 
                            :name="$profile->user->name" 
                            :initials="$profile->user->initials()" 
                            size="lg" 
                        />
                        <div>
                            <flux:heading size="xl">{{ $profile->user->name }}</flux:heading>
                            <p class="text-xs text-zinc-500">
                                Applied: {{ $profile->created_at?->format('F j, Y, g:i a') }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <flux:badge color="amber" variant="solid">{{ __('Pending Manual Audit') }}</flux:badge>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <span class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider">{{ __('Pricing & Mode') }}</span>
                        <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                            Rp {{ number_format($profile->base_rate, 0, ',', '.') }} / {{ ucfirst($profile->tariff_mode->value) }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider">{{ __('Languages Spoken') }}</span>
                        <div class="flex flex-wrap gap-1 mt-0.5">
                            @foreach ($profile->languages as $lang)
                                <flux:badge size="sm" variant="neutral">{{ strtoupper($lang) }}</flux:badge>
                            @endforeach
                        </div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider">{{ __('KTP ID Number') }}</span>
                        <p class="text-sm font-mono font-medium text-zinc-800 dark:text-zinc-200">
                            {{ $profile->ktp_number }}
                        </p>
                    </div>
                </div>

                <!-- Bio & Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-zinc-50 dark:bg-zinc-950 p-4 rounded-xl border dark:border-zinc-800">
                    <div>
                        <span class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider">{{ __('Biography') }}</span>
                        <p class="text-xs text-zinc-700 dark:text-zinc-300 mt-1 leading-relaxed whitespace-pre-line">
                            {{ $profile->bio ?: __('No biography provided.') }}
                        </p>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider">{{ __('Vehicle Details') }}</span>
                        <p class="text-xs text-zinc-700 dark:text-zinc-300 mt-1 leading-relaxed whitespace-pre-line">
                            {{ $profile->vehicle_details ?: __('No vehicle details registered.') }}
                        </p>
                    </div>
                </div>

                <!-- Document Audit Section -->
                <div>
                    <flux:heading size="md" class="mb-3">{{ __('Compliance Documents') }}</flux:heading>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- KTP Photo card -->
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 flex flex-col justify-between gap-3 bg-zinc-50/50 dark:bg-zinc-900/30">
                            <div>
                                <flux:heading size="sm" class="flex items-center gap-1.5">
                                    <flux:icon.identification class="size-4 text-zinc-500" />
                                    {{ __('National ID (KTP) Photo') }}
                                </flux:heading>
                                <flux:text class="text-[10px] mt-1">{{ __('Used to verify legal identity and citizenship.') }}</flux:text>
                            </div>
                            
                            @if (in_array(strtolower(pathinfo($profile->ktp_photo, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
                                <img src="{{ route('admin.documents.show', [$profile->id, 'ktp_photo']) }}" class="max-h-28 object-cover rounded-lg border dark:border-zinc-800">
                            @endif

                            <flux:button 
                                href="{{ route('admin.documents.show', [$profile->id, 'ktp_photo']) }}" 
                                target="_blank" 
                                size="sm" 
                                variant="ghost"
                                icon-trailing="arrow-top-right-on-square"
                            >
                                {{ __('Open Secure Document') }}
                            </flux:button>
                        </div>

                        <!-- KTPP License card -->
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 flex flex-col justify-between gap-3 bg-zinc-50/50 dark:bg-zinc-900/30">
                            <div>
                                <flux:heading size="sm" class="flex items-center gap-1.5">
                                    <flux:icon.award class="size-4 text-zinc-500" />
                                    {{ __('KTPP HPI License') }}
                                </flux:heading>
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

                            <flux:button 
                                href="{{ route('admin.documents.show', [$profile->id, 'ktpp_file']) }}" 
                                target="_blank" 
                                size="sm" 
                                variant="ghost"
                                icon-trailing="arrow-top-right-on-square"
                            >
                                {{ __('Open Secure Document') }}
                            </flux:button>
                        </div>

                        <!-- SKCK Police Clearance card -->
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 flex flex-col justify-between gap-3 bg-zinc-50/50 dark:bg-zinc-900/30">
                            <div>
                                <flux:heading size="sm" class="flex items-center gap-1.5">
                                    <flux:icon.shield class="size-4 text-zinc-500" />
                                    {{ __('Police Record Check (SKCK)') }}
                                </flux:heading>
                                <p class="text-[10px] text-zinc-500 mt-1">
                                    Expires: <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $profile->skck_expired_at->format('F j, Y') }}</span>
                                </p>
                            </div>

                            @if (in_array(strtolower(pathinfo($profile->skck_file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
                                <img src="{{ route('admin.documents.show', [$profile->id, 'skck_file']) }}" class="max-h-28 object-cover rounded-lg border dark:border-zinc-800">
                            @endif

                            <flux:button 
                                href="{{ route('admin.documents.show', [$profile->id, 'skck_file']) }}" 
                                target="_blank" 
                                size="sm" 
                                variant="ghost"
                                icon-trailing="arrow-top-right-on-square"
                            >
                                {{ __('Open Secure Document') }}
                            </flux:button>
                        </div>

                        <!-- Medical Certificate Card -->
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 flex flex-col justify-between gap-3 bg-zinc-50/50 dark:bg-zinc-900/30">
                            <div>
                                <flux:heading size="sm" class="flex items-center gap-1.5">
                                    <flux:icon.heart class="size-4 text-zinc-500" />
                                    {{ __('Medical Certificate') }}
                                </flux:heading>
                                <flux:text class="text-[10px] mt-1">{{ __('Signed health letter stating fitness for duty.') }}</flux:text>
                            </div>

                            @if (in_array(strtolower(pathinfo($profile->surat_sehat_file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
                                <img src="{{ route('admin.documents.show', [$profile->id, 'surat_sehat_file']) }}" class="max-h-28 object-cover rounded-lg border dark:border-zinc-800">
                            @endif

                            <flux:button 
                                href="{{ route('admin.documents.show', [$profile->id, 'surat_sehat_file']) }}" 
                                target="_blank" 
                                size="sm" 
                                variant="ghost"
                                icon-trailing="arrow-top-right-on-square"
                            >
                                {{ __('Open Secure Document') }}
                            </flux:button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Footer approval actions -->
            <div class="border-t border-zinc-100 dark:border-zinc-800 pt-6 mt-6 flex flex-col gap-4">
                <div class="flex gap-3 justify-end">
                    <!-- Reject Modal / Accordion trigger -->
                    <flux:modal.trigger name="reject-modal">
                        <flux:button variant="danger" icon="x-mark">
                            {{ __('Reject Verification') }}
                        </flux:button>
                    </flux:modal.trigger>

                    <flux:button wire:click="approve" variant="primary" icon="check" class="px-6">
                        {{ __('Approve Verification') }}
                    </flux:button>
                </div>

                <!-- Rejection Reason Modal -->
                <flux:modal name="reject-modal" class="md:w-130 space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('Reject Guide Verification') }}</flux:heading>
                        <flux:text>{{ __('Specify feedback explaining why this guides documents were rejected. They will be notified to upload corrections.') }}</flux:text>
                    </div>

                    <flux:textarea 
                        wire:model="rejectionReason" 
                        label="Rejection Feedback" 
                        placeholder="Provide details (e.g. SKCK has expired, photo is blurry...)" 
                        rows="4" 
                        required 
                    />

                    <div class="flex gap-2 justify-end">
                        <flux:modal.close>
                            <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button 
                            wire:click="reject" 
                            variant="danger" 
                            class="px-6"
                        >
                            {{ __('Confirm Rejection') }}
                        </flux:button>
                    </div>
                </flux:modal>
            </div>
        @else
            <!-- Placeholder state -->
            <div class="flex flex-col items-center justify-center text-center py-20 my-auto">
                <flux:icon.document-magnifying-glass class="size-16 text-zinc-300 dark:text-zinc-700 mb-4" />
                <flux:heading size="lg">{{ __('Guide Verification Audit') }}</flux:heading>
                <flux:text class="max-w-xs mt-1">
                    {{ __('Select a pending tour guide applicant from the sidebar list to inspect their private files and credentials.') }}
                </flux:text>
            </div>
        @endif
    </div>
</div>
