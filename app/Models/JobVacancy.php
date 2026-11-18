<?php

namespace App\Models;

use Database\Factories\JobVacancyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobVacancy extends Model
{
    /** @use HasFactory<JobVacancyFactory> */
    use HasFactory;

    protected $table = 'lowongan';

    protected $primaryKey = 'id_lowongan';

    public const CREATED_AT = 'dibuat_pada';

    public const UPDATED_AT = 'diperbarui_pada';

    protected $attributes = [
        'status_lowongan' => 'aktif',
        'upah' => 0,
    ];

    protected $fillable = [
        'id_penyedia',
        'id_kategori',
        'judul',
        'deskripsi',
        'lokasi',
        'latitude',
        'longitude',
        'upah',
        'waktu_pekerjaan',
        'status_lowongan',
    ];

    protected function casts(): array
    {
        return [
            'waktu_pekerjaan' => 'datetime',
            'upah' => 'decimal:2',
        ];
    }

    /**
     * Get the employer who posted the vacancy.
     *
     * @return BelongsTo<User, $this>
     */
    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_penyedia', 'id_pengguna');
    }

    /**
     * Get the category of the vacancy.
     *
     * @return BelongsTo<KategoriPekerjaan, $this>
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriPekerjaan::class, 'id_kategori', 'id_kategori');
    }

    /**
     * Get applications for this vacancy.
     *
     * @return HasMany<Lamaran, $this>
     */
    public function lamaran(): HasMany
    {
        return $this->hasMany(Lamaran::class, 'id_lowongan', 'id_lowongan');
    }

    /**
     * Determine whether the vacancy is open for applicants.
     */
    public function isOpen(): bool
    {
        return $this->status_lowongan === 'aktif';
    }

    /**
     * Get human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_lowongan) {
            'aktif' => 'Dibuka',
            'ditutup' => 'Ditutup',
            'selesai' => 'Selesai',
            default => ucfirst($this->status_lowongan ?? 'aktif'),
        };
    }

    /**
     * Dynamic applicants count.
     */
    public function getApplicantsCountAttribute(): int
    {
        return $this->lamaran()->count();
    }
}
