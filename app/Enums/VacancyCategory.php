<?php

namespace App\Enums;

enum VacancyCategory: string
{
    case Cleaning = 'kebersihan';
    case Packing = 'packing';
    case HomeRepair = 'perbaikan_rumah';
    case Moving = 'angkut_pindahan';
    case Gardening = 'berkebun';
    case Other = 'lainnya';

    /**
     * Get the human readable label for the category.
     */
    public function label(): string
    {
        return match ($this) {
            self::Cleaning => 'Kebersihan',
            self::Packing => 'Packing & Gudang',
            self::HomeRepair => 'Perbaikan Rumah',
            self::Moving => 'Angkut & Pindahan',
            self::Gardening => 'Berkebun',
            self::Other => 'Lainnya',
        };
    }
}
