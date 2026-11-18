<?php

namespace Database\Factories;

use App\Models\KategoriPekerjaan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KategoriPekerjaan>
 */
class KategoriPekerjaanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_kategori' => fake()->word(),
            'status_kategori' => 'aktif',
        ];
    }
}
