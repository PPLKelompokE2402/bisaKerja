<?php

namespace App\Http\Controllers;
use App\Models\JobVacancy;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
  public function index(Request $request)
    {
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

        $jobs = $query->latest('dibuat_pada')->paginate(9)->withQueryString();

        return view('jobs.index', compact('jobs'));
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