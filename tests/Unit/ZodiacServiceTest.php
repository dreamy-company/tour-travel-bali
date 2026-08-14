<?php

namespace Tests\Unit;

use App\Enums\ZodiacSign;
use App\Services\ZodiacService;
use Carbon\Carbon;
use Tests\TestCase;

class ZodiacServiceTest extends TestCase
{
    /**
     * Each sign resolves correctly on its boundary dates.
     */
    public function test_from_date_maps_every_sign_boundary(): void
    {
        $boundaries = [
            [ZodiacSign::ARIES, '1990-03-21', '1990-04-19'],
            [ZodiacSign::TAURUS, '1990-04-20', '1990-05-20'],
            [ZodiacSign::GEMINI, '1990-05-21', '1990-06-20'],
            [ZodiacSign::CANCER, '1990-06-21', '1990-07-22'],
            [ZodiacSign::LEO, '1990-07-23', '1990-08-22'],
            [ZodiacSign::VIRGO, '1990-08-23', '1990-09-22'],
            [ZodiacSign::LIBRA, '1990-09-23', '1990-10-22'],
            [ZodiacSign::SCORPIO, '1990-10-23', '1990-11-21'],
            [ZodiacSign::SAGITTARIUS, '1990-11-22', '1990-12-21'],
            [ZodiacSign::CAPRICORN, '1990-12-22', '1991-01-19'],
            [ZodiacSign::AQUARIUS, '1991-01-20', '1991-02-18'],
            [ZodiacSign::PISCES, '1991-02-19', '1991-03-20'],
        ];

        foreach ($boundaries as [$sign, $start, $end]) {
            $this->assertSame($sign, ZodiacService::fromDate($start), "{$sign->value} start ({$start})");
            $this->assertSame($sign, ZodiacService::fromDate($end), "{$sign->value} end ({$end})");
        }
    }

    public function test_from_date_accepts_carbon_instances(): void
    {
        $this->assertSame(
            ZodiacSign::LEO,
            ZodiacService::fromDate(Carbon::parse('1995-08-10'))
        );
    }

    public function test_from_date_returns_null_when_date_is_missing(): void
    {
        $this->assertNull(ZodiacService::fromDate(null));
        $this->assertNull(ZodiacService::fromDate(''));
    }

    public function test_compatibility_is_symmetric(): void
    {
        foreach (ZodiacSign::cases() as $a) {
            foreach (ZodiacSign::cases() as $b) {
                $this->assertSame(
                    $a->compatibility($b),
                    $b->compatibility($a),
                    "Compatibility must be symmetric for {$a->value} × {$b->value}"
                );
            }
        }
    }

    public function test_compatibility_known_pairs(): void
    {
        $this->assertSame(95, ZodiacSign::ARIES->compatibility(ZodiacSign::LEO));
        $this->assertSame(95, ZodiacSign::SCORPIO->compatibility(ZodiacSign::PISCES));
        $this->assertSame(92, ZodiacSign::LEO->compatibility(ZodiacSign::SAGITTARIUS));
        $this->assertSame(90, ZodiacSign::LIBRA->compatibility(ZodiacSign::GEMINI));
        $this->assertSame(50, ZodiacSign::ARIES->compatibility(ZodiacSign::CANCER));
        $this->assertSame(70, ZodiacSign::ARIES->compatibility(ZodiacSign::ARIES));
    }

    public function test_compatibility_returns_null_when_sign_unknown(): void
    {
        $this->assertNull(ZodiacService::compatibility(null, ZodiacSign::LEO));
        $this->assertNull(ZodiacService::compatibility(ZodiacSign::LEO, null));
    }

    public function test_compatibility_label_tiers(): void
    {
        $this->assertSame('Cosmic Match', ZodiacService::compatibilityLabel(95));
        $this->assertSame('Great Vibe', ZodiacService::compatibilityLabel(85));
        $this->assertSame('Good Fit', ZodiacService::compatibilityLabel(75));
        $this->assertSame('Balanced', ZodiacService::compatibilityLabel(60));
        $this->assertSame('Complicated', ZodiacService::compatibilityLabel(50));
        $this->assertSame('—', ZodiacService::compatibilityLabel(null));
    }

    public function test_signs_expose_symbols_and_elements(): void
    {
        $this->assertSame('♌', ZodiacSign::LEO->symbol());
        $this->assertSame('♓', ZodiacSign::PISCES->symbol());
        $this->assertSame('fire', ZodiacSign::ARIES->element());
        $this->assertSame('water', ZodiacSign::CANCER->element());
        $this->assertSame(12, count(ZodiacSign::cases()));
    }
}
