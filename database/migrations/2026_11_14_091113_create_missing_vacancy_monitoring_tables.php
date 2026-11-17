<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $legacy = Schema::hasTable('lamaran') && ! Schema::hasColumn('lamaran', 'id_lamaran');
        if ($legacy && (DB::table('lamaran')->exists() || Schema::hasTable('lamaran_monitoring_legacy'))) {
            throw new RuntimeException('Skema lamaran lama perlu dipetakan terlebih dahulu. Migration dihentikan agar data lamaran tidak berubah.');
        }

        if (! Schema::hasTable('kategori_pekerjaan')) {
            Schema::create('kategori_pekerjaan', function (Blueprint $table): void {
                $table->id('id_kategori');
                $table->string('nama_kategori', 100);
                $table->text('deskripsi')->nullable();
                $table->string('status_kategori')->default('aktif');
            });
        }

        if (! Schema::hasTable('lowongan')) {
            Schema::create('lowongan', function (Blueprint $table): void {
                $table->id('id_lowongan');
                $table->foreignId('id_penyedia')->nullable()->constrained('pengguna', 'id_pengguna')->restrictOnDelete();
                $table->foreignId('id_kategori')->nullable()->constrained('kategori_pekerjaan', 'id_kategori')->nullOnDelete();
                $table->string('judul', 150);
                $table->text('deskripsi');
                $table->string('lokasi');
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->decimal('upah', 15, 2)->default(0);
                $table->timestamp('waktu_pekerjaan')->nullable();
                $table->string('status_lowongan')->default('aktif');
                $table->timestamp('dibuat_pada')->nullable();
                $table->timestamp('diperbarui_pada')->nullable();
            });
        }

        if ($legacy) {
            Schema::rename('lamaran', 'lamaran_monitoring_legacy');
        }

        if (! Schema::hasTable('lamaran')) {
            Schema::create('lamaran', function (Blueprint $table): void {
                $table->id('id_lamaran');
                $table->foreignId('id_lowongan')->constrained('lowongan', 'id_lowongan')->restrictOnDelete();
                $table->foreignId('id_pencari')->constrained('pengguna', 'id_pengguna')->restrictOnDelete();
                $table->text('pesan_lamaran')->nullable();
                $table->string('status_lamaran')->default('menunggu');
                $table->string('status_pekerjaan')->nullable();
                $table->timestamp('tanggal_lamaran')->nullable();
                $table->timestamp('diperbarui_pada')->nullable();
                $table->timestamp('tanggal_mulai')->nullable();
                $table->timestamp('tanggal_selesai')->nullable();
                $table->text('catatan_penyelesaian')->nullable();
                $table->unique(['id_lowongan', 'id_pencari']);
            });
        }
    }

    /**
     * Block destructive rollback because some tables may have existed before this migration.
     */
    public function down(): void
    {
        throw new RuntimeException('Rollback otomatis dinonaktifkan untuk melindungi tabel yang mungkin sudah ada. Gunakan migration perbaikan maju.');
    }
};
