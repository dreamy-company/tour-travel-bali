<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-neutral-100 antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-2xl flex-col gap-6">
                <a href="{{ route('home') }}" class="flex items-center justify-center gap-2" wire:navigate>
                    <span class="flex aspect-square size-10 items-center justify-center rounded-xl bg-rausch text-white shadow-airbnb">
                        <x-app-logo-icon class="size-5 fill-current" />
                    </span>
                    <span class="text-lg font-semibold tracking-[-0.3px] text-ink dark:text-white">{{ config('app.name', 'BaliGuide') }}</span>
                </a>
                <div class="flex flex-col gap-6">
                    <div class="rounded-xl border bg-white dark:bg-stone-950 dark:border-stone-800 text-stone-800 dark:text-zinc-200 shadow-xs">
                        <div class="px-6 py-8 md:px-10">{{ $slot }}</div>
                    </div>
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
