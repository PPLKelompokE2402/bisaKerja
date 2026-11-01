<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    // Menghubungkan Model Job ke tabel job_listings di MySQL
    protected $table = 'job_listings';

    // Mengizinkan semua kolom diisi (mass assignment)
    protected $guarded = [];
}