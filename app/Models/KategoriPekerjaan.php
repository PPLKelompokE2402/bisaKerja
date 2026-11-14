<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriPekerjaan extends Model
{
    use HasFactory;

    protected $table = 'kategori_pekerjaan';

    protected $primaryKey = 'id_kategori';

    public $timestamps = false;

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
        'status_kategori',
    ];

    /**
     * Get the vacancies in this category.
     *
     * @return HasMany<JobVacancy, $this>
     */
    public function lowongan(): HasMany
    {
        return $this->hasMany(JobVacancy::class, 'id_kategori', 'id_kategori');
    }
}

