<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-stone-950 flex flex-col items-center justify-center p-4">
    <div class="max-w-md w-full border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-stone-900 p-8 shadow-xl text-center space-y-6">
        <!-- Success Icon Check -->
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400">
            <svg class="size-8 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
            </svg>
        </div>

        <div class="space-y-2">
            <h2 class="text-xl font-extrabold text-zinc-900 dark:text-white">{{ __('Application Under Review') }}</h2>
            <p class="text-xs text-zinc-500 leading-relaxed">
                {{ __('Terima kasih! Your guide application has been successfully submitted to the Bali Provincial Customary Tourism platform.') }}
            </p>
        </div>

        <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-950 text-xs text-zinc-650 dark:text-zinc-400 leading-relaxed text-left border dark:border-zinc-850">
            <p class="font-bold text-zinc-900 dark:text-white mb-1.5">{{ __('What happens next?') }}</p>
            <ul class="list-disc pl-4 space-y-1">
                <li>{{ __('Your account status is currently set to pending verification (hidden from search).') }}</li>
                <li>{{ __('Our audit team will review your uploaded KTP identity, headshot, HPI KTPP license, and police SKCK certificate.') }}</li>
                <li>{{ __('Verification typically takes 1–2 business days. You will be notified by email once approved.') }}</li>
            </ul>
        </div>

        <div class="pt-2">
            <a href="{{ route('home') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 text-xs font-bold rounded-lg border border-zinc-200 hover:bg-zinc-50 text-zinc-700 dark:border-zinc-800 dark:hover:bg-zinc-900 dark:text-zinc-300 transition-colors">
                {{ __('Return Home') }}
            </a>
        </div>
    </div>
</body>
</html>
