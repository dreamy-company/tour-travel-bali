@if (auth()->user()->role === \App\Enums\UserRole::CUSTOMER)
    {{-- Customers get a clean top-navbar page (no sidebar) --}}
    <x-layouts::customer :title="__('Dashboard')">
        @include('partials.dashboard-content')
    </x-layouts::customer>
@else
    {{-- Admin & guide keep the sidebar app shell --}}
    <x-layouts::app :title="__('Dashboard')">
        @include('partials.dashboard-content')
    </x-layouts::app>
@endif
