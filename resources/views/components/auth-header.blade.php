@props([
    'title',
    'description',
])

<div class="flex w-full flex-col gap-1.5 text-center">
    <h1 class="text-2xl font-semibold tracking-[-0.44px] text-ink">{{ $title }}</h1>
    <p class="text-sm text-muted">{{ $description }}</p>
</div>
