<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <title>
            {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
        </title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-white text-ink antialiased">
        {{-- Airbnb-style top navigation (top-nav token: 80px, white, hairline bottom) --}}
        <header class="sticky top-0 z-40 h-20 bg-white/95 dark:bg-zinc-900/95 backdrop-blur border-b border-hairline dark:border-zinc-800">
            <div class="mx-auto flex h-full max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                {{-- Brand --}}
                <a href="{{ auth()->check() ? route('dashboard') : route('guides.index') }}" wire:navigate class="flex items-center gap-2 shrink-0">
                    <span class="flex aspect-square size-9 items-center justify-center rounded-[10px] bg-rausch text-white">
                        <x-app-logo-icon class="size-5 fill-current" />
                    </span>
                    <span class="hidden sm:block text-lg font-semibold tracking-[-0.3px] text-ink dark:text-white">BaliGuide</span>
                </a>

                {{-- Center nav links --}}
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('dashboard') }}" wire:navigate class="px-4 py-2 text-sm font-semibold text-ink dark:text-zinc-200 hover:bg-surface-soft dark:hover:bg-zinc-800 rounded-full transition-colors {{ request()->routeIs('dashboard') ? 'text-ink dark:text-white' : 'text-muted dark:text-zinc-400' }}">
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('guides.index') }}" wire:navigate class="px-4 py-2 text-sm font-semibold hover:bg-surface-soft dark:hover:bg-zinc-800 rounded-full transition-colors {{ request()->routeIs('guides.index') ? 'text-ink dark:text-white' : 'text-muted dark:text-zinc-400' }}">
                        {{ __('Search Guides') }}
                    </a>
                    @auth
                        @if (auth()->user()->role === \App\Enums\UserRole::CUSTOMER || auth()->user()->role === \App\Enums\UserRole::GUIDE)
                            <a href="{{ route('dashboard') }}" wire:navigate class="px-4 py-2 text-sm font-semibold hover:bg-surface-soft dark:hover:bg-zinc-800 rounded-full transition-colors text-muted dark:text-zinc-400 {{ request()->routeIs('chat.*') ? 'text-ink dark:text-white' : '' }}">
                                {{ __('Messages') }}
                            </a>
                        @endif
                    @endauth
                </nav>

                {{-- Right: account / auth actions --}}
                <div class="flex items-center gap-2 shrink-0">
                    @auth
                        {{-- Desktop account dropdown --}}
                        <div class="hidden lg:block relative" x-data="{ open: false }" @click.outside="open = false">
                            <button type="button" @click="open = !open" class="flex items-center gap-2 rounded-full border border-hairline dark:border-zinc-700 p-1 pl-1.5 pr-3 hover:shadow-airbnb transition-shadow">
                                <svg class="size-4 text-muted" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                                <span class="flex size-7 items-center justify-center rounded-full bg-rausch text-xs font-semibold text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </button>
                            <div x-show="open" x-transition.opacity x-cloak class="absolute right-0 mt-2 w-60 rounded-[14px] border border-hairline dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2 shadow-airbnb">
                                <div class="px-3 py-2 border-b border-hairline-soft dark:border-zinc-800">
                                    <p class="text-sm font-semibold text-ink dark:text-white truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-muted truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" wire:navigate class="mt-1 flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-ink dark:text-zinc-200 hover:bg-surface-soft dark:hover:bg-zinc-800 transition-colors">
                                    {{ __('Settings') }}
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-ink dark:text-zinc-200 hover:bg-surface-soft dark:hover:bg-zinc-800 transition-colors">
                                        {{ __('Log out') }}
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Mobile: avatar links to dashboard --}}
                        <a href="{{ route('dashboard') }}" wire:navigate class="lg:hidden flex size-9 items-center justify-center rounded-full bg-rausch text-xs font-semibold text-white">
                            {{ auth()->user()->initials() }}
                        </a>

                        {{-- Mobile hamburger --}}
                        <button type="button" class="md:hidden flex size-9 items-center justify-center rounded-full border border-hairline dark:border-zinc-700 text-ink dark:text-zinc-200" x-data @click="$store.customerNav.toggle()">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                        </button>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-medium text-ink dark:text-zinc-200 hover:bg-surface-soft dark:hover:bg-zinc-800 rounded-full transition-colors">
                            {{ __('Log in') }}
                        </a>
                        <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center px-5 py-2.5 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active transition-colors shadow-airbnb">
                            {{ __('Sign up') }}
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        {{-- Mobile nav sheet --}}
        <div x-data x-show="$store.customerNav.open" x-cloak x-transition.opacity @keydown.escape.window="$store.customerNav.close()" class="fixed inset-0 z-50 md:hidden">
            <div class="absolute inset-0 bg-black/50" @click="$store.customerNav.close()"></div>
            <div class="absolute right-0 top-0 h-full w-72 bg-white dark:bg-zinc-900 shadow-airbnb p-5 flex flex-col gap-1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                <div class="flex items-center justify-between pb-4 border-b border-hairline-soft dark:border-zinc-800">
                    <span class="flex items-center gap-2">
                        <span class="flex aspect-square size-8 items-center justify-center rounded-lg bg-rausch text-white">
                            <x-app-logo-icon class="size-4 fill-current" />
                        </span>
                        <span class="font-semibold text-ink dark:text-white">BaliGuide</span>
                    </span>
                    <button type="button" @click="$store.customerNav.close()" class="flex size-8 items-center justify-center rounded-full hover:bg-surface-soft dark:hover:bg-zinc-800 text-muted">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <a href="{{ route('dashboard') }}" wire:navigate @click="$store.customerNav.close()" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-ink dark:text-zinc-200 hover:bg-surface-soft dark:hover:bg-zinc-800 transition-colors">
                    {{ __('Dashboard') }}
                </a>
                <a href="{{ route('guides.index') }}" wire:navigate @click="$store.customerNav.close()" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-ink dark:text-zinc-200 hover:bg-surface-soft dark:hover:bg-zinc-800 transition-colors">
                    {{ __('Search Guides') }}
                </a>
                @guest
                    <a href="{{ route('login') }}" wire:navigate @click="$store.customerNav.close()" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-ink dark:text-zinc-200 hover:bg-surface-soft dark:hover:bg-zinc-800 transition-colors">
                        {{ __('Log in') }}
                    </a>
                    <a href="{{ route('register') }}" wire:navigate @click="$store.customerNav.close()" class="mt-2 inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active transition-colors">
                        {{ __('Sign up') }}
                    </a>
                @endguest
            </div>
        </div>

        <style>[x-cloak]{display:none!important}</style>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('customerNav', {
                    open: false,
                    toggle() { this.open = !this.open; },
                    close() { this.open = false; },
                });
            });
        </script>

        {{-- Page content --}}
        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            {{ $slot }}
        </main>
    </body>
</html>
