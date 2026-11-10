<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::query();

        // Filter PBI-04 (Judul/Perusahaan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        // Filter PBI-05 (Lokasi)
        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        $jobs = $query->latest()->paginate(9);

        return view('jobs.index', compact('jobs'));
    }

    // Fungsi Detail Lowongan (Menampilkan halaman show.blade.php)
    public function show($id)
    {
        $job = Job::findOrFail($id);
        return view('jobs.show', compact('job'));
    }
}