<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Job;

class JobSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Memastikan halaman utama pencarian lowongan (/jobs) dapat diakses dengan normal (HTTP 200)
     */
    public function test_halaman_pencarian_lowongan_dapat_diakses(): void
    {
        $response = $this->get('/jobs');

        $response->assertStatus(200);
        $response->assertSee('Cari pekerjaan di sekitar rumah');
    }

    /**
     * Test 2: Memastikan daftar lowongan dari database tampil di halaman pencarian
     */
    public function test_menampilkan_daftar_lowongan_yang_tersedia(): void
    {
        Job::create([
            'title' => 'Asisten Rumah Tangga Harian',
            'company' => 'Keluarga Budi',
            'location' => 'Jakarta Selatan',
            'type' => 'Harian',
            'description' => 'Membersihkan rumah dan menyetrika pakaian harian.',
            'requirements' => 'Jujur, rajin, dan berpengalaman.',
            'salary_min' => 150000,
            'salary_max' => 200000,
        ]);

        $response = $this->get('/jobs');

        $response->assertStatus(200);
        $response->assertSee('Asisten Rumah Tangga Harian');
        $response->assertSee('Jakarta Selatan');
    }

    /**
     * Test 3 (PBI-04): Pencarian berdasarkan kata kunci pekerjaan (keyword) berhasil memfilter data
     */
    public function test_pencarian_lowongan_berdasarkan_kata_kunci_pekerjaan(): void
    {
        Job::create([
            'title' => 'Tukang Kebun Harian',
            'company' => 'Perumahan Melati',
            'location' => 'Depok',
            'type' => 'Harian',
            'description' => 'Merapikan taman dan menyiram tanaman.',
            'requirements' => 'Mengerti perawatan tanaman dan sehat jasmani.',
            'salary_min' => 120000,
            'salary_max' => 150000,
        ]);

        Job::create([
            'title' => 'Jaga Toko Kelontong',
            'company' => 'Toko Berkah',
            'location' => 'Bekasi',
            'type' => 'Harian',
            'description' => 'Melayani pembeli di warung.',
            'requirements' => 'Ramah, teliti menghitung uang.',
            'salary_min' => 100000,
            'salary_max' => 130000,
        ]);

        $response = $this->get('/jobs?keyword=Kebun');

        $response->assertStatus(200);
        $response->assertSee('Tukang Kebun Harian');
        $response->assertDontSee('Jaga Toko Kelontong');
    }

    /**
     * Test 4 (PBI-05): Pencarian berdasarkan lokasi berhasil menampilkan lowongan sesuai kota
     */
    public function test_pencarian_lowongan_berdasarkan_lokasi(): void
    {
        Job::create([
            'title' => 'Kurir Paket Harian',
            'company' => 'Ekspedisi Cepat',
            'location' => 'Bandung',
            'type' => 'Harian',
            'description' => 'Mengantar paket area kota.',
            'requirements' => 'Memiliki SIM C dan motor pribadi.',
            'salary_min' => 150000,
            'salary_max' => 250000,
        ]);

        Job::create([
            'title' => 'Cuci Mobil Panggilan',
            'company' => 'Auto Clean',
            'location' => 'Surabaya',
            'type' => 'Harian',
            'description' => 'Cuci kendaraan di rumah pelanggan.',
            'requirements' => 'Teliti, cekatan, dan pekerja keras.',
            'salary_min' => 100000,
            'salary_max' => 180000,
        ]);

        $response = $this->get('/jobs?location=Bandung');

        $response->assertStatus(200);
        $response->assertSee('Kurir Paket Harian');
        $response->assertDontSee('Cuci Mobil Panggilan');
    }

    /**
     * Test 5 (PBI-04 & PBI-05): Pencarian kombinasi kata kunci DAN lokasi secara bersamaan
     */
    public function test_pencarian_kombinasi_kata_kunci_dan_lokasi(): void
    {
        Job::create([
            'title' => 'Barista Paruh Waktu',
            'company' => 'Kopi Senja',
            'location' => 'Jakarta Selatan',
            'type' => 'Freelance',
            'description' => 'Meracik kopi dan melayani pelanggan.',
            'requirements' => 'Berpenampilan menarik dan komunikatif.',
            'salary_min' => 120000,
            'salary_max' => 160000,
        ]);

        Job::create([
            'title' => 'Barista Paruh Waktu',
            'company' => 'Kopi Pagi',
            'location' => 'Bogor',
            'type' => 'Freelance',
            'description' => 'Meracik kopi shift pagi.',
            'requirements' => 'Disiplin waktu dan ramah.',
            'salary_min' => 110000,
            'salary_max' => 150000,
        ]);

        $response = $this->get('/jobs?keyword=Barista&location=Jakarta Selatan');

        $response->assertStatus(200);
        $response->assertSee('Kopi Senja');
        $response->assertDontSee('Kopi Pagi');
    }

    /**
     * Test 6: Menampilkan pesan kosong ("Lowongan tidak ditemukan") jika hasil pencarian nihil
     */
    public function test_menampilkan_pesan_kosong_jika_lowongan_tidak_ditemukan(): void
    {
        Job::create([
            'title' => 'Asisten Dapur',
            'company' => 'Warung Makan Sederhana',
            'location' => 'Tangerang',
            'type' => 'Harian',
            'description' => 'Membantu persiapan bahan masakan.',
            'requirements' => 'Menjaga kebersihan dan cekatan.',
            'salary_min' => 100000,
            'salary_max' => 140000,
        ]);

        $response = $this->get('/jobs?keyword=Astronot&location=Mars');

        $response->assertStatus(200);
        $response->assertSee('Lowongan tidak ditemukan');
    }

    /**
     * Test 7: Halaman detail lowongan (/jobs/{id}) dapat diakses dan menampilkan data lengkap
     */
    public function test_halaman_detail_lowongan_dapat_diakses(): void
    {
        $job = Job::create([
            'title' => 'Supir Pribadi Harian',
            'company' => 'Bapak Hendra',
            'location' => 'Jakarta Timur',
            'type' => 'Harian',
            'description' => 'Mengantar jemput anak sekolah dan keperluan keluarga.',
            'requirements' => 'Memiliki SIM A aktif dan hafal rute Jakarta.',
            'salary_min' => 200000,
            'salary_max' => 250000,
        ]);

        $response = $this->get('/jobs/' . $job->id);

        $response->assertStatus(200);
        $response->assertSee('Supir Pribadi Harian');
        $response->assertSee('Jakarta Timur');
    }

    /**
     * Test 8: Mengakses ID lowongan yang tidak ada di database mengembalikan halaman 404 Not Found
     */
    public function test_akses_detail_lowongan_tidak_valid_mengembalikan_404(): void
    {
        $response = $this->get('/jobs/99999');

        $response->assertStatus(404);
    }

    /**
     * Test 9 (Edge Case - Belum Diimplementasi): Filter pencarian berdasarkan batas minimal gaji
     */
    public function test_filter_pencarian_berdasarkan_gaji_minimal(): void
    {
        Job::create([
            'title' => 'Teknisi AC Panggilan',
            'company' => 'Dingin Sejuk',
            'location' => 'Jakarta Barat',
            'type' => 'Harian',
            'description' => 'Servis dan cuci AC rumah.',
            'requirements' => 'Berpengalaman servis AC.',
            'salary_min' => 250000,
            'salary_max' => 350000,
        ]);

        Job::create([
            'title' => 'Helper Gudang Harian',
            'company' => 'Logistik Jaya',
            'location' => 'Jakarta Barat',
            'type' => 'Harian',
            'description' => 'Bongkar muat barang ringan.',
            'requirements' => 'Fisik kuat dan jujur.',
            'salary_min' => 90000,
            'salary_max' => 110000,
        ]);

        $response = $this->get('/jobs?min_salary=200000');

        $response->assertStatus(200);
        $response->assertSee('Teknisi AC Panggilan');
        $response->assertDontSee('Helper Gudang Harian');
    }

    /**
     * Test 10 (Negative Case - Validasi Input): Menolak input pencarian yang melebihi batas maksimal 100 karakter
     */
    public function test_validasi_menolak_input_keyword_terlalu_panjang(): void
    {
        $longKeyword = str_repeat('a', 150);

        $response = $this->get('/jobs?keyword=' . $longKeyword);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('keyword');
    }
}