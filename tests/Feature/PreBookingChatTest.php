<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Livewire\Chat\ChatRoom;
use App\Models\ChatMessage;
use App\Models\GuideProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PreBookingChatTest extends TestCase
{
    use RefreshDatabase;

    private function makeCustomerAndGuide(): array
    {
        $customer = User::factory()->customer()->create();
        $guide = User::factory()->guide()->create(['status' => UserStatus::ACTIVE]);
        GuideProfile::factory()->create([
            'user_id' => $guide->id,
            'is_verified' => true,
        ]);

        return [$customer, $guide];
    }

    /**
     * FR-02-02: A customer can start a pre-booking chat with a verified guide.
     */
    public function test_customer_can_send_pre_booking_message_to_guide(): void
    {
        [$customer, $guide] = $this->makeCustomerAndGuide();

        Livewire::actingAs($customer)
            ->test(ChatRoom::class, ['receiver' => $guide])
            ->set('newMessage', 'Hi! Would you be free next Saturday?')
            ->call('sendMessage')
            ->assertSet('newMessage', '');

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $customer->id,
            'receiver_id' => $guide->id,
            'booking_id' => null,
            'message' => 'Hi! Would you be free next Saturday?',
        ]);
    }

    /**
     * FR-02-02: Pre-booking messages are scoped to the sender/receiver pair.
     */
    public function test_pre_booking_thread_is_scoped_to_pair(): void
    {
        [$customer, $guide] = $this->makeCustomerAndGuide();
        $otherGuide = User::factory()->guide()->create();
        GuideProfile::factory()->create(['user_id' => $otherGuide->id, 'is_verified' => true]);

        ChatMessage::create([
            'sender_id' => $customer->id,
            'receiver_id' => $otherGuide->id,
            'message' => 'Question for other guide',
        ]);

        Livewire::actingAs($customer)
            ->test(ChatRoom::class, ['receiver' => $guide])
            ->assertDontSee('Question for other guide');
    }

    /**
     * FR-02-02: A guide can reply and both sides see the same thread.
     */
    public function test_guide_can_reply_to_pre_booking_thread(): void
    {
        [$customer, $guide] = $this->makeCustomerAndGuide();

        ChatMessage::create([
            'sender_id' => $customer->id,
            'receiver_id' => $guide->id,
            'message' => 'Available next Saturday?',
        ]);

        Livewire::actingAs($guide)
            ->test(ChatRoom::class, ['receiver' => $customer])
            ->assertSee('Available next Saturday?')
            ->set('newMessage', 'Yes, I am free all day!')
            ->call('sendMessage');

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $guide->id,
            'receiver_id' => $customer->id,
            'booking_id' => null,
        ]);
    }

    /**
     * FR-02-02: Customers cannot pre-booking chat with an unverified guide.
     */
    public function test_customer_cannot_chat_unverified_guide_before_booking(): void
    {
        $customer = User::factory()->customer()->create();
        $unverified = User::factory()->guide()->create();
        GuideProfile::factory()->create([
            'user_id' => $unverified->id,
            'is_verified' => false,
        ]);

        Livewire::actingAs($customer)
            ->test(ChatRoom::class, ['receiver' => $unverified])
            ->assertStatus(403);
    }

    /**
     * The standalone chat page renders inside the customer top-navbar layout.
     */
    public function test_chat_page_uses_customer_navbar_layout(): void
    {
        [$customer, $guide] = $this->makeCustomerAndGuide();

        $this->actingAs($customer)
            ->get(route('chat.room', ['receiver' => $guide->id]))
            ->assertOk()
            ->assertSee('BaliGuide')
            ->assertDontSee('flux-sidebar');
    }
}
