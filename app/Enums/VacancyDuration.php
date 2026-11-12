<?php

namespace App\Enums;

enum VacancyDuration: string
{
    case UnderTwoHours = 'kurang_2_jam';
    case TwoToFourHours = '2_4_jam';
    case FourToEightHours = '4_8_jam';
    case FullDay = 'seharian';

    /**
     * Get the human readable label for the duration.
     */
    public function label(): string
    {
        return match ($this) {
            self::UnderTwoHours => 'Kurang dari 2 jam',
            self::TwoToFourHours => '2 - 4 jam',
            self::FourToEightHours => '4 - 8 jam',
            self::FullDay => 'Seharian penuh',
        };
    }
}
