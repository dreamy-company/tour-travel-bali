<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $guide_id
 * @property string $title
 * @property string $description
 * @property float $price
 * @property array<int, string> $destinations
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'guide_id',
    'title',
    'description',
    'price',
    'destinations',
    'is_active',
])]
class TourPackage extends Model
{
    /** @use HasFactory<\Database\Factories\TourPackageFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'destinations' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the guide (user) that owns the tour package.
     *
     * @return BelongsTo<User, $this>
     */
    public function guide(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guide_id');
    }

    /**
     * Get the bookings that use this tour package.
     *
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'tour_package_id');
    }
}
