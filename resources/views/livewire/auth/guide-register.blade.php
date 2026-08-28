<div class="flex flex-col gap-6 py-8">
    {{-- Header --}}
    <div class="space-y-1.5 text-center">
        <h1 class="text-2xl sm:text-3xl font-semibold tracking-[-0.44px] text-ink">{{ __('Register as a Tour Guide') }}</h1>
        <p class="text-sm text-muted">{{ __('Complete the 4-step verification to list your services and meet travelers in Bali.') }}</p>
    </div>

    {{-- Step Progress Indicator --}}
    <div class="my-2 space-y-3">
        <!-- Progress bar line -->
        <div class="relative flex items-center justify-between">
            <!-- Background Track -->
            <div class="absolute left-0 top-1/2 h-1 w-full -translate-y-1/2 rounded-full bg-surface-strong"></div>
            <!-- Active Progress Line -->
            <div 
                class="absolute left-0 top-1/2 h-1 -translate-y-1/2 rounded-full bg-rausch transition-all duration-500 ease-out"
                style="width: {{ (($currentStep - 1) / 3) * 100 }}%"
            ></div>

            <!-- Step 1 Node -->
            <div class="relative z-10 flex flex-col items-center gap-1.5">
                <div class="flex size-9 items-center justify-center rounded-full text-xs font-bold transition-all duration-300 {{ $currentStep > 1 ? 'bg-rausch text-white shadow-airbnb' : ($currentStep === 1 ? 'bg-white text-rausch border-2 border-rausch ring-4 ring-rausch/15 shadow-airbnb font-extrabold' : 'bg-white text-muted-soft border border-hairline') }}">
                    @if ($currentStep > 1)
                        <svg class="size-4 stroke-current stroke-[2.5]" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    @else
                        1
                    @endif
                </div>
                <span class="text-[11px] font-semibold {{ $currentStep >= 1 ? 'text-ink' : 'text-muted-soft' }}">{{ __('Account') }}</span>
            </div>

            <!-- Step 2 Node -->
            <div class="relative z-10 flex flex-col items-center gap-1.5">
                <div class="flex size-9 items-center justify-center rounded-full text-xs font-bold transition-all duration-300 {{ $currentStep > 2 ? 'bg-rausch text-white shadow-airbnb' : ($currentStep === 2 ? 'bg-white text-rausch border-2 border-rausch ring-4 ring-rausch/15 shadow-airbnb font-extrabold' : 'bg-white text-muted-soft border border-hairline') }}">
                    @if ($currentStep > 2)
                        <svg class="size-4 stroke-current stroke-[2.5]" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    @else
                        2
                    @endif
                </div>
                <span class="text-[11px] font-semibold {{ $currentStep >= 2 ? 'text-ink' : 'text-muted-soft' }}">{{ __('Identity & Vibe') }}</span>
            </div>

            <!-- Step 3 Node -->
            <div class="relative z-10 flex flex-col items-center gap-1.5">
                <div class="flex size-9 items-center justify-center rounded-full text-xs font-bold transition-all duration-300 {{ $currentStep > 3 ? 'bg-rausch text-white shadow-airbnb' : ($currentStep === 3 ? 'bg-white text-rausch border-2 border-rausch ring-4 ring-rausch/15 shadow-airbnb font-extrabold' : 'bg-white text-muted-soft border border-hairline') }}">
                    @if ($currentStep > 3)
                        <svg class="size-4 stroke-current stroke-[2.5]" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    @else
                        3
                    @endif
                </div>
                <span class="text-[11px] font-semibold {{ $currentStep >= 3 ? 'text-ink' : 'text-muted-soft' }}">{{ __('Legality & HPI') }}</span>
            </div>

            <!-- Step 4 Node -->
            <div class="relative z-10 flex flex-col items-center gap-1.5">
                <div class="flex size-9 items-center justify-center rounded-full text-xs font-bold transition-all duration-300 {{ $currentStep >= 4 ? 'bg-white text-rausch border-2 border-rausch ring-4 ring-rausch/15 shadow-airbnb font-extrabold' : 'bg-white text-muted-soft border border-hairline' }}">
                    4
                </div>
                <span class="text-[11px] font-semibold {{ $currentStep >= 4 ? 'text-ink' : 'text-muted-soft' }}">{{ __('SOP & Ethics') }}</span>
            </div>
        </div>
    </div>

    <!-- Wizard Form Steps -->
    <div class="mt-2">
        <!-- ========================================== -->
        <!-- STEP 1: Account Creation                   -->
        <!-- ========================================== -->
        @if ($currentStep === 1)
            <div class="flex flex-col gap-6 animate-fade-in">
                <div class="border-b border-hairline pb-3">
                    <h2 class="text-base font-semibold text-ink flex items-center gap-2">
                        <span class="flex size-6 items-center justify-center rounded-full bg-rausch/10 text-xs font-bold text-rausch">1</span>
                        {{ __('Step 1 · Account Credentials') }}
                    </h2>
                    <p class="text-xs text-muted mt-1">{{ __('Set up your login credentials and contact details.') }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Full Name -->
                    <div class="space-y-1.5">
                        <label for="name" class="block text-sm font-semibold text-ink">{{ __('Full Name') }} <span class="text-rausch">*</span></label>
                        <input 
                            id="name"
                            wire:model.blur="name" 
                            type="text" 
                            placeholder="e.g. I Wayan Sudarta" 
                            required 
                            autofocus
                            class="w-full rounded-lg border border-hairline bg-white px-3.5 py-2.5 text-sm text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch transition-all"
                        />
                        @error('name') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-sm font-semibold text-ink">{{ __('Email Address') }} <span class="text-rausch">*</span></label>
                        <input 
                            id="email"
                            wire:model.blur="email" 
                            type="email" 
                            placeholder="wayan@example.com" 
                            required 
                            class="w-full rounded-lg border border-hairline bg-white px-3.5 py-2.5 text-sm text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch transition-all"
                        />
                        @error('email') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Password -->
                    <div class="space-y-1.5" x-data="{ show: false }">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-sm font-semibold text-ink">{{ __('Password') }} <span class="text-rausch">*</span></label>
                            <button 
                                type="button" 
                                @click="show = !show" 
                                class="text-xs font-medium text-muted hover:text-ink transition-colors select-none flex items-center gap-1 cursor-pointer"
                                tabindex="-1"
                            >
                                <span x-show="!show" class="flex items-center gap-1">
                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                    <span>{{ __('Show') }}</span>
                                </span>
                                <span x-show="show" x-cloak class="flex items-center gap-1">
                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                    <span>{{ __('Hide') }}</span>
                                </span>
                            </button>
                        </div>
                        <div class="relative">
                            <input 
                                id="password"
                                wire:model.blur="password" 
                                :type="show ? 'text' : 'password'"
                                type="password" 
                                placeholder="{{ __('Create password') }}" 
                                required 
                                class="w-full rounded-lg border border-hairline bg-white px-3.5 py-2.5 text-sm text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch transition-all pr-10"
                            />
                            <button 
                                type="button" 
                                @click="show = !show" 
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-muted-soft hover:text-ink cursor-pointer transition-colors"
                                tabindex="-1"
                                aria-label="{{ __('Toggle password visibility') }}"
                            >
                                <svg x-show="!show" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                <svg x-show="show" x-cloak class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            </button>
                        </div>
                        @error('password') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-1.5" x-data="{ show: false }">
                        <div class="flex items-center justify-between">
                            <label for="password_confirmation" class="block text-sm font-semibold text-ink">{{ __('Confirm Password') }} <span class="text-rausch">*</span></label>
                            <button 
                                type="button" 
                                @click="show = !show" 
                                class="text-xs font-medium text-muted hover:text-ink transition-colors select-none flex items-center gap-1 cursor-pointer"
                                tabindex="-1"
                            >
                                <span x-show="!show" class="flex items-center gap-1">
                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                    <span>{{ __('Show') }}</span>
                                </span>
                                <span x-show="show" x-cloak class="flex items-center gap-1">
                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                    <span>{{ __('Hide') }}</span>
                                </span>
                            </button>
                        </div>
                        <div class="relative">
                            <input 
                                id="password_confirmation"
                                wire:model.blur="password_confirmation" 
                                :type="show ? 'text' : 'password'"
                                type="password" 
                                placeholder="{{ __('Repeat password') }}" 
                                required 
                                class="w-full rounded-lg border border-hairline bg-white px-3.5 py-2.5 text-sm text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch transition-all pr-10"
                            />
                            <button 
                                type="button" 
                                @click="show = !show" 
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-muted-soft hover:text-ink cursor-pointer transition-colors"
                                tabindex="-1"
                                aria-label="{{ __('Toggle password visibility') }}"
                            >
                                <svg x-show="!show" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                <svg x-show="show" x-cloak class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            </button>
                        </div>
                        @error('password_confirmation') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Phone / WhatsApp -->
                <div class="space-y-1.5">
                    <label for="phone_number" class="block text-sm font-semibold text-ink">{{ __('WhatsApp / Phone Number') }} <span class="text-rausch">*</span></label>
                    <input 
                        id="phone_number"
                        wire:model.blur="phone_number" 
                        type="text" 
                        placeholder="e.g. 081234567891" 
                        required 
                        class="w-full rounded-lg border border-hairline bg-white px-3.5 py-2.5 text-sm text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch transition-all"
                    />
                    <p class="text-xs text-muted">{{ __('Used for tour coordination, booking notifications, and tourist communication.') }}</p>
                    @error('phone_number') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end pt-3">
                    <button 
                        wire:click="nextStep" 
                        type="button"
                        class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-lg bg-rausch px-7 py-3 text-sm font-semibold text-white shadow-airbnb transition-colors hover:bg-rausch-active"
                    >
                        <span>{{ __('Continue to Identity (Step 2)') }}</span>
                        <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- STEP 2: Identity & Guiding Personality     -->
        <!-- ========================================== -->
        @if ($currentStep === 2)
            <div class="flex flex-col gap-6 animate-fade-in">
                <div class="border-b border-hairline pb-3">
                    <h2 class="text-base font-semibold text-ink flex items-center gap-2">
                        <span class="flex size-6 items-center justify-center rounded-full bg-rausch/10 text-xs font-bold text-rausch">2</span>
                        {{ __('Step 2 · Identity & Matching Personality') }}
                    </h2>
                    <p class="text-xs text-muted mt-1">{{ __('Share your national ID, communication vibe, and tour specializations for tourist matching.') }}</p>
                </div>

                <!-- National ID (KTP/NIK) & Languages -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- National ID Number -->
                    <div class="space-y-1.5">
                        <label for="ktp_number" class="block text-sm font-semibold text-ink">{{ __('National ID (NIK / KTP) Number') }} <span class="text-rausch">*</span></label>
                        <input 
                            id="ktp_number"
                            wire:model.blur="ktp_number" 
                            type="text" 
                            placeholder="16-digit national ID number" 
                            maxlength="16" 
                            required 
                            class="w-full rounded-lg border border-hairline bg-white px-3.5 py-2.5 text-sm text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch transition-all"
                        />
                        <p class="text-[11px] text-muted">{{ __('Must match your official KTP for verification.') }}</p>
                        @error('ktp_number') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Languages spoken -->
                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-ink">{{ __('Languages Spoken') }} <span class="text-rausch">*</span></label>
                        <div class="grid grid-cols-2 gap-2 mt-1">
                            @foreach ([
                                'id' => '🇮🇩 Indonesian',
                                'en' => '🇬🇧 English',
                                'jp' => '🇯🇵 Japanese',
                                'fr' => '🇫🇷 French',
                                'de' => '🇩🇪 German',
                            ] as $code => $label)
                                <label class="flex items-center gap-2.5 rounded-lg border px-3 py-2 text-xs font-medium cursor-pointer transition-all {{ in_array($code, $languages) ? 'border-rausch bg-rausch/5 text-ink ring-1 ring-rausch/20 font-semibold' : 'border-hairline bg-white text-body hover:bg-surface-soft' }}">
                                    <input type="checkbox" wire:model="languages" value="{{ $code }}" class="size-3.5 rounded border-hairline text-rausch focus:ring-rausch focus:ring-2" />
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('languages') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Communication Style (SRS Matching Engine) -->
                <div class="space-y-2">
                    <div class="flex items-baseline justify-between">
                        <label class="block text-sm font-semibold text-ink">{{ __('Communication Style') }} <span class="text-rausch">*</span></label>
                        <span class="text-xs text-muted">{{ __('Used by travelers to match with your frequency') }}</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @php
                            $styleMeta = [
                                'santai' => ['emoji' => '🌴', 'title' => 'Santai (Relaxed)', 'desc' => 'Easygoing, friendly, casual pace & leisure vibes'],
                                'edukatif' => ['emoji' => '🏛️', 'title' => 'Edukatif (Educational)', 'desc' => 'Rich cultural history, temple traditions & deep context'],
                                'profesional' => ['emoji' => '👔', 'title' => 'Profesional (Professional)', 'desc' => 'Punctual, formal hospitality & organized itinerary'],
                                'ekspresif' => ['emoji' => '🎉', 'title' => 'Ekspresif (Expressive)', 'desc' => 'High energy, enthusiastic storytelling & dynamic fun'],
                            ];
                        @endphp

                        @foreach (\App\Enums\CommunicationStyle::cases() as $style)
                            @php $meta = $styleMeta[$style->value] ?? ['emoji' => '✨', 'title' => $style->label(), 'desc' => '']; @endphp
                            <label class="relative flex flex-col p-3.5 rounded-xl border cursor-pointer transition-all {{ $communication_style === $style->value ? 'border-rausch bg-rausch/[0.03] ring-2 ring-rausch/20 shadow-sm' : 'border-hairline bg-white hover:border-border-strong hover:bg-surface-soft/60' }}">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">{{ $meta['emoji'] }}</span>
                                        <span class="text-sm font-semibold text-ink">{{ $meta['title'] }}</span>
                                    </div>
                                    <input 
                                        type="radio" 
                                        name="communication_style" 
                                        wire:model="communication_style" 
                                        value="{{ $style->value }}" 
                                        class="size-4 text-rausch border-hairline focus:ring-rausch"
                                    />
                                </div>
                                <p class="text-xs text-muted leading-relaxed pl-7">{{ $meta['desc'] }}</p>
                            </label>
                        @endforeach
                    </div>
                    @error('communication_style') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Activity Specializations (SRS Matching) -->
                <div class="space-y-2">
                    <div class="flex items-baseline justify-between">
                        <label class="block text-sm font-semibold text-ink">{{ __('Activity Specializations') }} <span class="text-rausch">*</span></label>
                        <span class="text-xs text-muted">{{ __('Select all areas where you excel') }}</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                        @php
                            $specIcons = [
                                'cafe_hopping' => '☕',
                                'photography' => '📸',
                                'nightlife' => '🍹',
                                'nature' => '🌿',
                                'culture_history' => '🛕',
                                'healing' => '🧘',
                            ];
                        @endphp
                        @foreach (\App\Enums\Specialization::cases() as $spec)
                            <label class="flex items-center gap-2.5 rounded-xl border p-3 text-xs cursor-pointer transition-all {{ in_array($spec->value, $specializations) ? 'border-rausch bg-rausch/5 text-ink ring-1 ring-rausch/20 font-semibold' : 'border-hairline bg-white text-body hover:bg-surface-soft hover:border-hairline-soft' }}">
                                <input 
                                    type="checkbox" 
                                    wire:model="specializations" 
                                    value="{{ $spec->value }}" 
                                    class="size-3.5 rounded border-hairline text-rausch focus:ring-rausch focus:ring-2" 
                                />
                                <span class="text-base">{{ $specIcons[$spec->value] ?? '📍' }}</span>
                                <span class="truncate">{{ $spec->label() }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('specializations') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Photo Uploads: KTP and Headshot -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- KTP Photo Upload -->
                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-ink">{{ __('National ID (KTP) Photo') }} <span class="text-rausch">*</span></label>
                        <div class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-hairline p-5 bg-surface-soft hover:bg-white hover:border-rausch transition-all text-center group cursor-pointer">
                            <input type="file" wire:model="ktp_photo" id="ktp_photo" class="absolute inset-0 size-full opacity-0 cursor-pointer" accept="image/*" />
                            
                            <div wire:loading.remove wire:target="ktp_photo" class="flex flex-col items-center pointer-events-none">
                                <div class="flex size-10 items-center justify-center rounded-full bg-white shadow-airbnb text-muted-soft group-hover:text-rausch transition-colors mb-2">
                                    <svg class="size-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z"/></svg>
                                </div>
                                <span class="text-xs font-semibold text-ink">{{ __('Upload KTP Photo') }}</span>
                                <span class="text-[11px] text-muted-soft mt-0.5">{{ __('JPG, PNG up to 2MB') }}</span>
                            </div>

                            <div wire:loading wire:target="ktp_photo" class="flex flex-col items-center gap-2 py-2">
                                <svg class="size-6 animate-spin text-rausch" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span class="text-xs text-muted font-medium">{{ __('Uploading...') }}</span>
                            </div>
                        </div>

                        @if ($ktp_photo)
                            <div class="mt-2 flex items-center gap-3 rounded-lg border border-hairline bg-white p-2.5 shadow-airbnb">
                                <img src="{{ $ktp_photo->temporaryUrl() }}" class="size-10 rounded-md object-cover border border-hairline shrink-0" alt="KTP preview" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-semibold text-ink">{{ $ktp_photo->getClientOriginalName() }}</p>
                                    <span class="text-[10px] text-emerald-600 font-medium flex items-center gap-1">
                                        <svg class="size-3 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                                        {{ __('Ready to submit') }}
                                    </span>
                                </div>
                            </div>
                        @endif
                        @error('ktp_photo') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Headshot Photo Upload -->
                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-ink">{{ __('Recent Headshot Photo') }} <span class="text-rausch">*</span></label>
                        <div class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-hairline p-5 bg-surface-soft hover:bg-white hover:border-rausch transition-all text-center group cursor-pointer">
                            <input type="file" wire:model="headshot" id="headshot" class="absolute inset-0 size-full opacity-0 cursor-pointer" accept="image/*" />
                            
                            <div wire:loading.remove wire:target="headshot" class="flex flex-col items-center pointer-events-none">
                                <div class="flex size-10 items-center justify-center rounded-full bg-white shadow-airbnb text-muted-soft group-hover:text-rausch transition-colors mb-2">
                                    <svg class="size-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                                </div>
                                <span class="text-xs font-semibold text-ink">{{ __('Upload Headshot Photo') }}</span>
                                <span class="text-[11px] text-muted-soft mt-0.5">{{ __('Clear portrait, JPG/PNG max 2MB') }}</span>
                            </div>

                            <div wire:loading wire:target="headshot" class="flex flex-col items-center gap-2 py-2">
                                <svg class="size-6 animate-spin text-rausch" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span class="text-xs text-muted font-medium">{{ __('Uploading...') }}</span>
                            </div>
                        </div>

                        @if ($headshot)
                            <div class="mt-2 flex items-center gap-3 rounded-lg border border-hairline bg-white p-2.5 shadow-airbnb">
                                <img src="{{ $headshot->temporaryUrl() }}" class="size-10 rounded-md object-cover border border-hairline shrink-0" alt="Headshot preview" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-semibold text-ink">{{ $headshot->getClientOriginalName() }}</p>
                                    <span class="text-[10px] text-emerald-600 font-medium flex items-center gap-1">
                                        <svg class="size-3 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                                        {{ __('Ready to submit') }}
                                    </span>
                                </div>
                            </div>
                        @endif
                        @error('headshot') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Profile Bio -->
                <div class="space-y-1.5">
                    <div class="flex items-baseline justify-between">
                        <label for="bio" class="block text-sm font-semibold text-ink">{{ __('Profile Bio') }}</label>
                        <span class="text-xs text-muted-soft">{{ __('Optional') }}</span>
                    </div>
                    <textarea 
                        id="bio"
                        wire:model.blur="bio" 
                        placeholder="{{ __('Introduce yourself to travelers! Share your passion for Bali, local secrets, and guiding background...') }}" 
                        rows="3" 
                        class="w-full rounded-lg border border-hairline bg-white px-3.5 py-2.5 text-sm text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch transition-all"
                    ></textarea>
                    @error('bio') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Navigation -->
                <div class="flex items-center justify-between pt-3">
                    <button 
                        wire:click="prevStep" 
                        type="button" 
                        class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-hairline bg-white px-5 py-2.5 text-sm font-semibold text-ink hover:bg-surface-soft transition-colors"
                    >
                        <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                        <span>{{ __('Back') }}</span>
                    </button>
                    <button 
                        wire:click="nextStep" 
                        type="button" 
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-rausch px-7 py-3 text-sm font-semibold text-white shadow-airbnb transition-colors hover:bg-rausch-active"
                    >
                        <span>{{ __('Continue to Legality (Step 3)') }}</span>
                        <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- STEP 3: Legality (Tier 2 KYC)              -->
        <!-- ========================================== -->
        @if ($currentStep === 3)
            <div class="flex flex-col gap-6 animate-fade-in">
                <div class="border-b border-hairline pb-3">
                    <h2 class="text-base font-semibold text-ink flex items-center gap-2">
                        <span class="flex size-6 items-center justify-center rounded-full bg-rausch/10 text-xs font-bold text-rausch">3</span>
                        {{ __('Step 3 · Official Licenses & Certifications') }}
                    </h2>
                    <p class="text-xs text-muted mt-1">{{ __('Bali provincial regulations require verified HPI licensing to ensure tourist safety and service quality.') }}</p>
                </div>

                <!-- KTPP (HPI License Number & Expiry) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label for="ktpp_number" class="block text-sm font-semibold text-ink">{{ __('KTPP (Official HPI License) Number') }} <span class="text-rausch">*</span></label>
                        <input 
                            id="ktpp_number"
                            wire:model.blur="ktpp_number" 
                            type="text" 
                            placeholder="e.g. HPI-BALI-12345" 
                            required 
                            class="w-full rounded-lg border border-hairline bg-white px-3.5 py-2.5 text-sm text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch transition-all"
                        />
                        @error('ktpp_number') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="ktpp_expired_at" class="block text-sm font-semibold text-ink">{{ __('KTPP License Expiry Date') }} <span class="text-rausch">*</span></label>
                        <input 
                            id="ktpp_expired_at"
                            wire:model.blur="ktpp_expired_at" 
                            type="date" 
                            required 
                            class="w-full rounded-lg border border-hairline bg-white px-3.5 py-2.5 text-sm text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch transition-all"
                        />
                        @error('ktpp_expired_at') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- KTPP Document Upload -->
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-ink">{{ __('KTPP License Document Scan') }} <span class="text-rausch">*</span></label>
                    <div class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-hairline p-5 bg-surface-soft hover:bg-white hover:border-rausch transition-all text-center group cursor-pointer">
                        <input type="file" wire:model="ktpp_file" id="ktpp_file" class="absolute inset-0 size-full opacity-0 cursor-pointer" accept="image/*,application/pdf" />
                        
                        <div wire:loading.remove wire:target="ktpp_file" class="flex flex-col items-center pointer-events-none">
                            <div class="flex size-10 items-center justify-center rounded-full bg-white shadow-airbnb text-muted-soft group-hover:text-rausch transition-colors mb-2">
                                <svg class="size-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                            </div>
                            <span class="text-xs font-semibold text-ink">{{ __('Upload KTPP Document Scan') }}</span>
                            <span class="text-[11px] text-muted-soft mt-0.5">{{ __('PDF, JPG, or PNG up to 5MB') }}</span>
                        </div>

                        <div wire:loading wire:target="ktpp_file" class="flex flex-col items-center gap-2 py-2">
                            <svg class="size-6 animate-spin text-rausch" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span class="text-xs text-muted font-medium">{{ __('Uploading document...') }}</span>
                        </div>
                    </div>

                    @if ($ktpp_file)
                        <div class="mt-2 flex items-center gap-2.5 rounded-lg border border-hairline bg-white p-2.5 shadow-airbnb">
                            <span class="flex size-8 items-center justify-center rounded bg-rausch/10 text-rausch font-bold text-xs">DOC</span>
                            <p class="truncate text-xs font-semibold text-ink flex-1">{{ $ktpp_file->getClientOriginalName() }}</p>
                            <span class="text-[10px] text-emerald-600 font-medium">✓ Uploaded</span>
                        </div>
                    @endif
                    @error('ktpp_file') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- SKCK Police Clearance Scan & Expiry -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- SKCK Upload -->
                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-ink">{{ __('SKCK Police Clearance Doc') }} <span class="text-rausch">*</span></label>
                        <div class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-hairline p-5 bg-surface-soft hover:bg-white hover:border-rausch transition-all text-center group cursor-pointer">
                            <input type="file" wire:model="skck_file" id="skck_file" class="absolute inset-0 size-full opacity-0 cursor-pointer" accept="image/*,application/pdf" />
                            
                            <div wire:loading.remove wire:target="skck_file" class="flex flex-col items-center pointer-events-none">
                                <div class="flex size-10 items-center justify-center rounded-full bg-white shadow-airbnb text-muted-soft group-hover:text-rausch transition-colors mb-2">
                                    <svg class="size-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                                </div>
                                <span class="text-xs font-semibold text-ink">{{ __('Upload SKCK Clearance') }}</span>
                                <span class="text-[11px] text-muted-soft mt-0.5">{{ __('PDF, JPG, PNG max 5MB') }}</span>
                            </div>

                            <div wire:loading wire:target="skck_file" class="flex flex-col items-center gap-2 py-2">
                                <svg class="size-6 animate-spin text-rausch" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span class="text-xs text-muted font-medium">{{ __('Uploading...') }}</span>
                            </div>
                        </div>

                        @if ($skck_file)
                            <div class="mt-2 flex items-center gap-2.5 rounded-lg border border-hairline bg-white p-2.5 shadow-airbnb">
                                <span class="flex size-8 items-center justify-center rounded bg-rausch/10 text-rausch font-bold text-xs">DOC</span>
                                <p class="truncate text-xs font-semibold text-ink flex-1">{{ $skck_file->getClientOriginalName() }}</p>
                                <span class="text-[10px] text-emerald-600 font-medium">✓ Uploaded</span>
                            </div>
                        @endif
                        @error('skck_file') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- SKCK Expiry Date -->
                    <div class="space-y-1.5">
                        <label for="skck_expired_at" class="block text-sm font-semibold text-ink">{{ __('SKCK Expiry Date') }} <span class="text-rausch">*</span></label>
                        <input 
                            id="skck_expired_at"
                            wire:model.blur="skck_expired_at" 
                            type="date" 
                            required 
                            class="w-full rounded-lg border border-hairline bg-white px-3.5 py-2.5 text-sm text-ink placeholder:text-muted-soft focus:outline-hidden focus:ring-2 focus:ring-rausch transition-all"
                        />
                        <p class="text-xs text-muted">{{ __('SKCK certificates are generally valid for 6 months.') }}</p>
                        @error('skck_expired_at') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Medical Fitness Certificate (Surat Sehat) -->
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-ink">{{ __('Medical Fitness Certificate (Surat Sehat) Scan') }} <span class="text-rausch">*</span></label>
                    <div class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-hairline p-5 bg-surface-soft hover:bg-white hover:border-rausch transition-all text-center group cursor-pointer">
                        <input type="file" wire:model="surat_sehat_file" id="surat_sehat_file" class="absolute inset-0 size-full opacity-0 cursor-pointer" accept="image/*,application/pdf" />
                        
                        <div wire:loading.remove wire:target="surat_sehat_file" class="flex flex-col items-center pointer-events-none">
                            <div class="flex size-10 items-center justify-center rounded-full bg-white shadow-airbnb text-muted-soft group-hover:text-rausch transition-colors mb-2">
                                <svg class="size-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            </div>
                            <span class="text-xs font-semibold text-ink">{{ __('Upload Surat Sehat Document Scan') }}</span>
                            <span class="text-[11px] text-muted-soft mt-0.5">{{ __('Issued by licensed doctor/clinic, max 5MB') }}</span>
                        </div>

                        <div wire:loading wire:target="surat_sehat_file" class="flex flex-col items-center gap-2 py-2">
                            <svg class="size-6 animate-spin text-rausch" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span class="text-xs text-muted font-medium">{{ __('Uploading...') }}</span>
                        </div>
                    </div>

                    @if ($surat_sehat_file)
                        <div class="mt-2 flex items-center gap-2.5 rounded-lg border border-hairline bg-white p-2.5 shadow-airbnb">
                            <span class="flex size-8 items-center justify-center rounded bg-rausch/10 text-rausch font-bold text-xs">DOC</span>
                            <p class="truncate text-xs font-semibold text-ink flex-1">{{ $surat_sehat_file->getClientOriginalName() }}</p>
                            <span class="text-[10px] text-emerald-600 font-medium">✓ Uploaded</span>
                        </div>
                    @endif
                    @error('surat_sehat_file') <span class="text-xs text-error-text mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Navigation -->
                <div class="flex items-center justify-between pt-3">
                    <button 
                        wire:click="prevStep" 
                        type="button" 
                        class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-hairline bg-white px-5 py-2.5 text-sm font-semibold text-ink hover:bg-surface-soft transition-colors"
                    >
                        <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                        <span>{{ __('Back') }}</span>
                    </button>
                    <button 
                        wire:click="nextStep" 
                        type="button" 
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-rausch px-7 py-3 text-sm font-semibold text-white shadow-airbnb transition-colors hover:bg-rausch-active"
                    >
                        <span>{{ __('Continue to SOP Agreement (Step 4)') }}</span>
                        <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- STEP 4: Digital SOP Agreement & Apply      -->
        <!-- ========================================== -->
        @if ($currentStep === 4)
            <div class="flex flex-col gap-6 animate-fade-in">
                <div class="border-b border-hairline pb-3">
                    <h2 class="text-base font-semibold text-ink flex items-center gap-2">
                        <span class="flex size-6 items-center justify-center rounded-full bg-rausch/10 text-xs font-bold text-rausch">4</span>
                        {{ __('Step 4 · Balinese Customary Tourism Quality SOP') }}
                    </h2>
                    <p class="text-xs text-muted mt-1">{{ __('Review and digitally sign the ethical conduct agreement in accordance with Bali regional regulations.') }}</p>
                </div>

                <!-- Bali Code of Ethics SOP Agreement Box -->
                <div class="rounded-2xl border border-hairline bg-surface-soft p-6 space-y-4">
                    <div class="flex items-center gap-2.5 pb-2 border-b border-hairline">
                        <span class="flex size-8 items-center justify-center rounded-full bg-rausch text-white">
                            <svg class="size-4 stroke-current stroke-2" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z"/></svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-ink">{{ __('Standard Operating Procedure & Guiding Ethics') }}</h3>
                            <span class="text-[11px] text-muted">{{ __('In compliance with Bali Governor Regulation No. 5 of 2020') }}</span>
                        </div>
                    </div>
                    
                    <div class="text-xs text-body space-y-3 leading-relaxed max-h-[220px] overflow-y-auto pr-1">
                        <p class="font-medium text-ink">
                            {{ __('All certified tour guides registered on BaliGuide must uphold the highest standards of cultural respect, guest hospitality, and ecological preservation:') }}
                        </p>
                        <ul class="space-y-2 pl-1">
                            <li class="flex items-start gap-2.5">
                                <span class="text-base shrink-0 mt-[-2px]">🛕</span>
                                <span><strong>{{ __('Sanctity of Holy Sites') }}:</strong> {{ __('Respect holy temples, sacred precincts, and traditional ceremonial rituals (adat). Ensure tourists wear appropriate attire (kamen & selendang) before entering.') }}</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-base shrink-0 mt-[-2px]">🌿</span>
                                <span><strong>{{ __('Eco-Friendly Tourism') }}:</strong> {{ __('Promote zero single-use plastics, cleanliness in nature reserves, and support sustainable local village economies.') }}</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-base shrink-0 mt-[-2px]">🤝</span>
                                <span><strong>{{ __('Truthful Cultural Narration') }}:</strong> {{ __('Convey Balinese customs, arts, and philosophy (Tri Hita Karana) with authentic knowledge and dignity.') }}</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-base shrink-0 mt-[-2px]">⚖️</span>
                                <span><strong>{{ __('Pricing Transparency & Escrow') }}:</strong> {{ __('Honor quoted booking rates and escrow transactions without demanding off-platform unauthorized surcharges.') }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-3 border-t border-hairline">
                        <label class="flex items-start gap-3 cursor-pointer select-none p-3 rounded-xl border border-hairline bg-white hover:border-rausch transition-all">
                            <input 
                                type="checkbox" 
                                wire:model="signed_sop" 
                                class="size-4 mt-0.5 rounded border-hairline text-rausch focus:ring-rausch focus:ring-2"
                                required
                            />
                            <span class="text-xs font-semibold text-ink leading-snug">
                                {{ __('I hereby digitally sign and agree to strictly abide by the Bali Customary Quality Tourism SOP, regional regulations, and platform code of conduct.') }}
                            </span>
                        </label>
                        @error('signed_sop') <span class="text-xs text-error-text mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Navigation & Final Submit -->
                <div class="flex items-center justify-between pt-3">
                    <button 
                        wire:click="prevStep" 
                        type="button" 
                        class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-hairline bg-white px-5 py-2.5 text-sm font-semibold text-ink hover:bg-surface-soft transition-colors"
                    >
                        <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                        <span>{{ __('Back') }}</span>
                    </button>
                    <button 
                        wire:click="register" 
                        type="button" 
                        wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-rausch px-8 py-3 text-sm font-semibold text-white shadow-airbnb transition-colors hover:bg-rausch-active disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="register">{{ __('Submit Guide Application') }}</span>
                        <span wire:loading wire:target="register" class="flex items-center gap-2">
                            <svg class="size-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            {{ __('Processing application...') }}
                        </span>
                        <svg wire:loading.remove wire:target="register" class="size-4 stroke-current stroke-[2.5]" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Login Link footer --}}
    <div class="text-center text-sm text-muted mt-2">
        <span>{{ __('Already have an account?') }}</span>
        <a href="{{ route('login') }}" wire:navigate class="font-semibold text-rausch hover:text-rausch-active transition-colors">{{ __('Log in') }}</a>
    </div>
</div>
