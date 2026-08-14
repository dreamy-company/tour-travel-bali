<?php

namespace App\Services;

use App\Enums\ZodiacSign;
use Carbon\CarbonInterface;

/**
 * Zodiac matching helpers: derive a sign from a birth date and score
 * the compatibility between two signs with a friendly label.
 */
final class ZodiacService
{
    /**
     * Derive the zodiac sign for a birth date, or null when missing/invalid.
     */
    public static function fromDate(CarbonInterface|string|null $date): ?ZodiacSign
    {
        return ZodiacSign::fromDate($date);
    }

    /**
     * Compatibility score (0–100) between two signs, or null when either
     * sign is unknown.
     */
    public static function compatibility(?ZodiacSign $a, ?ZodiacSign $b): ?int
    {
        if ($a === null || $b === null) {
            return null;
        }

        return $a->compatibility($b);
    }

    /**
     * Friendly tier label for a compatibility score.
     */
    public static function compatibilityLabel(?int $score): string
    {
        return match (true) {
            $score === null => '—',
            $score >= 90 => 'Cosmic Match',
            $score >= 80 => 'Great Vibe',
            $score >= 70 => 'Good Fit',
            $score >= 60 => 'Balanced',
            default => 'Complicated',
        };
    }
}
