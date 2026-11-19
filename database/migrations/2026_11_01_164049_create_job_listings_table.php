<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->string('title');           // Judul / Posisi Pekerjaan
            $table->string('company');         // Nama Perusahaan
            $table->string('location');        // Lokasi (Kota/Remote)
            $table->string('type');            // Full-time, Part-time, Internship
            $table->bigInteger('salary_min')->nullable(); // Gaji Minimal
            $table->bigInteger('salary_max')->nullable(); // Gaji Maksimal
            $table->text('description');       // Deskripsi Pekerjaan
            $table->text('requirements');      // Persyaratan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};