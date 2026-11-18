<?php

namespace Tests\Feature;

use App\Models\JobVacancy;
use App\Models\Lamaran;
use App\Models\User;
use Database\Factories\KategoriPekerjaanFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProviderApplicantTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/penyedia/pelamar-saya';

    public function test_provider_can_open_the_protected_page_with_an_existing_sanctum_token(): void
    {
        $provider = User::factory()->employer()->create();
        $token = $provider->createToken('test')->plainTextToken;

        $this->withToken($token)->getJson(self::ENDPOINT.'/halaman')
            ->assertOk()->assertSee('Pelamar Saya')->assertSee('applicants-empty')
            ->assertSee('applicants-loading')->assertSee('applicants-detail')
            ->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_all_new_endpoints_require_authentication(): void
    {
        foreach (['', '/halaman', '/1', '/1/berkas/cv', '/1/berkas/foto'] as $path) {
            $this->getJson(self::ENDPOINT.$path)->assertUnauthorized();
        }
    }

    public function test_provider_name_is_escaped_in_the_shared_layout(): void
    {
        Sanctum::actingAs(User::factory()->employer()->create(['nama' => '<script>alert("xss")</script>']));
        $this->getJson(self::ENDPOINT.'/halaman')->assertOk()
            ->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert("xss")</script>', false);
    }

    public function test_job_seekers_and_admins_cannot_access_any_new_endpoint(): void
    {
        foreach (['pencari_kerja', 'admin'] as $role) {
            Sanctum::actingAs(User::factory()->create(['role' => $role]));
            foreach (['', '/halaman', '/1', '/1/berkas/cv', '/1/berkas/foto'] as $path) {
                $this->getJson(self::ENDPOINT.$path)->assertForbidden();
            }
        }
    }

    public function test_list_and_summary_only_include_owned_jobs_and_preserve_statuses(): void
    {
        $provider = User::factory()->employer()->create();
        $job = JobVacancy::factory()->create(['id_penyedia' => $provider->getKey()]);
        $own = collect(['menunggu', 'diterima', 'ditolak'])->map(fn (string $status) => Lamaran::factory()->create(['id_lowongan' => $job->id_lowongan, 'status_lamaran' => $status])
        );
        $foreign = Lamaran::factory()->create();
        $unowned = Lamaran::factory()->create(['id_lowongan' => JobVacancy::factory()->create(['id_penyedia' => null])->id_lowongan]);
        Sanctum::actingAs($provider);

        $response = $this->getJson(self::ENDPOINT)->assertOk()
            ->assertExactJsonStructure(['summary' => ['total', 'menunggu', 'diterima', 'ditolak'], 'data' => ['*' => ['id', 'nama_pelamar', 'pekerjaan', 'tanggal_melamar', 'status', 'status_label']], 'meta' => ['current_page', 'last_page', 'per_page', 'total', 'from', 'to']])
            ->assertJsonPath('summary', ['total' => 3, 'menunggu' => 1, 'diterima' => 1, 'ditolak' => 1])
            ->assertJsonCount(3, 'data')->assertJsonPath('meta.total', 3)
            ->assertJsonMissing(['id' => $foreign->id_lamaran])->assertJsonMissing(['id' => $unowned->id_lamaran]);

        $this->assertEqualsCanonicalizing($own->pluck('id_lamaran')->all(), array_column($response->json('data'), 'id'));
        $this->assertEqualsCanonicalizing(['Menunggu', 'Diterima', 'Ditolak'], array_column($response->json('data'), 'status_label'));
        $this->assertDatabaseCount('lamaran', 5);
    }

    public function test_search_by_name_or_job_and_status_filter_can_be_combined_without_leaking_data(): void
    {
        $provider = User::factory()->employer()->create();
        $job = JobVacancy::factory()->create(['id_penyedia' => $provider->getKey(), 'judul' => 'Bersihkan halaman']);
        $siti = User::factory()->create(['nama' => 'Siti Aminah']);
        $application = Lamaran::factory()->create(['id_lowongan' => $job->id_lowongan, 'id_pencari' => $siti->getKey(), 'status_lamaran' => 'diterima']);
        Lamaran::factory()->create(['id_lowongan' => $job->id_lowongan, 'status_lamaran' => 'ditolak']);
        $foreign = Lamaran::factory()->create(['id_pencari' => $siti->getKey(), 'status_lamaran' => 'diterima']);
        Sanctum::actingAs($provider);

        $this->getJson(self::ENDPOINT.'?search=Siti')->assertOk()
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $application->id_lamaran);
        $this->getJson(self::ENDPOINT.'?search=halaman&status=diterima')->assertOk()
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $application->id_lamaran)
            ->assertJsonPath('summary.total', 2)->assertJsonMissing(['id' => $foreign->id_lamaran]);
        foreach (['menunggu' => 0, 'diterima' => 1, 'ditolak' => 1] as $status => $count) {
            $this->getJson(self::ENDPOINT.'?status='.$status)->assertOk()->assertJsonCount($count, 'data');
        }
        $this->getJson(self::ENDPOINT.'?search=tidak-ada')->assertOk()->assertJsonCount(0, 'data')
            ->assertJsonPath('summary.total', 2);
        $this->getJson(self::ENDPOINT.'?search=%27%20OR%201%3D1--')->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_invalid_search_status_and_pagination_are_rejected(): void
    {
        Sanctum::actingAs(User::factory()->employer()->create());
        foreach (['status=selesai', 'status[]=menunggu', 'search[]=test', 'search='.str_repeat('a', 101), 'page=0', 'page=-1', 'page=abc'] as $query) {
            $this->getJson(self::ENDPOINT.'?'.$query)->assertUnprocessable();
        }
    }

    public function test_pagination_has_a_stable_order_and_keeps_filters(): void
    {
        $provider = User::factory()->employer()->create();
        $job = JobVacancy::factory()->create(['id_penyedia' => $provider->getKey()]);
        $applications = Lamaran::factory()->count(16)->create(['id_lowongan' => $job->id_lowongan, 'tanggal_lamaran' => now()]);
        Sanctum::actingAs($provider);

        $first = $this->getJson(self::ENDPOINT.'?status=menunggu')->assertOk()
            ->assertJsonCount(15, 'data')->assertJsonPath('meta.last_page', 2)->assertJsonPath('meta.total', 16);
        $second = $this->getJson(self::ENDPOINT.'?status=menunggu&page=2')->assertOk()
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $applications->first()->id_lamaran)
            ->assertJsonPath('meta.current_page', 2)->assertJsonPath('summary.total', 16);
        $this->assertEmpty(array_intersect(array_column($first->json('data'), 'id'), array_column($second->json('data'), 'id')));
    }

    public function test_empty_provider_has_zero_counts_and_no_dummy_rows(): void
    {
        Sanctum::actingAs(User::factory()->employer()->create());
        $this->getJson(self::ENDPOINT)->assertOk()->assertJsonCount(0, 'data')
            ->assertJsonPath('summary', ['total' => 0, 'menunggu' => 0, 'diterima' => 0, 'ditolak' => 0])
            ->assertJsonPath('meta.total', 0);
    }

    public function test_details_only_expose_relevant_stored_fields(): void
    {
        Storage::fake('public');
        $application = Lamaran::factory()->create();
        $application->pencari->update(['keahlian' => 'Memasak', 'pengalaman_kerja' => 'Katering', 'alamat' => 'Alamat pribadi']);
        Sanctum::actingAs(User::findOrFail($application->lowongan->id_penyedia));

        $response = $this->getJson(self::ENDPOINT.'/'.$application->id_lamaran)->assertOk()
            ->assertJsonPath('data.pelamar.nama', $application->pencari->nama)
            ->assertJsonPath('data.pelamar.email', $application->pencari->email)
            ->assertJsonPath('data.pelamar.keahlian', 'Memasak')
            ->assertJsonPath('data.pelamar.has_cv', false)->assertJsonPath('data.pelamar.has_photo', false);
        foreach (['kata_sandi', 'remember_token', 'alamat', 'id_admin_verifikator', 'cv', 'foto_profil'] as $field) {
            $response->assertJsonMissingPath('data.pelamar.'.$field);
        }
        $this->getJson(self::ENDPOINT.'/'.$application->id_lamaran.'/berkas/cv')->assertNotFound();
    }

    public function test_other_providers_and_unowned_applications_are_inaccessible_including_files(): void
    {
        $foreign = Lamaran::factory()->create();
        $unowned = Lamaran::factory()->create(['id_lowongan' => JobVacancy::factory()->create(['id_penyedia' => null])->id_lowongan]);
        Sanctum::actingAs(User::factory()->employer()->create());
        foreach ([$foreign->id_lamaran, $unowned->id_lamaran, 999999] as $id) {
            foreach (['', '/berkas/cv', '/berkas/foto'] as $path) {
                $this->getJson(self::ENDPOINT.'/'.$id.$path)->assertNotFound();
            }
        }
    }

    public function test_owner_can_download_existing_cv_and_photo_without_exposing_storage_paths(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('cv/resume.pdf', '%PDF-1.4 test');
        Storage::disk('public')->put('foto-profil/photo.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII='));
        $application = Lamaran::factory()->create();
        $application->pencari->update(['cv' => 'cv/resume.pdf', 'foto_profil' => 'foto-profil/photo.png']);
        Sanctum::actingAs(User::findOrFail($application->lowongan->id_penyedia));

        $this->getJson(self::ENDPOINT.'/'.$application->id_lamaran)->assertOk()
            ->assertJsonPath('data.pelamar.has_cv', true)->assertJsonPath('data.pelamar.has_photo', true)
            ->assertDontSee('cv/resume.pdf')->assertDontSee('foto-profil/photo.png');
        $this->getJson(self::ENDPOINT.'/'.$application->id_lamaran.'/berkas/cv')->assertOk()
            ->assertDownload('CV-pelamar.pdf')->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Cache-Control', 'no-store, private')->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->getJson(self::ENDPOINT.'/'.$application->id_lamaran.'/berkas/foto')->assertOk()
            ->assertHeader('Content-Type', 'image/png');
    }

    public function test_invalid_stored_file_paths_cannot_be_used_to_read_other_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('secret.pdf', 'private');
        $application = Lamaran::factory()->create();
        Sanctum::actingAs(User::findOrFail($application->lowongan->id_penyedia));
        foreach (['../secret.pdf', 'cv/../secret.pdf', 'https://example.com/resume.pdf', 'secret.pdf', 'cv/file.html'] as $path) {
            $application->pencari->update(['cv' => $path]);
            $this->getJson(self::ENDPOINT.'/'.$application->id_lamaran.'/berkas/cv')->assertNotFound();
        }
    }

    public function test_existing_login_user_and_logout_still_use_the_same_token_flow(): void
    {
        $provider = User::factory()->employer()->create();
        $login = $this->postJson('/api/login', ['email' => $provider->email, 'kata_sandi' => 'password'])
            ->assertOk()->assertJsonPath('user.role', 'penyedia_pekerjaan');
        $token = $login->json('token');
        $this->withToken($token)->getJson('/api/user')->assertOk()->assertJsonPath('user.id_pengguna', $provider->getKey());
        $this->withToken($token)->getJson(self::ENDPOINT)->assertOk();
        $this->withToken($token)->postJson('/api/logout')->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_existing_job_seeker_registration_still_persists_profile_and_files(): void
    {
        Storage::fake('public');
        $this->postJson('/api/register/job-seeker', [
            'nama' => 'Pelamar Baru', 'email' => 'pelamar@example.test', 'kata_sandi' => 'password123',
            'alamat' => 'Koja', 'jenis_kelamin' => 'perempuan', 'deskripsi_diri' => 'Siap bekerja',
            'keahlian' => 'Memasak', 'pengalaman_kerja' => 'Katering',
            'foto_profil' => UploadedFile::fake()->createWithContent('photo.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII=')),
            'cv' => UploadedFile::fake()->create('resume.pdf', 10, 'application/pdf'),
        ])->assertCreated()->assertJsonPath('user.role', 'pencari_kerja')
            ->assertJsonPath('user.status_akun', 'menunggu_verifikasi');
        $user = User::where('email', 'pelamar@example.test')->firstOrFail();
        Storage::disk('public')->assertExists([$user->cv, $user->foto_profil]);
    }

    public function test_existing_web_navigation_still_renders(): void
    {
        foreach (['/', '/login', '/register', '/register/role', '/home', '/register/provider/biodata'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_monitoring_does_not_offer_a_status_mutation_endpoint(): void
    {
        $application = Lamaran::factory()->create();
        Sanctum::actingAs(User::findOrFail($application->lowongan->id_penyedia));
        $this->patchJson(self::ENDPOINT.'/'.$application->id_lamaran, ['status_lamaran' => 'diterima'])->assertStatus(405);
        $this->assertDatabaseHas('lamaran', ['id_lamaran' => $application->id_lamaran, 'status_lamaran' => 'menunggu']);
    }

    public function test_vacancy_page_and_monitoring_use_the_same_application_relationship(): void
    {
        $application = Lamaran::factory()->create();
        $provider = $application->lowongan->employer;
        $this->actingAs($provider)->get(route('employer.vacancies.index'))->assertOk()
            ->assertSeeText($application->lowongan->judul)->assertSeeText('1 pelamar')->assertSeeText('Pelamar Saya');
        $this->getJson(self::ENDPOINT)->assertOk()->assertJsonPath('summary.total', 1)
            ->assertJsonPath('data.0.id', $application->id_lamaran);
    }

    public function test_vacancy_create_update_and_status_flows_still_work_with_the_current_schema(): void
    {
        $provider = User::factory()->employer()->create();
        $category = KategoriPekerjaanFactory::new()->create();
        $payload = [
            'judul' => 'Bersihkan halaman', 'id_kategori' => $category->getKey(),
            'lokasi' => 'Koja', 'deskripsi' => 'Sapu halaman rumah',
        ];
        $this->actingAs($provider)->post(route('employer.vacancies.store'), $payload)
            ->assertRedirectToRoute('employer.vacancies.index')->assertSessionHasNoErrors();
        $vacancy = JobVacancy::query()->where('id_penyedia', $provider->getKey())->sole();
        $application = Lamaran::factory()->create(['id_lowongan' => $vacancy->getKey()]);
        $this->put(route('employer.vacancies.update', $vacancy), [...$payload, 'judul' => 'Rapikan taman'])
            ->assertRedirectToRoute('employer.vacancies.index')->assertSessionHasNoErrors();
        $this->patch(route('employer.vacancies.status.update', $vacancy), ['status' => 'ditutup'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('lowongan', ['id_lowongan' => $vacancy->getKey(), 'judul' => 'Rapikan taman', 'status_lowongan' => 'ditutup']);
        $this->getJson(self::ENDPOINT)->assertOk()->assertJsonPath('summary.total', 1)
            ->assertJsonPath('data.0.pekerjaan', 'Rapikan taman')->assertJsonPath('data.0.id', $application->getKey());
    }

    public function test_completed_work_does_not_replace_the_stored_application_status(): void
    {
        $application = Lamaran::factory()->create(['status_lamaran' => 'diterima', 'status_pekerjaan' => 'selesai']);
        Sanctum::actingAs($application->lowongan->employer);
        $this->getJson(self::ENDPOINT.'/'.$application->getKey())->assertOk()
            ->assertJsonPath('data.status', 'diterima')->assertJsonPath('data.status_label', 'Diterima')
            ->assertJsonPath('data.status_pekerjaan', 'selesai');
        $this->assertDatabaseHas('lamaran', ['id_lamaran' => $application->getKey(), 'status_lamaran' => 'diterima', 'status_pekerjaan' => 'selesai']);
    }
}
