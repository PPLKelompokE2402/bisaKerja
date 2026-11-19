<?php

namespace Tests\Feature;

use App\Models\JobVacancy;
use App\Models\KategoriPekerjaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_search_filters_by_active_category_and_location(): void
    {
        $cleaning = KategoriPekerjaan::factory()->create(['nama_kategori' => 'Kebersihan']);
        $gardening = KategoriPekerjaan::factory()->create(['nama_kategori' => 'Taman']);
        $provider = User::factory()->employer()->create();

        JobVacancy::factory()->create([
            'id_penyedia' => $provider->getKey(),
            'id_kategori' => $cleaning->getKey(),
            'judul' => 'Bersihkan rumah Bandung',
            'lokasi' => 'Bandung',
        ]);
        JobVacancy::factory()->create([
            'id_penyedia' => $provider->getKey(),
            'id_kategori' => $gardening->getKey(),
            'judul' => 'Rawat taman Bandung',
            'lokasi' => 'Bandung',
        ]);
        JobVacancy::factory()->create([
            'id_penyedia' => $provider->getKey(),
            'id_kategori' => $cleaning->getKey(),
            'judul' => 'Bersihkan rumah Jakarta',
            'lokasi' => 'Jakarta',
        ]);
        JobVacancy::factory()->closed()->create([
            'id_penyedia' => $provider->getKey(),
            'id_kategori' => $cleaning->getKey(),
            'judul' => 'Lowongan ditutup Bandung',
            'lokasi' => 'Bandung',
        ]);

        $response = $this->get(route('jobs.index', [
            'id_kategori' => $cleaning->getKey(),
            'location' => 'Bandung',
        ]));

        $response->assertOk()
            ->assertSeeText('Bersihkan rumah Bandung')
            ->assertDontSeeText('Rawat taman Bandung')
            ->assertDontSeeText('Bersihkan rumah Jakarta')
            ->assertDontSeeText('Lowongan ditutup Bandung');
    }

    public function test_job_search_filters_by_title_location_and_category_independently(): void
    {
        $cleaning = KategoriPekerjaan::factory()->create(['nama_kategori' => 'Kebersihan']);
        $gardening = KategoriPekerjaan::factory()->create(['nama_kategori' => 'Taman']);
        $provider = User::factory()->employer()->create();

        JobVacancy::factory()->create([
            'id_penyedia' => $provider->getKey(),
            'id_kategori' => $cleaning->getKey(),
            'judul' => 'Bersihkan rumah Bandung',
            'lokasi' => 'Bandung',
        ]);
        JobVacancy::factory()->create([
            'id_penyedia' => $provider->getKey(),
            'id_kategori' => $cleaning->getKey(),
            'judul' => 'Bersihkan rumah Jakarta',
            'lokasi' => 'Jakarta',
        ]);
        JobVacancy::factory()->create([
            'id_penyedia' => $provider->getKey(),
            'id_kategori' => $gardening->getKey(),
            'judul' => 'Rawat taman Bandung',
            'lokasi' => 'Bandung',
        ]);

        $this->get(route('jobs.index', ['keyword' => 'Rawat']))
            ->assertOk()
            ->assertSeeText('Rawat taman Bandung')
            ->assertDontSeeText('Bersihkan rumah Bandung')
            ->assertDontSeeText('Bersihkan rumah Jakarta');

        $this->get(route('jobs.index', ['location' => 'Jakarta']))
            ->assertOk()
            ->assertSeeText('Bersihkan rumah Jakarta')
            ->assertDontSeeText('Bersihkan rumah Bandung')
            ->assertDontSeeText('Rawat taman Bandung');

        $this->get(route('jobs.index', ['id_kategori' => $cleaning->getKey()]))
            ->assertOk()
            ->assertSeeText('Bersihkan rumah Bandung')
            ->assertSeeText('Bersihkan rumah Jakarta')
            ->assertDontSeeText('Rawat taman Bandung');
    }

    public function test_inactive_categories_cannot_be_used_as_search_filters(): void
    {
        $category = KategoriPekerjaan::factory()->create(['status_kategori' => 'nonaktif']);

        $this->get(route('jobs.index', ['id_kategori' => $category->getKey()]))
            ->assertSessionHasErrors('id_kategori');
    }

    public function test_landing_page_hides_vacancies_from_suspended_providers(): void
    {
        $provider = User::factory()->employer()->create(['status_akun' => 'ditangguhkan']);
        JobVacancy::factory()->create([
            'id_penyedia' => $provider->getKey(),
            'judul' => 'Lowongan penyedia ditangguhkan',
        ]);

        $this->get(route('welcome'))
            ->assertOk()
            ->assertDontSeeText('Lowongan penyedia ditangguhkan');
    }

    public function test_active_vacancy_from_verified_provider_appears_on_both_public_pages_and_detail(): void
    {
        $provider = User::factory()->employer()->create(['status_akun' => 'aktif']);
        $vacancy = JobVacancy::factory()->create([
            'id_penyedia' => $provider->getKey(),
            'judul' => 'Bersihkan selokan',
        ]);

        $this->get(route('welcome'))
            ->assertOk()
            ->assertSeeText('Bersihkan selokan');
        $this->get(route('jobs.index'))
            ->assertOk()
            ->assertSeeText('Bersihkan selokan');
        $this->get(route('jobs.show', $vacancy->getKey()))
            ->assertOk()
            ->assertSeeText('Bersihkan selokan');
    }

    public function test_closed_and_unverified_provider_vacancies_are_not_public_or_openable(): void
    {
        $pendingProvider = User::factory()->employer()->create([
            'status_akun' => 'menunggu_verifikasi',
        ]);
        $unverifiedVacancy = JobVacancy::factory()->create([
            'id_penyedia' => $pendingProvider->getKey(),
            'judul' => 'Lowongan penyedia belum diverifikasi',
        ]);
        $activeProvider = User::factory()->employer()->create(['status_akun' => 'aktif']);
        $closedVacancy = JobVacancy::factory()->closed()->create([
            'id_penyedia' => $activeProvider->getKey(),
            'judul' => 'Lowongan yang sudah ditutup',
        ]);

        foreach ([route('welcome'), route('jobs.index')] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertDontSeeText('Lowongan penyedia belum diverifikasi')
                ->assertDontSeeText('Lowongan yang sudah ditutup');
        }

        $this->get(route('jobs.show', $unverifiedVacancy->getKey()))->assertNotFound();
        $this->get(route('jobs.show', $closedVacancy->getKey()))->assertNotFound();
    }
}
