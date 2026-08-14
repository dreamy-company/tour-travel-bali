<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\ZodiacSign;
use App\Services\ZodiacService;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $phone_number
 * @property Carbon|null $birth_date
 * @property UserRole $role
 * @property UserStatus $status
 * @property array<int, string>|null $traveler_preferences
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'phone_number', 'birth_date', 'role', 'status', 'traveler_preferences'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date' => 'date',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
            'traveler_preferences' => 'array',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    /**
     * Derive the zodiac sign from the birth date, or null when unset.
     */
    public function zodiac(): ?ZodiacSign
    {
        return ZodiacService::fromDate($this->birth_date);
    }

    /**
     * Get the guide profile associated with the user.
     *
     * @return HasOne<GuideProfile, $this>
     */
    public function guideProfile(): HasOne
    {
        return $this->hasOne(GuideProfile::class);
    }

    /**
     * Get the tour packages created by the user (as a guide).
     *
     * @return HasMany<TourPackage, $this>
     */
    public function tourPackages(): HasMany
    {
        return $this->hasMany(TourPackage::class, 'guide_id');
    }

    /**
     * Get the bookings made by the user (as a customer).
     *
     * @return HasMany<Booking, $this>
     */
    public function customerBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    /**
     * Get the bookings assigned to the user (as a guide).
     *
     * @return HasMany<Booking, $this>
     */
    public function guideBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'guide_id');
    }

    /**
     * Get the wallet associated with the user (as a guide).
     *
     * @return HasOne<GuideWallet, $this>
     */
    public function guideWallet(): HasOne
    {
        return $this->hasOne(GuideWallet::class, 'guide_id');
    }

    /**
     * Get the withdrawals requested by the user (as a guide).
     *
     * @return HasMany<Withdrawal, $this>
     */
    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class, 'guide_id');
    }

    /**
     * Get the reviews written by the user (as a customer).
     *
     * @return HasMany<Review, $this>
     */
    public function customerReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

    /**
     * Get the reviews received by the user (as a guide).
     *
     * @return HasMany<Review, $this>
     */
    public function guideReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'guide_id');
    }

    /**
     * Get the chat messages sent by the user.
     *
     * @return HasMany<ChatMessage, $this>
     */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    /**
     * Get the favorite (wishlist) entries created by the user.
     *
     * @return HasMany<Favorite, $this>
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'customer_id');
    }

    /**
     * Get the guides saved by the user (wishlist).
     *
     * @return BelongsToMany<User, $this>
     */
    public function favoritedGuides(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites', 'customer_id', 'guide_id');
    }
}
