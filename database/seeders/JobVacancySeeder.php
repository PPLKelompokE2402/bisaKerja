<?php

namespace Database\Seeders;

use App\Enums\VacancyCategory;
use App\Enums\VacancyDuration;
use App\Enums\VacancyStatus;
use App\Models\JobVacancy;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobVacancySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed a demo employer with the vacancies shown in the mockup.
     */
    public function run(): void
    {
        $employer = User::factory()->employer()->create([
            'name' => 'Pemberi Kerja Demo',
            'email' => 'pemberikerja@bisakerja.test',
        ]);

        $vacancies = [
            [
                'title' => 'Bersihkan halaman',
                'category' => VacancyCategory::Cleaning,
                'location' => 'Sukajadi, Bandung',
                'duration' => VacancyDuration::TwoToFourHours,
                'status' => VacancyStatus::Open,
                'applicants_count' => 5,
            ],
            [
                'title' => 'Bantu packing pesanan',
                'category' => VacancyCategory::Packing,
                'location' => 'Coblong, Bandung',
                'duration' => VacancyDuration::FourToEightHours,
                'status' => VacancyStatus::Open,
                'applicants_count' => 8,
            ],
            [
                'title' => 'Perbaiki keran',
                'category' => VacancyCategory::HomeRepair,
                'location' => 'Cicendo, Bandung',
                'duration' => VacancyDuration::UnderTwoHours,
                'status' => VacancyStatus::Closed,
                'applicants_count' => 3,
            ],
        ];

        foreach ($vacancies as $vacancy) {
            JobVacancy::factory()
                ->for($employer, 'employer')
                ->create([...$vacancy, 'notes' => null]);
        }
    }
}
