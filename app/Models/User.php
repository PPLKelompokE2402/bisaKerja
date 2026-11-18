<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'pengguna';

    protected $primaryKey = 'id_pengguna';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nama',
        'email',
        'kata_sandi',
        'nomor_telepon',
        'alamat',
        'tanggal_lahir',
        'jenis_kelamin',
        'deskripsi_diri',
        'keahlian',
        'pengalaman_kerja',
        'cv',
        'foto_profil',
        'jenis_pemberi',
        'latitude',
        'longitude',
        'role',
        'status_akun',
        'tanggal_verifikasi',
        'id_admin_verifikator',
        'catatan_verifikasi',
    ];

    protected $hidden = [
        'kata_sandi',
    ];
}
