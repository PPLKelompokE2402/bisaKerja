<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'kata_sandi' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'pencari_kerja',
            'status_akun' => 'aktif',
        ];
    }

    /**
     * Indicate that the user is an employer (pemberi kerja).
     */
    public function employer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'penyedia_pekerjaan',
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_akun' => 'menunggu_verifikasi',
        ]);
    }
}
