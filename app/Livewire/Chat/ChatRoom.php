<?php

namespace App\Livewire\Chat;

use App\Models\Booking;
use App\Models\ChatMessage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChatRoom extends Component
{
    public int $bookingId;
    public string $newMessage = '';

    /**
     * Mount the component and authorize access.
     */
    public function mount(int $bookingId): void
    {
        $booking = Booking::find($bookingId);

        if (! $booking) {
            abort(404, 'Booking not found.');
        }

        if (Auth::id() !== $booking->customer_id && Auth::id() !== $booking->guide_id) {
            abort(403, 'Unauthorized access to this chat room.');
        }

        $this->bookingId = $bookingId;
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
        return ChatMessage::with('sender')
            ->where('booking_id', $this->bookingId)
            ->orderBy('created_at', 'asc')
            ->get();
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
        return view('livewire.chat.chat-room', [
            'messages' => $this->fetchMessages(),
        ]);
    }
}
