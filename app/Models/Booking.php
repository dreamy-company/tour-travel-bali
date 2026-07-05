<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $customer_id
 * @property int $guide_id
 * @property int|null $tour_package_id
 * @property string $pickup_location
 * @property string|null $dropoff_location
 * @property array|null $custom_destinations
 * @property Carbon $schedule_date
 * @property string $pickup_time
 * @property float $total_price
 * @property BookingStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'customer_id',
    'guide_id',
    'tour_package_id',
    'pickup_location',
    'dropoff_location',
    'custom_destinations',
    'schedule_date',
    'pickup_time',
    'total_price',
    'status',
])]
class Booking extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'schedule_date' => 'date',
            'custom_destinations' => 'array',
            'total_price' => 'decimal:2',
            'status' => BookingStatus::class,
        ];
    }

    /**
     * Get the customer (user) associated with the booking.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the guide (user) associated with the booking.
     */
    public function guide(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guide_id');
    }

    /**
     * Get the tour package associated with the booking.
     */
    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class, 'tour_package_id');
    }

    /**
     * Get the escrow transaction associated with the booking.
     */
    public function escrowTransaction(): HasOne
    {
        return $this->hasOne(EscrowTransaction::class, 'booking_id');
    }

    /**
     * Get the review associated with the booking.
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'booking_id');
    }

    /**
     * Get the chat messages for the booking.
     */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'booking_id');
    }
}
