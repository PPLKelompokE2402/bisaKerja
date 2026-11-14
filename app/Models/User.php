<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'pengguna';

    protected $primaryKey = 'id_pengguna';

    public const CREATED_AT = 'dibuat_pada';

    public const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nama',
        'email',
        'kata_sandi',
        'nomor_telepon',
        'role',
        'status_akun',
        'alamat',
        'jenis_kelamin',
        'deskripsi_diri',
        'cv',
        'foto_profil',
        'keahlian',
        'pengalaman_kerja',
    ];

    protected $hidden = [
        'kata_sandi',
        'remember_token',
    ];

    /**
     * Get the password name for authentication.
     */
    public function getAuthPasswordName(): string
    {
        return 'kata_sandi';
    }

    /**
     * Get the password for authentication.
     */
    public function getAuthPassword(): string
    {
        return $this->kata_sandi;
    }

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName(): string
    {
        return 'id_pengguna';
    }

    /**
     * Compatibility accessor for name.
     */
    public function getNameAttribute(): ?string
    {
        return $this->attributes['nama'] ?? null;
    }

    /**
     * Get the job vacancies posted by the employer.
     *
     * @return HasMany<JobVacancy, $this>
     */
    public function jobVacancies(): HasMany
    {
        return $this->hasMany(JobVacancy::class, 'id_penyedia', 'id_pengguna');
    }

    /**
     * Determine whether the user is an employer (penyedia pekerjaan).
     */
    public function isEmployer(): bool
    {
        return ($this->role ?? '') === 'penyedia_pekerjaan';
    }

    protected function casts(): array
    {
        return [
            'kata_sandi' => 'hashed',
            'tanggal_verifikasi' => 'datetime',
            'dibuat_pada' => 'datetime',
            'diperbarui_pada' => 'datetime',
        ];
    }
}
