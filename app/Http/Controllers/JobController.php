<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use App\Models\KategoriPekerjaan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'id_kategori' => [
                'nullable',
                'integer',
                Rule::exists('kategori_pekerjaan', 'id_kategori')->where('status_kategori', 'aktif'),
            ],
        ]);

        $query = JobVacancy::with(['employer', 'kategori'])
            ->where('status_lowongan', 'aktif')
            ->whereHas('employer', function ($q) {
                $q->where('status_akun', 'aktif')
                    ->where('role', 'penyedia_pekerjaan');
            });

        // Pencarian berdasarkan judul pekerjaan
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('judul', 'like', "%{$keyword}%")
                    ->orWhereHas('employer', function ($employerQuery) use ($keyword) {
                        $employerQuery->where('nama', 'like', "%{$keyword}%");
                    });
            });
        }

        // Filter berdasarkan lokasi
        if ($request->filled('location')) {
            $location = $request->location;

            $query->where('lokasi', 'like', "%{$location}%");
        }

        if (isset($validated['id_kategori'])) {
            $query->where('id_kategori', $validated['id_kategori']);
        }

        $jobs = $query->latest('dibuat_pada')->paginate(9)->withQueryString();
        $categories = KategoriPekerjaan::where('status_kategori', 'aktif')->orderBy('nama_kategori')->get();

        return view('jobs.index', compact('jobs', 'categories'));
    }

    /**
     * Menampilkan detail lowongan.
     */
    public function show($id)
    {
        $job = JobVacancy::with(['employer', 'kategori'])
            ->where('status_lowongan', 'aktif')
            ->whereHas('employer', function ($q) {
                $q->where('status_akun', 'aktif')
                    ->where('role', 'penyedia_pekerjaan');
            })
            ->findOrFail($id);

        return view('jobs.show', compact('job'));
    }
}
