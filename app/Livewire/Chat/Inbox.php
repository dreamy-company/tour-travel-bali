<?php

namespace App\Livewire\Chat;

use App\Enums\UserRole;
use App\Models\ChatMessage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Unified message inbox for guides (and customers).
 *
 * Surfaces both pre-booking threads (booking_id null — FR-02-02) and
 * booking-scoped conversations, each with the last message, time, and
 * an unread badge, linking into the reactive ChatRoom.
 */
#[Title('Messages')]
class Inbox extends Component
{
    /**
     * All conversations involving the current user, newest first.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    #[Computed]
    public function threads(): \Illuminate\Support\Collection
    {
        $me = Auth::id();

        $messages = ChatMessage::with(['sender', 'booking.guide', 'booking.customer'])
            ->where(function ($q) use ($me): void {
                $q->where('sender_id', $me)->orWhere('receiver_id', $me);
            })
            ->latest()
            ->get();

        // Group pre-booking messages by the partner user.
        $preBooking = $messages->whereNull('booking_id')
            ->groupBy(fn (ChatMessage $m) => $m->sender_id === $me ? $m->receiver_id : $m->sender_id)
            ->map(function (Collection $group) use ($me): array {
                $last = $group->first();
                $partner = $last->sender_id === $me ? $last->receiver : $last->sender;

                return [
                    'id' => 'pre-' . $partner?->id,
                    'partner_id' => $partner?->id,
                    'partner_name' => $partner?->name ?? __('Unknown'),
                    'partner_initials' => $partner ? $partner->initials() : '?',
                    'booking_id' => null,
                    'booking_label' => __('Pre-Booking Chat'),
                    'last_message' => $last->message,
                    'last_at' => $last->created_at,
                    'unread' => $group->where('receiver_id', $me)->where('is_read', false)->count(),
                ];
            });

        // Group booking-scoped messages by the booking.
        $booking = $messages->whereNotNull('booking_id')
            ->groupBy('booking_id')
            ->map(function (Collection $group) use ($me): array {
                $last = $group->first();
                $partner = $last->sender_id === $me ? $last->receiver : $last->sender;

                return [
                    'id' => 'booking-' . $last->booking_id,
                    'partner_id' => $partner?->id,
                    'partner_name' => $partner?->name ?? __('Unknown'),
                    'partner_initials' => $partner ? $partner->initials() : '?',
                    'booking_id' => $last->booking_id,
                    'booking_label' => __('Booking') . ' #' . str_pad((string) $last->booking_id, 8, '0', STR_PAD_LEFT),
                    'last_message' => $last->message,
                    'last_at' => $last->created_at,
                    'unread' => $group->where('receiver_id', $me)->where('is_read', false)->count(),
                ];
            });

        return $preBooking
            ->concat($booking)
            ->sortByDesc(fn (array $t) => $t['last_at']?->timestamp ?? 0)
            ->values();
    }

    /**
     * Total unread messages across all threads.
     */
    #[Computed]
    public function totalUnread(): int
    {
        return ChatMessage::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();
    }

    /**
     * Render the component view.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        $layout = Auth::user()?->role === UserRole::CUSTOMER ? 'layouts.customer' : 'layouts.app';

        return view('livewire.chat.inbox', [
            'threads' => $this->threads,
            'totalUnread' => $this->totalUnread,
        ])->layout($layout);
    }
}
