<?php

namespace App\Http\Controllers\Employer;

use App\Enums\VacancyStatus;
use App\Http\Controllers\Controller;
use App\Models\JobVacancy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class JobVacancyStatusController extends Controller
{
    /**
     * Open or close the given job vacancy.
     */
    public function update(Request $request, JobVacancy $vacancy): RedirectResponse
    {
        Gate::authorize('update', $vacancy);

        $validated = $request->validate([
            'status' => ['required', Rule::enum(VacancyStatus::class)],
        ]);

        $vacancy->status = VacancyStatus::from($validated['status']);
        $vacancy->save();

        $message = $vacancy->isOpen()
            ? 'Lowongan berhasil dibuka kembali.'
            : 'Lowongan berhasil ditutup.';

        return back()->with('status', $message);
    }
}
