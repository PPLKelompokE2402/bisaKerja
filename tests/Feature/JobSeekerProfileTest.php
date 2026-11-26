<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class JobSeekerProfileTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $connection = DB::connection();
        if ($connection->getDriverName() !== 'sqlite' || $connection->getDatabaseName() !== ':memory:') {
            throw new RuntimeException('JobSeekerProfileTest hanya boleh menggunakan SQLite in-memory.');
        }

        Schema::dropIfExists('pengguna');
        Schema::create('pengguna', function (Blueprint $table): void {
            $table->id('id_pengguna');
            $table->string('nama', 100)->nullable();
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
            $table->rememberToken();
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
        });
    }

    public function test_profile_routes_require_job_seeker_authentication_and_reject_suspended_accounts(): void
    {
        $this->get(route('profile.edit'))->assertRedirectToRoute('login');
        $this->put(route('profile.update'))->assertRedirectToRoute('login');

        foreach ([
            User::factory()->employer()->create(),
            User::factory()->create(['role' => 'admin']),
            User::factory()->create(['status_akun' => 'ditangguhkan']),
        ] as $user) {
            $this->actingAs($user)->get(route('profile.edit'))->assertForbidden();
            $this->actingAs($user)->put(route('profile.update'))->assertForbidden();
        }
    }

    public function test_job_seeker_can_update_their_profile_and_upload_a_private_cv(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $jobSeeker = User::factory()->create([
            'nama' => 'Nama Lama',
            'foto_profil' => 'foto-profil/foto-lama.jpg',
            'cv' => 'cv/cv-lama.pdf',
        ]);
        $otherJobSeeker = User::factory()->create(['nama' => 'Profil Orang Lain']);

        Storage::disk('public')->put('foto-profil/foto-lama.jpg', 'old photo');
        Storage::disk('public')->put('cv/cv-lama.pdf', 'old public CV');
        Storage::disk('local')->put('cv/cv-lama.pdf', 'old private CV');

        $this->actingAs($jobSeeker)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertViewIs('profile.edit')
            ->assertSee('value="Nama Lama"', false)
            ->assertSee('Pengalaman Kerja');

        $this->actingAs($jobSeeker)
            ->put(route('profile.update'), [
                'id_pengguna' => $otherJobSeeker->getKey(),
                'nama' => 'Nama Baru',
                'nomor_telepon' => '081234567890',
                'alamat' => 'Walang',
                'jenis_kelamin' => 'laki-laki',
                'deskripsi_diri' => 'Mahasiswa yang mencari pekerjaan rumah.',
                'keahlian' => 'Bisa cat tembok 1 rumah',
                'pengalaman_kerja' => 'Pernah membawa anjing jalan-jalan.',
                'foto_profil' => UploadedFile::fake()->image('foto-baru.jpg'),
                'cv' => UploadedFile::fake()->createWithContent('cv-baru.pdf', '%PDF-1.4 test CV'),
            ])
            ->assertRedirectToRoute('profile.edit')
            ->assertSessionHas('success', 'Profil berhasil diperbarui.');

        $jobSeeker->refresh();
        $this->assertSame('Nama Baru', $jobSeeker->nama);
        $this->assertSame('081234567890', $jobSeeker->nomor_telepon);
        $this->assertSame('Walang', $jobSeeker->alamat);
        $this->assertSame('laki-laki', $jobSeeker->jenis_kelamin);
        $this->assertSame('Bisa cat tembok 1 rumah', $jobSeeker->keahlian);
        $this->assertSame('Pernah membawa anjing jalan-jalan.', $jobSeeker->pengalaman_kerja);
        $this->assertStringStartsWith('foto-profil/', $jobSeeker->foto_profil);
        $this->assertStringStartsWith('cv/', $jobSeeker->cv);

        Storage::disk('public')->assertExists($jobSeeker->foto_profil);
        Storage::disk('public')->assertMissing('foto-profil/foto-lama.jpg');
        Storage::disk('public')->assertMissing('cv/cv-lama.pdf');
        Storage::disk('public')->assertMissing($jobSeeker->cv);
        Storage::disk('local')->assertExists($jobSeeker->cv);
        Storage::disk('local')->assertMissing('cv/cv-lama.pdf');

        $this->assertSame('Profil Orang Lain', $otherJobSeeker->fresh()->nama);
    }

    public function test_invalid_profile_fields_and_files_are_rejected_without_saving_changes(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $jobSeeker = User::factory()->create(['nama' => 'Nama Sebelum']);

        $this->actingAs($jobSeeker)
            ->from(route('profile.edit'))
            ->put(route('profile.update'), [
                'nama' => 'Nama Baru',
                'nomor_telepon' => str_repeat('1', 21),
                'alamat' => 'Bandung',
                'keahlian' => '',
                'pengalaman_kerja' => 'Belum ada',
                'cv' => UploadedFile::fake()->create('berkas.exe', 100, 'application/octet-stream'),
            ])
            ->assertSessionHasErrors(['nomor_telepon', 'keahlian', 'cv']);

        $this->assertSame('Nama Sebelum', $jobSeeker->fresh()->nama);
        Storage::disk('local')->assertDirectoryEmpty('cv');
        Storage::disk('public')->assertDirectoryEmpty('foto-profil');
    }
}
