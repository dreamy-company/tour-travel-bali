<?php

namespace App\Livewire\Chat;

use App\Models\Booking;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
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
     * Get the active booking.
     */
    #[Computed]
    public function booking(): ?Booking
    {
        return Booking::with(['customer', 'guide'])->find($this->bookingId);
    }

    /**
     * Get chat messages in chronological order.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, ChatMessage>
     */
    #[Computed]
    public function messages()
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
        $this->validate([
            'newMessage' => ['required', 'string', 'max:1000'],
        ]);

        ChatMessage::create([
            'booking_id' => $this->bookingId,
            'sender_id' => Auth::id(),
            'message' => trim($this->newMessage),
        ]);

        $this->newMessage = '';

        // Dispatches event to trigger Alpine scroll
        $this->dispatch('chat-message-sent');
    }

    /**
     * Render the component view.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.chat.chat-room');
    }
}
