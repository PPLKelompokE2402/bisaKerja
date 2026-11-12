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
}
