<?php

namespace Database\Factories;

use App\Models\JobVacancy;
use App\Models\Lamaran;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lamaran>
 */
class LamaranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_lowongan' => JobVacancy::factory(),
            'id_pencari' => User::factory(),
            'status_lamaran' => 'menunggu',
        ];
    }
}
