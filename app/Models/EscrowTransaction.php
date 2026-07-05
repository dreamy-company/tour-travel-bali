<?php

namespace App\Models;

use App\Enums\EscrowStatus;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $booking_id
 * @property string $transaction_reference
 * @property PaymentMethod $payment_method
 * @property float $gross_amount
 * @property float $platform_commission
 * @property float $guide_net_amount
 * @property EscrowStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'booking_id',
    'transaction_reference',
    'payment_method',
    'gross_amount',
    'platform_commission',
    'guide_net_amount',
    'status',
    'snap_token',
    'redirect_url',
])]
class EscrowTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\EscrowTransactionFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payment_method' => PaymentMethod::class,
            'gross_amount' => 'decimal:2',
            'platform_commission' => 'decimal:2',
            'guide_net_amount' => 'decimal:2',
            'status' => EscrowStatus::class,
        ];
    }

    /**
     * Get the booking associated with the escrow transaction.
     *
     * @return BelongsTo<Booking, $this>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
