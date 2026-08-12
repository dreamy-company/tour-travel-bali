<?php

namespace App\Enums;

/**
 * Guide activity specializations used by the matching engine (FR-02-01).
 *
 * SRS: Array: ['cafe_hopping', 'photography', 'nightlife', 'nature', 'culture_history', 'healing']
 */
enum Specialization: string
{
    case CAFE_HOPPING = 'cafe_hopping';
    case PHOTOGRAPHY = 'photography';
    case NIGHTLIFE = 'nightlife';
    case NATURE = 'nature';
    case CULTURE_HISTORY = 'culture_history';
    case HEALING = 'healing';

    /**
     * Human-readable labels for UI display.
     *
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::CAFE_HOPPING->value => 'Café Hopping & Culinary',
            self::PHOTOGRAPHY->value => 'Photography & Scenic Spots',
            self::NIGHTLIFE->value => 'Nightlife & Sunset Bars',
            self::NATURE->value => 'Nature & Adventure',
            self::CULTURE_HISTORY->value => 'Culture & History',
            self::HEALING->value => 'Healing & Wellness',
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
