<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        $jobs = [
            [
                'title' => 'Asisten Rumah Tangga (ART)',
                'company' => 'Keluarga Pak Budi',
                'location' => 'Jakarta Selatan',
                'type' => 'Full-time',
                'salary_min' => 2500000,
                'salary_max' => 3500000,
                'description' => 'Mencari ART berpengalaman untuk memasak, mencuci, dan membersihkan rumah harian.',
                'requirements' => "1. Jujur dan bertanggung jawab\n2. Berpengalaman minimal 1 tahun\n3. Memiliki KTP aktif",
            ],
            [
                'title' => 'Tukang Kebun & Bersih Halaman',
                'company' => 'Bu Siska',
                'location' => 'Jakarta Timur',
                'type' => 'Part-time',
                'salary_min' => 150000,
                'salary_max' => 250000,
                'description' => 'Dibutuhkan jasa pemotongan rumput dan perawatan tanaman seminggu 2 kali.',
                'requirements' => "1. Paham cara merawat tanaman hias\n2. Membawa peralatan pemotong rumput sendiri",
            ],
            [
                'title' => 'Pengasuh Anak / Baby Sitter',
                'company' => 'Keluarga Dokter Rian',
                'location' => 'Jakarta Barat',
                'type' => 'Full-time',
                'salary_min' => 3000000,
                'salary_max' => 4500000,
                'description' => 'Mendampingi dan merawat balita usia 2 tahun, menyiapkan makanan dan perlengkapan anak.',
                'requirements' => "1. Sabar dan sayang anak\n2. Diutamakan lulusan kebidanan/keperawatan/pengalaman Baby Sitter",
            ],
            [
                'title' => 'Sopir Pribadi',
                'company' => 'Pak Hendra',
                'location' => 'Jakarta Utara',
                'type' => 'Full-time',
                'salary_min' => 3500000,
                'salary_max' => 4500000,
                'description' => 'Mengantar jemput majikan dan keluarga untuk keperluan harian serta merawat kebersihan mobil.',
                'requirements' => "1. Memiliki SIM A aktif\n2. Hafal rute jalanan Jabodetabek",
            ],
            [
                'title' => 'Tukang Cat & Perbaikan Rumah',
                'company' => 'Renovasi Home',
                'location' => 'Jakarta Pusat',
                'type' => 'Contract',
                'salary_min' => 200000,
                'salary_max' => 300000,
                'description' => 'Pengecatan ulang tembok interior dan perbaikan bocor plafon ringan.',
                'requirements' => "1. Berpengalaman di bidang pertukangan rumah\n2. Rapi dan teliti",
            ],
            [
                'title' => 'Laundry & Setrika Harian',
                'company' => 'Laundry Berkah Rumahan',
                'location' => 'Bekasi',
                'type' => 'Part-time',
                'salary_min' => 100000,
                'salary_max' => 180000,
                'description' => 'Membantu proses mencuci lipat dan setrika uap pakaian harian.',
                'requirements' => "1. Rapi dalam melipat dan menyetrika\n2. Bekerja dengan cepat",
            ],
        ];

        foreach ($jobs as $job) {
            Job::create($job);
        }
    }
}