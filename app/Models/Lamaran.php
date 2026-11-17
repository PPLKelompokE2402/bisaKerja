<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lamaran extends Model
{
    use HasFactory;

    protected $table = 'lamaran';

    protected $primaryKey = 'id_lamaran';

    public const CREATED_AT = 'tanggal_lamaran';

    public const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'id_lowongan',
        'id_pencari',
        'pesan_lamaran',
        'status_lamaran',
        'status_pekerjaan',
        'tanggal_mulai',
        'tanggal_selesai',
        'catatan_penyelesaian',
    ];

    /**
     * Get the vacancy for this application.
     *
     * @return BelongsTo<JobVacancy, $this>
     */
    public function lowongan(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class, 'id_lowongan', 'id_lowongan');
    }

    /**
     * Get the job seeker who applied.
     *
     * @return BelongsTo<User, $this>
     */
    public function pencari(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pencari', 'id_pengguna');
    }
}

