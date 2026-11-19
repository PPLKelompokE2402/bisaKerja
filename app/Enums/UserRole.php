<?php

namespace App\Enums;

enum UserRole: string
{
    case JobSeeker = 'pencari_kerja';
    case Employer = 'penyedia_pekerjaan';
    case Admin = 'admin';

    /**
     * Get the human readable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::JobSeeker => 'Pencari Kerja',
            self::Employer => 'Penyedia Pekerjaan',
            self::Admin => 'Admin',
        };
    }
}
