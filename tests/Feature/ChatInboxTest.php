<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Livewire\Chat\Inbox;
use App\Models\Booking;
use App\Models\ChatMessage;
use App\Models\GuideProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChatInboxTest extends TestCase
{
    use RefreshDatabase;

    private function makeCustomerAndGuide(): array
    {
        $customer = User::factory()->customer()->create(['name' => 'Traveler Alex']);
        $guide = User::factory()->guide()->create(['name' => 'Guide Made', 'status' => UserStatus::ACTIVE]);
        GuideProfile::factory()->create(['user_id' => $guide->id, 'is_verified' => true]);
        return [$customer, $guide];
    }

    /**
     * A guide sees an incoming pre-booking chat in their inbox.
     */
    public function test_guide_inbox_lists_pre_booking_thread(): void
    {
        [$customer, $guide] = $this->makeCustomerAndGuide();

        ChatMessage::create([
            'sender_id' => $customer->id,
            'receiver_id' => $guide->id,
            'message' => 'Hi! Are you free next Saturday for a Ubud tour?',
        ]);

        Livewire::actingAs($guide)
            ->test(Inbox::class)
            ->assertSee('Traveler Alex')
            ->assertSee('Hi! Are you free next Saturday')
            ->assertSee('Pre-Booking Chat')
            ->assertSee('1');
    }

    /**
     * Unread count is shown and opening the thread marks messages read.
     */
    public function test_opening_thread_marks_messages_as_read(): void
    {
        [$customer, $guide] = $this->makeCustomerAndGuide();

        ChatMessage::create([
            'sender_id' => $customer->id,
            'receiver_id' => $guide->id,
            'message' => 'Question about the itinerary',
        ]);

        $this->actingAs($guide)
            ->get(route('chat.room', ['receiver' => $customer->id]))
            ->assertOk();

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $customer->id,
            'receiver_id' => $guide->id,
            'is_read' => true,
        ]);
    }

    /**
     * Booking-scoped threads are listed with their booking reference.
     */
    public function test_guide_inbox_lists_booking_thread(): void
    {
        [$customer, $guide] = $this->makeCustomerAndGuide();

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
            'pickup_location' => 'Kuta Hotel',
            'custom_destinations' => ['Ubud'],
            'schedule_date' => now()->addDays(2)->toDateString(),
            'pickup_time' => '09:00',
            'total_price' => 500000.00,
            'status' => 'pending_confirmation',
        ]);

        ChatMessage::create([
            'booking_id' => $booking->id,
            'sender_id' => $customer->id,
            'receiver_id' => $guide->id,
            'message' => 'Can we start at 8am instead?',
        ]);

        Livewire::actingAs($guide)
            ->test(Inbox::class)
            ->assertSee('Traveler Alex')
            ->assertSee('Booking #' . str_pad((string) $booking->id, 8, '0', STR_PAD_LEFT))
            ->assertSee('Can we start at 8am instead?');
    }

    /**
     * A guide can reply from the pre-booking thread page.
     */
    public function test_guide_can_reply_to_pre_booking_chat(): void
    {
        [$customer, $guide] = $this->makeCustomerAndGuide();

        ChatMessage::create([
            'sender_id' => $customer->id,
            'receiver_id' => $guide->id,
            'message' => 'Available next Saturday?',
        ]);

        Livewire::actingAs($guide)
            ->test(\App\Livewire\Chat\ChatRoom::class, ['receiver' => $customer])
            ->assertSee('Available next Saturday?')
            ->set('newMessage', 'Yes, I am free all day!')
            ->call('sendMessage');

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $guide->id,
            'receiver_id' => $customer->id,
            'booking_id' => null,
            'message' => 'Yes, I am free all day!',
        ]);
    }

    /**
     * Guests cannot access the inbox.
     */
    public function test_guest_cannot_access_inbox(): void
    {
        $this->get(route('chat.inbox'))->assertRedirect(route('login'));
    }
}
