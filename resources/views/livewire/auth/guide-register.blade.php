<div class="flex flex-col gap-6">
    <x-auth-header 
        :title="__('Register as a Tour Guide')" 
        :description="__('Complete the 3-step verification to list your services in Bali.')" 
    />

    <!-- Step Progress Indicator -->
    <div class="relative flex items-center justify-between my-4">
        <!-- Background Track -->
        <div class="absolute left-0 top-1/2 h-0.5 w-full -translate-y-1/2 bg-zinc-200 dark:bg-zinc-800"></div>
        <!-- Active Progress Line -->
        <div 
            class="absolute left-0 top-1/2 h-0.5 -translate-y-1/2 bg-zinc-950 dark:bg-white transition-all duration-300"
            style="width: {{ (($currentStep - 1) / 2) * 100 }}%"
        ></div>

        <!-- Step 1 -->
        <div class="relative z-10 flex flex-col items-center gap-1.5">
            <div class="flex h-8 w-8 items-center justify-center rounded-full border text-xs font-semibold transition-all duration-300 {{ $currentStep >= 1 ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-white dark:text-black dark:border-white' : 'bg-white text-zinc-500 border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800' }}">
                1
            </div>
            <span class="text-[10px] md:text-xs font-medium text-zinc-600 dark:text-zinc-400">{{ __('Account') }}</span>
        </div>

        <!-- Step 2 -->
        <div class="relative z-10 flex flex-col items-center gap-1.5">
            <div class="flex h-8 w-8 items-center justify-center rounded-full border text-xs font-semibold transition-all duration-300 {{ $currentStep >= 2 ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-white dark:text-black dark:border-white' : 'bg-white text-zinc-500 border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800' }}">
                2
            </div>
            <span class="text-[10px] md:text-xs font-medium text-zinc-600 dark:text-zinc-400">{{ __('Profile & ID') }}</span>
        </div>

        <!-- Step 3 -->
        <div class="relative z-10 flex flex-col items-center gap-1.5">
            <div class="flex h-8 w-8 items-center justify-center rounded-full border text-xs font-semibold transition-all duration-300 {{ $currentStep >= 3 ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-white dark:text-black dark:border-white' : 'bg-white text-zinc-500 border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800' }}">
                3
            </div>
            <span class="text-[10px] md:text-xs font-medium text-zinc-600 dark:text-zinc-400">{{ __('Legality') }}</span>
        </div>
    </div>

    <!-- Wizard Form -->
    <div class="mt-4">
        <!-- STEP 1: Account details -->
        @if ($currentStep === 1)
            <div class="flex flex-col gap-6">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">{{ __('Basic Account Details') }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Full Name') }}</label>
                        <input 
                            wire:model.blur="name" 
                            type="text" 
                            placeholder="e.g. Wayan Sudarta" 
                            required 
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Email Address') }}</label>
                        <input 
                            wire:model.blur="email" 
                            type="email" 
                            placeholder="wayan@example.com" 
                            required 
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Password') }}</label>
                        <input 
                            wire:model.blur="password" 
                            type="password" 
                            placeholder="{{ __('Create Password') }}" 
                            required 
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Confirm Password') }}</label>
                        <input 
                            wire:model.blur="password_confirmation" 
                            type="password" 
                            placeholder="{{ __('Repeat Password') }}" 
                            required 
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('password_confirmation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('WhatsApp / Phone Number') }}</label>
                    <input 
                        wire:model.blur="phone_number" 
                        type="text" 
                        placeholder="e.g. +628123456789" 
                        required 
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                    />
                    @error('phone_number') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end mt-4">
                    <button 
                        wire:click="nextStep" 
                        type="button"
                        class="inline-flex items-center gap-1.5 px-6 py-2.5 text-sm font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs"
                    >
                        {{ __('Next: Profile Details') }}
                    </button>
                </div>
            </div>
        @endif

        <!-- STEP 2: Profile details -->
        @if ($currentStep === 2)
            <div class="flex flex-col gap-6">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">{{ __('Profile & Pricing details') }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('National ID (KTP) Number') }}</label>
                        <input 
                            wire:model.blur="ktp_number" 
                            type="text" 
                            placeholder="16-digit number" 
                            maxlength="16" 
                            required 
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('ktp_number') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- KTP Upload -->
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('National ID (KTP) Photo') }}</label>
                        <div class="mt-1 flex items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-800 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-950">
                            <input type="file" wire:model="ktp_photo" id="ktp_photo" class="hidden" accept="image/*">
                            <label for="ktp_photo" class="cursor-pointer flex flex-col items-center justify-center text-center">
                                <svg class="size-8 text-zinc-400 dark:text-zinc-600 mb-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"/></svg>
                                <span class="text-xs font-semibold text-zinc-650 dark:text-zinc-400">Click to Upload KTP Photo</span>
                                <span class="text-[10px] text-zinc-500">Max 2MB (Image only)</span>
                            </label>
                        </div>
                        @error('ktp_photo') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror

                        <!-- Upload Preview -->
                        @if ($ktp_photo)
                            <div class="mt-2 text-sm text-zinc-500 flex items-center gap-2 bg-zinc-50 dark:bg-zinc-950 p-2 rounded-lg border dark:border-zinc-800">
                                @if (in_array(strtolower($ktp_photo->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                    <img src="{{ $ktp_photo->temporaryUrl() }}" class="h-16 w-16 rounded object-cover border dark:border-zinc-800">
                                @else
                                    <svg class="size-8 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                @endif
                                <div class="overflow-hidden">
                                    <p class="font-medium text-zinc-700 dark:text-zinc-300 text-xs truncate">{{ $ktp_photo->getClientOriginalName() }}</p>
                                    <p class="text-[10px] text-zinc-500">{{ round($ktp_photo->getSize() / 1024) }} KB</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tariff Mode -->
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Tariff Pricing Mode') }}</label>
                        <select 
                            wire:model.blur="tariff_mode" 
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        >
                            <option value="daily">{{ __('Daily Rate') }}</option>
                            <option value="hourly">{{ __('Hourly Rate') }}</option>
                            <option value="package">{{ __('Fixed Package Rate') }}</option>
                        </select>
                        @error('tariff_mode') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Base Rate (IDR)') }}</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-zinc-400 text-sm">Rp</span>
                            <input 
                                wire:model.blur="base_rate" 
                                type="number" 
                                placeholder="e.g. 500000" 
                                required 
                                class="w-full text-sm pl-9 pr-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                            />
                        </div>
                        @error('base_rate') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Languages checkboxes -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Languages Spoken') }}</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="inline-flex items-center gap-2 text-sm text-zinc-655 dark:text-zinc-400 cursor-pointer">
                            <input type="checkbox" wire:model="languages" value="id" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800" />
                            <span>Indonesian</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-zinc-655 dark:text-zinc-400 cursor-pointer">
                            <input type="checkbox" wire:model="languages" value="en" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800" />
                            <span>English</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-zinc-655 dark:text-zinc-400 cursor-pointer">
                            <input type="checkbox" wire:model="languages" value="jp" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800" />
                            <span>Japanese</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-zinc-655 dark:text-zinc-400 cursor-pointer">
                            <input type="checkbox" wire:model="languages" value="fr" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800" />
                            <span>French</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-zinc-655 dark:text-zinc-400 cursor-pointer">
                            <input type="checkbox" wire:model="languages" value="de" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800" />
                            <span>German</span>
                        </label>
                    </div>
                    @error('languages') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Short Bio / Introduction') }}</label>
                    <textarea 
                        wire:model.blur="bio" 
                        placeholder="Tell tourists about your background, experience, and local wisdom." 
                        rows="3" 
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                    ></textarea>
                    @error('bio') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Vehicle Details (Optional)') }}</label>
                    <textarea 
                        wire:model.blur="vehicle_details" 
                        placeholder="Describe your vehicle type, model, year, and capacity if you provide transport." 
                        rows="2" 
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                    ></textarea>
                    @error('vehicle_details') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-between mt-4">
                    <button 
                        wire:click="prevStep" 
                        type="button" 
                        class="inline-flex items-center px-4 py-2.5 text-sm font-semibold rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-650 dark:text-zinc-400 transition-colors"
                    >
                        {{ __('Back') }}
                    </button>
                    <button 
                        wire:click="nextStep" 
                        type="button" 
                        class="inline-flex items-center gap-1.5 px-6 py-2.5 text-sm font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs"
                    >
                        {{ __('Next: Legality Docs') }}
                    </button>
                </div>
            </div>
        @endif

        <!-- STEP 3: Tourism legality uploads -->
        @if ($currentStep === 3)
            <div class="flex flex-col gap-6">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">{{ __('Tourism Legality Documents') }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('KTPP (HPI Guide License) Number') }}</label>
                        <input 
                            wire:model.blur="ktpp_number" 
                            type="text" 
                            placeholder="e.g. HPI-12345" 
                            required 
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('ktpp_number') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('KTPP Expiry Date') }}</label>
                        <input 
                            wire:model.blur="ktpp_expired_at" 
                            type="date" 
                            required 
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('ktpp_expired_at') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- KTPP file upload -->
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('KTPP License File Scan (PDF/Image)') }}</label>
                    <div class="mt-1 flex items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-800 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-950">
                        <input type="file" wire:model="ktpp_file" id="ktpp_file" class="hidden" accept="image/*,application/pdf">
                        <label for="ktpp_file" class="cursor-pointer flex flex-col items-center justify-center text-center">
                            <svg class="size-8 text-zinc-400 dark:text-zinc-600 mb-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                            <span class="text-xs font-semibold text-zinc-650 dark:text-zinc-400">Click to Upload KTPP Scan</span>
                            <span class="text-[10px] text-zinc-500">Max 5MB (PDF, JPG, PNG)</span>
                        </label>
                    </div>
                    @error('ktpp_file') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror

                    @if ($ktpp_file)
                        <div class="mt-2 text-sm text-zinc-500 flex items-center gap-2 bg-zinc-50 dark:bg-zinc-950 p-2 rounded-lg border dark:border-zinc-800">
                            @if (in_array(strtolower($ktpp_file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <img src="{{ $ktpp_file->temporaryUrl() }}" class="h-16 w-16 rounded object-cover border dark:border-zinc-800">
                            @else
                                <svg class="size-8 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                            @endif
                            <div class="overflow-hidden">
                                <p class="font-medium text-zinc-700 dark:text-zinc-300 text-xs truncate">{{ $ktpp_file->getClientOriginalName() }}</p>
                                <p class="text-[10px] text-zinc-500">{{ round($ktpp_file->getSize() / 1024) }} KB</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- SKCK file upload -->
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Police Clearance (SKCK) Scan') }}</label>
                        <div class="mt-1 flex items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-800 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-950">
                            <input type="file" wire:model="skck_file" id="skck_file" class="hidden" accept="image/*,application/pdf">
                            <label for="skck_file" class="cursor-pointer flex flex-col items-center justify-center text-center">
                                <svg class="size-8 text-zinc-400 dark:text-zinc-600 mb-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                <span class="text-xs font-semibold text-zinc-650 dark:text-zinc-400">Click to Upload SKCK Scan</span>
                                <span class="text-[10px] text-zinc-500">Max 5MB (PDF, JPG, PNG)</span>
                            </label>
                        </div>
                        @error('skck_file') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror

                        @if ($skck_file)
                            <div class="mt-2 text-sm text-zinc-500 flex items-center gap-2 bg-zinc-50 dark:bg-zinc-950 p-2 rounded-lg border dark:border-zinc-800">
                                @if (in_array(strtolower($skck_file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                    <img src="{{ $skck_file->temporaryUrl() }}" class="h-16 w-16 rounded object-cover border dark:border-zinc-800">
                                @else
                                    <svg class="size-8 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                @endif
                                <div class="overflow-hidden">
                                    <p class="font-medium text-zinc-700 dark:text-zinc-300 text-xs truncate">{{ $skck_file->getClientOriginalName() }}</p>
                                    <p class="text-[10px] text-zinc-500">{{ round($skck_file->getSize() / 1024) }} KB</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('SKCK Expiry Date') }}</label>
                        <input 
                            wire:model.blur="skck_expired_at" 
                            type="date" 
                            required 
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-white text-zinc-950 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white"
                        />
                        @error('skck_expired_at') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Surat Sehat upload -->
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300 block">{{ __('Medical Certificate (Surat Sehat) Scan') }}</label>
                    <div class="mt-1 flex items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-800 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-950">
                        <input type="file" wire:model="surat_sehat_file" id="surat_sehat_file" class="hidden" accept="image/*,application/pdf">
                        <label for="surat_sehat_file" class="cursor-pointer flex flex-col items-center justify-center text-center">
                            <svg class="size-8 text-zinc-400 dark:text-zinc-600 mb-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                            <span class="text-xs font-semibold text-zinc-650 dark:text-zinc-400">Click to Upload Medical Certificate</span>
                            <span class="text-[10px] text-zinc-500">Max 5MB (PDF, JPG, PNG)</span>
                        </label>
                    </div>
                    @error('surat_sehat_file') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror

                    @if ($surat_sehat_file)
                        <div class="mt-2 text-sm text-zinc-500 flex items-center gap-2 bg-zinc-50 dark:bg-zinc-950 p-2 rounded-lg border dark:border-zinc-800">
                            @if (in_array(strtolower($surat_sehat_file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <img src="{{ $surat_sehat_file->temporaryUrl() }}" class="h-16 w-16 rounded object-cover border dark:border-zinc-800">
                            @else
                                <svg class="size-8 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                            @endif
                            <div class="overflow-hidden">
                                <p class="font-medium text-zinc-700 dark:text-zinc-300 text-xs truncate">{{ $surat_sehat_file->getClientOriginalName() }}</p>
                                <p class="text-[10px] text-zinc-500">{{ round($surat_sehat_file->getSize() / 1024) }} KB</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Bali Code of Ethics SOP Agreement Box -->
                <div class="mt-4 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950">
                    <h4 class="text-sm font-semibold text-zinc-800 dark:text-zinc-200 mb-2 flex items-center gap-1.5">
                        <svg class="size-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z"/></svg>
                        {{ __('Balinese Customary Tourism Regulation Compliance') }}
                    </h4>
                    <p class="text-xs text-zinc-650 dark:text-zinc-400 leading-relaxed mb-4">
                        By checking the box below, you digitally sign the platform’s Code of Ethics and commit to upholding the strict regulations of the Bali Provincial Government and the Himpunan Pramuwisata Indonesia (HPI). You agree to represent the local culture with respect, protect the environment, and comply with traditional customary rules (<em>adat</em>).
                    </p>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input 
                            type="checkbox" 
                            wire:model="signed_sop" 
                            class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800"
                            required
                        />
                        <span class="text-sm font-medium text-zinc-750 dark:text-zinc-300">I hereby digitally sign and agree to the Bali Code of Ethics & custom regulations.</span>
                    </label>
                    @error('signed_sop') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-between mt-4">
                    <button 
                        wire:click="prevStep" 
                        type="button" 
                        class="inline-flex items-center px-4 py-2.5 text-sm font-semibold rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-655 dark:text-zinc-400 transition-colors"
                    >
                        {{ __('Back') }}
                    </button>
                    <button 
                        wire:click="register" 
                        type="button" 
                        class="inline-flex items-center gap-1.5 px-8 py-2.5 text-sm font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-xs"
                    >
                        {{ __('Submit Registration & Apply') }}
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Login Link footer -->
    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400 mt-4">
        <span>{{ __('Already have an account?') }}</span>
        <a href="{{ route('login') }}" wire:navigate class="font-semibold text-zinc-900 hover:text-zinc-850 dark:text-white dark:hover:text-zinc-200 underline">{{ __('Log in') }}</a>
    </div>
</div>
