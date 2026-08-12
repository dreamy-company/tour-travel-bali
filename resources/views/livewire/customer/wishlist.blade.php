<div class="space-y-6">
    @if (session()->has('success'))
        <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 flex items-center justify-between" role="alert">
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 flex items-center justify-between" role="alert">
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-medium tracking-[-0.44px] text-ink">{{ __('My Favorites') }}</h1>
            <p class="text-sm text-muted mt-1">{{ __('Guides you have saved for later.') }}</p>
        </div>
        <a href="{{ route('guides.index') }}" wire:navigate class="inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active transition-colors self-start sm:self-auto">
            {{ __('Discover More Guides') }}
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($this->favoriteGuides as $guide)
            @php $profile = $guide->guideProfile; @endphp
            <div class="flex flex-col gap-3">
                {{-- Photo plate --}}
                <a href="{{ route('guides.show', $profile) }}" wire:navigate class="group relative block aspect-[4/3] overflow-hidden rounded-[14px] bg-surface-soft border border-hairline">
                    @if ($profile->headshot)
                        <img src="{{ route('guides.photo', $profile) }}" alt="{{ $guide->name }}" class="absolute inset-0 h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" />
                    @else
                        <div class="absolute inset-0 bg-gradient-to-br from-surface-strong via-surface-soft to-white flex items-center justify-center">
                            <span class="flex size-20 items-center justify-center rounded-full bg-white text-2xl font-semibold text-ink shadow-airbnb">{{ $guide->initials() }}</span>
                        </div>
                    @endif
                    <span class="absolute top-3 left-3 inline-flex items-center rounded-full bg-white/95 px-2.5 py-1 text-xs font-semibold text-ink shadow-airbnb">
                        <svg class="size-3.5 fill-current text-rausch mr-1" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg>
                        {{ __('HPI Verified') }}
                    </span>
                    <span class="absolute top-3 right-3 flex size-9 items-center justify-center rounded-full bg-rausch text-white shadow-airbnb">
                        <svg class="size-4 fill-current" viewBox="0 0 24 24"><path d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z"/></svg>
                    </span>
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

                    <p class="text-sm text-ink"><span class="font-semibold">Rp {{ number_format($profile->base_rate, 0, ',', '.') }}</span> <span class="text-muted text-xs">/ {{ $profile->tariff_mode->value }}</span></p>

                    <div class="flex gap-2 pt-1.5">
                        <a href="{{ route('guides.show', $profile) }}" wire:navigate class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active transition-colors">
                            {{ __('View Profile') }}
                        </a>
                        <button type="button" wire:click="removeFavorite({{ $guide->id }})" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-lg border border-hairline text-ink hover:bg-surface-soft transition-colors">
                            <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            {{ __('Remove') }}
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full border border-dashed border-hairline rounded-[14px] p-16 text-center flex flex-col items-center gap-4">
                <div class="flex size-16 items-center justify-center rounded-full bg-surface-soft text-muted">
                    <svg class="size-8 fill-current" viewBox="0 0 24 24"><path d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z"/></svg>
                </div>
                <div class="space-y-1">
                    <h3 class="text-lg font-medium text-ink">{{ __('No Favorites Yet') }}</h3>
                    <p class="text-sm text-muted max-w-sm">{{ __('Tap the heart on any guide card to save them here for later.') }}</p>
                </div>
                <a href="{{ route('guides.index') }}" wire:navigate class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active transition-colors">
                    {{ __('Browse Guides') }}
                </a>
            </div>
        @endforelse
    </div>
</div>
