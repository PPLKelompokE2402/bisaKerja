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
        Schema::table('pengguna', function (Blueprint $table) {
            $table->string('foto_profil', 255)->nullable()->after('cv');
            $table->text('keahlian')->nullable()->after('foto_profil');
            $table->text('pengalaman_kerja')->nullable()->after('keahlian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
               $table->dropColumn([
                'foto_profil',
                'keahlian',
                'pengalaman_kerja',
            ]);
        });
    }
};
