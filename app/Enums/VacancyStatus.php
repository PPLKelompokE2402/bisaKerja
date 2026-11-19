<?php

namespace App\Enums;

enum VacancyStatus: string
{
    case Open = 'dibuka';
    case Closed = 'ditutup';

    /**
     * Get the human readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Open => 'Dibuka',
            self::Closed => 'Ditutup',
        };
    }
}
