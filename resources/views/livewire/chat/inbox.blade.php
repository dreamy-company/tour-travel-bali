<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-medium tracking-[-0.44px] text-ink">{{ __('Messages') }}</h1>
            <p class="text-sm text-muted mt-1">{{ __('Pre-booking chats and booking conversations, all in one place.') }}</p>
        </div>
        @if ($totalUnread > 0)
            <span class="inline-flex items-center rounded-full bg-rausch px-3 py-1 text-xs font-semibold text-white">{{ $totalUnread }} {{ __('unread') }}</span>
        @endif
    </div>

    <div class="border border-hairline rounded-[14px] bg-white divide-y divide-hairline-soft overflow-hidden">
        @forelse ($threads as $thread)
            <a
                href="{{ $thread['booking_id'] ? route('chat.room', ['receiver' => $thread['partner_id'], 'booking' => $thread['booking_id']]) : route('chat.room', ['receiver' => $thread['partner_id']]) }}"
                wire:navigate
                class="flex items-center gap-4 px-5 py-4 hover:bg-surface-soft transition-colors"
            >
                <div class="flex size-11 shrink-0 items-center justify-center rounded-full bg-surface-soft text-sm font-semibold text-ink">
                    {{ $thread['partner_initials'] }}
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-ink truncate">{{ $thread['partner_name'] }}</p>
                        <span class="text-xs text-muted-soft shrink-0">{{ $thread['last_at']?->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-muted truncate">{{ $thread['last_message'] }}</p>
                    <p class="text-[11px] text-muted-soft mt-0.5">
                        <span class="{{ $thread['booking_id'] ? 'text-rausch font-medium' : '' }}">{{ $thread['booking_label'] }}</span>
                    </p>
                </div>

                @if ($thread['unread'] > 0)
                    <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-rausch text-xs font-bold text-white">{{ $thread['unread'] }}</span>
                @else
                    <svg class="size-5 shrink-0 text-hairline" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                @endif
            </a>
        @empty
            <div class="p-16 text-center flex flex-col items-center gap-4">
                <div class="flex size-16 items-center justify-center rounded-full bg-surface-soft text-muted">
                    <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375m-13.5 3.01H18A2.25 2.25 0 0 0 20.25 10.5v-3.75A2.25 2.25 0 0 0 18 4.5H6A2.25 2.25 0 0 0 3.75 6.75v3.75a2.25 2.25 0 0 0 1.252 2.022L3 17.25h12.75a2.25 2.25 0 0 0 2.25-2.25v-.375c0-.621-.504-1.125-1.125-1.125H3.75"/></svg>
                </div>
                <div class="space-y-1">
                    <h3 class="text-lg font-medium text-ink">{{ __('No conversations yet') }}</h3>
                    <p class="text-sm text-muted max-w-sm">{{ __('When travelers start a pre-booking chat with you, or you get a booking, the conversation will appear here.') }}</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
