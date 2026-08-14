<?php

namespace App\Enums;

use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Western zodiac signs used to match travelers with guides by
 * zodiac compatibility (FR-02-01 extension).
 *
 * Each sign carries a symbol, an element, a calendar date range, and a
 * compatibility score (0–100) against every other sign.
 */
enum ZodiacSign: string
{
    case ARIES = 'aries';
    case TAURUS = 'taurus';
    case GEMINI = 'gemini';
    case CANCER = 'cancer';
    case LEO = 'leo';
    case VIRGO = 'virgo';
    case LIBRA = 'libra';
    case SCORPIO = 'scorpio';
    case SAGITTARIUS = 'sagittarius';
    case CAPRICORN = 'capricorn';
    case AQUARIUS = 'aquarius';
    case PISCES = 'pisces';

    /**
     * Compatibility scores (0–100) between signs, keyed by enum value.
     * Symmetric: matrix[a][b] === matrix[b][a].
     *
     * Same element (fire/earth/air/water) pairs land 70–80, the classic
     * high-synastry pairs land 85+, and hard aspects land 50–60.
     *
     * @var array<string, array<string, int>>
     */
    private const COMPATIBILITY = [
        'aries' => ['aries' => 70, 'taurus' => 55, 'gemini' => 85, 'cancer' => 50, 'leo' => 95, 'virgo' => 55, 'libra' => 80, 'scorpio' => 50, 'sagittarius' => 90, 'capricorn' => 55, 'aquarius' => 88, 'pisces' => 60],
        'taurus' => ['aries' => 55, 'taurus' => 65, 'gemini' => 55, 'cancer' => 88, 'leo' => 60, 'virgo' => 95, 'libra' => 55, 'scorpio' => 88, 'sagittarius' => 50, 'capricorn' => 90, 'aquarius' => 55, 'pisces' => 85],
        'gemini' => ['aries' => 85, 'taurus' => 55, 'gemini' => 75, 'cancer' => 70, 'leo' => 80, 'virgo' => 55, 'libra' => 90, 'scorpio' => 55, 'sagittarius' => 75, 'capricorn' => 55, 'aquarius' => 88, 'pisces' => 60],
        'cancer' => ['aries' => 50, 'taurus' => 88, 'gemini' => 70, 'cancer' => 60, 'leo' => 55, 'virgo' => 88, 'libra' => 50, 'scorpio' => 95, 'sagittarius' => 55, 'capricorn' => 80, 'aquarius' => 50, 'pisces' => 92],
        'leo' => ['aries' => 95, 'taurus' => 60, 'gemini' => 80, 'cancer' => 55, 'leo' => 70, 'virgo' => 70, 'libra' => 88, 'scorpio' => 50, 'sagittarius' => 92, 'capricorn' => 55, 'aquarius' => 75, 'pisces' => 55],
        'virgo' => ['aries' => 55, 'taurus' => 95, 'gemini' => 55, 'cancer' => 88, 'leo' => 70, 'virgo' => 65, 'libra' => 55, 'scorpio' => 88, 'sagittarius' => 50, 'capricorn' => 92, 'aquarius' => 55, 'pisces' => 82],
        'libra' => ['aries' => 80, 'taurus' => 55, 'gemini' => 90, 'cancer' => 50, 'leo' => 88, 'virgo' => 55, 'libra' => 75, 'scorpio' => 65, 'sagittarius' => 88, 'capricorn' => 50, 'aquarius' => 92, 'pisces' => 55],
        'scorpio' => ['aries' => 50, 'taurus' => 88, 'gemini' => 55, 'cancer' => 95, 'leo' => 50, 'virgo' => 88, 'libra' => 65, 'scorpio' => 55, 'sagittarius' => 55, 'capricorn' => 90, 'aquarius' => 55, 'pisces' => 95],
        'sagittarius' => ['aries' => 90, 'taurus' => 50, 'gemini' => 75, 'cancer' => 55, 'leo' => 92, 'virgo' => 50, 'libra' => 88, 'scorpio' => 55, 'sagittarius' => 70, 'capricorn' => 65, 'aquarius' => 88, 'pisces' => 55],
        'capricorn' => ['aries' => 55, 'taurus' => 90, 'gemini' => 55, 'cancer' => 80, 'leo' => 55, 'virgo' => 92, 'libra' => 50, 'scorpio' => 90, 'sagittarius' => 65, 'capricorn' => 60, 'aquarius' => 55, 'pisces' => 88],
        'aquarius' => ['aries' => 88, 'taurus' => 55, 'gemini' => 88, 'cancer' => 50, 'leo' => 75, 'virgo' => 55, 'libra' => 92, 'scorpio' => 55, 'sagittarius' => 88, 'capricorn' => 55, 'aquarius' => 75, 'pisces' => 85],
        'pisces' => ['aries' => 60, 'taurus' => 85, 'gemini' => 60, 'cancer' => 92, 'leo' => 55, 'virgo' => 82, 'libra' => 55, 'scorpio' => 95, 'sagittarius' => 55, 'capricorn' => 88, 'aquarius' => 85, 'pisces' => 65],
    ];

    /**
     * Unicode astrological symbol for display.
     */
    public function symbol(): string
    {
        return match ($this) {
            self::ARIES => '♈',
            self::TAURUS => '♉',
            self::GEMINI => '♊',
            self::CANCER => '♋',
            self::LEO => '♌',
            self::VIRGO => '♍',
            self::LIBRA => '♎',
            self::SCORPIO => '♏',
            self::SAGITTARIUS => '♐',
            self::CAPRICORN => '♑',
            self::AQUARIUS => '♒',
            self::PISCES => '♓',
        };
    }

    /**
     * Human-readable English label.
     */
    public function label(): string
    {
        return match ($this) {
            self::ARIES => 'Aries',
            self::TAURUS => 'Taurus',
            self::GEMINI => 'Gemini',
            self::CANCER => 'Cancer',
            self::LEO => 'Leo',
            self::VIRGO => 'Virgo',
            self::LIBRA => 'Libra',
            self::SCORPIO => 'Scorpio',
            self::SAGITTARIUS => 'Sagittarius',
            self::CAPRICORN => 'Capricorn',
            self::AQUARIUS => 'Aquarius',
            self::PISCES => 'Pisces',
        };
    }

    /**
     * Classic element (fire / earth / air / water).
     */
    public function element(): string
    {
        return match ($this) {
            self::ARIES, self::LEO, self::SAGITTARIUS => 'fire',
            self::TAURUS, self::VIRGO, self::CAPRICORN => 'earth',
            self::GEMINI, self::LIBRA, self::AQUARIUS => 'air',
            self::CANCER, self::SCORPIO, self::PISCES => 'water',
        };
    }

    /**
     * Emoji representation of the element.
     */
    public function elementEmoji(): string
    {
        return match ($this->element()) {
            'fire' => '🔥',
            'earth' => '🌍',
            'air' => '💨',
            'water' => '💧',
            default => '',
        };
    }

    /**
     * Calendar month the sign starts in (1–12). Capricorn wraps year-end (12).
     */
    public function monthStart(): int
    {
        return match ($this) {
            self::ARIES => 3,
            self::TAURUS => 4,
            self::GEMINI => 5,
            self::CANCER => 6,
            self::LEO => 7,
            self::VIRGO => 8,
            self::LIBRA => 9,
            self::SCORPIO => 10,
            self::SAGITTARIUS => 11,
            self::CAPRICORN => 12,
            self::AQUARIUS => 1,
            self::PISCES => 2,
        };
    }

    /**
     * Calendar day (within monthStart) the sign starts on.
     */
    public function dayStart(): int
    {
        return match ($this) {
            self::ARIES => 21,
            self::TAURUS => 20,
            self::GEMINI => 21,
            self::CANCER => 21,
            self::LEO => 23,
            self::VIRGO => 23,
            self::LIBRA => 23,
            self::SCORPIO => 23,
            self::SAGITTARIUS => 22,
            self::CAPRICORN => 22,
            self::AQUARIUS => 20,
            self::PISCES => 19,
        };
    }

    /**
     * Calendar month the sign ends in (1–12). Capricorn wraps year-end (1).
     */
    public function monthEnd(): int
    {
        return match ($this) {
            self::ARIES => 4,
            self::TAURUS => 5,
            self::GEMINI => 6,
            self::CANCER => 7,
            self::LEO => 8,
            self::VIRGO => 9,
            self::LIBRA => 10,
            self::SCORPIO => 11,
            self::SAGITTARIUS => 12,
            self::CAPRICORN => 1,
            self::AQUARIUS => 2,
            self::PISCES => 3,
        };
    }

    /**
     * Calendar day (within monthEnd) the sign ends on.
     */
    public function dayEnd(): int
    {
        return match ($this) {
            self::ARIES => 19,
            self::TAURUS => 20,
            self::GEMINI => 20,
            self::CANCER => 22,
            self::LEO => 22,
            self::VIRGO => 22,
            self::LIBRA => 22,
            self::SCORPIO => 21,
            self::SAGITTARIUS => 21,
            self::CAPRICORN => 19,
            self::AQUARIUS => 18,
            self::PISCES => 20,
        };
    }

    /**
     * Resolve the zodiac sign for a given birth date, or null when missing.
     */
    public static function fromDate(CarbonInterface|string|null $date): ?self
    {
        if ($date === null || $date === '') {
            return null;
        }

        $day = $date instanceof CarbonInterface ? $date : Carbon::parse($date);
        $month = (int) $day->format('n');
        $dayOfMonth = (int) $day->format('j');

        foreach (self::cases() as $sign) {
            $startMonth = $sign->monthStart();
            $endMonth = $sign->monthEnd();

            $inRange = $startMonth <= $endMonth
                ? (($month > $startMonth || ($month === $startMonth && $dayOfMonth >= $sign->dayStart()))
                    && ($month < $endMonth || ($month === $endMonth && $dayOfMonth <= $sign->dayEnd())))
                : (($month === $startMonth && $dayOfMonth >= $sign->dayStart())
                    || ($month === $endMonth && $dayOfMonth <= $sign->dayEnd()));

            if ($inRange) {
                return $sign;
            }
        }

        return null;
    }

    /**
     * Compatibility score (0–100) with another sign.
     */
    public function compatibility(self $other): int
    {
        return self::COMPATIBILITY[$this->value][$other->value];
    }

    /**
     * Human-readable labels keyed by enum value.
     *
     * @return array<string, string>
     */
    public static function labels(): array
    {
        $labels = [];

        foreach (self::cases() as $sign) {
            $labels[$sign->value] = $sign->symbol().' '.$sign->label();
        }

        return $labels;
    }
}
