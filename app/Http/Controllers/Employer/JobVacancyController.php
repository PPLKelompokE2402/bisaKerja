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
            'vacancies' => $request->user()->jobVacancies()->with('kategori')->withCount('lamaran')->latest('dibuat_pada')->get(),
            'categories' => KategoriPekerjaan::where('status_kategori', 'aktif')->get(),
        ]);
    }

    /**
     * Store a newly posted job vacancy.
     */
    public function store(JobVacancyRequest $request): RedirectResponse
    {
        // Hanya akun yang sudah diverifikasi Admin
        // yang boleh membuat lowongan.
        if ($request->user()->status_akun !== 'aktif') {
            return to_route('employer.vacancies.index')
                ->with('error', 'Akun Anda belum terverifikasi Admin. Anda belum dapat membuat lowongan pekerjaan.');
        }

        Gate::authorize('create', JobVacancy::class);

        $request->user()->jobVacancies()->create([
            ...$request->validated(),
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
     * Close the vacancy while preserving its applications and history.
     */
    public function destroy(JobVacancy $vacancy): RedirectResponse
    {
        Gate::authorize('delete', $vacancy);

        $vacancy->update(['status_lowongan' => 'ditutup']);

        return to_route('employer.vacancies.index')
            ->with('status', 'Lowongan berhasil ditutup. Data dan riwayat lamaran tetap disimpan.');
    }
}
