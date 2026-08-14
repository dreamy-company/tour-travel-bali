<div class="flex flex-col gap-16 lg:gap-24">
    {{-- HERO --}}
    <section class="relative overflow-hidden rounded-[24px] border border-hairline">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-br from-rausch/90 via-rausch/70 to-[#b3203f]/80"></div>
            <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:20px_20px]"></div>
        </div>

        <div class="relative z-10 px-6 py-16 sm:px-12 sm:py-24 max-w-3xl">
            <span class="inline-flex items-center rounded-full bg-white/15 backdrop-blur px-3.5 py-1.5 text-xs font-semibold text-white mb-6">
                <svg class="size-3.5 fill-current mr-1.5" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg>
                Verified HPI/KTPP licensed local guides
            </span>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-[-1px] text-white leading-[1.05]">
                Temukan Pemandu Lokal Bali yang <span class="underline decoration-white/40 decoration-4 underline-offset-8">Sefrekuensi</span> &amp; Berlisensi
            </h1>

            <p class="mt-6 text-base sm:text-lg text-white/85 leading-relaxed max-w-xl">
                Match with verified local guides by your travel interest and communication vibe — then chat, book a custom itinerary, and pay securely through escrow.
            </p>

            <div class="mt-9 flex flex-wrap items-center gap-4">
                <a href="{{ route('guides.index') }}" wire:navigate class="inline-flex items-center gap-2.5 px-8 py-4 text-base font-semibold rounded-full bg-white text-[#e00b41] hover:bg-zinc-100 transition-colors shadow-airbnb">
                    <svg class="size-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    {{ __('Search Guides') }}
                </a>
                <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center gap-2 px-6 py-4 text-base font-semibold rounded-full text-white hover:bg-white/10 transition-colors">
                    {{ __('Create free account') }}
                    <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- QUICK MATCHING VIBE SELECTOR --}}
    <section class="space-y-6">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-2xl sm:text-3xl font-semibold tracking-[-0.44px] text-ink">{{ __('What is your vibe today?') }}</h2>
                <p class="text-sm text-muted mt-1.5">{{ __('Pick a travel mood and we will match you with guides of the same frequency.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach ($this->vibes() as $vibe)
                <a
                    href="{{ route('guides.index', ['vibe' => $vibe['key']]) }}"
                    wire:navigate
                    class="group flex flex-col gap-3 rounded-[14px] border border-hairline bg-white p-5 hover:border-rausch hover:shadow-airbnb transition-all"
                >
                    <span class="flex size-12 items-center justify-center rounded-full bg-surface-soft text-2xl group-hover:bg-rausch/10 transition-colors">{{ $vibe['emoji'] }}</span>
                    <div>
                        <h3 class="text-base font-semibold text-ink">{{ $vibe['label'] }}</h3>
                        <p class="text-xs text-muted leading-relaxed mt-0.5">{{ $vibe['blurb'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ZODIAC COSMIC MATCH --}}
    <section class="space-y-6">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-2xl sm:text-3xl font-semibold tracking-[-0.44px] text-ink">{{ __('What is your sign?') }}</h2>
                <p class="text-sm text-muted mt-1.5">{{ __('Match with guides by zodiac sign compatibility — a cosmic pairing for your Bali trip.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach (\App\Enums\ZodiacSign::cases() as $sign)
                <a
                    href="{{ route('guides.index', ['zodiac' => $sign->value]) }}"
                    wire:navigate
                    class="group flex flex-col items-center gap-1.5 rounded-[14px] border border-hairline bg-white p-4 text-center hover:border-rausch hover:shadow-airbnb transition-all"
                >
                    <span class="text-2xl leading-none group-hover:scale-110 transition-transform">{{ $sign->symbol() }}</span>
                    <span class="text-sm font-semibold text-ink">{{ $sign->label() }}</span>
                    <span class="text-[10px] text-muted-soft uppercase tracking-wider">{{ ucfirst($sign->element()) }}</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- FEATURED VERIFIED GUIDES --}}
    <section class="space-y-6">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-2xl sm:text-3xl font-semibold tracking-[-0.44px] text-ink">{{ __('Featured Verified Guides') }}</h2>
                <p class="text-sm text-muted mt-1.5">{{ __('Top-rated local guides, verified under Bali provincial standards.') }}</p>
            </div>
            <a href="{{ route('guides.index') }}" wire:navigate class="text-sm font-semibold text-ink underline underline-offset-4 hover:text-rausch transition-colors hidden sm:inline-flex">
                {{ __('View all') }}
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse ($this->featuredGuides as $guide)
                @php $profile = $guide->guideProfile; @endphp
                <a href="{{ route('guides.show', $profile) }}" wire:navigate class="group flex flex-col gap-3">
                    <div class="relative aspect-[4/3] overflow-hidden rounded-[14px] bg-surface-soft border border-hairline">
                        @if ($profile->headshot)
                            <img src="{{ route('guides.photo', $profile) }}" alt="{{ $guide->name }}" class="absolute inset-0 h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" />
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-surface-strong via-surface-soft to-white flex items-center justify-center">
                                <span class="flex size-16 items-center justify-center rounded-full bg-white text-xl font-semibold text-ink shadow-airbnb">{{ $guide->initials() }}</span>
                            </div>
                        @endif
                        <span class="absolute top-3 left-3 inline-flex items-center rounded-full bg-white/95 px-2.5 py-1 text-xs font-semibold text-ink shadow-airbnb">
                            <svg class="size-3.5 fill-current text-rausch mr-1" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg>
                            {{ __('HPI Verified') }}
                        </span>
                    </div>
                    <div class="flex flex-col gap-1 px-0.5">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="text-base font-semibold text-ink group-hover:text-rausch transition-colors truncate">{{ $guide->name }}</h3>
                            <span class="flex items-center gap-1 text-sm font-semibold text-ink shrink-0">
                                <svg class="size-3.5 fill-current text-star" viewBox="0 0 20 20"><path d="M10.868 2.784a.75.75 0 0 0-1.736 0l-1.87 3.79-4.183.608a.75.75 0 0 0-.416 1.28l3.028 2.951-.715 4.167a.75.75 0 0 0 1.09.79l3.74-1.966 3.74 1.966a.75.75 0 0 0 1.09-.79l-.715-4.167 3.028-2.951a.75.75 0 0 0-.416-1.28l-4.183-.608-1.87-3.79Z"/></svg>
                                {{ $guide->guide_reviews_avg_rating ? number_format($guide->guide_reviews_avg_rating, 2) : '—' }}
                            </span>
                        </div>
                        <p class="text-sm text-ink mt-0.5">
                            <span class="font-semibold">Rp {{ number_format($profile->base_rate, 0, ',', '.') }}</span>
                            <span class="text-muted text-xs">/ {{ $profile->tariff_mode->value }}</span>
                        </p>
                    </div>
                </a>
            @empty
                <p class="col-span-full text-sm text-muted">{{ __('Featured guides will appear here once guides are verified.') }}</p>
            @endforelse
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section class="space-y-8">
        <div class="text-center space-y-2">
            <h2 class="text-2xl sm:text-3xl font-semibold tracking-[-0.44px] text-ink">{{ __('How It Works') }}</h2>
            <p class="text-sm text-muted">{{ __('From matching to a completed tour in four simple steps.') }}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ([
                ['step' => '1', 'title' => 'Find Guide', 'text' => 'Filter verified local guides by specialization, vibe, tariff, and even zodiac sign.'],
                ['step' => '2', 'title' => 'Pre-Booking Chat', 'text' => 'Chat directly to align expectations and craft your custom itinerary.'],
                ['step' => '3', 'title' => 'Secure Escrow Pay', 'text' => 'Pay through escrow — funds are released to the guide only after the tour completes.'],
                ['step' => '4', 'title' => 'Start Tour', 'text' => 'Track your trip live and rate your guide after an unforgettable day.'],
            ] as $how)
                <div class="relative rounded-[14px] border border-hairline bg-white p-6 pt-8">
                    <span class="absolute -top-5 left-6 flex size-10 items-center justify-center rounded-full bg-rausch text-white font-bold text-base shadow-airbnb">{{ $how['step'] }}</span>
                    <h3 class="text-base font-semibold text-ink mt-2">{{ __($how['title']) }}</h3>
                    <p class="text-sm text-muted leading-relaxed mt-1.5">{{ __($how['text']) }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- TRUST BAND --}}
    <section class="rounded-[24px] bg-ink text-white px-8 py-12 flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="space-y-1.5 text-center sm:text-left">
            <h2 class="text-xl sm:text-2xl font-semibold tracking-[-0.3px]">{{ __('Ready to meet your perfect guide?') }}</h2>
            <p class="text-sm text-white/70">{{ __('Free to browse — only pay when your tour is confirmed.') }}</p>
        </div>
        <a href="{{ route('guides.index') }}" wire:navigate class="inline-flex items-center gap-2 px-8 py-4 text-base font-semibold rounded-full bg-rausch text-white hover:bg-rausch-active transition-colors shadow-airbnb shrink-0">
            {{ __('Start Matching') }}
        </a>
    </section>
</div>
