<?php

namespace App\Livewire\Chat;

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Chat')]
class ChatRoom extends Component
{
    public ?int $bookingId = null;
    public int $receiverId;
    public string $newMessage = '';

    /**
     * Mount the component.
     *
     * Two entry modes:
     * 1. Pre-booking chat (FR-02-02): mounted with a receiver user and no booking.
     * 2. Booking-scoped chat: mounted with a bookingId (legacy usage).
     *
     * @param  User|null  $receiver  Route-bound receiver (pre-booking chat)
     * @param  int|null  $bookingId  Booking-scoped thread
     */
    public function mount(?User $receiver = null, ?int $bookingId = null): void
    {
        // Inbox links open booking threads via ?booking= query param.
        if ($bookingId === null && Request::query('booking')) {
            $bookingId = (int) Request::query('booking');
        }

        if ($bookingId !== null) {
            $booking = Booking::find($bookingId);

            if (! $booking) {
                abort(404, 'Booking not found.');
            }

            if (Auth::id() !== $booking->customer_id && Auth::id() !== $booking->guide_id) {
                abort(403, 'Unauthorized access to this chat room.');
            }

            $this->bookingId = $bookingId;
            $this->receiverId = Auth::id() === $booking->customer_id
                ? $booking->guide_id
                : $booking->customer_id;

            $this->markThreadAsRead();

            return;
        }

        // Pre-booking chat between a customer and a verified guide.
        if (! $receiver) {
            abort(404, 'Chat recipient not found.');
        }

        $this->receiverId = $receiver->id;
        $this->authorizePreBookingChat($receiver);
        $this->markThreadAsRead();
    }

    /**
     * Mark all incoming messages in the current thread as read.
     */
    protected function markThreadAsRead(): void
    {
        $me = Auth::id();

        ChatMessage::where('receiver_id', $me)
            ->where('is_read', false)
            ->when(
                $this->bookingId !== null,
                fn ($q) => $q->where('booking_id', $this->bookingId),
                fn ($q) => $q->whereNull('booking_id')->where('sender_id', $this->receiverId)
            )
            ->update(['is_read' => true]);
    }

    /**
     * Polled while the chat is open: keeps read receipts fresh.
     */
    public function pollMessages(): void
    {
        $this->markThreadAsRead();
    }

    /**
     * Only customer ↔ guide conversations are permitted pre-booking.
     */
    protected function authorizePreBookingChat(User $receiver): void
    {
        $me = Auth::user();

        if (! $me || $me->id === $receiver->id) {
            abort(403, 'Unauthorized access to this chat room.');
        }

        $roles = [$me->role->value, $receiver->role->value];

        $isPair = in_array(UserRole::CUSTOMER->value, $roles, true)
            && in_array(UserRole::GUIDE->value, $roles, true);

        if (! $isPair) {
            abort(403, 'Unauthorized access to this chat room.');
        }

        // A customer may only start a pre-booking chat with a verified guide.
        if ($me->role === UserRole::CUSTOMER
            && (! $receiver->guideProfile || ! $receiver->guideProfile->is_verified)) {
            abort(403, 'This guide is not yet available for pre-booking chat.');
        }
    }

    /**
     * Fetch chat messages in chronological order.
     * Kept as a private method so Livewire never serializes or validates
     * the Eloquent Collection as a tracked public property.
     *
     * @return Collection<int, ChatMessage>
     */
    private function fetchMessages(): Collection
    {
        $query = ChatMessage::with('sender');

        if ($this->bookingId !== null) {
            $query->where('booking_id', $this->bookingId);
        } else {
            $me = Auth::id();
            $query->whereNull('booking_id')
                ->where(function ($q) use ($me): void {
                    $q->where(fn ($sub) => $sub->where('sender_id', $me)->where('receiver_id', $this->receiverId))
                        ->orWhere(fn ($sub) => $sub->where('sender_id', $this->receiverId)->where('receiver_id', $me));
                });
        }

        return $query->orderBy('created_at', 'asc')->get();
    }

    /**
     * Get the conversation partner.
     */
    public function receiver(): User
    {
        return User::findOrFail($this->receiverId);
    }

    /**
     * Send a new message.
     */
    public function sendMessage(): void
    {
        // Only validate the scalar newMessage string — no Collection in scope.
        $this->validate([
            'newMessage' => ['required', 'string', 'max:1000'],
        ]);

        ChatMessage::create([
            'booking_id' => $this->bookingId,
            'sender_id' => Auth::id(),
            'receiver_id' => $this->receiverId,
            'message' => trim($this->newMessage),
        ]);

        $this->newMessage = '';

        // Dispatches event to trigger Alpine auto-scroll
        $this->dispatch('chat-message-sent');
    }

    /**
     * Render the component view.
     * Messages are passed directly here — the Eloquent Collection never
     * touches Livewire's property tracking or validation pipeline.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        // Standalone chat page: customers get the top-navbar layout,
        // guides keep the sidebar app layout. Embedded usage ignores layouts.
        $layout = Auth::user()?->role === UserRole::CUSTOMER ? 'layouts.customer' : 'layouts.app';

        return view('livewire.chat.chat-room', [
            'messages' => $this->fetchMessages(),
            'receiver' => $this->receiver(),
        ])->layout($layout);
    }
}
