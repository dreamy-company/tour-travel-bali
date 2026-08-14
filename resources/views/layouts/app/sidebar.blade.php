<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>

                    @if (auth()->user()?->role === \App\Enums\UserRole::CUSTOMER)
                        <flux:sidebar.item icon="magnifying-glass" :href="route('guides.index')" :current="request()->routeIs('guides.index')" wire:navigate>
                            {{ __('Search Guides') }}
                        </flux:sidebar.item>
                    @endif

                    @if (auth()->user()?->role === \App\Enums\UserRole::CUSTOMER || auth()->user()?->role === \App\Enums\UserRole::GUIDE)
                        <flux:sidebar.item icon="chat-bubble-oval-left-ellipsis" :href="route('chat.inbox')" :current="request()->routeIs('chat.*')" wire:navigate>
                            {{ __('Messages') }}
                        </flux:sidebar.item>
                    @endif

                    @if (auth()->user()?->role === \App\Enums\UserRole::ADMIN)
                        <flux:sidebar.item icon="users" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>
                            {{ __('User Management') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="shield-check" :href="route('admin.verification')" :current="request()->routeIs('admin.verification')" wire:navigate>
                            {{ __('Guide Verification') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="credit-card" :href="route('admin.finance')" :current="request()->routeIs('admin.finance')" wire:navigate>
                            {{ __('Withdrawal Approvals') }}
                        </flux:sidebar.item>
                    @endif

                    @if (auth()->user()?->role === \App\Enums\UserRole::GUIDE)
                        <flux:sidebar.item icon="briefcase" :href="route('guide.services')" :current="request()->routeIs('guide.services')" wire:navigate>
                            {{ __('Manage Services') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="list-bullet" :href="route('guide.orders')" :current="request()->routeIs('guide.orders')" wire:navigate>
                            {{ __('Tour Orders') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="credit-card" :href="route('guide.payouts')" :current="request()->routeIs('guide.payouts')" wire:navigate>
                            {{ __('Earnings & Payouts') }}
                        </flux:sidebar.item>
                    @endif
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            @auth
                <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
            @else
                <div class="hidden lg:flex items-center gap-2 p-3">
                    <a href="{{ route('login') }}" wire:navigate class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-full text-ink hover:bg-surface-soft dark:text-zinc-300 dark:hover:bg-zinc-800 transition-colors">
                        {{ __('Log in') }}
                    </a>
                    <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active transition-colors">
                        {{ __('Sign up') }}
                    </a>
                </div>
            @endauth
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            @auth
            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
            @else
                <a href="{{ route('login') }}" wire:navigate class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-rausch text-white hover:bg-rausch-active transition-colors">
                    {{ __('Log in') }}
                </a>
            @endauth
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
