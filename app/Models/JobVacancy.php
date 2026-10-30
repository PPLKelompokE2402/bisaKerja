<?php

namespace App\Models;

use App\Enums\VacancyCategory;
use App\Enums\VacancyDuration;
use App\Enums\VacancyStatus;
use Database\Factories\JobVacancyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'category', 'location', 'work_date', 'duration', 'notes'])]
class JobVacancy extends Model
{
    /** @use HasFactory<JobVacancyFactory> */
    use HasFactory;

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'dibuka',
        'applicants_count' => 0,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => VacancyCategory::class,
            'duration' => VacancyDuration::class,
            'status' => VacancyStatus::class,
            'work_date' => 'date',
            'applicants_count' => 'integer',
        ];
    }

    /**
     * Get the employer who posted the vacancy.
     *
     * @return BelongsTo<User, $this>
     */
    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Determine whether the vacancy is open for applicants.
     */
    public function isOpen(): bool
    {
        return $this->status === VacancyStatus::Open;
    }
}
