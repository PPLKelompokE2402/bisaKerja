<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use HasFactory;

    /**
     * Nama tabel terkait.
     */
    protected $table = 'job_applications';

    /**
     * Kolom yang dapat diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'user_id',
        'job_id',
        'applicant_name',
        'date_of_birth',
        'age',
        'residence',
        'skills',
        'cv_path',
        'status',
    ];

    /**
     * Tipe data casts atribut model.
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'age' => 'integer',
        ];
    }

    /**
     * Relasi ke pelamar (User).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke lowongan pekerjaan (Job).
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    /**
     * Memeriksa apakah lamaran dapat dibatalkan oleh pencari kerja.
     * Hanya lamaran yang masih dalam proses peninjauan (pending/menunggu) yang dapat dibatalkan.
     */
    public function isCancellable(): bool
    {
        return in_array(strtolower((string) $this->status), ['pending', 'menunggu'], true);
    }

    /**
     * Mendapatkan label status dalam Bahasa Indonesia sesuai screenshot.
     */
    public function getStatusLabelAttribute(): string
    {
        return match (strtolower((string) $this->status)) {
            'pending', 'menunggu'     => 'Menunggu',
            'accepted', 'diterima'    => 'Diterima',
            'rejected', 'ditolak'     => 'Ditolak',
            'completed', 'selesai'    => 'Selesai',
            'cancelled', 'dibatalkan' => 'Dibatalkan',
            default                   => ucfirst((string) $this->status),
        };
    }

    /**
     * Mendapatkan kelas CSS badge status sesuai styling mockup UI.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match (strtolower((string) $this->status)) {
            'pending', 'menunggu'     => 'bg-amber-50 text-amber-700 border border-amber-300',
            'accepted', 'diterima'    => 'bg-emerald-50 text-emerald-700 border border-emerald-300',
            'rejected', 'ditolak'     => 'bg-red-50 text-red-700 border border-red-300',
            'completed', 'selesai'    => 'bg-cyan-50 text-cyan-700 border border-cyan-300',
            'cancelled', 'dibatalkan' => 'bg-gray-100 text-gray-600 border border-gray-300',
            default                   => 'bg-gray-50 text-gray-700 border border-gray-300',
        };
    }

    /**
     * Format tanggal pengajuan lamaran dalam Bahasa Indonesia (contoh: 14 Oktober 2026).
     */
    public function getFormattedAppliedDateAttribute(): string
    {
        if (!$this->created_at) {
            return '-';
        }

        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $day = $this->created_at->format('j');
        $month = $bulanIndo[(int) $this->created_at->format('n')] ?? $this->created_at->format('F');
        $year = $this->created_at->format('Y');

        return "{$day} {$month} {$year}";
    }

    /**
     * Format upah dari relasi job (contoh: Rp80.000).
     */
    public function getFormattedSalaryAttribute(): string
    {
        if (!$this->job) {
            return '-';
        }

        $min = $this->job->salary_min;
        $max = $this->job->salary_max;

        if ($min && $max && $min !== $max) {
            return 'Rp' . number_format($min, 0, ',', '.') . ' - Rp' . number_format($max, 0, ',', '.');
        }

        if ($min) {
            return 'Rp' . number_format($min, 0, ',', '.');
        }

        if ($max) {
            return 'Rp' . number_format($max, 0, ',', '.');
        }

        return 'Sesuai kesepakatan';
    }
}
