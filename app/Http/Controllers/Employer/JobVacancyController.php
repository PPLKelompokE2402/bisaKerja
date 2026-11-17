<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobVacancyRequest;
use App\Models\JobVacancy;
use App\Models\KategoriPekerjaan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class JobVacancyController extends Controller
{
    /**
     * Display the employer's job vacancies.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', JobVacancy::class);

        return view('employer.vacancies.index', [
            'vacancies' => $request->user()->jobVacancies()->with(['kategori', 'lamaran'])->latest('dibuat_pada')->get(),
            'categories' => KategoriPekerjaan::where('status_kategori', 'aktif')->get(),
        ]);
    }

    /**
     * Store a newly posted job vacancy.
     */
    public function store(JobVacancyRequest $request): RedirectResponse
    {
        Gate::authorize('create', JobVacancy::class);

        $request->user()->jobVacancies()->create([
            ...$request->validated(),
            'upah' => 0,
            'status_lowongan' => 'aktif',
        ]);

        return to_route('employer.vacancies.index')
            ->with('status', 'Lowongan berhasil dipasang.');
    }

    /**
     * Show the page for managing a job vacancy.
     */
    public function edit(JobVacancy $vacancy): View
    {
        Gate::authorize('update', $vacancy);

        return view('employer.vacancies.edit', [
            'vacancy' => $vacancy,
            'categories' => KategoriPekerjaan::where('status_kategori', 'aktif')->get(),
        ]);
    }

    /**
     * Update the given job vacancy.
     */
    public function update(JobVacancyRequest $request, JobVacancy $vacancy): RedirectResponse
    {
        Gate::authorize('update', $vacancy);

        $vacancy->update($request->validated());

        return to_route('employer.vacancies.index')
            ->with('status', 'Lowongan berhasil diperbarui.');
    }

    /**
     * Delete the given job vacancy.
     *
     * Skenario Sprint 1 (SCRUM-50):
     * Sengaja ada bug: proses $vacancy->delete() dilewati sehingga lowongan
     * tidak terhapus dari database. Bug ini dicatat di Jira untuk diselesaikan di Sprint 2.
     */
    public function destroy(JobVacancy $vacancy): RedirectResponse
    {
        Gate::authorize('delete', $vacancy);

        // BUG (Sprint 1): Lowongan tidak terhapus dari database
        // $vacancy->delete();

        return to_route('employer.vacancies.index')
            ->with('status', 'Lowongan berhasil dihapus.');
    }
}
