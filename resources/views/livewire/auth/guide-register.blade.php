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
                <flux:heading size="lg">{{ __('Basic Account Details') }}</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input 
                        wire:model.blur="name" 
                        label="{{ __('Full Name') }}" 
                        placeholder="e.g. Wayan Sudarta" 
                        required 
                    />

                    <flux:input 
                        wire:model.blur="email" 
                        type="email" 
                        label="{{ __('Email Address') }}" 
                        placeholder="wayan@example.com" 
                        required 
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input 
                        wire:model.blur="password" 
                        type="password" 
                        label="{{ __('Password') }}" 
                        placeholder="{{ __('Create Password') }}" 
                        viewable 
                        required 
                    />

                    <flux:input 
                        wire:model.blur="password_confirmation" 
                        type="password" 
                        label="{{ __('Confirm Password') }}" 
                        placeholder="{{ __('Repeat Password') }}" 
                        viewable 
                        required 
                    />
                </div>

                <flux:input 
                    wire:model.blur="phone_number" 
                    label="{{ __('WhatsApp / Phone Number') }}" 
                    placeholder="e.g. +628123456789" 
                    required 
                />

                <div class="flex justify-end mt-4">
                    <flux:button wire:click="nextStep" variant="primary" class="px-6">
                        {{ __('Next: Profile Details') }}
                    </flux:button>
                </div>
            </div>
        @endif

        <!-- STEP 2: Profile details -->
        @if ($currentStep === 2)
            <div class="flex flex-col gap-6">
                <flux:heading size="lg">{{ __('Profile & Pricing details') }}</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input 
                        wire:model.blur="ktp_number" 
                        label="{{ __('National ID (KTP) Number') }}" 
                        placeholder="16-digit number" 
                        maxlength="16" 
                        required 
                    />

                    <!-- KTP Upload -->
                    <div>
                        <flux:label>{{ __('National ID (KTP) Photo') }}</flux:label>
                        <div class="mt-1 flex items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-800 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-950">
                            <input type="file" wire:model="ktp_photo" id="ktp_photo" class="hidden" accept="image/*">
                            <label for="ktp_photo" class="cursor-pointer flex flex-col items-center justify-center text-center">
                                <flux:icon.camera class="size-8 text-zinc-400 dark:text-zinc-600 mb-1" />
                                <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">Click to Upload KTP Photo</span>
                                <span class="text-[10px] text-zinc-500">Max 2MB (Image only)</span>
                            </label>
                        </div>
                        <flux:error name="ktp_photo" />

                        <!-- Upload Preview -->
                        @if ($ktp_photo)
                            <div class="mt-2 text-sm text-zinc-500 flex items-center gap-2 bg-zinc-50 dark:bg-zinc-950 p-2 rounded-lg border dark:border-zinc-800">
                                @if (in_array(strtolower($ktp_photo->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                    <img src="{{ $ktp_photo->temporaryUrl() }}" class="h-16 w-16 rounded object-cover border dark:border-zinc-800">
                                @else
                                    <flux:icon.document class="size-8 text-zinc-400" />
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
                    <flux:select wire:model.blur="tariff_mode" label="{{ __('Tariff Pricing Mode') }}">
                        <flux:option value="daily">{{ __('Daily Rate') }}</flux:option>
                        <flux:option value="hourly">{{ __('Hourly Rate') }}</flux:option>
                        <flux:option value="package">{{ __('Fixed Package Rate') }}</flux:option>
                    </flux:select>

                    <flux:input 
                        wire:model.blur="base_rate" 
                        type="number" 
                        label="{{ __('Base Rate (IDR)') }}" 
                        placeholder="e.g. 500000" 
                        prefix="Rp" 
                        required 
                    />
                </div>

                <!-- Languages checkboxes -->
                <div>
                    <flux:label class="mb-2 block">{{ __('Languages Spoken') }}</flux:label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <flux:checkbox wire:model="languages" value="id" label="Indonesian" />
                        <flux:checkbox wire:model="languages" value="en" label="English" />
                        <flux:checkbox wire:model="languages" value="jp" label="Japanese" />
                        <flux:checkbox wire:model="languages" value="fr" label="French" />
                        <flux:checkbox wire:model="languages" value="de" label="German" />
                    </div>
                    <flux:error name="languages" />
                </div>

                <flux:textarea 
                    wire:model.blur="bio" 
                    label="{{ __('Short Bio / Introduction') }}" 
                    placeholder="Tell tourists about your background, experience, and local wisdom." 
                    rows="3" 
                />

                <flux:textarea 
                    wire:model.blur="vehicle_details" 
                    label="{{ __('Vehicle Details (Optional)') }}" 
                    placeholder="Describe your vehicle type, model, year, and capacity if you provide transport." 
                    rows="2" 
                />

                <div class="flex justify-between mt-4">
                    <flux:button wire:click="prevStep" variant="ghost">
                        {{ __('Back') }}
                    </flux:button>
                    <flux:button wire:click="nextStep" variant="primary" class="px-6">
                        {{ __('Next: Legality Docs') }}
                    </flux:button>
                </div>
            </div>
        @endif

        <!-- STEP 3: Tourism legality uploads -->
        @if ($currentStep === 3)
            <div class="flex flex-col gap-6">
                <flux:heading size="lg">{{ __('Tourism Legality Documents') }}</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input 
                        wire:model.blur="ktpp_number" 
                        label="{{ __('KTPP (HPI Guide License) Number') }}" 
                        placeholder="e.g. HPI-12345" 
                        required 
                    />

                    <flux:input 
                        wire:model.blur="ktpp_expired_at" 
                        type="date" 
                        label="{{ __('KTPP Expiry Date') }}" 
                        required 
                    />
                </div>

                <!-- KTPP file upload -->
                <div>
                    <flux:label>{{ __('KTPP License File Scan (PDF/Image)') }}</flux:label>
                    <div class="mt-1 flex items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-800 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-950">
                        <input type="file" wire:model="ktpp_file" id="ktpp_file" class="hidden" accept="image/*,application/pdf">
                        <label for="ktpp_file" class="cursor-pointer flex flex-col items-center justify-center text-center">
                            <flux:icon.document-text class="size-8 text-zinc-400 dark:text-zinc-600 mb-1" />
                            <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">Click to Upload KTPP Scan</span>
                            <span class="text-[10px] text-zinc-500">Max 5MB (PDF, JPG, PNG)</span>
                        </label>
                    </div>
                    <flux:error name="ktpp_file" />

                    @if ($ktpp_file)
                        <div class="mt-2 text-sm text-zinc-500 flex items-center gap-2 bg-zinc-50 dark:bg-zinc-950 p-2 rounded-lg border dark:border-zinc-800">
                            @if (in_array(strtolower($ktpp_file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <img src="{{ $ktpp_file->temporaryUrl() }}" class="h-16 w-16 rounded object-cover border dark:border-zinc-800">
                            @else
                                <flux:icon.document class="size-8 text-zinc-400" />
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
                    <div>
                        <flux:label>{{ __('Police Clearance (SKCK) Scan') }}</flux:label>
                        <div class="mt-1 flex items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-800 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-950">
                            <input type="file" wire:model="skck_file" id="skck_file" class="hidden" accept="image/*,application/pdf">
                            <label for="skck_file" class="cursor-pointer flex flex-col items-center justify-center text-center">
                                <flux:icon.document-text class="size-8 text-zinc-400 dark:text-zinc-600 mb-1" />
                                <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">Click to Upload SKCK Scan</span>
                                <span class="text-[10px] text-zinc-500">Max 5MB (PDF, JPG, PNG)</span>
                            </label>
                        </div>
                        <flux:error name="skck_file" />

                        @if ($skck_file)
                            <div class="mt-2 text-sm text-zinc-500 flex items-center gap-2 bg-zinc-50 dark:bg-zinc-950 p-2 rounded-lg border dark:border-zinc-800">
                                @if (in_array(strtolower($skck_file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                    <img src="{{ $skck_file->temporaryUrl() }}" class="h-16 w-16 rounded object-cover border dark:border-zinc-800">
                                @else
                                    <flux:icon.document class="size-8 text-zinc-400" />
                                @endif
                                <div class="overflow-hidden">
                                    <p class="font-medium text-zinc-700 dark:text-zinc-300 text-xs truncate">{{ $skck_file->getClientOriginalName() }}</p>
                                    <p class="text-[10px] text-zinc-500">{{ round($skck_file->getSize() / 1024) }} KB</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <flux:input 
                        wire:model.blur="skck_expired_at" 
                        type="date" 
                        label="{{ __('SKCK Expiry Date') }}" 
                        required 
                    />
                </div>

                <!-- Surat Sehat upload -->
                <div>
                    <flux:label>{{ __('Medical Certificate (Surat Sehat) Scan') }}</flux:label>
                    <div class="mt-1 flex items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-800 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-950">
                        <input type="file" wire:model="surat_sehat_file" id="surat_sehat_file" class="hidden" accept="image/*,application/pdf">
                        <label for="surat_sehat_file" class="cursor-pointer flex flex-col items-center justify-center text-center">
                            <flux:icon.document-text class="size-8 text-zinc-400 dark:text-zinc-600 mb-1" />
                            <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">Click to Upload Medical Certificate</span>
                            <span class="text-[10px] text-zinc-500">Max 5MB (PDF, JPG, PNG)</span>
                        </label>
                    </div>
                    <flux:error name="surat_sehat_file" />

                    @if ($surat_sehat_file)
                        <div class="mt-2 text-sm text-zinc-500 flex items-center gap-2 bg-zinc-50 dark:bg-zinc-950 p-2 rounded-lg border dark:border-zinc-800">
                            @if (in_array(strtolower($surat_sehat_file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <img src="{{ $surat_sehat_file->temporaryUrl() }}" class="h-16 w-16 rounded object-cover border dark:border-zinc-800">
                            @else
                                <flux:icon.document class="size-8 text-zinc-400" />
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
                    <flux:heading size="sm" class="mb-2 flex items-center gap-1.5">
                        <flux:icon.shield class="size-4 text-emerald-600 dark:text-emerald-400" />
                        {{ __('Balinese Customary Tourism Regulation Compliance') }}
                    </flux:heading>
                    <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed mb-4">
                        By checking the box below, you digitally sign the platform’s Code of Ethics and commit to upholding the strict regulations of the Bali Provincial Government and the Himpunan Pramuwisata Indonesia (HPI). You agree to represent the local culture with respect, protect the environment, and comply with traditional customary rules (<em>adat</em>).
                    </p>
                    <flux:checkbox 
                        wire:model="signed_sop" 
                        label="I hereby digitally sign and agree to the Bali Code of Ethics & custom regulations." 
                        required 
                    />
                    <flux:error name="signed_sop" />
                </div>

                <div class="flex justify-between mt-4">
                    <flux:button wire:click="prevStep" variant="ghost">
                        {{ __('Back') }}
                    </flux:button>
                    <flux:button wire:click="register" variant="primary" class="px-8">
                        {{ __('Submit Registration & Apply') }}
                    </flux:button>
                </div>
            </div>
        @endif
    </div>

    <!-- Login Link footer -->
    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400 mt-4">
        <span>{{ __('Already have an account?') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>
