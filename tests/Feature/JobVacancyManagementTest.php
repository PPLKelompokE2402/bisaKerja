<?php

namespace Tests\Feature;

use App\Enums\VacancyCategory;
use App\Enums\VacancyDuration;
use App\Enums\VacancyStatus;
use App\Models\JobVacancy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobVacancyManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Build a valid vacancy payload.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'title' => 'Bersihkan halaman rumah',
            'category' => VacancyCategory::Cleaning->value,
            'location' => 'Kel. Sukajadi, Jl. Melati No. 12',
            'work_date' => now()->addDays(3)->format('Y-m-d'),
            'duration' => VacancyDuration::TwoToFourHours->value,
            'notes' => 'Sapu dan pengki disediakan.',
            ...$overrides,
        ];
    }

    public function test_employer_sees_only_their_own_vacancies(): void
    {
        $employer = User::factory()->employer()->create();
        JobVacancy::factory()->for($employer, 'employer')->create(['title' => 'Perbaiki keran', 'applicants_count' => 3]);
        JobVacancy::factory()->create(['title' => 'Lowongan milik orang lain']);

        $response = $this->actingAs($employer)->get(route('employer.vacancies.index'));

        $response->assertOk();
        $response->assertSeeText('Perbaiki keran');
        $response->assertSeeText('3 pelamar');
        $response->assertDontSeeText('Lowongan milik orang lain');
    }

    public function test_job_seeker_cannot_view_the_vacancy_management_page(): void
    {
        $jobSeeker = User::factory()->create();

        $this->actingAs($jobSeeker)->get(route('employer.vacancies.index'))->assertForbidden();
    }

    public function test_guest_is_served_as_the_first_seeded_employer_until_login_exists(): void
    {
        $demoEmployer = User::factory()->employer()->create();
        JobVacancy::factory()->for($demoEmployer, 'employer')->create(['title' => 'Bantu packing pesanan']);

        $this->get(route('employer.vacancies.index'))
            ->assertOk()
            ->assertSeeText('Bantu packing pesanan');
    }

    public function test_guest_gets_service_unavailable_when_no_employer_has_been_seeded(): void
    {
        $this->get(route('employer.vacancies.index'))->assertStatus(503);
    }

    public function test_employer_can_post_a_vacancy(): void
    {
        $employer = User::factory()->employer()->create();

        $response = $this->actingAs($employer)->post(route('employer.vacancies.store'), $this->validPayload());

        $response->assertRedirectToRoute('employer.vacancies.index');
        $response->assertSessionHas('status', 'Lowongan berhasil dipasang.');
        $this->assertDatabaseHas('job_vacancies', [
            'user_id' => $employer->id,
            'title' => 'Bersihkan halaman rumah',
            'category' => VacancyCategory::Cleaning->value,
            'status' => VacancyStatus::Open->value,
            'applicants_count' => 0,
        ]);
    }

    public function test_job_seeker_cannot_post_a_vacancy(): void
    {
        $jobSeeker = User::factory()->create();

        $this->actingAs($jobSeeker)
            ->post(route('employer.vacancies.store'), $this->validPayload())
            ->assertForbidden();

        $this->assertDatabaseCount('job_vacancies', 0);
    }

    public function test_posting_a_vacancy_requires_the_mandatory_fields(): void
    {
        $employer = User::factory()->employer()->create();

        $response = $this->actingAs($employer)->post(route('employer.vacancies.store'), []);

        $response->assertSessionHasErrors([
            'title' => 'Kolom judul pekerjaan wajib diisi.',
            'category' => 'Kolom kategori wajib diisi.',
            'location' => 'Kolom lokasi & patokan wajib diisi.',
            'work_date' => 'Kolom tanggal pelaksanaan wajib diisi.',
            'duration' => 'Kolom estimasi durasi wajib diisi.',
        ]);
        $response->assertSessionDoesntHaveErrors('notes');
    }

    public function test_work_date_cannot_be_in_the_past(): void
    {
        $employer = User::factory()->employer()->create();

        $response = $this->actingAs($employer)->post(
            route('employer.vacancies.store'),
            $this->validPayload(['work_date' => now()->subDay()->format('Y-m-d')]),
        );

        $response->assertSessionHasErrors(['work_date' => 'Tanggal pelaksanaan tidak boleh sebelum hari ini.']);
    }

    public function test_category_must_be_one_of_the_available_options(): void
    {
        $employer = User::factory()->employer()->create();

        $response = $this->actingAs($employer)->post(
            route('employer.vacancies.store'),
            $this->validPayload(['category' => 'tidak-ada']),
        );

        $response->assertSessionHasErrors(['category' => 'Pilihan kategori tidak valid.']);
    }

    public function test_employer_can_open_the_manage_page_of_their_vacancy(): void
    {
        $vacancy = JobVacancy::factory()->create(['title' => 'Perbaiki keran']);

        $this->actingAs($vacancy->employer)
            ->get(route('employer.vacancies.edit', $vacancy))
            ->assertOk()
            ->assertSee('value="Perbaiki keran"', false);
    }

    public function test_employer_cannot_open_the_manage_page_of_another_employers_vacancy(): void
    {
        $vacancy = JobVacancy::factory()->create();
        $otherEmployer = User::factory()->employer()->create();

        $this->actingAs($otherEmployer)
            ->get(route('employer.vacancies.edit', $vacancy))
            ->assertNotFound();
    }

    public function test_employer_can_update_their_vacancy(): void
    {
        $vacancy = JobVacancy::factory()->create();

        $response = $this->actingAs($vacancy->employer)->put(
            route('employer.vacancies.update', $vacancy),
            $this->validPayload(['title' => 'Cat ulang pagar', 'category' => VacancyCategory::HomeRepair->value]),
        );

        $response->assertRedirectToRoute('employer.vacancies.index');
        $response->assertSessionHas('status', 'Lowongan berhasil diperbarui.');
        $this->assertSame('Cat ulang pagar', $vacancy->fresh()->title);
        $this->assertSame(VacancyCategory::HomeRepair, $vacancy->fresh()->category);
    }

    public function test_employer_cannot_update_another_employers_vacancy(): void
    {
        $vacancy = JobVacancy::factory()->create(['title' => 'Judul asli']);
        $otherEmployer = User::factory()->employer()->create();

        $this->actingAs($otherEmployer)
            ->put(route('employer.vacancies.update', $vacancy), $this->validPayload(['title' => 'Diubah orang lain']))
            ->assertNotFound();

        $this->assertSame('Judul asli', $vacancy->fresh()->title);
    }

    public function test_employer_can_delete_their_vacancy(): void
    {
        $this->markTestIncomplete('Sprint 1 Known Bug (SCRUM-50): Lowongan belum terhapus dari database, dijadwalkan fix di Sprint 2.');

        $vacancy = JobVacancy::factory()->create();

        $response = $this->actingAs($vacancy->employer)->delete(route('employer.vacancies.destroy', $vacancy));

        $response->assertRedirectToRoute('employer.vacancies.index');
        $response->assertSessionHas('status', 'Lowongan berhasil dihapus.');
        $this->assertModelMissing($vacancy);
    }

    public function test_sprint_1_bug_vacancy_is_not_deleted_on_destroy(): void
    {
        $vacancy = JobVacancy::factory()->create();

        $response = $this->actingAs($vacancy->employer)->delete(route('employer.vacancies.destroy', $vacancy));

        $response->assertRedirectToRoute('employer.vacancies.index');
        $response->assertSessionHas('status', 'Lowongan berhasil dihapus.');
        // Konfirmasi bug Sprint 1: data lowongan masih ada di database
        $this->assertModelExists($vacancy);
    }

    public function test_employer_cannot_delete_another_employers_vacancy(): void
    {
        $vacancy = JobVacancy::factory()->create();
        $otherEmployer = User::factory()->employer()->create();

        $this->actingAs($otherEmployer)
            ->delete(route('employer.vacancies.destroy', $vacancy))
            ->assertNotFound();

        $this->assertModelExists($vacancy);
    }

    public function test_employer_can_close_their_vacancy(): void
    {
        $vacancy = JobVacancy::factory()->create();

        $response = $this->actingAs($vacancy->employer)
            ->from(route('employer.vacancies.edit', $vacancy))
            ->patch(route('employer.vacancies.status.update', $vacancy), ['status' => VacancyStatus::Closed->value]);

        $response->assertRedirect(route('employer.vacancies.edit', $vacancy));
        $response->assertSessionHas('status', 'Lowongan berhasil ditutup.');
        $this->assertSame(VacancyStatus::Closed, $vacancy->fresh()->status);
    }

    public function test_employer_can_reopen_their_closed_vacancy(): void
    {
        $vacancy = JobVacancy::factory()->closed()->create();

        $response = $this->actingAs($vacancy->employer)
            ->patch(route('employer.vacancies.status.update', $vacancy), ['status' => VacancyStatus::Open->value]);

        $response->assertSessionHas('status', 'Lowongan berhasil dibuka kembali.');
        $this->assertSame(VacancyStatus::Open, $vacancy->fresh()->status);
    }

    public function test_status_must_be_a_valid_vacancy_status(): void
    {
        $vacancy = JobVacancy::factory()->create();

        $this->actingAs($vacancy->employer)
            ->patch(route('employer.vacancies.status.update', $vacancy), ['status' => 'diarsipkan'])
            ->assertSessionHasErrors('status');

        $this->assertSame(VacancyStatus::Open, $vacancy->fresh()->status);
    }

    public function test_employer_cannot_change_the_status_of_another_employers_vacancy(): void
    {
        $vacancy = JobVacancy::factory()->create();
        $otherEmployer = User::factory()->employer()->create();

        $this->actingAs($otherEmployer)
            ->patch(route('employer.vacancies.status.update', $vacancy), ['status' => VacancyStatus::Closed->value])
            ->assertNotFound();

        $this->assertSame(VacancyStatus::Open, $vacancy->fresh()->status);
    }
}
