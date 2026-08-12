<?php

namespace App\Models;

use App\Enums\CommunicationStyle;
use App\Enums\TariffMode;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $ktp_number
 * @property string $ktp_photo
 * @property string $ktpp_number
 * @property string $ktpp_file
 * @property Carbon $ktpp_expired_at
 * @property string $skck_file
 * @property Carbon $skck_expired_at
 * @property string $surat_sehat_file
 * @property string|null $vehicle_details
 * @property string|null $bio
 * @property CommunicationStyle|null $communication_style
 * @property array<int, string>|null $specializations
 * @property array<int, string> $languages
 * @property TariffMode $tariff_mode
 * @property float $base_rate
 * @property bool $is_verified
 * @property Carbon|null $signed_sop_at
  * @property string|null $rejection_reason
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'user_id',
    'ktp_number',
    'ktp_photo',
    'headshot',
    'ktpp_number',
    'ktpp_file',
    'ktpp_expired_at',
    'skck_file',
    'skck_expired_at',
    'surat_sehat_file',
    'vehicle_details',
    'bio',
    'communication_style',
    'specializations',
    'languages',
    'tariff_mode',
    'base_rate',
    'is_verified',
    'signed_sop_at',
    'rejection_reason',
    'strikes',
])]
class GuideProfile extends Model
{
    /** @use HasFactory<\Database\Factories\GuideProfileFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ktpp_expired_at' => 'date',
            'skck_expired_at' => 'date',
            'languages' => 'array',
            'communication_style' => CommunicationStyle::class,
            'specializations' => 'array',
            'tariff_mode' => TariffMode::class,
            'base_rate' => 'decimal:2',
            'is_verified' => 'boolean',
            'signed_sop_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the guide profile.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
