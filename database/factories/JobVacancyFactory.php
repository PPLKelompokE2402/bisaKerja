<?php

namespace Database\Factories;

use App\Models\JobVacancy;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobVacancy>
 */
class JobVacancyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_penyedia' => User::factory()->employer(),
            'id_kategori' => KategoriPekerjaanFactory::new(),
            'judul' => fake()->sentence(3),
            'lokasi' => fake()->streetAddress().', Bandung',
            'waktu_pekerjaan' => fake()->dateTimeBetween('+1 day', '+1 month'),
            'deskripsi' => fake()->sentence(),
            'status_lowongan' => 'aktif',
            'upah' => 70000,
        ];
    }

    /**
     * Indicate that the vacancy is closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_lowongan' => 'ditutup',
        ]);
    }
}
