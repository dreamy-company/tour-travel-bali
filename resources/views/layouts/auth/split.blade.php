@php
    $isGuideAuth = request()->routeIs('register.guide');
    $containerWidth = $isGuideAuth ? 'max-w-xl xl:max-w-2xl' : ($maxWidth ?? 'max-w-md');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white text-ink antialiased">
        <div class="grid min-h-svh lg:h-screen lg:grid-cols-2 lg:overflow-hidden">
            {{-- LEFT: Branded Bali panel (Fixed on desktop) --}}
            <div class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-gradient-to-br from-rausch via-[#d90b45] to-[#7d0b2c] p-10 xl:p-14 text-white lg:h-full">
                {{-- Decorative layers --}}
                <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:20px_20px]"></div>
                <div class="absolute -top-28 -right-20 size-96 rounded-full bg-white/10 blur-3xl"></div>
                <div class="absolute -bottom-36 -left-20 size-96 rounded-full bg-black/20 blur-3xl"></div>

                {{-- Brand --}}
                <a href="{{ route('home') }}" wire:navigate class="relative z-20 flex w-fit items-center gap-2.5">
                    <span class="flex aspect-square size-10 items-center justify-center rounded-xl border border-white/20 bg-white/15 backdrop-blur">
                        <x-app-logo-icon class="size-5 fill-current" />
                    </span>
                    <span class="text-lg font-semibold tracking-[-0.3px]">BaliGuide</span>
                </a>

                {{-- Headline + value props --}}
                <div class="relative z-20 max-w-md space-y-9">
                    @if ($isGuideAuth)
                        <h2 class="text-3xl xl:text-4xl font-bold leading-[1.15] tracking-[-0.8px]">
                            {{ __('Jadilah pemandu resmi Bali dan raih wisatawan') }}
                            <span class="underline decoration-white/40 decoration-4 underline-offset-8">{{ __('sefrekuensi') }}</span>
                            {{ __('denganmu.') }}
                        </h2>

                        <ul class="space-y-4">
                            @foreach ([
                                ['d' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', 'text' => __('Verifikasi resmi lisensi HPI & identitas terpercaya')],
                                ['d' => 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', 'text' => __('Pembayaran aman dengan sistem escrow otomatis')],
                                ['d' => 'M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0 3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z', 'text' => __('Pencocokan gaya komunikasi & zodiak dengan tamu')],
                                ['d' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5', 'text' => __('Atur tarif harian dan paket tur kustom secara mandiri')],
                            ] as $prop)
                                <li class="flex items-start gap-3">
                                    <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-white/15">
                                        <svg class="size-3.5 fill-none stroke-current" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $prop['d'] }}"/></svg>
                                    </span>
                                    <span class="text-sm leading-relaxed text-white/90">{{ $prop['text'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <h2 class="text-3xl xl:text-4xl font-bold leading-[1.15] tracking-[-0.8px]">
                            {{ __('Temukan pemandu lokal Bali yang') }}
                            <span class="underline decoration-white/40 decoration-4 underline-offset-8">{{ __('sefrekuensi') }}</span>
                            {{ __('denganmu.') }}
                        </h2>

                        <ul class="space-y-4">
                            @foreach ([
                                ['d' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', 'text' => __('Verified HPI/KTPP licensed local guides')],
                                ['d' => 'M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z', 'text' => __('Chat before booking to craft your itinerary')],
                                ['d' => 'M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z', 'text' => __('Secure escrow — funds release after the tour')],
                                ['d' => 'M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0 3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z', 'text' => __('Zodiac sign matching — find your cosmic guide')],
                            ] as $prop)
                                <li class="flex items-start gap-3">
                                    <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-white/15">
                                        <svg class="size-3.5 fill-none stroke-current" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $prop['d'] }}"/></svg>
                                    </span>
                                    <span class="text-sm leading-relaxed text-white/90">{{ $prop['text'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- Testimonial --}}
                <div class="relative z-20 max-w-md space-y-3 border-t border-white/20 pt-6">
                    @if ($isGuideAuth)
                        <p class="text-sm italic leading-relaxed text-white/90">
                            &ldquo;{{ __('Bergabung dengan BaliGuide membuat tamu internasional lebih percaya karena lisensi HPI saya terverifikasi. Pembayaran escrow selalu tepat waktu!') }}&rdquo;
                        </p>
                        <p class="text-xs font-semibold text-white/70">{{ __('Made Sukarma · Pemandu Wisata Resmi HPI, Ubud') }}</p>
                    @else
                        <p class="text-sm italic leading-relaxed text-white/90">
                            &ldquo;{{ __('Wayan was the perfect match — same energy, same love for sunsets. The escrow payment made the whole trip completely stress-free.') }}&rdquo;
                        </p>
                        <p class="text-xs font-semibold text-white/70">{{ __('Sarah · Solo traveler, Singapore') }}</p>
                    @endif
                </div>
            </div>

            {{-- RIGHT: Auth form (Independent scrolling on desktop) --}}
            <div class="flex min-h-screen lg:min-h-0 lg:h-full items-start lg:items-center justify-center px-6 py-10 sm:px-10 overflow-y-auto">
                <div class="flex w-full {{ $containerWidth }} flex-col my-auto">
                    {{-- Mobile brand --}}
                    <a href="{{ route('home') }}" wire:navigate class="mb-8 flex items-center justify-center gap-2 lg:hidden">
                        <span class="flex aspect-square size-10 items-center justify-center rounded-xl bg-rausch text-white shadow-airbnb">
                            <x-app-logo-icon class="size-5 fill-current" />
                        </span>
                        <span class="text-lg font-semibold tracking-[-0.3px] text-ink">BaliGuide</span>
                    </a>

                    <div class="flex flex-col gap-6">
                        {{ $slot }}
                    </div>

                    <p class="mt-10 text-center text-xs text-muted-soft">
                        © {{ date('Y') }} BaliGuide · {{ __('Verified local tour guides in Bali') }}
                    </p>
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
