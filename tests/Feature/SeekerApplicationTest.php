<?php

namespace Tests\Feature;

use App\Models\JobVacancy;
use App\Models\Lamaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SeekerApplicationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/pencari/lamaran-saya';

    public function test_authenticated_job_seeker_can_open_the_two_table_page_with_the_existing_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('seeker-test')->plainTextToken;
        $response = $this->withToken($token)->getJson(self::ENDPOINT.'/halaman')
            ->assertOk()->assertSee('Pencari Kerja')->assertSee('Lamaran Saya')->assertSee('Riwayat pekerjaan')
            ->assertSee('Pantau perkembangan lamaran pekerjaanmu.')->assertSee('Daftar pekerjaan yang sudah kamu kerjakan.')
            ->assertHeader('Cache-Control', 'no-store, private');
        $this->assertSame(2, substr_count($response->getContent(), '<table>'));
    }

    public function test_authenticated_job_seeker_can_open_main_page_directly_without_exposing_application_data(): void
    {
        $jobSeeker = User::factory()->create(['role' => 'pencari_kerja']);
        $vacancy = JobVacancy::factory()->create(['judul' => 'Lowongan Rahasia Pelamar']);
        Lamaran::factory()->create([
            'id_pencari' => $jobSeeker->getKey(),
            'id_lowongan' => $vacancy->getKey(),
            'pesan_lamaran' => 'Pesan rahasia pelamar',
        ]);

        $response = $this->actingAs($jobSeeker)
            ->get(route('seeker.applications'))
            ->assertOk()
            ->assertViewIs('seeker.applications')
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertSee('Lamaran')
            ->assertSee('Riwayat pekerjaan')
            ->assertSee('seeker-applications.js')
            ->assertSee('data-endpoint="'.route('seeker.applications.index').'"', false)
            ->assertDontSee('Memuat halaman Lamaran Saya...')
            ->assertDontSee('seeker-applications-entry.js')
            ->assertDontSee('Lowongan Rahasia Pelamar')
            ->assertDontSee('Pesan rahasia pelamar');

        $this->assertSame(2, substr_count($response->getContent(), '<table>'));
    }

    public function test_stateful_session_job_seeker_can_fetch_application_data_without_bearer_token(): void
    {
        $jobSeeker = User::factory()->create(['role' => 'pencari_kerja']);
        $vacancy = JobVacancy::factory()->create(['judul' => 'Pekerjaan Via Session']);
        Lamaran::factory()->create([
            'id_pencari' => $jobSeeker->getKey(),
            'id_lowongan' => $vacancy->getKey(),
        ]);

        $this->actingAs($jobSeeker, 'web')
            ->withHeaders(['Referer' => 'http://127.0.0.1:8000/lamaran-saya'])
            ->getJson(self::ENDPOINT)
            ->assertOk()
            ->assertJsonPath('applications.data.0.pekerjaan', 'Pekerjaan Via Session');
    }

    public function test_guest_is_redirected_from_the_seeker_application_page_to_login(): void
    {
        $this->get(route('seeker.applications'))->assertRedirectToRoute('login');
    }

    #[DataProvider('restrictedRoles')]
    public function test_other_roles_cannot_open_the_seeker_application_page(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)->get(route('seeker.applications'))->assertForbidden();
    }

    public function test_guest_cannot_open_or_submit_a_job_application(): void
    {
        $vacancy = JobVacancy::factory()->create();

        $this->get(route('jobs.apply', $vacancy->id_lowongan))
            ->assertRedirectToRoute('login');
        $this->post(route('jobs.apply.store', $vacancy->id_lowongan), ['pesan_lamaran' => 'Saya tertarik.'])
            ->assertRedirectToRoute('login');

        $this->assertDatabaseCount('lamaran', 0);
    }

    public function test_active_job_seeker_can_open_the_application_form(): void
    {
        $vacancy = JobVacancy::factory()->create();
        $jobSeeker = User::factory()->create([
            'role' => 'pencari_kerja',
            'status_akun' => 'aktif',
        ]);

        $this->actingAs($jobSeeker)
            ->get(route('jobs.apply', $vacancy->id_lowongan))
            ->assertOk()
            ->assertViewIs('jobs.apply')
            ->assertViewHas('job', $vacancy);
    }

    public function test_suspended_job_seeker_cannot_open_the_application_form(): void
    {
        $vacancy = JobVacancy::factory()->create();
        $jobSeeker = User::factory()->create([
            'role' => 'pencari_kerja',
            'status_akun' => 'ditangguhkan',
        ]);

        $this->actingAs($jobSeeker)
            ->get(route('jobs.apply', $vacancy->id_lowongan))
            ->assertForbidden();
    }

    #[DataProvider('restrictedRoles')]
    public function test_other_roles_cannot_open_the_application_form(string $role): void
    {
        $vacancy = JobVacancy::factory()->create();
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)
            ->get(route('jobs.apply', $vacancy->id_lowongan))
            ->assertForbidden();
    }

    public function test_non_seekers_cannot_submit_a_job_application(): void
    {
        $vacancy = JobVacancy::factory()->create();
        $provider = User::factory()->employer()->create();

        $this->actingAs($provider)
            ->post(route('jobs.apply.store', $vacancy->id_lowongan), ['pesan_lamaran' => 'Saya tertarik.'])
            ->assertForbidden();

        $this->assertDatabaseCount('lamaran', 0);
    }

    public function test_applications_are_rejected_for_vacancies_from_inactive_providers(): void
    {
        $provider = User::factory()->employer()->create(['status_akun' => 'ditangguhkan']);
        $vacancy = JobVacancy::factory()->create(['id_penyedia' => $provider->getKey()]);
        $jobSeeker = User::factory()->create();

        $this->actingAs($jobSeeker)
            ->post(route('jobs.apply.store', $vacancy->id_lowongan), ['pesan_lamaran' => 'Saya tertarik.'])
            ->assertNotFound();

        $this->assertDatabaseCount('lamaran', 0);
    }

    public function test_closed_vacancies_cannot_receive_new_applications(): void
    {
        $vacancy = JobVacancy::factory()->closed()->create();
        $jobSeeker = User::factory()->create();

        $this->actingAs($jobSeeker)
            ->post(route('jobs.apply.store', $vacancy->id_lowongan), ['pesan_lamaran' => 'Saya tertarik.'])
            ->assertNotFound();

        $this->assertDatabaseCount('lamaran', 0);
    }

    public function test_guests_cannot_fetch_either_protected_endpoint(): void
    {
        $this->getJson(self::ENDPOINT)->assertUnauthorized();
        $this->getJson(self::ENDPOINT.'/halaman')->assertUnauthorized();
    }

    public static function restrictedRoles(): array
    {
        return ['provider' => ['penyedia_pekerjaan'], 'admin' => ['admin']];
    }

    #[DataProvider('restrictedRoles')]
    public function test_other_roles_cannot_fetch_the_html_or_data(string $role): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => $role]));
        $this->getJson(self::ENDPOINT)->assertForbidden();
        $this->getJson(self::ENDPOINT.'/halaman')->assertForbidden();
    }

    public function test_work_name_location_wage_application_date_and_each_application_status_come_from_database(): void
    {
        $user = User::factory()->create();
        foreach (['menunggu', 'diterima', 'ditolak'] as $index => $status) {
            $vacancy = JobVacancy::factory()->create(['judul' => 'Pekerjaan '.$index, 'lokasi' => 'Alamat '.$index, 'upah' => 80000 + $index]);
            Lamaran::factory()->create([
                'id_pencari' => $user->getKey(), 'id_lowongan' => $vacancy->getKey(),
                'status_lamaran' => $status, 'tanggal_lamaran' => '2026-10-14 01:30:00',
            ]);
        }
        Sanctum::actingAs($user);

        $response = $this->getJson(self::ENDPOINT)->assertOk()->assertJsonCount(3, 'applications.data')->assertJsonCount(0, 'history.data');
        $data = array_reverse($response->json('applications.data'));
        $this->assertSame(['Menunggu', 'Diterima', 'Ditolak'], array_column($data, 'status_label'));
        $this->assertSame(['menunggu', 'diterima', 'ditolak'], array_column($data, 'status'));
        $this->assertSame(['Pekerjaan 0', 'Pekerjaan 1', 'Pekerjaan 2'], array_column($data, 'pekerjaan'));
        $this->assertSame(['Alamat 0', 'Alamat 1', 'Alamat 2'], array_column($data, 'lokasi'));
        $this->assertSame(['80000.00', '80001.00', '80002.00'], array_column($data, 'upah'));
        $this->assertSame(['2026-10-14T01:30:00+00:00', '2026-10-14T01:30:00+00:00', '2026-10-14T01:30:00+00:00'], array_column($data, 'tanggal_melamar'));
    }

    public function test_completed_work_moves_to_history_without_overwriting_the_accepted_application_status(): void
    {
        $application = Lamaran::factory()->create(['status_lamaran' => 'diterima', 'status_pekerjaan' => 'selesai']);
        Sanctum::actingAs($application->pencari);
        $this->getJson(self::ENDPOINT)->assertOk()->assertJsonCount(0, 'applications.data')->assertJsonCount(1, 'history.data')
            ->assertJsonPath('history.data.0.id', $application->getKey())->assertJsonPath('history.data.0.status', 'selesai')
            ->assertJsonPath('history.data.0.status_label', 'Selesai')->assertJsonPath('history.data.0.status_lamaran', 'diterima');
        $this->assertDatabaseHas('lamaran', ['id_lamaran' => $application->getKey(), 'status_lamaran' => 'diterima', 'status_pekerjaan' => 'selesai']);
    }

    public function test_vacancy_status_and_completion_date_alone_do_not_mark_the_application_as_completed(): void
    {
        $application = Lamaran::factory()->create(['status_lamaran' => 'ditolak', 'status_pekerjaan' => 'belum_mulai', 'tanggal_selesai' => now()]);
        $application->lowongan->update(['status_lowongan' => 'selesai']);
        Sanctum::actingAs($application->pencari);
        $this->getJson(self::ENDPOINT)->assertOk()->assertJsonCount(1, 'applications.data')->assertJsonCount(0, 'history.data')
            ->assertJsonPath('applications.data.0.status_label', 'Ditolak');
    }

    public function test_other_users_applications_cannot_leak_through_nonfinished_work_statuses_or_query_parameters(): void
    {
        $user = User::factory()->create();
        $own = Lamaran::factory()->create(['id_pencari' => $user->getKey(), 'status_pekerjaan' => 'belum_mulai']);
        $ownHistory = Lamaran::factory()->create(['id_pencari' => $user->getKey(), 'status_pekerjaan' => 'selesai']);
        $other = User::factory()->create();
        foreach (['belum_mulai', 'berlangsung', 'selesai'] as $status) {
            Lamaran::factory()->create(['id_pencari' => $other->getKey(), 'status_pekerjaan' => $status]);
        }
        Sanctum::actingAs($user);
        $this->getJson(self::ENDPOINT.'?id_pencari='.$other->getKey().'&user_id='.$other->getKey())
            ->assertOk()->assertJsonCount(1, 'applications.data')->assertJsonCount(1, 'history.data')
            ->assertJsonPath('applications.data.0.id', $own->getKey())->assertJsonPath('history.data.0.id', $ownHistory->getKey());
    }

    public function test_ongoing_work_does_not_replace_the_application_status(): void
    {
        $application = Lamaran::factory()->create(['status_lamaran' => 'menunggu', 'status_pekerjaan' => 'berlangsung']);
        Sanctum::actingAs($application->pencari);
        $this->getJson(self::ENDPOINT)->assertOk()->assertJsonCount(1, 'applications.data')->assertJsonCount(0, 'history.data')
            ->assertJsonPath('applications.data.0.status', 'menunggu')->assertJsonPath('applications.data.0.status_label', 'Menunggu');
    }

    public function test_empty_account_returns_two_empty_collections_with_no_dummy_rows(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $this->getJson(self::ENDPOINT)->assertOk()->assertJsonPath('applications.data', [])->assertJsonPath('history.data', [])
            ->assertJsonPath('applications.meta.total', 0)->assertJsonPath('history.meta.total', 0);
    }

    public function test_data_exposure_is_limited_to_the_table_fields(): void
    {
        $application = Lamaran::factory()->create(['pesan_lamaran' => 'Pesan pribadi']);
        Sanctum::actingAs($application->pencari);
        $this->getJson(self::ENDPOINT)->assertOk()->assertExactJsonStructure([
            'applications' => [
                'data' => ['*' => ['id', 'pekerjaan', 'lokasi', 'upah', 'tanggal_melamar', 'status', 'status_label', 'status_lamaran', 'status_pekerjaan']],
                'meta' => ['current_page', 'last_page', 'total', 'from', 'to'],
            ],
            'history' => ['data', 'meta' => ['current_page', 'last_page', 'total', 'from', 'to']],
        ])->assertDontSee('Pesan pribadi')->assertDontSee($application->pencari->email);
    }

    public function test_each_request_reads_status_changes_from_the_same_application_row(): void
    {
        $application = Lamaran::factory()->create();
        Sanctum::actingAs($application->pencari);
        $this->getJson(self::ENDPOINT)->assertOk()->assertJsonPath('applications.data.0.status_label', 'Menunggu');
        $application->update(['status_lamaran' => 'diterima']);
        $this->getJson(self::ENDPOINT)->assertOk()->assertJsonPath('applications.data.0.status_label', 'Diterima');
        $application->update(['status_pekerjaan' => 'selesai']);
        $this->getJson(self::ENDPOINT)->assertOk()->assertJsonCount(0, 'applications.data')
            ->assertJsonPath('history.data.0.id', $application->getKey())->assertJsonPath('history.data.0.status_label', 'Selesai');
        $this->assertDatabaseCount('lamaran', 1);
    }

    public function test_missing_date_zero_wage_and_fractional_wage_are_returned_without_error(): void
    {
        $application = Lamaran::factory()->create();
        $application->lowongan->update(['upah' => 0]);
        DB::table('lamaran')->where('id_lamaran', $application->getKey())->update(['tanggal_lamaran' => null]);
        Sanctum::actingAs($application->pencari);
        $this->getJson(self::ENDPOINT)->assertOk()->assertJsonPath('applications.data.0.tanggal_melamar', null)
            ->assertJsonPath('applications.data.0.upah', '0.00');
        $application->lowongan->update(['upah' => 12345.50]);
        $this->getJson(self::ENDPOINT)->assertOk()->assertJsonPath('applications.data.0.upah', '12345.50');
    }

    public function test_both_tables_are_paginated_independently_in_a_stable_order(): void
    {
        $user = User::factory()->create();
        $active = Lamaran::factory()->count(16)->create(['id_pencari' => $user->getKey(), 'tanggal_lamaran' => '2026-10-14 01:00:00']);
        $history = Lamaran::factory()->count(16)->create(['id_pencari' => $user->getKey(), 'status_pekerjaan' => 'selesai', 'tanggal_lamaran' => '2026-10-13 01:00:00']);
        Sanctum::actingAs($user);
        $this->getJson(self::ENDPOINT.'?applications_page=2')->assertOk()
            ->assertJsonCount(1, 'applications.data')->assertJsonCount(15, 'history.data')
            ->assertJsonPath('applications.data.0.id', $active->first()->getKey())
            ->assertJsonPath('applications.meta.current_page', 2)->assertJsonPath('history.meta.current_page', 1);
        $this->getJson(self::ENDPOINT.'?history_page=2')->assertOk()
            ->assertJsonCount(15, 'applications.data')->assertJsonCount(1, 'history.data')
            ->assertJsonPath('history.data.0.id', $history->first()->getKey())
            ->assertJsonPath('applications.meta.current_page', 1)->assertJsonPath('history.meta.current_page', 2);
    }

    public static function invalidPages(): array
    {
        return [
            'zero' => ['applications_page=0', 'applications_page'],
            'negative' => ['history_page=-1', 'history_page'],
            'array' => ['history_page[]=1', 'history_page'],
            'string' => ['applications_page=abc', 'applications_page'],
            'fraction' => ['applications_page=1.5', 'applications_page'],
        ];
    }

    public function test_status_changes_cannot_leave_the_user_on_an_empty_page_when_other_applications_still_exist(): void
    {
        $user = User::factory()->create();
        $applications = Lamaran::factory()->count(16)->create(['id_pencari' => $user->getKey()]);
        Sanctum::actingAs($user);
        $this->getJson(self::ENDPOINT.'?applications_page=999&history_page=999')->assertOk()
            ->assertJsonCount(1, 'applications.data')->assertJsonPath('applications.meta.current_page', 2)
            ->assertJsonPath('history.meta.current_page', 1);
        $applications->last()->update(['status_pekerjaan' => 'selesai']);
        $this->getJson(self::ENDPOINT.'?applications_page=2')->assertOk()
            ->assertJsonCount(15, 'applications.data')->assertJsonCount(1, 'history.data')
            ->assertJsonPath('applications.meta.current_page', 1)->assertJsonPath('applications.meta.total', 15);
    }

    #[DataProvider('invalidPages')]
    public function test_invalid_page_parameters_are_rejected(string $query, string $field): void
    {
        Sanctum::actingAs(User::factory()->create());
        $this->getJson(self::ENDPOINT.'?'.$query)->assertUnprocessable()->assertJsonValidationErrors($field);
    }

    public function test_the_feature_does_not_add_a_status_or_application_write_endpoint(): void
    {
        $application = Lamaran::factory()->create();
        Sanctum::actingAs($application->pencari);
        $this->postJson(self::ENDPOINT, ['status_lamaran' => 'diterima'])->assertStatus(405);
        $this->assertDatabaseHas('lamaran', ['id_lamaran' => $application->getKey(), 'status_lamaran' => 'menunggu']);
    }
}
