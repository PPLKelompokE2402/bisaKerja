
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Nama boleh kosong saat registrasi awal
        Schema::table('pengguna', function (Blueprint $table) {
            $table->string('nama', 100)->nullable()->change();
        });

        // Tambahkan status belum_lengkap
        DB::statement("
            ALTER TABLE pengguna
            MODIFY status_akun
            ENUM(
                'belum_lengkap',
                'menunggu_verifikasi',
                'aktif',
                'ditangguhkan',
                'ditolak'
            )
            NOT NULL DEFAULT 'belum_lengkap'
        ");
    }

    public function down(): void
    {
        // Kembalikan status baru ke status sebelumnya
        DB::table('pengguna')
            ->where('status_akun', 'belum_lengkap')
            ->update(['status_akun' => 'menunggu_verifikasi']);

        DB::statement("
            ALTER TABLE pengguna
            MODIFY status_akun
            ENUM(
                'menunggu_verifikasi',
                'aktif',
                'ditangguhkan',
                'ditolak'
            )
            NOT NULL DEFAULT 'menunggu_verifikasi'
        ");

        // Kembalikan nama menjadi NOT NULL
        Schema::table('pengguna', function (Blueprint $table) {
            $table->string('nama', 100)->nullable(false)->change();
        });
    }
};
