<div class="flex flex-col gap-6">
    <x-auth-header 
        :title="__('Register as a Tour Guide')" 
        :description="__('Complete the 4-step verification to list your services in Bali.')" 
    />

    <!-- Step Progress Indicator -->
    <div class="relative flex items-center justify-between my-4">
        <!-- Background Track -->
        <div class="absolute left-0 top-1/2 h-0.5 w-full -translate-y-1/2 bg-zinc-200 dark:bg-zinc-800"></div>
        <!-- Active Progress Line -->
        <div 
            class="absolute left-0 top-1/2 h-0.5 -translate-y-1/2 bg-emerald-500 transition-all duration-300"
            style="width: {{ (($currentStep - 1) / 3) * 100 }}%"
        ></div>

        <!-- Step 1 -->
        <div class="relative z-10 flex flex-col items-center gap-1.5">
            <div class="flex h-8 w-8 items-center justify-center rounded-full border text-xs font-semibold transition-all duration-300 {{ $currentStep >= 1 ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white text-zinc-500 border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800' }}">
                1
            </div>
            <span class="text-[10px] md:text-xs font-medium text-zinc-600 dark:text-zinc-400">{{ __('Account') }}</span>
        </div>

        <!-- Step 2 -->
        <div class="relative z-10 flex flex-col items-center gap-1.5">
            <div class="flex h-8 w-8 items-center justify-center rounded-full border text-xs font-semibold transition-all duration-300 {{ $currentStep >= 2 ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white text-zinc-500 border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800' }}">
                2
            </div>
            <span class="text-[10px] md:text-xs font-medium text-zinc-600 dark:text-zinc-400">{{ __('Identity') }}</span>
        </div>

        <!-- Step 3 -->
        <div class="relative z-10 flex flex-col items-center gap-1.5">
            <div class="flex h-8 w-8 items-center justify-center rounded-full border text-xs font-semibold transition-all duration-300 {{ $currentStep >= 3 ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white text-zinc-500 border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800' }}">
                3
            </div>
            <span class="text-[10px] md:text-xs font-medium text-zinc-600 dark:text-zinc-400">{{ __('Legality') }}</span>
        </div>

        <!-- Step 4 -->
        <div class="relative z-10 flex flex-col items-center gap-1.5">
            <div class="flex h-8 w-8 items-center justify-center rounded-full border text-xs font-semibold transition-all duration-300 {{ $currentStep >= 4 ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white text-zinc-500 border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800' }}">
                4
            </div>
            <span class="text-[10px] md:text-xs font-medium text-zinc-600 dark:text-zinc-400">{{ __('SOP') }}</span>
        </div>
    </div>

    <!-- Wizard Form -->
    <div class="mt-4">
        <!-- STEP 1: Account Creation -->
        @if ($currentStep === 1)
            <div class="flex flex-col gap-6">
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50">{{ __('Step 1: Account Creation') }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('Full Name') }}</label>
                        <input 
                            wire:model.blur="name" 
                            type="text" 
                            placeholder="e.g. Wayan Sudarta" 
                            required 
                            class="w-full text-xs px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('name') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('Email Address') }}</label>
                        <input 
                            wire:model.blur="email" 
                            type="email" 
                            placeholder="wayan@gmail.com" 
                            required 
                            class="w-full text-xs px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('email') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('Password') }}</label>
                        <input 
                            wire:model.blur="password" 
                            type="password" 
                            placeholder="{{ __('Create Password') }}" 
                            required 
                            class="w-full text-xs px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('password') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('Confirm Password') }}</label>
                        <input 
                            wire:model.blur="password_confirmation" 
                            type="password" 
                            placeholder="{{ __('Repeat Password') }}" 
                            required 
                            class="w-full text-xs px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('password_confirmation') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('WhatsApp / Phone Number') }}</label>
                    <input 
                        wire:model.blur="phone_number" 
                        type="text" 
                        placeholder="e.g. 081234567891" 
                        required 
                        class="w-full text-xs px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                    />
                    @error('phone_number') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end mt-4">
                    <button 
                        wire:click="nextStep" 
                        type="button"
                        class="inline-flex items-center gap-1.5 px-6 py-2.5 text-xs font-bold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs"
                    >
                        {{ __('Next: Identity (Tier 1)') }}
                    </button>
                </div>
            </div>
        @endif

        <!-- STEP 2: Identity (Tier 1 KYC) -->
        @if ($currentStep === 2)
            <div class="flex flex-col gap-6">
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50">{{ __('Step 2: Identity (Tier 1 KYC)') }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('National ID (KTP/NIK) Number') }}</label>
                        <input 
                            wire:model.blur="ktp_number" 
                            type="text" 
                            placeholder="16-digit national ID number" 
                            maxlength="16" 
                            required 
                            class="w-full text-xs px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('ktp_number') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Languages spoken -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('Languages Spoken') }}</label>
                        <div class="grid grid-cols-2 gap-2 mt-2">
                            <label class="inline-flex items-center gap-2 text-xs text-zinc-650 dark:text-zinc-400 cursor-pointer">
                                <input type="checkbox" wire:model="languages" value="id" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800" />
                                <span>Indonesian</span>
                            </label>
                            <label class="inline-flex items-center gap-2 text-xs text-zinc-650 dark:text-zinc-400 cursor-pointer">
                                <input type="checkbox" wire:model="languages" value="en" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800" />
                                <span>English</span>
                            </label>
                            <label class="inline-flex items-center gap-2 text-xs text-zinc-650 dark:text-zinc-400 cursor-pointer">
                                <input type="checkbox" wire:model="languages" value="jp" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800" />
                                <span>Japanese</span>
                            </label>
                            <label class="inline-flex items-center gap-2 text-xs text-zinc-650 dark:text-zinc-400 cursor-pointer">
                                <input type="checkbox" wire:model="languages" value="de" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800" />
                                <span>German</span>
                            </label>
                        </div>
                        @error('languages') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Communication Style & Specializations (SRS Matching Parameters) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('Communication Style') }}</label>
                        <select
                            wire:model.blur="communication_style"
                            required
                            class="w-full text-xs px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        >
                            <option value="">{{ __('Select your communication style') }}</option>
                            @foreach (\App\Enums\CommunicationStyle::cases() as $style)
                                <option value="{{ $style->value }}">{{ $style->label() }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-zinc-500">{{ __('This is used by travelers to match with guides of the same vibe.') }}</p>
                        @error('communication_style') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('Activity Specializations') }}</label>
                        <div class="grid grid-cols-1 gap-2 mt-2">
                            @foreach (\App\Enums\Specialization::cases() as $spec)
                                <label class="inline-flex items-center gap-2 text-xs text-zinc-650 dark:text-zinc-400 cursor-pointer">
                                    <input type="checkbox" wire:model="specializations" value="{{ $spec->value }}" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800" />
                                    <span>{{ $spec->label() }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('specializations') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- KTP and Headshot Uploads -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('National ID (KTP) Photo') }}</label>
                        <div class="mt-1 flex items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-800 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-950">
                            <input type="file" wire:model="ktp_photo" id="ktp_photo" class="hidden" accept="image/*">
                            <label for="ktp_photo" class="cursor-pointer flex flex-col items-center justify-center text-center">
                                <svg class="size-8 text-zinc-400 dark:text-zinc-600 mb-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/></svg>
                                <span class="text-xs font-semibold text-zinc-650 dark:text-zinc-400">Click to Upload KTP Photo</span>
                                <span class="text-[10px] text-zinc-500">Max 2MB</span>
                            </label>
                        </div>
                        @error('ktp_photo') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        @if ($ktp_photo)
                            <div class="mt-2 text-xs text-zinc-500 flex items-center gap-2 bg-zinc-50 p-2 rounded-lg border">
                                <img src="{{ $ktp_photo->temporaryUrl() }}" class="h-10 w-10 rounded object-cover">
                                <p class="truncate">{{ $ktp_photo->getClientOriginalName() }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('Recent Headshot Photo') }}</label>
                        <div class="mt-1 flex items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-800 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-950">
                            <input type="file" wire:model="headshot" id="headshot" class="hidden" accept="image/*">
                            <label for="headshot" class="cursor-pointer flex flex-col items-center justify-center text-center">
                                <svg class="size-8 text-zinc-400 dark:text-zinc-600 mb-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                                <span class="text-xs font-semibold text-zinc-650 dark:text-zinc-400">Click to Upload Headshot</span>
                                <span class="text-[10px] text-zinc-500">Max 2MB</span>
                            </label>
                        </div>
                        @error('headshot') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        @if ($headshot)
                            <div class="mt-2 text-xs text-zinc-500 flex items-center gap-2 bg-zinc-50 p-2 rounded-lg border">
                                <img src="{{ $headshot->temporaryUrl() }}" class="h-10 w-10 rounded object-cover">
                                <p class="truncate">{{ $headshot->getClientOriginalName() }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('Profile Bio') }}</label>
                    <textarea 
                        wire:model.blur="bio" 
                        placeholder="Tell visitors about your tour guiding experience and passion for Bali..." 
                        rows="3" 
                        class="w-full text-xs px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                    ></textarea>
                    @error('bio') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-between mt-4">
                    <button wire:click="prevStep" type="button" class="inline-flex items-center px-4 py-2.5 text-xs font-semibold rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-650 dark:text-zinc-400 transition-colors">
                        {{ __('Back') }}
                    </button>
                    <button wire:click="nextStep" type="button" class="inline-flex items-center gap-1.5 px-6 py-2.5 text-xs font-bold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs">
                        {{ __('Next: Legality (Tier 2)') }}
                    </button>
                </div>
            </div>
        @endif

        <!-- STEP 3: Legality (Tier 2 KYC) -->
        @if ($currentStep === 3)
            <div class="flex flex-col gap-6">
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50">{{ __('Step 3: Legality (Tier 2 KYC)') }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('KTPP (Official HPI License) Number') }}</label>
                        <input 
                            wire:model.blur="ktpp_number" 
                            type="text" 
                            placeholder="e.g. HPI-12345" 
                            required 
                            class="w-full text-xs px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('ktpp_number') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('KTPP Expiry Date') }}</label>
                        <input 
                            wire:model.blur="ktpp_expired_at" 
                            type="date" 
                            required 
                            class="w-full text-xs px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('ktpp_expired_at') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- KTPP file upload -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('KTPP License Doc Scan') }}</label>
                    <div class="mt-1 flex items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-800 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-950">
                        <input type="file" wire:model="ktpp_file" id="ktpp_file" class="hidden" accept="image/*,application/pdf">
                        <label for="ktpp_file" class="cursor-pointer flex flex-col items-center justify-center text-center">
                            <svg class="size-8 text-zinc-400 dark:text-zinc-600 mb-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                            <span class="text-xs font-semibold text-zinc-650 dark:text-zinc-400">Click to Upload KTPP Scan</span>
                            <span class="text-[10px] text-zinc-500">Max 5MB (PDF or Image)</span>
                        </label>
                    </div>
                    @error('ktpp_file') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    @if ($ktpp_file)
                        <p class="text-xs text-zinc-500 mt-2 truncate">{{ $ktpp_file->getClientOriginalName() }}</p>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- SKCK file upload -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('SKCK Police Clearance Doc Scan') }}</label>
                        <div class="mt-1 flex items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-800 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-950">
                            <input type="file" wire:model="skck_file" id="skck_file" class="hidden" accept="image/*,application/pdf">
                            <label for="skck_file" class="cursor-pointer flex flex-col items-center justify-center text-center">
                                <svg class="size-8 text-zinc-400 dark:text-zinc-600 mb-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                <span class="text-xs font-semibold text-zinc-650 dark:text-zinc-400">Click to Upload SKCK Scan</span>
                                <span class="text-[10px] text-zinc-500">Max 5MB</span>
                            </label>
                        </div>
                        @error('skck_file') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        @if ($skck_file)
                            <p class="text-xs text-zinc-500 mt-2 truncate">{{ $skck_file->getClientOriginalName() }}</p>
                        @endif
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('SKCK Certificate Expiry Date') }}</label>
                        <input 
                            wire:model.blur="skck_expired_at" 
                            type="date" 
                            required 
                            class="w-full text-xs px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('skck_expired_at') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Medical Certificate -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 block">{{ __('Medical Fitness Certificate (Surat Sehat) Scan') }}</label>
                    <div class="mt-1 flex items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-800 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-950">
                        <input type="file" wire:model="surat_sehat_file" id="surat_sehat_file" class="hidden" accept="image/*,application/pdf">
                        <label for="surat_sehat_file" class="cursor-pointer flex flex-col items-center justify-center text-center">
                            <svg class="size-8 text-zinc-400 dark:text-zinc-600 mb-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                            <span class="text-xs font-semibold text-zinc-650 dark:text-zinc-400">Click to Upload Medical Doc</span>
                            <span class="text-[10px] text-zinc-500">Max 5MB</span>
                        </label>
                    </div>
                    @error('surat_sehat_file') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    @if ($surat_sehat_file)
                        <p class="text-xs text-zinc-500 mt-2 truncate">{{ $surat_sehat_file->getClientOriginalName() }}</p>
                    @endif
                </div>

                <div class="flex justify-between mt-4">
                    <button wire:click="prevStep" type="button" class="inline-flex items-center px-4 py-2.5 text-xs font-semibold rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-655 dark:text-zinc-400 transition-colors">
                        {{ __('Back') }}
                    </button>
                    <button wire:click="nextStep" type="button" class="inline-flex items-center gap-1.5 px-6 py-2.5 text-xs font-bold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs">
                        {{ __('Next: SOP Agreement') }}
                    </button>
                </div>
            </div>
        @endif

        <!-- STEP 4: Digital SOP Agreement -->
        @if ($currentStep === 4)
            <div class="flex flex-col gap-6">
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50">{{ __('Step 4: Digital SOP Agreement') }}</h3>

                <!-- Bali Code of Ethics SOP Agreement Box -->
                <div class="p-5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 space-y-4">
                    <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                        <svg class="size-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z"/></svg>
                        {{ __('Balinese Customary Tourism Quality SOP') }}
                    </h4>
                    
                    <div class="text-xs text-zinc-650 dark:text-zinc-400 space-y-3 leading-relaxed max-h-[250px] overflow-y-auto pr-1">
                        <p>
                            In accordance with **Bali Governor Regulation No. 5 of 2020** regarding the Implementation of Quality and Cultural Balinese Tourism, all registered tour guides on this platform must:
                        </p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Present Balinese cultural heritage, local customs, and traditions with the utmost accuracy, authenticity, and respect.</li>
                            <li>Prevent any behavior that disrespects holy places, temple sanctuaries, or traditional customary ceremonies (<em>adat</em>).</li>
                            <li>Comply strictly with the local dress codes and ethics when guiding travelers within sacred precincts.</li>
                            <li>Promote environmental sustainability, zero waste, and support local community enterprises across Balinese villages.</li>
                        </ul>
                        <p>
                            Failure to uphold these guidelines will result in immediate suspension, progressive profile compliance strikes, or permanent bans.
                        </p>
                    </div>

                    <hr class="border-zinc-200 dark:border-zinc-800" />

                    <label class="inline-flex items-start gap-3 cursor-pointer select-none">
                        <input 
                            type="checkbox" 
                            wire:model="signed_sop" 
                            class="rounded mt-0.5 border-zinc-300 text-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800"
                            required
                        />
                        <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                            I hereby digitally sign and agree to abide by the Bali Customary Quality Tourism SOP and Governor regulations.
                        </span>
                    </label>
                    @error('signed_sop') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-between mt-4">
                    <button wire:click="prevStep" type="button" class="inline-flex items-center px-4 py-2.5 text-xs font-semibold rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-655 dark:text-zinc-400 transition-colors">
                        {{ __('Back') }}
                    </button>
                    <button 
                        wire:click="register" 
                        type="button" 
                        class="inline-flex items-center gap-1.5 px-8 py-2.5 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-colors shadow-xs"
                    >
                        {{ __('Submit Registration & Apply') }}
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Login Link footer -->
    <div class="space-x-1 text-center text-xs text-zinc-500 mt-4">
        <span>{{ __('Already have an account?') }}</span>
        <a href="{{ route('login') }}" wire:navigate class="font-semibold text-zinc-950 hover:underline dark:text-white">{{ __('Log in') }}</a>
    </div>
</div>
