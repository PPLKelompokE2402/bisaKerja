<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id('id_pengguna');
            $table->string('nama', 255);
            $table->string('email', 150)->unique();
            $table->string('kata_sandi');
            $table->string('nomor_telepon', 20)->nullable();
            $table->string('role')->default('pencari_kerja')->index();
            $table->string('status_akun')->default('menunggu_verifikasi');
            $table->text('alamat')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->text('deskripsi_diri')->nullable();
            $table->string('cv')->nullable();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->foreignId('id_admin_verifikator')->nullable()
                ->constrained('pengguna', 'id_pengguna')->nullOnDelete();
            $table->text('catatan_verifikasi')->nullable();
            $table->rememberToken();
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
