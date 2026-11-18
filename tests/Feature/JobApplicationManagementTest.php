<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobApplicationManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;
    protected Job $jobPending;
    protected Job $jobAccepted;
    protected Job $jobRejected;
    protected Job $jobCompleted;

    protected function setUp(): void
    {
        parent::setUp();

        // Akun pencari kerja utama
        $this->user = User::factory()->create([
            'name'  => 'Nasyifa Pencari Kerja',
            'email' => 'nasyifa@example.com',
        ]);

        // Akun pencari kerja lain
        $this->otherUser = User::factory()->create([
            'name'  => 'User Lain',
            'email' => 'other@example.com',
        ]);

        // Lowongan untuk testing
        $this->jobPending = Job::create([
            'title'       => 'Bersihkan halaman',
            'company'     => 'Ibu Ani',
            'location'    => 'Jl. Walang Baru, Koja',
            'type'        => 'Part-time',
            'salary_min'  => 80000,
            'salary_max'  => 80000,
            'description' => 'Membersihkan pekarangan dan menyiram tanaman.',
            'requirements'=> 'Teliti dan bertanggung jawab.',
        ]);

        $this->jobAccepted = Job::create([
            'title'       => 'Bantu packing pesanan',
            'company'     => 'Toko Berkah',
            'location'    => 'Jl. Mahoni, Koja',
            'type'        => 'Part-time',
            'salary_min'  => 70000,
            'salary_max'  => 70000,
            'description' => 'Membantu packing pesanan barang.',
            'requirements'=> 'Cekatan.',
        ]);

        $this->jobRejected = Job::create([
            'title'       => 'Perbaiki keran',
            'company'     => 'Bapak Joko',
            'location'    => 'Jl. Menteng, Koja',
            'type'        => 'Daily',
            'salary_min'  => 60000,
            'salary_max'  => 60000,
            'description' => 'Memperbaiki instalasi pipa keran bocor.',
            'requirements'=> 'Memahami pipa air.',
        ]);

        $this->jobCompleted = Job::create([
            'title'       => 'Cat tembok',
            'company'     => 'Bapak Budi',
            'location'    => 'Jl. Mahoni, Koja',
            'type'        => 'Daily',
            'salary_min'  => 70000,
            'salary_max'  => 70000,
            'description' => 'Pengecatan ulang tembok kamar.',
            'requirements'=> 'Rapi.',
        ]);
    }

    /**
     * Uji halaman pemantauan lamaran dapat diakses dan menampilkan komponen antarmuka utama.
     */
    public function test_job_seeker_can_access_application_tracking_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('applications.index'));

        $response->assertStatus(200);
        $response->assertSee('Lamaran');
        $response->assertSee('Pantau perkembangan lamaran pekerjaanmu.');
        $response->assertSee('Pencari Kerja');
        $response->assertSee('Lamaran Saya');
        $response->assertSee('Riwayat pekerjaan');
        $response->assertSee('Daftar pekerjaan yang sudah kamu kerjakan');
    }

    /**
     * Uji pencari kerja dapat melihat lamarannya sendiri beserta status aktual dari database.
     */
    public function test_job_seeker_sees_their_own_applications_with_dynamic_data(): void
    {
        // Buat lamaran untuk user utama
        JobApplication::create([
            'user_id'        => $this->user->id,
            'job_id'         => $this->jobPending->id,
            'applicant_name' => $this->user->name,
            'date_of_birth'  => '2000-01-01',
            'age'            => 26,
            'residence'      => 'Koja, Jakarta Utara',
            'skills'         => 'Pembersihan',
            'status'         => 'pending',
        ]);

        JobApplication::create([
            'user_id'        => $this->user->id,
            'job_id'         => $this->jobAccepted->id,
            'applicant_name' => $this->user->name,
            'date_of_birth'  => '2000-01-01',
            'age'            => 26,
            'residence'      => 'Koja, Jakarta Utara',
            'skills'         => 'Packing',
            'status'         => 'accepted',
        ]);

        JobApplication::create([
            'user_id'        => $this->user->id,
            'job_id'         => $this->jobRejected->id,
            'applicant_name' => $this->user->name,
            'date_of_birth'  => '2000-01-01',
            'age'            => 26,
            'residence'      => 'Koja, Jakarta Utara',
            'skills'         => 'Pipa',
            'status'         => 'rejected',
        ]);

        // Buat lowongan dan lamaran untuk user lain
        $otherJob = Job::create([
            'title'       => 'Pekerjaan Rahasia Orang Lain',
            'company'     => 'PT Rahasia',
            'location'    => 'Jakarta',
            'type'        => 'Full-time',
            'description' => 'Deskripsi rahasia',
            'requirements'=> 'Syarat',
        ]);

        JobApplication::create([
            'user_id'        => $this->otherUser->id,
            'job_id'         => $otherJob->id,
            'applicant_name' => $this->otherUser->name,
            'date_of_birth'  => '1998-05-10',
            'age'            => 28,
            'residence'      => 'Tebet',
            'skills'         => 'Rahasia',
            'status'         => 'pending',
        ]);

        $response = $this->actingAs($this->user)->get(route('applications.index'));

        $response->assertStatus(200);

        // Memastikan lowongan milik user terlihat beserta lokasinya
        $response->assertSee('Bersihkan halaman');
        $response->assertSee('Jl. Walang Baru, Koja');
        $response->assertSee('Rp80.000');
        $response->assertSee('Menunggu');

        $response->assertSee('Bantu packing pesanan');
        $response->assertSee('Jl. Mahoni, Koja');
        $response->assertSee('Rp70.000');
        $response->assertSee('Diterima');

        $response->assertSee('Perbaiki keran');
        $response->assertSee('Jl. Menteng, Koja');
        $response->assertSee('Rp60.000');
        $response->assertSee('Ditolak');

        // Memastikan lamaran orang lain TIDAK terlihat
        $response->assertDontSee('Pekerjaan Rahasia Orang Lain');
    }

    /**
     * Uji pencari kerja dapat membatalkan lamaran yang masih berstatus menunggu (pending).
     */
    public function test_job_seeker_can_cancel_pending_application(): void
    {
        $application = JobApplication::create([
            'user_id'        => $this->user->id,
            'job_id'         => $this->jobPending->id,
            'applicant_name' => $this->user->name,
            'date_of_birth'  => '2000-01-01',
            'age'            => 26,
            'residence'      => 'Koja, Jakarta Utara',
            'skills'         => 'Pembersihan',
            'status'         => 'pending',
        ]);

        $response = $this->actingAs($this->user)->patch(route('applications.cancel', $application->id));

        $response->assertRedirect(route('applications.index'));
        $response->assertSessionHas('success', 'Lamaran pekerjaan berhasil dibatalkan.');

        // Memastikan perubahan status tersimpan di database
        $this->assertDatabaseHas('job_applications', [
            'id'     => $application->id,
            'status' => 'cancelled',
        ]);

        $this->assertEquals('cancelled', $application->fresh()->status);
    }

    /**
     * Uji lamaran yang sudah diterima atau ditolak TIDAK dapat dibatalkan.
     */
    public function test_job_seeker_cannot_cancel_accepted_or_rejected_application(): void
    {
        $acceptedApp = JobApplication::create([
            'user_id'        => $this->user->id,
            'job_id'         => $this->jobAccepted->id,
            'applicant_name' => $this->user->name,
            'date_of_birth'  => '2000-01-01',
            'age'            => 26,
            'residence'      => 'Koja, Jakarta Utara',
            'skills'         => 'Packing',
            'status'         => 'accepted',
        ]);

        $rejectedApp = JobApplication::create([
            'user_id'        => $this->user->id,
            'job_id'         => $this->jobRejected->id,
            'applicant_name' => $this->user->name,
            'date_of_birth'  => '2000-01-01',
            'age'            => 26,
            'residence'      => 'Koja, Jakarta Utara',
            'skills'         => 'Pipa',
            'status'         => 'rejected',
        ]);

        // Mencoba membatalkan lamaran yang diterima
        $responseAccepted = $this->actingAs($this->user)->patch(route('applications.cancel', $acceptedApp->id));
        $responseAccepted->assertRedirect(route('applications.index'));
        $responseAccepted->assertSessionHas('error');
        $this->assertEquals('accepted', $acceptedApp->fresh()->status);

        // Mencoba membatalkan lamaran yang ditolak
        $responseRejected = $this->actingAs($this->user)->patch(route('applications.cancel', $rejectedApp->id));
        $responseRejected->assertRedirect(route('applications.index'));
        $responseRejected->assertSessionHas('error');
        $this->assertEquals('rejected', $rejectedApp->fresh()->status);
    }

    /**
     * Uji pencari kerja tidak dapat membatalkan lamaran milik pencari kerja lain.
     */
    public function test_job_seeker_cannot_cancel_another_users_application(): void
    {
        $otherApplication = JobApplication::create([
            'user_id'        => $this->otherUser->id,
            'job_id'         => $this->jobPending->id,
            'applicant_name' => $this->otherUser->name,
            'date_of_birth'  => '1998-05-10',
            'age'            => 28,
            'residence'      => 'Tebet',
            'skills'         => 'Keahlian lain',
            'status'         => 'pending',
        ]);

        $response = $this->actingAs($this->user)->patch(route('applications.cancel', $otherApplication->id));

        $response->assertRedirect(route('applications.index'));
        $response->assertSessionHas('error', 'Lamaran pekerjaan tidak ditemukan.');

        // Status milik user lain tidak boleh berubah
        $this->assertEquals('pending', $otherApplication->fresh()->status);
    }

    /**
     * Uji daftar riwayat pekerjaan menampilkan pekerjaan yang sudah selesai dikerjakan.
     */
    public function test_completed_jobs_appear_in_work_history_section(): void
    {
        // Lamaran aktif (pending)
        JobApplication::create([
            'user_id'        => $this->user->id,
            'job_id'         => $this->jobPending->id,
            'applicant_name' => $this->user->name,
            'date_of_birth'  => '2000-01-01',
            'age'            => 26,
            'residence'      => 'Koja, Jakarta Utara',
            'skills'         => 'Pembersihan',
            'status'         => 'pending',
        ]);

        // Pekerjaan yang sudah selesai
        JobApplication::create([
            'user_id'        => $this->user->id,
            'job_id'         => $this->jobCompleted->id,
            'applicant_name' => $this->user->name,
            'date_of_birth'  => '2000-01-01',
            'age'            => 26,
            'residence'      => 'Koja, Jakarta Utara',
            'skills'         => 'Pengecatan',
            'status'         => 'completed',
        ]);

        $response = $this->actingAs($this->user)->get(route('applications.index'));

        $response->assertStatus(200);

        // Memastikan riwayat pekerjaan muncul dengan badge Selesai
        $response->assertSee('Riwayat pekerjaan');
        $response->assertSee('Cat tembok');
        $response->assertSee('Selesai');
    }
}

