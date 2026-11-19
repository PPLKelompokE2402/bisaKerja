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
        Sanctum::actingAs($provider);

        $response = $this->getJson(self::ENDPOINT)->assertOk()
            ->assertExactJsonStructure(['summary' => ['total', 'menunggu', 'diterima', 'ditolak'], 'data' => ['*' => ['id', 'nama_pelamar', 'pekerjaan', 'tanggal_melamar', 'status', 'status_label']], 'meta' => ['current_page', 'last_page', 'per_page', 'total', 'from', 'to']])
            ->assertJsonPath('summary', ['total' => 3, 'menunggu' => 1, 'diterima' => 1, 'ditolak' => 1])
            ->assertJsonCount(3, 'data')->assertJsonPath('meta.total', 3)
            ->assertJsonMissing(['id' => $foreign->id_lamaran]);

        $this->assertEqualsCanonicalizing($own->pluck('id_lamaran')->all(), array_column($response->json('data'), 'id'));
        $this->assertEqualsCanonicalizing(['Menunggu', 'Diterima', 'Ditolak'], array_column($response->json('data'), 'status_label'));
        $this->assertDatabaseCount('lamaran', 4);
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
        Storage::fake('local');
        $application = Lamaran::factory()->create();
        $application->pencari->update([
            'tanggal_lahir' => '1990-04-03',
            'keahlian' => 'Memasak',
            'pengalaman_kerja' => 'Katering',
            'alamat' => 'Alamat pribadi',
        ]);
        Sanctum::actingAs(User::findOrFail($application->lowongan->id_penyedia));

        $response = $this->getJson(self::ENDPOINT.'/'.$application->id_lamaran)->assertOk()
            ->assertJsonPath('data.pelamar.nama', $application->pencari->nama)
            ->assertJsonPath('data.pelamar.email', $application->pencari->email)
            ->assertJsonPath('data.pelamar.tanggal_lahir', '1990-04-03')
            ->assertJsonPath('data.pelamar.alamat', 'Alamat pribadi')
            ->assertJsonPath('data.pelamar.keahlian', 'Memasak')
            ->assertJsonPath('data.pelamar.has_cv', false)->assertJsonPath('data.pelamar.has_photo', false);
        foreach (['kata_sandi', 'remember_token', 'id_admin_verifikator', 'cv', 'foto_profil'] as $field) {
            $response->assertJsonMissingPath('data.pelamar.'.$field);
        }
        $this->getJson(self::ENDPOINT.'/'.$application->id_lamaran.'/berkas/cv')->assertNotFound();
    }

    public function test_other_providers_applications_are_inaccessible_including_files(): void
    {
        $foreign = Lamaran::factory()->create();
        Sanctum::actingAs(User::factory()->employer()->create());
        foreach ([$foreign->id_lamaran, 999999] as $id) {
            foreach (['', '/berkas/cv', '/berkas/foto'] as $path) {
                $this->getJson(self::ENDPOINT.'/'.$id.$path)->assertNotFound();
            }
        }
    }

    public function test_owner_can_download_existing_cv_and_photo_without_exposing_storage_paths(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Storage::disk('local')->put('cv/resume.pdf', '%PDF-1.4 test');
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
        Storage::disk('public')->assertMissing('cv/resume.pdf');
    }

    public function test_invalid_stored_file_paths_cannot_be_used_to_read_other_files(): void
    {
        Storage::fake('public');
        Storage::fake('local');
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
        $login = $this->postJson('/api/login', ['login' => $provider->email, 'kata_sandi' => 'password'])
            ->assertOk()->assertJsonPath('user.role', 'penyedia_pekerjaan');
        $token = $login->json('token');
        $this->withToken($token)->getJson('/api/user')->assertOk()->assertJsonPath('user.id_pengguna', $provider->getKey());
        $this->withToken($token)->getJson(self::ENDPOINT)->assertOk();
        $this->withToken($token)->postJson('/api/logout')->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_job_seekers_providers_and_admins_can_log_in_with_their_stored_roles(): void
    {
        $provider = User::factory()->employer()->create();
        $vacancy = JobVacancy::factory()->create(['id_penyedia' => $provider->getKey()]);
        $users = [
            'pencari_kerja' => User::factory()->create(),
            'penyedia_pekerjaan' => $provider,
            'admin' => User::factory()->create(['role' => 'admin']),
        ];

        foreach ($users as $role => $user) {
            $login = $this->postJson('/api/login', [
                'login' => $user->email,
                'kata_sandi' => 'password',
            ])->assertOk()
                ->assertJsonPath('user.id_pengguna', $user->getKey())
                ->assertJsonPath('user.role', $role)
                ->assertJsonStructure(['token', 'token_type']);

            $this->postJson('/auth/session', ['token' => $login->json('token')])->assertOk();

            if ($role === 'admin') {
                $this->get(route('home'))->assertRedirectToRoute('admin.dashboard');
                $this->get(route('admin.dashboard'))->assertOk();
                $this->get(route('employer.vacancies.index'))->assertForbidden();
            } elseif ($role === 'penyedia_pekerjaan') {
                $this->get(route('home'))->assertOk()->assertViewIs('jobs.home');
                $this->get(route('employer.vacancies.index'))->assertOk();
                $this->get(route('admin.dashboard'))->assertForbidden();
            } else {
                $this->get(route('home'))->assertRedirectToRoute('jobs.index');
                $this->post(route('jobs.apply.store', $vacancy->id_lowongan))
                    ->assertRedirectToRoute('jobs.apply', $vacancy->id_lowongan);
                $this->get(route('employer.vacancies.index'))->assertForbidden();
            }
        }
    }

    public function test_job_seeker_registration_persists_identity_and_requires_profile_completion(): void
    {
        $this->postJson('/api/register/job-seeker', [
            'username' => 'pelamar-baru',
            'email' => 'pelamar@example.test',
            'kata_sandi' => 'password123',
            'role' => 'pencari_kerja',
        ])->assertCreated()->assertJsonPath('user.role', 'pencari_kerja')
            ->assertJsonPath('user.status_akun', 'belum_lengkap');
        $user = User::where('email', 'pelamar@example.test')->firstOrFail();
        $this->assertSame('pelamar-baru', $user->username);
        $this->assertSame('belum_lengkap', $user->status_akun);
    }

    public function test_editing_an_active_provider_profile_without_new_verification_document_keeps_it_active(): void
    {
        Storage::fake('public');
        $provider = User::factory()->employer()->create([
            'status_akun' => 'aktif',
            'dokumen_verifikasi' => 'dokumen-penyedia/current-document.pdf',
        ]);
        Sanctum::actingAs($provider);

        $this->postJson('/api/provider/profile', [
            'nama' => 'Penyedia Aktif',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Bandung',
            'jenis_pemberi' => 'Individu',
        ])->assertOk()->assertJsonPath('user.status_akun', 'aktif');

        $this->assertDatabaseHas('pengguna', [
            'id_pengguna' => $provider->getKey(),
            'status_akun' => 'aktif',
        ]);
    }

    public function test_replacing_verification_document_requeues_review_and_replaces_old_file_safely(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('dokumen-penyedia/old.pdf', '%PDF-old');
        Storage::disk('public')->put('foto-penyedia/old.jpg', 'old-photo');
        $provider = User::factory()->employer()->create([
            'status_akun' => 'aktif',
            'dokumen_verifikasi' => 'dokumen-penyedia/old.pdf',
            'foto_profil' => 'foto-penyedia/old.jpg',
        ]);
        Sanctum::actingAs($provider);

        $this->postJson('/api/provider/profile', [
            'nama' => 'Penyedia Diperbarui',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Bandung',
            'jenis_pemberi' => 'Individu',
            'dokumen_verifikasi' => UploadedFile::fake()->create('new.pdf', 10, 'application/pdf'),
            'foto_profil' => UploadedFile::fake()->image('new.jpg'),
        ])->assertOk()->assertJsonPath('user.status_akun', 'menunggu_verifikasi');

        $provider->refresh();
        $this->assertNotSame('dokumen-penyedia/old.pdf', $provider->dokumen_verifikasi);
        $this->assertNotSame('foto-penyedia/old.jpg', $provider->foto_profil);
        Storage::disk('public')->assertMissing('dokumen-penyedia/old.pdf');
        Storage::disk('public')->assertMissing('foto-penyedia/old.jpg');
        Storage::disk('public')->assertExists([$provider->dokumen_verifikasi, $provider->foto_profil]);
        $this->assertDatabaseHas('pengguna', [
            'id_pengguna' => $provider->getKey(),
            'status_akun' => 'menunggu_verifikasi',
            'dokumen_verifikasi' => $provider->dokumen_verifikasi,
            'foto_profil' => $provider->foto_profil,
        ]);
    }

    public function test_provider_profile_menu_opens_the_completion_form_for_incomplete_biodata(): void
    {
        $provider = User::factory()->employer()->create([
            'status_akun' => 'belum_lengkap',
        ]);

        $this->actingAs($provider)
            ->get(route('provider.biodata'))
            ->assertOk()
            ->assertViewIs('auth.biodata-penyedia')
            ->assertSee('Selesaikan Pendaftaran')
            ->assertSee('name="nama"', false);
    }

    public function test_complete_provider_profile_displays_the_saved_database_information(): void
    {
        $provider = User::factory()->employer()->create([
            'nama' => 'Penyedia Tersimpan',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Bandung',
            'jenis_kelamin' => 'Perempuan',
            'deskripsi_diri' => 'Menyediakan pekerjaan rumah tangga.',
            'jenis_pemberi' => 'Usaha/UMKM',
            'nama_usaha' => 'Usaha Bersih',
            'dokumen_verifikasi' => 'dokumen-penyedia/tersimpan.pdf',
            'status_akun' => 'aktif',
        ]);

        $this->actingAs($provider)
            ->get(route('provider.biodata'))
            ->assertOk()
            ->assertViewIs('provider.profile')
            ->assertSee('Penyedia Tersimpan')
            ->assertSee('081234567890')
            ->assertSee('Bandung')
            ->assertSee('Menyediakan pekerjaan rumah tangga.')
            ->assertSee('Usaha Bersih')
            ->assertSee('Aktif / terverifikasi')
            ->assertSee(route('provider.profile.edit'));
    }

    public function test_edit_profile_form_is_prefilled_and_profile_changes_keep_active_status(): void
    {
        $provider = User::factory()->employer()->create([
            'nama' => 'Nama Sebelum',
            'nomor_telepon' => '081111111111',
            'alamat' => 'Lokasi Sebelum',
            'jenis_kelamin' => 'Laki-laki',
            'deskripsi_diri' => 'Deskripsi sebelum.',
            'jenis_pemberi' => 'Individu',
            'dokumen_verifikasi' => 'dokumen-penyedia/current.pdf',
            'status_akun' => 'aktif',
        ]);

        $this->actingAs($provider)
            ->get(route('provider.profile.edit'))
            ->assertOk()
            ->assertViewIs('auth.biodata-penyedia')
            ->assertSee('value="Nama Sebelum"', false)
            ->assertSee('value="081111111111"', false)
            ->assertSee('value="Lokasi Sebelum"', false)
            ->assertSee('Deskripsi sebelum.')
            ->assertSee('Simpan Perubahan');

        $this->put(route('provider.profile.update'), [
            'nama' => 'Nama Sesudah',
            'nomor_telepon' => '082222222222',
            'alamat' => 'Lokasi Sesudah',
            'jenis_kelamin' => 'Perempuan',
            'deskripsi_diri' => 'Deskripsi sesudah.',
            'jenis_pemberi' => 'Individu',
        ])
            ->assertRedirectToRoute('provider.biodata')
            ->assertSessionHas('status', 'Biodata berhasil diperbarui.');

        $this->assertDatabaseHas('pengguna', [
            'id_pengguna' => $provider->getKey(),
            'nama' => 'Nama Sesudah',
            'nomor_telepon' => '082222222222',
            'alamat' => 'Lokasi Sesudah',
            'status_akun' => 'aktif',
        ]);
    }

    public function test_replacing_document_from_profile_form_requeues_verification(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('dokumen-penyedia/current.pdf', '%PDF-current');
        $provider = User::factory()->employer()->create([
            'nama' => 'Penyedia Aktif',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Bandung',
            'jenis_pemberi' => 'Individu',
            'dokumen_verifikasi' => 'dokumen-penyedia/current.pdf',
            'status_akun' => 'aktif',
        ]);

        $this->actingAs($provider)
            ->put(route('provider.profile.update'), [
                'nama' => 'Penyedia Aktif',
                'nomor_telepon' => '081234567890',
                'alamat' => 'Bandung',
                'jenis_pemberi' => 'Individu',
                'dokumen_verifikasi' => UploadedFile::fake()->create('new.pdf', 10, 'application/pdf'),
            ])
            ->assertRedirectToRoute('provider.biodata');

        $provider->refresh();
        $this->assertSame('menunggu_verifikasi', $provider->status_akun);
        $this->assertNotSame('dokumen-penyedia/current.pdf', $provider->dokumen_verifikasi);
        Storage::disk('public')->assertMissing('dokumen-penyedia/current.pdf');
        Storage::disk('public')->assertExists($provider->dokumen_verifikasi);
    }

    public function test_provider_profile_routes_are_authenticated_role_restricted_and_scoped_to_the_current_user(): void
    {
        $this->get(route('provider.biodata'))->assertRedirectToRoute('login');

        $jobSeeker = User::factory()->create();
        $this->actingAs($jobSeeker)->get(route('provider.biodata'))->assertForbidden();

        $provider = User::factory()->employer()->create([
            'nama' => 'Profil Milik Saya',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Bandung',
            'jenis_pemberi' => 'Individu',
            'dokumen_verifikasi' => 'dokumen-penyedia/milik-saya.pdf',
            'status_akun' => 'aktif',
        ]);
        $otherProvider = User::factory()->employer()->create([
            'nama' => 'Profil Penyedia Lain',
            'nomor_telepon' => '089999999999',
            'alamat' => 'Jakarta',
            'jenis_pemberi' => 'Individu',
            'dokumen_verifikasi' => 'dokumen-penyedia/milik-lain.pdf',
            'status_akun' => 'aktif',
        ]);

        $this->actingAs($provider)
            ->get(route('provider.biodata'))
            ->assertOk()
            ->assertSee('Profil Milik Saya')
            ->assertDontSee('Profil Penyedia Lain');

        $this->put(route('provider.profile.update'), [
            'id_pengguna' => $otherProvider->getKey(),
            'nama' => 'Profil Milik Saya Diperbarui',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Bandung',
            'jenis_pemberi' => 'Individu',
        ])->assertRedirectToRoute('provider.biodata');

        $this->assertDatabaseHas('pengguna', [
            'id_pengguna' => $provider->getKey(),
            'nama' => 'Profil Milik Saya Diperbarui',
        ]);
        $this->assertDatabaseHas('pengguna', [
            'id_pengguna' => $otherProvider->getKey(),
            'nama' => 'Profil Penyedia Lain',
        ]);
    }

    public function test_public_navigation_renders_and_home_redirects_to_login_for_guests(): void
    {
        foreach (['/', '/login', '/register', '/register/role', '/register/provider/biodata'] as $path) {
            $this->get($path)->assertOk();
        }

        $this->get('/home')->assertRedirectToRoute('login');
        $this->get(route('employer.vacancies.index'))->assertRedirectToRoute('login');
    }

    public function test_web_logout_revokes_the_login_token_and_clears_the_authenticated_session(): void
    {
        $provider = User::factory()->employer()->create();
        $token = $provider->createToken('web-session')->plainTextToken;
        $tokenId = $provider->tokens()->sole()->getKey();

        $this->post('/auth/session', ['token' => $token])->assertOk();
        $this->get(route('home'))->assertOk()->assertViewIs('jobs.home');
        $this->post(route('logout'))->assertRedirectToRoute('login');

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $tokenId]);
        $this->get(route('home'))->assertRedirectToRoute('login');
    }

    public function test_web_logout_revokes_a_legacy_session_token_owned_by_the_authenticated_user(): void
    {
        $provider = User::factory()->employer()->create();
        $token = $provider->createToken('legacy-web-session')->plainTextToken;
        $tokenId = $provider->tokens()->sole()->getKey();

        $this->actingAs($provider)
            ->post(route('logout'), ['auth_token' => $token])
            ->assertRedirectToRoute('login');

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $tokenId]);
    }

    public function test_admin_pages_are_restricted_to_admin_sessions(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirectToRoute('login');

        $jobSeeker = User::factory()->create();
        foreach (['admin.dashboard', 'admin.verifikasi', 'admin.pengguna'] as $routeName) {
            $this->actingAs($jobSeeker)
                ->get(route($routeName))
                ->assertForbidden();
        }

        $admin = User::factory()->create(['role' => 'admin']);
        foreach (['admin.dashboard', 'admin.verifikasi', 'admin.pengguna'] as $routeName) {
            $this->actingAs($admin)
                ->get(route($routeName))
                ->assertOk();
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
            'lokasi' => 'Koja', 'deskripsi' => 'Sapu halaman rumah', 'upah' => 80000,
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
