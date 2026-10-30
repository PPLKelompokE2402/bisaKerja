<?php

namespace App\Policies;

use App\Models\JobVacancy;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JobVacancyPolicy
{
    /**
     * Determine whether the user can view their list of vacancies.
     */
    public function viewAny(User $user): bool
    {
        return $user->isEmployer();
    }

    /**
     * Determine whether the user can post a new vacancy.
     */
    public function create(User $user): bool
    {
        return $user->isEmployer();
    }

    /**
     * Determine whether the user can update the vacancy.
     */
    public function update(User $user, JobVacancy $jobVacancy): Response
    {
        return $this->ownsVacancy($user, $jobVacancy);
    }

    /**
     * Determine whether the user can delete the vacancy.
     */
    public function delete(User $user, JobVacancy $jobVacancy): Response
    {
        return $this->ownsVacancy($user, $jobVacancy);
    }

    /**
     * Allow only the employer who posted the vacancy, hiding its existence from others.
     */
    private function ownsVacancy(User $user, JobVacancy $jobVacancy): Response
    {
        return $user->isEmployer() && $jobVacancy->user_id === $user->id
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
