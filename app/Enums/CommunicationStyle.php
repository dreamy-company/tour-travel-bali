<?php

namespace App\Enums;

/**
 * Communication styles used by the matching engine (FR-02-01).
 *
 * SRS: enum('santai', 'edukatif', 'profesional', 'ekspresif')
 */
enum CommunicationStyle: string
{
    case SANTAI = 'santai';
    case EDUKATIF = 'edukatif';
    case PROFESIONAL = 'profesional';
    case EKSPRESIF = 'ekspresif';

    /**
     * Human-readable labels for UI display.
     *
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::SANTAI->value => 'Santai (Relaxed)',
            self::EDUKATIF->value => 'Edukatif (Educational)',
            self::PROFESIONAL->value => 'Profesional (Professional)',
            self::EKSPRESIF->value => 'Ekspresif (Expressive)',
        ];
    }

    /**
     * Get the label for the current case.
     */
    public function label(): string
    {
        return self::labels()[$this->value];
    }
}
