<?php

namespace Database\Factories;

use App\Enums\VacancyCategory;
use App\Enums\VacancyDuration;
use App\Enums\VacancyStatus;
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
            'user_id' => User::factory()->employer(),
            'title' => fake()->sentence(3),
            'category' => fake()->randomElement(VacancyCategory::cases()),
            'location' => fake()->streetAddress().', Bandung',
            'work_date' => fake()->dateTimeBetween('+1 day', '+1 month'),
            'duration' => fake()->randomElement(VacancyDuration::cases()),
            'notes' => fake()->optional()->sentence(),
            'status' => VacancyStatus::Open,
            'applicants_count' => fake()->numberBetween(0, 10),
        ];
    }

    /**
     * Indicate that the vacancy is closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VacancyStatus::Closed,
        ]);
    }
}
