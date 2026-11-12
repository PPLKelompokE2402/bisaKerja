<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JobApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Job $job;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat user pencari kerja untuk testing
        $this->user = User::factory()->create([
            'name'  => 'Budi Santoso',
            'email' => 'budi@example.com',
        ]);

        // Buat lowongan pekerjaan untuk testing
        $this->job = Job::create([
            'title'        => 'Staf Administrasi',
            'company'      => 'PT Maju Bersama',
            'location'     => 'Jakarta',
            'description'  => 'Melakukan tugas administrasi kantor sehari-hari.',
            'requirements' => 'Pendidikan minimal SMA/SMK sederajat.',
            'salary_min'   => 4500000,
            'salary_max'   => 5500000,
            'type'         => 'Penuh Waktu',
        ]);
    }

    /**
     * Uji halaman form pengajuan lamaran dapat dibuka dengan data lowongan yang tepat.
     */
    public function test_job_application_form_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('jobs.apply', $this->job->id));

        $response->assertStatus(200);
        $response->assertSee('Form Pengajuan Lamaran Pekerjaan');
        $response->assertSee('Staf Administrasi');
        $response->assertSee('PT Maju Bersama');
        $response->assertSee('applicant_name');
        $response->assertSee('date_of_birth');
        $response->assertSee('age');
        $response->assertSee('residence');
        $response->assertSee('skills');
        $response->assertSee('Upload CV bersifat opsional');
    }

    /**
     * Uji pengajuan lamaran berhasil TANPA melampirkan berkas CV (CV bersifat opsional).
     */
    public function test_application_submission_succeeds_without_cv(): void
    {
        $payload = [
            'applicant_name' => 'Budi Santoso',
            'date_of_birth'  => '2001-05-15',
            'age'            => 25,
            'residence'      => 'Jakarta Selatan',
            'skills'         => 'Pengarsipan data, Microsoft Excel, dan komunikasi efektif.',
        ];

        $response = $this->actingAs($this->user)->post(route('jobs.apply.store', $this->job->id), $payload);

        $response->assertRedirect(route('jobs.apply', $this->job->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('job_applications', [
            'user_id'        => $this->user->id,
            'job_id'         => $this->job->id,
            'applicant_name' => 'Budi Santoso',
            'age'            => 25,
            'residence'      => 'Jakarta Selatan',
            'skills'         => 'Pengarsipan data, Microsoft Excel, dan komunikasi efektif.',
            'cv_path'        => null,
            'status'         => 'pending',
        ]);
    }

    /**
     * Uji pengajuan lamaran berhasil DENGAN melampirkan berkas CV (PDF).
     */
    public function test_application_submission_succeeds_with_cv_file(): void
    {
        Storage::fake('local');

        $cvFile = UploadedFile::fake()->create('curriculum_vitae.pdf', 500, 'application/pdf');

        $payload = [
            'applicant_name' => 'Budi Santoso',
            'date_of_birth'  => '2001-05-15',
            'age'            => 25,
            'residence'      => 'Jakarta Selatan',
            'skills'         => 'Laravel, PHP, MySQL, Tailwind CSS.',
            'cv'             => $cvFile,
        ];

        $response = $this->actingAs($this->user)->post(route('jobs.apply.store', $this->job->id), $payload);

        $response->assertRedirect(route('jobs.apply', $this->job->id));
        $response->assertSessionHas('success');

        $application = JobApplication::where('user_id', $this->user->id)
            ->where('job_id', $this->job->id)
            ->first();

        $this->assertNotNull($application);
        $this->assertNotNull($application->cv_path);
        $this->assertEquals('pending', $application->status);

        Storage::disk('local')->assertExists($application->cv_path);
    }

    /**
     * Uji validasi gagal jika kolom wajib tidak diisi.
     */
    public function test_submission_fails_when_required_fields_are_missing(): void
    {
        $response = $this->actingAs($this->user)->post(route('jobs.apply.store', $this->job->id), []);

        $response->assertSessionHasErrors([
            'applicant_name',
            'date_of_birth',
            'age',
            'residence',
            'skills',
        ]);
    }

    /**
     * Uji validasi gagal jika tanggal lahir berupa tanggal di masa depan.
     */
    public function test_submission_fails_when_date_of_birth_is_future(): void
    {
        $futureDate = now()->addDays(5)->format('Y-m-d');

        $payload = [
            'applicant_name' => 'Budi Santoso',
            'date_of_birth'  => $futureDate,
            'age'            => 25,
            'residence'      => 'Jakarta Selatan',
            'skills'         => 'Administrasi',
        ];

        $response = $this->actingAs($this->user)->post(route('jobs.apply.store', $this->job->id), $payload);

        $response->assertSessionHasErrors(['date_of_birth']);
    }

    /**
     * Uji validasi gagal jika berkas CV bukan bertipe pdf/doc/docx atau berukuran > 2MB.
     */
    public function test_submission_fails_when_cv_format_is_invalid(): void
    {
        Storage::fake('local');

        $invalidFile = UploadedFile::fake()->create('script.exe', 100, 'application/x-msdownload');

        $payload = [
            'applicant_name' => 'Budi Santoso',
            'date_of_birth'  => '2001-05-15',
            'age'            => 25,
            'residence'      => 'Jakarta Selatan',
            'skills'         => 'Administrasi',
            'cv'             => $invalidFile,
        ];

        $response = $this->actingAs($this->user)->post(route('jobs.apply.store', $this->job->id), $payload);

        $response->assertSessionHasErrors(['cv']);
    }

    /**
     * Uji pelamar tidak dapat melamar dua kali pada lowongan pekerjaan yang sama.
     */
    public function test_job_seeker_cannot_apply_twice_to_the_same_job(): void
    {
        // Lamaran pertama
        JobApplication::create([
            'user_id'        => $this->user->id,
            'job_id'         => $this->job->id,
            'applicant_name' => 'Budi Santoso',
            'date_of_birth'  => '2001-05-15',
            'age'            => 25,
            'residence'      => 'Jakarta Selatan',
            'skills'         => 'Administrasi kantor',
            'status'         => 'pending',
        ]);

        // Percobaan lamaran kedua
        $payload = [
            'applicant_name' => 'Budi Santoso Kedua',
            'date_of_birth'  => '2001-05-15',
            'age'            => 25,
            'residence'      => 'Jakarta Selatan',
            'skills'         => 'Administrasi kantor',
        ];

        $response = $this->actingAs($this->user)->post(route('jobs.apply.store', $this->job->id), $payload);

        $response->assertSessionHas('error');
        $this->assertEquals(1, JobApplication::where('user_id', $this->user->id)->where('job_id', $this->job->id)->count());
    }
}
