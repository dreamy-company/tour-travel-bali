<?php

namespace Database\Factories;

use App\Models\ChatMessage;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChatMessage>
 */
class ChatMessageFactory extends Factory
{
    protected $model = ChatMessage::class;

    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'sender_id' => User::factory(),
            'receiver_id' => User::factory(),
            'message' => fake()->sentence(),
            'is_read' => false,
        ];
    }

    /**
     * A pre-booking chat message (no linked booking yet).
     */
    public function preBooking(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_id' => null,
        ]);
    }
}
