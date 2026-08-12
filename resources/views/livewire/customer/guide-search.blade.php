<div>
    <!-- LEFT: Sidebar Filters -->
    <aside class="lg:w-72 shrink-0">
        <div class="lg:sticky lg:top-24 border border-hairline rounded-[14px] bg-white p-6 flex flex-col gap-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold tracking-[-0.3px] text-ink">{{ __('Filters') }}</h3>
                    <p class="text-sm text-muted mt-0.5">{{ __('Match with guides of the same interest and vibe.') }}</p>
                </div>
                @if ($selectedSpecializations || $communicationStyle !== '' || $minPrice !== '' || $maxPrice !== '' || $tariffMode !== '' || $selectedLanguages)
                    <button type="button" wire:click="resetFilters" class="text-sm font-semibold text-rausch hover:text-rausch-active underline underline-offset-4">
                        {{ __('Clear') }}
                    </button>
                @endif
            </div>

            <hr class="border-hairline" />

            {{-- 1. Activity Specializations --}}
            <div class="space-y-2.5">
                <label class="text-sm font-semibold text-ink block">{{ __('Specializations') }}</label>
                <div class="flex flex-col gap-2.5">
                    @foreach ([['value' => 'cafe_hopping', 'label' => 'Kafe'], ['value' => 'nature', 'label' => 'Alam'], ['value' => 'nightlife', 'label' => 'Nightlife'], ['value' => 'photography', 'label' => 'Fotografi'], ['value' => 'culture_history', 'label' => 'Budaya/Sejarah'], ['value' => 'healing', 'label' => 'Healing/Santai']] as $specOption)
                        <label class="inline-flex items-center gap-2.5 text-sm text-body cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model.live="selectedSpecializations"
                                value="{{ $specOption['value'] }}"
                                class="size-4 rounded border-hairline text-rausch focus:ring-rausch focus:ring-2"
                            />
                            <span>{{ $specOption['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <hr class="border-hairline" />

            {{-- 2. Communication Style --}}
            <div class="space-y-2.5">
                <label class="text-sm font-semibold text-ink block">{{ __('Communication Style') }}</label>
                <select
                    wire:model.live="communicationStyle"
                    class="w-full text-sm px-3.5 py-2.5 rounded-full border border-hairline bg-white text-ink focus:outline-hidden focus:ring-2 focus:ring-rausch"
                >
                    <option value="">{{ __('Any vibe') }}</option>
                    @foreach (\App\Enums\CommunicationStyle::cases() as $style)
                        <option value="{{ $style->value }}">{{ ucfirst($style->value) }}</option>
                    @endforeach
                </select>
            </div>

            <hr class="border-hairline" />

            {{-- 3. Tariff Range + Mode Toggle --}}
            <div class="space-y-3">
                <label class="text-sm font-semibold text-ink block">{{ __('Tariff') }}</label>
                <div class="grid grid-cols-2 gap-2">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-muted-soft">Rp</span>
                        <input
                            wire:model.live.debounce.400ms="minPrice"
                            type="number"
                            min="0"
                            placeholder="Min"
                            class="w-full text-sm pl-8 pr-2 py-2 rounded-full border border-hairline bg-white text-ink focus:outline-hidden focus:ring-2 focus:ring-rausch"
                        />
                    </div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-muted-soft">Rp</span>
                        <input
                            wire:model.live.debounce.400ms="maxPrice"
                            type="number"
                            min="0"
                            placeholder="Max"
                            class="w-full text-sm pl-8 pr-2 py-2 rounded-full border border-hairline bg-white text-ink focus:outline-hidden focus:ring-2 focus:ring-rausch"
                        />
                    </div>
                </div>

                {{-- Mode toggle: Hourly / Daily --}}
                <div class="grid grid-cols-2 gap-1 p-1 rounded-full bg-surface-soft">
                    <button
                        type="button"
                        wire:click="$set('tariffMode', '')"
                        class="rounded-full px-3 py-1.5 text-sm font-medium transition-colors {{ $tariffMode === '' ? 'bg-white text-ink shadow-airbnb' : 'text-muted hover:text-ink' }}"
                    >
                        {{ __('Any') }}
                    </button>
                    <button
                        type="button"
                        wire:click="$set('tariffMode', 'hourly')"
                        class="rounded-full px-3 py-1.5 text-sm font-medium transition-colors {{ $tariffMode === 'hourly' ? 'bg-white text-ink shadow-airbnb' : 'text-muted hover:text-ink' }}"
                    >
                        {{ __('Hourly') }}
                    </button>
                    <button
                        type="button"
                        wire:click="$set('tariffMode', 'daily')"
                        class="rounded-full px-3 py-1.5 text-sm font-medium transition-colors {{ $tariffMode === 'daily' ? 'bg-white text-ink shadow-airbnb' : 'text-muted hover:text-ink' }}"
                    >
                        {{ __('Daily') }}
                    </button>
                </div>
            </div>

            <hr class="border-hairline" />

            {{-- 4. Languages --}}
            <div class="space-y-2.5">
                <label class="text-sm font-semibold text-ink block">{{ __('Languages') }}</label>
                <div class="flex flex-col gap-2.5">
                    @foreach (['id' => 'Indonesian', 'en' => 'English', 'jp' => 'Japanese'] as $code => $label)
                        <label class="inline-flex items-center gap-2.5 text-sm text-body cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model.live="selectedLanguages"
                                value="{{ $code }}"
                                class="size-4 rounded border-hairline text-rausch focus:ring-rausch focus:ring-2"
                            />
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </aside>

    <!-- RIGHT: Guide Grid -->
    <div class="flex-1 min-w-0">
        <div class="flex items-end justify-between mb-5">
            <div>
                <h2 class="text-2xl font-medium tracking-[-0.44px] text-ink">{{ __('Verified Tour Guides') }}</h2>
                <p class="text-sm text-muted mt-1">
                    {{ trans_choice(':count verified local guide|:count verified local guides', $this->guides->count()) }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse ($this->guides as $guide)
                @php $profile = $guide->guideProfile; @endphp
                <div class="flex flex-col gap-3">
                    {{-- Photo plate --}}
                    <a href="{{ route('guides.show', $profile) }}" wire:navigate class="group relative block aspect-[4/3] overflow-hidden rounded-[14px] bg-surface-soft border border-hairline">
                        @if ($profile->headshot)
                            <img
                                src="{{ route('guides.photo', $profile) }}"
                                alt="{{ $guide->name }}"
                                class="absolute inset-0 h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            />
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-surface-strong via-surface-soft to-white flex items-center justify-center">
                                <span class="flex size-20 items-center justify-center rounded-full bg-white text-2xl font-semibold text-ink shadow-airbnb">{{ $guide->initials() }}</span>
                            </div>
                        @endif

                        {{-- Verified HPI/KTPP badge --}}
                        <div class="absolute top-3 left-3 inline-flex items-center rounded-full bg-white/95 px-2.5 py-1 text-xs font-semibold text-ink shadow-airbnb">
                            <svg class="size-3.5 fill-current text-rausch mr-1" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg>
                            {{ __('HPI Verified') }}
                        </div>
                    </a>

                    {{-- Meta --}}
                    <div class="flex flex-col gap-1.5 px-0.5">
                        <div class="flex items-start justify-between gap-3">
                            <a href="{{ route('guides.show', $profile) }}" wire:navigate class="text-base font-semibold text-ink hover:text-rausch transition-colors truncate">{{ $guide->name }}</a>
                            <span class="flex items-center gap-1 text-sm font-semibold text-ink shrink-0">
                                <svg class="size-3.5 fill-current text-star" viewBox="0 0 20 20"><path d="M10.868 2.784a.75.75 0 0 0-1.736 0l-1.87 3.79-4.183.608a.75.75 0 0 0-.416 1.28l3.028 2.951-.715 4.167a.75.75 0 0 0 1.09.79l3.74-1.966 3.74 1.966a.75.75 0 0 0 1.09-.79l-.715-4.167 3.028-2.951a.75.75 0 0 0-.416-1.28l-4.183-.608-1.87-3.79Z"/></svg>
                                {{ $guide->guide_reviews_avg_rating ? number_format($guide->guide_reviews_avg_rating, 2) : '—' }}
                            </span>
                        </div>

                        {{-- Specialization tags --}}
                        <div class="flex flex-wrap gap-1.5">
                            @foreach (\App\Enums\Specialization::cases() as $spec)
                                @if (in_array($spec->value, $profile->specializations ?? [], true))
                                    <span class="inline-flex items-center rounded-full bg-surface-soft px-2.5 py-1 text-xs font-medium text-ink">{{ $spec->label() }}</span>
                                @endif
                            @endforeach
                        </div>

                        {{-- Base rate + View Profile --}}
                        <div class="flex items-center justify-between pt-1 border-t border-hairline-soft mt-1">
                            <span class="text-sm text-ink">
                                <span class="font-semibold">Rp {{ number_format($profile->base_rate, 0, ',', '.') }}</span>
                                <span class="text-muted text-xs">/{{ $profile->tariff_mode->value }}</span>
                            </span>
                            <a
                                href="{{ route('guides.show', $profile) }}"
                                wire:navigate
                                class="inline-flex items-center gap-1 px-4 py-2 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active transition-colors"
                            >
                                {{ __('View Profile') }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20 border border-dashed border-hairline rounded-[14px] flex flex-col items-center justify-center">
                    <svg class="size-12 text-muted-soft mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.978 11.978 0 0 1 12 20.25a11.978 11.978 0 0 1-3-1.013v-.11c0-1.109.326-2.14.887-3M9 19.128c-.89-.13-1.748-.415-2.522-.823a4.122 4.122 0 0 0-4.321 6.326 9.302 9.302 0 0 0 3.738-2.316m3.105-3.32a4.125 4.125 0 0 1 7.533-2.493M9 16.058v-.003c0-1.113.285-2.16.786-3.07M9 16.058A9 9 0 0 0 2.25 15M12 5.25a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm7.5 7.5a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5ZM4.5 12.75a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z"/></svg>
                    <h3 class="text-base font-semibold text-ink">{{ __('No Guides Found') }}</h3>
                    <p class="text-sm text-muted max-w-xs mt-1">{{ __('We could not find any verified tour guides matching your active filters. Try widening your criteria.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
