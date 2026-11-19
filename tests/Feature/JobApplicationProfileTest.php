<?php

namespace Tests\Feature;

use App\Models\JobVacancy;
use App\Models\Lamaran;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use RuntimeException;
use Tests\TestCase;

class JobApplicationProfileTest extends TestCase
{
    private const PROVIDER_ENDPOINT = '/api/penyedia/pelamar-saya';

    protected function setUp(): void
    {
        parent::setUp();

        $connection = DB::connection();
        if ($connection->getDriverName() !== 'sqlite' || $connection->getDatabaseName() !== ':memory:') {
            throw new RuntimeException('JobApplicationProfileTest hanya boleh menggunakan SQLite in-memory.');
        }

        foreach (['lamaran', 'lowongan', 'kategori_pekerjaan', 'pengguna'] as $table) {
            Schema::dropIfExists($table);
        }

        $this->createTestSchema();
        Storage::fake('local');
        Storage::fake('public');
    }

    public function test_apply_form_prefills_saved_profile_and_has_a_calculated_age_field(): void
    {
        $provider = User::factory()->employer()->create();
        $vacancy = $this->createVacancy($provider);
        $seeker = User::factory()->create([
            'nama' => 'Siti Pelamar',
            'tanggal_lahir' => '1995-03-08',
            'alamat' => 'Bandung',
            'keahlian' => 'Membersihkan rumah',
        ]);

        $response = $this->actingAs($seeker)
            ->get(route('jobs.apply', $vacancy->getKey()));

        $response
            ->assertOk()
            ->assertViewIs('jobs.apply')
            ->assertSee('value="Siti Pelamar"', false)
            ->assertSee('value="1995-03-08"', false)
            ->assertSee('Bandung', false)
            ->assertSee('Membersihkan rumah', false)
            ->assertSee('id="umur"', false)
            ->assertDontSee('name="umur"', false)
            ->assertSee('name="pesan_lamaran"', false)
            ->assertSee('Dihitung otomatis berdasarkan tanggal lahir.', false)
            ->assertSee('name="cv"', false);

        $this->assertSame(6, substr_count($response->getContent(), '<span class="text-red-500">*</span>'));

        foreach ([
            '/name="nama_lengkap"\s+id="nama_lengkap"[^>]*required/s',
            '/name="tanggal_lahir"\s+id="tanggal_lahir"[^>]*required/s',
            '/id="umur"[^>]*required[^>]*readonly/s',
            '/name="tempat_tinggal"\s+id="tempat_tinggal"[^>]*required/s',
            '/name="keahlian"\s+id="keahlian"[^>]*required/s',
            '/name="pesan_lamaran"\s+id="pesan_lamaran"[^>]*required/s',
        ] as $pattern) {
            $this->assertMatchesRegularExpression($pattern, $response->getContent());
        }
    }

    public function test_required_profile_fields_are_validated_and_saved_for_later_applications(): void
    {
        $provider = User::factory()->employer()->create();
        $firstVacancy = $this->createVacancy($provider);
        $secondVacancy = $this->createVacancy($provider);
        $seeker = User::factory()->create(['nama' => 'Nama Lama']);

        $this->actingAs($seeker)
            ->from(route('jobs.apply', $firstVacancy->getKey()))
            ->post(route('jobs.apply.store', $firstVacancy->getKey()))
            ->assertSessionHasErrors([
                'nama_lengkap',
                'tanggal_lahir',
                'tempat_tinggal',
                'keahlian',
                'pesan_lamaran',
            ]);

        $this->assertDatabaseCount('lamaran', 0);

        $this->actingAs($seeker)
            ->from(route('jobs.apply', $firstVacancy->getKey()))
            ->post(route('jobs.apply.store', $firstVacancy->getKey()), $this->validProfile([
                'tanggal_lahir' => now()->addDay()->toDateString(),
            ]))
            ->assertSessionHasErrors('tanggal_lahir');

        $this->get(route('jobs.apply', $firstVacancy->getKey()))
            ->assertSee('value="Maya Pelamar"', false)
            ->assertSee('value="'.now()->addDay()->toDateString().'"', false)
            ->assertSee('Bekasi', false)
            ->assertSee('Membersihkan rumah dan halaman', false)
            ->assertSee('Saya tertarik dengan pekerjaan ini.', false);

        $this->actingAs($seeker)
            ->from(route('jobs.apply', $firstVacancy->getKey()))
            ->post(route('jobs.apply.store', $firstVacancy->getKey()), $this->validProfile([
                'nama_lengkap' => '   ',
                'tempat_tinggal' => " \t ",
                'keahlian' => " \n ",
                'pesan_lamaran' => '   ',
            ]))
            ->assertSessionHasErrors([
                'nama_lengkap',
                'tempat_tinggal',
                'keahlian',
                'pesan_lamaran',
            ]);

        $this->assertDatabaseCount('lamaran', 0);

        $this->actingAs($seeker)
            ->post(route('jobs.apply.store', $firstVacancy->getKey()), $this->validProfile())
            ->assertRedirectToRoute('jobs.apply', $firstVacancy->getKey())
            ->assertSessionHasNoErrors();

        $seeker->refresh();
        $this->assertSame('Maya Pelamar', $seeker->nama);
        $this->assertSame('1994-06-12', $seeker->tanggal_lahir->toDateString());
        $this->assertSame('Bekasi', $seeker->alamat);
        $this->assertSame('Membersihkan rumah dan halaman', $seeker->keahlian);
        $this->assertNull($seeker->cv);
        $this->assertDatabaseHas('lamaran', [
            'id_lowongan' => $firstVacancy->getKey(),
            'id_pencari' => $seeker->getKey(),
            'pesan_lamaran' => 'Saya tertarik dengan pekerjaan ini.',
            'status_lamaran' => 'menunggu',
        ]);

        $this->get(route('jobs.apply', $secondVacancy->getKey()))
            ->assertOk()
            ->assertSee('value="Maya Pelamar"', false)
            ->assertSee('value="1994-06-12"', false)
            ->assertSee('Bekasi', false)
            ->assertSee('Membersihkan rumah dan halaman', false);
    }

    public function test_invalid_birth_dates_and_profile_lengths_are_rejected(): void
    {
        $provider = User::factory()->employer()->create();
        $vacancy = $this->createVacancy($provider);
        $seeker = User::factory()->create();

        $this->actingAs($seeker)
            ->from(route('jobs.apply', $vacancy->getKey()))
            ->post(route('jobs.apply.store', $vacancy->getKey()), $this->validProfile([
                'tanggal_lahir' => 'not-a-date',
            ]))
            ->assertSessionHasErrors('tanggal_lahir');

        $this->actingAs($seeker)
            ->from(route('jobs.apply', $vacancy->getKey()))
            ->post(route('jobs.apply.store', $vacancy->getKey()), $this->validProfile([
                'nama_lengkap' => str_repeat('N', 101),
                'tempat_tinggal' => str_repeat('A', 1001),
                'keahlian' => str_repeat('K', 2001),
                'pesan_lamaran' => str_repeat('P', 3001),
            ]))
            ->assertSessionHasErrors([
                'nama_lengkap',
                'tempat_tinggal',
                'keahlian',
                'pesan_lamaran',
            ]);

        $this->assertDatabaseCount('lamaran', 0);
    }

    public function test_pdf_cv_is_private_and_downloadable_only_by_authorized_users(): void
    {
        $provider = User::factory()->employer()->create();
        $vacancy = $this->createVacancy($provider);
        $seeker = User::factory()->create();
        $payload = $this->validProfile([
            'cv' => UploadedFile::fake()->createWithContent('resume.pdf', '%PDF-1.4 test CV'),
            'umur' => 999,
        ]);

        $this->actingAs($seeker)
            ->post(route('jobs.apply.store', $vacancy->getKey()), $payload)
            ->assertRedirectToRoute('jobs.apply', $vacancy->getKey());

        $seeker->refresh();
        $application = Lamaran::query()->where('id_pencari', $seeker->getKey())->sole();
        $this->assertStringStartsWith('cv/', $seeker->cv);
        Storage::disk('local')->assertExists($seeker->cv);
        Storage::disk('public')->assertMissing($seeker->cv);

        Sanctum::actingAs($provider);
        $this->getJson(self::PROVIDER_ENDPOINT.'/'.$application->getKey())
            ->assertOk()
            ->assertJsonPath('data.pelamar.nama', 'Maya Pelamar')
            ->assertJsonPath('data.pelamar.tanggal_lahir', '1994-06-12')
            ->assertJsonPath('data.pelamar.umur', $seeker->tanggal_lahir->age)
            ->assertJsonPath('data.pelamar.alamat', 'Bekasi')
            ->assertJsonPath('data.pelamar.keahlian', 'Membersihkan rumah dan halaman')
            ->assertJsonPath('data.pelamar.has_cv', true)
            ->assertJsonPath('data.pelamar.cv_extension', 'pdf')
            ->assertJsonPath('data.pesan_lamaran', 'Saya tertarik dengan pekerjaan ini.');

        $this->getJson(self::PROVIDER_ENDPOINT.'/'.$application->getKey().'/berkas/cv')
            ->assertOk()
            ->assertDownload('CV-pelamar.pdf')
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->app['auth']->forgetGuards();
        $this->getJson(self::PROVIDER_ENDPOINT.'/'.$application->getKey().'/berkas/cv')->assertUnauthorized();

        Sanctum::actingAs(User::factory()->employer()->create());
        $this->getJson(self::PROVIDER_ENDPOINT.'/'.$application->getKey().'/berkas/cv')->assertNotFound();

        $this->app['auth']->forgetGuards();
        $this->get(route('admin.users.cv', $seeker->getKey()))->assertRedirectToRoute('login');

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('admin.users.cv', $seeker->getKey()))
            ->assertOk()
            ->assertDownload('CV-pelamar.pdf');

        $this->actingAs(User::factory()->create())
            ->get(route('admin.users.cv', $seeker->getKey()))
            ->assertForbidden();
    }

    public function test_doc_and_docx_cv_files_are_accepted_and_kept_private(): void
    {
        $provider = User::factory()->employer()->create();

        foreach ([
            ['doc', 'application/msword'],
            ['docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        ] as [$extension, $mimeType]) {
            $seeker = User::factory()->create();
            $vacancy = $this->createVacancy($provider);
            $payload = $this->validProfile([
                'cv' => UploadedFile::fake()->create("resume.{$extension}", 100, $mimeType),
            ]);

            $this->actingAs($seeker)
                ->post(route('jobs.apply.store', $vacancy->getKey()), $payload)
                ->assertRedirectToRoute('jobs.apply', $vacancy->getKey())
                ->assertSessionHasNoErrors();

            $seeker->refresh();
            $this->assertSame($extension, pathinfo($seeker->cv, PATHINFO_EXTENSION));
            Storage::disk('local')->assertExists($seeker->cv);
            Storage::disk('public')->assertMissing($seeker->cv);
        }
    }

    public function test_invalid_or_oversized_cv_is_rejected_without_saving_an_application(): void
    {
        $provider = User::factory()->employer()->create();
        $vacancy = $this->createVacancy($provider);
        $seeker = User::factory()->create(['nama' => 'Nama Sebelum']);

        foreach ([
            UploadedFile::fake()->create('resume.exe', 100, 'application/octet-stream'),
            UploadedFile::fake()->create('resume.pdf', 2049, 'application/pdf'),
        ] as $cv) {
            $this->actingAs($seeker)
                ->from(route('jobs.apply', $vacancy->getKey()))
                ->post(route('jobs.apply.store', $vacancy->getKey()), $this->validProfile(['cv' => $cv]))
                ->assertSessionHasErrors('cv');
        }

        $this->assertDatabaseCount('lamaran', 0);
        $this->assertSame('Nama Sebelum', $seeker->fresh()->nama);
        $this->assertNull($seeker->fresh()->cv);
    }

    public function test_duplicate_application_is_rejected_without_overwriting_profile(): void
    {
        $provider = User::factory()->employer()->create();
        $vacancy = $this->createVacancy($provider);
        $seeker = User::factory()->create(['nama' => 'Nama Tersimpan']);
        Lamaran::factory()->create([
            'id_lowongan' => $vacancy->getKey(),
            'id_pencari' => $seeker->getKey(),
        ]);

        $this->actingAs($seeker)
            ->post(route('jobs.apply.store', $vacancy->getKey()), $this->validProfile())
            ->assertRedirectToRoute('jobs.apply', $vacancy->getKey())
            ->assertSessionHas('error');

        $this->assertDatabaseCount('lamaran', 1);
        $this->assertSame('Nama Tersimpan', $seeker->fresh()->nama);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validProfile(array $overrides = []): array
    {
        return array_merge([
            'nama_lengkap' => 'Maya Pelamar',
            'tanggal_lahir' => '1994-06-12',
            'tempat_tinggal' => 'Bekasi',
            'keahlian' => 'Membersihkan rumah dan halaman',
            'pesan_lamaran' => 'Saya tertarik dengan pekerjaan ini.',
        ], $overrides);
    }

    private function createVacancy(User $provider): JobVacancy
    {
        return JobVacancy::query()->create([
            'id_penyedia' => $provider->getKey(),
            'id_kategori' => null,
            'judul' => 'Pekerjaan Bersih-Bersih',
            'deskripsi' => 'Membersihkan rumah.',
            'lokasi' => 'Bekasi',
            'upah' => 75000,
            'waktu_pekerjaan' => now()->addDay(),
            'status_lowongan' => 'aktif',
        ]);
    }

    private function createTestSchema(): void
    {
        Schema::create('pengguna', function (Blueprint $table): void {
            $table->id('id_pengguna');
            $table->string('nama', 100)->nullable();
            $table->string('username', 50)->nullable();
            $table->string('email', 150)->unique();
            $table->string('kata_sandi');
            $table->string('nomor_telepon', 20)->nullable();
            $table->string('role')->default('pencari_kerja');
            $table->string('status_akun')->default('aktif');
            $table->text('alamat')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->text('deskripsi_diri')->nullable();
            $table->string('cv')->nullable();
            $table->string('foto_profil')->nullable();
            $table->text('keahlian')->nullable();
            $table->text('pengalaman_kerja')->nullable();
            $table->string('jenis_pemberi')->nullable();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->unsignedBigInteger('id_admin_verifikator')->nullable();
            $table->text('catatan_verifikasi')->nullable();
            $table->rememberToken();
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
        });

        Schema::create('kategori_pekerjaan', function (Blueprint $table): void {
            $table->id('id_kategori');
            $table->string('nama_kategori', 100);
            $table->text('deskripsi')->nullable();
            $table->string('status_kategori')->default('aktif');
        });

        Schema::create('lowongan', function (Blueprint $table): void {
            $table->id('id_lowongan');
            $table->unsignedBigInteger('id_penyedia');
            $table->unsignedBigInteger('id_kategori')->nullable();
            $table->string('judul', 150);
            $table->text('deskripsi');
            $table->string('lokasi');
            $table->decimal('upah', 15, 2)->default(0);
            $table->timestamp('waktu_pekerjaan')->nullable();
            $table->string('status_lowongan')->default('aktif');
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
        });

        Schema::create('lamaran', function (Blueprint $table): void {
            $table->id('id_lamaran');
            $table->unsignedBigInteger('id_lowongan');
            $table->unsignedBigInteger('id_pencari');
            $table->text('pesan_lamaran')->nullable();
            $table->string('status_lamaran')->default('menunggu');
            $table->string('status_pekerjaan')->default('belum_mulai');
            $table->timestamp('tanggal_mulai')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();
            $table->text('catatan_penyelesaian')->nullable();
            $table->timestamp('tanggal_lamaran')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
            $table->unique(['id_lowongan', 'id_pencari']);
        });
    }
}
