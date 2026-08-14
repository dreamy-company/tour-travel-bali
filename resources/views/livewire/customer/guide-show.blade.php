<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- LEFT: Main content -->
    <div class="lg:col-span-8 min-w-0 flex flex-col gap-8">
        {{-- Breadcrumb --}}
        <a href="{{ route('guides.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-sm font-medium text-muted hover:text-ink transition-colors w-fit">
            <svg class="size-4 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg>
            {{ __('Back to all guides') }}
        </a>

        {{-- Header & Bio --}}
        <section class="border border-hairline rounded-[14px] bg-white p-6 sm:p-8 flex flex-col gap-6">
            <div class="flex flex-col sm:flex-row gap-6 sm:items-start">
                {{-- Headshot --}}
                <div class="shrink-0">
                    @if ($profile->headshot)
                        <img src="{{ route('guides.photo', $profile) }}" alt="{{ $profile->user->name }}" class="size-28 sm:size-32 rounded-full object-cover border-4 border-white shadow-airbnb" />
                    @else
                        <div class="flex size-28 sm:size-32 items-center justify-center rounded-full bg-surface-soft border border-hairline text-3xl font-semibold text-ink">
                            {{ $profile->user->initials() }}
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0 space-y-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl font-medium tracking-[-0.44px] text-ink leading-tight">{{ $profile->user->name }}</h1>
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/10">
                            <svg class="size-3.5 fill-current mr-1" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg>
                            {{ __('Verified') }} · HPI/KTPP {{ $profile->ktpp_number }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm">
                        <span class="flex items-center gap-1.5 text-ink font-medium">
                            <svg class="size-4 fill-current text-star" viewBox="0 0 20 20"><path d="M10.868 2.784a.75.75 0 0 0-1.736 0l-1.87 3.79-4.183.608a.75.75 0 0 0-.416 1.28l3.028 2.951-.715 4.167a.75.75 0 0 0 1.09.79l3.74-1.966 3.74 1.966a.75.75 0 0 0 1.09-.79l-.715-4.167 3.028-2.951a.75.75 0 0 0-.416-1.28l-4.183-.608-1.87-3.79Z"/></svg>
                            {{ $this->averageRating ? number_format($this->averageRating, 2) : 'New' }}
                            <span class="text-muted font-normal">({{ $this->reviews->count() }} {{ __('reviews') }})</span>
                        </span>
                        <span class="text-muted flex items-center gap-1.5">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m-18.432 0A8.959 8.959 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/></svg>
                            {{ $profile->communication_style?->label() ?? '—' }}
                        </span>
                        @if ($profile->user->zodiac() !== null)
                            @php $guideSign = $profile->user->zodiac(); @endphp
                            <span class="text-muted flex items-center gap-1.5" title="{{ ucfirst($guideSign->element()) }} element">
                                <span class="text-base leading-none">{{ $guideSign->symbol() }}</span>
                                {{ $guideSign->label() }}
                                <span class="text-xs">{{ $guideSign->elementEmoji() }}</span>
                            </span>
                        @endif
                        <span class="text-muted flex items-center gap-1.5">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                            {{ $profile->tariff_mode->value === 'hourly' ? 'Rp ' . number_format($profile->base_rate, 0, ',', '.') . ' / hour' : 'Rp ' . number_format($profile->base_rate, 0, ',', '.') . ' / day' }}
                        </span>
                    </div>

                    {{-- Languages --}}
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($profile->languages ?? [] as $lang)
                            <span class="inline-flex items-center rounded-full bg-surface-soft px-2.5 py-1 text-xs font-medium text-ink">{{ $lang }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Bio --}}
            <hr class="border-hairline" />
            <div class="space-y-2">
                <h2 class="text-sm font-semibold text-ink uppercase tracking-wider text-muted-soft">{{ __('About') }}</h2>
                <p class="text-sm sm:text-base text-body leading-relaxed whitespace-pre-line">{{ $profile->bio ?: __('This guide has not written a biography yet.') }}</p>
            </div>
        </section>

        {{-- Services & Packages --}}
        <section class="space-y-4">
            <h2 class="text-xl font-medium tracking-[-0.3px] text-ink">{{ __('Services & Packages') }}</h2>
            @forelse ($this->packages as $package)
                <div class="border border-hairline rounded-[14px] bg-white p-6 flex flex-col sm:flex-row sm:items-center gap-4 justify-between">
                    <div class="space-y-2 min-w-0">
                        <h3 class="text-base font-semibold text-ink">{{ $package->title }}</h3>
                        <p class="text-sm text-body leading-relaxed">{{ $package->description }}</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($package->destinations ?? [] as $dest)
                                <span class="inline-flex items-center rounded-full bg-surface-soft px-2.5 py-1 text-xs font-medium text-ink">{{ $dest }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="shrink-0 flex items-center gap-4">
                        <span class="text-lg font-semibold text-ink whitespace-nowrap">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                        <a
                            href="{{ route('guides.book', ['guideProfile' => $profile, 'package' => $package->id]) }}"
                            wire:navigate
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg border border-hairline text-ink hover:bg-surface-soft transition-colors"
                        >
                            {{ __('Select') }}
                        </a>
                    </div>
                </div>
            @empty
                <div class="border border-dashed border-hairline rounded-[14px] p-8 text-center text-sm text-muted">
                    {{ __('This guide offers custom itineraries instead of fixed packages.') }}
                </div>
            @endforelse
        </section>

        {{-- Reviews --}}
        <section class="space-y-4">
            <h2 class="text-xl font-medium tracking-[-0.3px] text-ink">{{ __('Reviews') }}</h2>
            @forelse ($this->reviews as $review)
                <div class="border border-hairline rounded-[14px] bg-white p-6 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="flex size-9 items-center justify-center rounded-full bg-surface-soft text-xs font-semibold text-ink">{{ $review->customer?->initials() ?? '?' }}</div>
                        <div>
                            <p class="text-sm font-semibold text-ink">{{ $review->customer?->name ?? __('Guest') }}</p>
                            <div class="flex items-center gap-0.5">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="size-3.5 {{ $i <= $review->rating ? 'fill-current text-star' : 'fill-current text-hairline' }}" viewBox="0 0 20 20"><path d="M10.868 2.784a.75.75 0 0 0-1.736 0l-1.87 3.79-4.183.608a.75.75 0 0 0-.416 1.28l3.028 2.951-.715 4.167a.75.75 0 0 0 1.09.79l3.74-1.966 3.74 1.966a.75.75 0 0 0 1.09-.79l-.715-4.167 3.028-2.951a.75.75 0 0 0-.416-1.28l-4.183-.608-1.87-3.79Z"/></svg>
                                @endfor
                            </div>
                        </div>
                        <span class="ml-auto text-xs text-muted-soft">{{ $review->created_at?->format('M Y') }}</span>
                    </div>
                    @if ($review->comment)
                        <p class="text-sm text-body leading-relaxed">{{ $review->comment }}</p>
                    @endif
                </div>
            @empty
                <div class="border border-dashed border-hairline rounded-[14px] p-8 text-center text-sm text-muted">
                    {{ __('No reviews yet — be the first traveler to book this guide.') }}
                </div>
            @endforelse
        </section>
    </div>

    <!-- RIGHT: Sticky CTA rail -->
    <aside class="lg:col-span-4">
        <div class="lg:sticky lg:top-24 flex flex-col gap-4">
            {{-- Book Now --}}
            <div class="border border-hairline rounded-[14px] bg-white p-6 shadow-airbnb space-y-4">
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-xs text-muted-soft uppercase tracking-wider font-semibold">{{ __('Base rate') }}</p>
                        <p class="text-2xl font-bold tracking-[-0.5px] text-ink">
                            Rp {{ number_format($profile->base_rate, 0, ',', '.') }}
                            <span class="text-sm font-medium text-muted">/ {{ $profile->tariff_mode->value }}</span>
                        </p>
                    </div>
                </div>
                <a
                    href="{{ route('guides.book', $profile) }}"
                    wire:navigate
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active transition-colors shadow-airbnb"
                >
                    {{ __('Book Now') }}
                </a>
                <p class="text-xs text-muted text-center leading-relaxed">
                    {{ __('Confirmation-first: your request is sent to the guide before any payment.') }}
                </p>
            </div>

            {{-- Zodiac compatibility (customer × guide) --}}
            @auth
                @php
                    $customerSign = auth()->user()->zodiac();
                    $guideSign = $profile->user->zodiac();
                    $zodiacScore = $customerSign !== null && $guideSign !== null ? $customerSign->compatibility($guideSign) : null;
                @endphp
                @if ($zodiacScore !== null)
                    <div class="border border-hairline rounded-[14px] bg-white p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-ink">✨ {{ __('Zodiac Match') }}</h3>
                            <span class="text-xs text-muted-soft">{{ \App\Services\ZodiacService::compatibilityLabel($zodiacScore) }}</span>
                        </div>
                        <div class="flex items-center justify-center gap-4 text-center">
                            <div class="space-y-1">
                                <p class="text-2xl leading-none">{{ $customerSign->symbol() }}</p>
                                <p class="text-xs font-semibold text-ink">{{ $customerSign->label() }}</p>
                                <p class="text-[10px] text-muted-soft uppercase tracking-wider">{{ __('You') }}</p>
                            </div>
                            <span class="text-2xl font-bold text-ink">{{ $zodiacScore }}%</span>
                            <div class="space-y-1">
                                <p class="text-2xl leading-none">{{ $guideSign->symbol() }}</p>
                                <p class="text-xs font-semibold text-ink">{{ $guideSign->label() }}</p>
                                <p class="text-[10px] text-muted-soft uppercase tracking-wider">{{ __('Guide') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endauth

            {{-- Pre-Booking Chat (FR-02-02) --}}
            <div class="space-y-2">
                <h3 class="text-sm font-semibold text-muted">{{ __('Pre-Booking Chat') }}</h3>
                @auth
                    @if (auth()->id() !== $profile->user_id)
                        @livewire('chat.chat-room', ['receiver' => $profile->user], key('prebooking-' . $profile->id))
                    @else
                        <div class="border border-hairline rounded-[14px] bg-surface-soft p-4 text-sm text-muted">{{ __('This is your own profile.') }}</div>
                    @endif
                @endauth
                @guest
                    <a href="{{ route('login') }}" wire:navigate class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium rounded-lg border border-hairline text-ink hover:bg-surface-soft transition-colors">
                        <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
                        {{ __('Log in to start chatting') }}
                    </a>
                @endguest
            </div>
        </div>
    </aside>
</div>
