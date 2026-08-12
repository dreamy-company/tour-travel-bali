<div 
    x-data="{
        scrollToBottom() {
            const container = this.$refs.messageContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }
    }"
    x-init="scrollToBottom();"
    @chat-message-sent.window="scrollToBottom();"
    class="flex flex-col border border-hairline dark:border-zinc-800 rounded-[14px] bg-white dark:bg-stone-950 p-4 gap-3 w-full shadow-airbnb"
>
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-hairline-soft dark:border-zinc-800 pb-2">
        <h4 class="text-sm font-semibold text-ink dark:text-zinc-50 flex items-center gap-1.5">
            <svg class="size-4 text-muted" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375m-13.5 3.01H18A2.25 2.25 0 0 0 20.25 10.5v-3.75A2.25 2.25 0 0 0 18 4.5H6A2.25 2.25 0 0 0 3.75 6.75v3.75a2.25 2.25 0 0 0 1.252 2.022L3 17.25h12.75a2.25 2.25 0 0 0 2.25-2.25v-.375c0-.621-.504-1.125-1.125-1.125H3.75"/></svg>
            {{ __('In-App Chat Support') }}
        </h4>
        <span class="inline-flex items-center rounded-full bg-surface-soft dark:bg-zinc-900 px-2 py-0.5 text-[11px] font-semibold text-rausch ring-1 ring-inset ring-rausch/15 animate-pulse">
            {{ __('Live Connection') }}
        </span>
    </div>

    <!-- Messages Container with Polling -->
    <div 
        x-ref="messageContainer"
        wire:poll.3s="$refresh"
        class="h-64 overflow-y-auto space-y-2 pr-1 scroll-smooth"
    >
        @forelse ($messages as $msg)
            @php
                $isMe = $msg->sender_id === auth()->id();
            @endphp
            <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} gap-2.5">
                @if (! $isMe)
                    <div class="size-6 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-bold text-[9px] text-zinc-650 dark:text-zinc-350 self-end shrink-0">
                        {{ $msg->sender->initials() }}
                    </div>
                @endif
                <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }} max-w-[75%] gap-0.5">
                    <div class="rounded-lg p-2.5 text-sm leading-normal {{ $isMe ? 'bg-rausch text-white dark:bg-zinc-800 dark:text-zinc-100' : 'bg-surface-soft text-ink dark:bg-zinc-900 dark:text-zinc-100' }}">
                        {{ $msg->message }}
                    </div>
                    <span class="text-[11px] text-muted-soft">
                        {{ $msg->created_at?->format('H:i') }}
                    </span>
                </div>
                @if ($isMe)
                    <div class="size-6 rounded-full bg-zinc-950 text-white dark:bg-zinc-850 dark:text-zinc-250 flex items-center justify-center font-bold text-[9px] self-end shrink-0">
                        {{ $msg->sender->initials() }}
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-10 flex flex-col items-center justify-center">
                <svg class="size-8 text-zinc-300 dark:text-zinc-700 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375m-13.5 3.01H18A2.25 2.25 0 0 0 20.25 10.5v-3.75a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6.75v3.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                <p class="text-[10px] text-zinc-400">{{ __('No messages yet. Send a message to start conversation.') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Input Bar -->
    <div class="flex gap-2 pt-2 border-t border-hairline-soft dark:border-zinc-800">
        <input 
            wire:model="newMessage" 
            wire:keydown.enter="sendMessage"
            type="text" 
            placeholder="Type message here..." 
            class="flex-1 text-sm px-3.5 py-2.5 rounded-full border border-hairline bg-white text-ink dark:border-zinc-800 dark:bg-zinc-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-rausch"
        />
        <button 
            wire:click="sendMessage" 
            type="button" 
            class="inline-flex items-center justify-center size-11 rounded-full bg-rausch text-white hover:bg-rausch-active dark:bg-white dark:text-black dark:hover:bg-zinc-100 transition-colors shadow-airbnb shrink-0"
        >
            <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
        </button>
    </div>
</div>
