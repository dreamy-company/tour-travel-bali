<?php

namespace Database\Factories;

use App\Models\EscrowTransaction;
use App\Models\Booking;
use App\Enums\PaymentMethod;
use App\Enums\EscrowStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EscrowTransaction>
 */
class EscrowTransactionFactory extends Factory
{
    protected $model = EscrowTransaction::class;

    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'transaction_reference' => fake()->uuid(),
            'payment_method' => PaymentMethod::QRIS,
            'gross_amount' => 750000.00,
            'platform_commission' => 75000.00,
            'guide_net_amount' => 675000.00,
            'status' => EscrowStatus::WAITING_PAYMENT,
        ];
    }
}
