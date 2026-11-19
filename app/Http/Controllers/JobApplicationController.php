<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use App\Models\Lamaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JobApplicationController extends Controller
{
    /**
     * Menampilkan form pengajuan lamaran.
     */
    public function create($id)
    {
        $job = $this->availableVacancy($id)->load(['employer', 'kategori']);

        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek apakah pengguna sudah pernah melamar lowongan ini.
        $existingApplication = Lamaran::where('id_pencari', $user->id_pengguna)
            ->where('id_lowongan', $job->id_lowongan)
            ->first();

        return view('jobs.apply', compact('job', 'existingApplication'));
    }

    /**
     * Menyimpan pengajuan lamaran ke tabel lamaran.
     */
    public function store(Request $request, $id)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $job = $this->availableVacancy($id);

        // Cegah pengguna melamar lowongan yang sama lebih dari sekali.
        $alreadyApplied = Lamaran::where('id_pencari', $user->id_pengguna)
            ->where('id_lowongan', $job->id_lowongan)
            ->exists();

        if ($alreadyApplied) {
            return redirect()->route('jobs.apply', $job->id_lowongan)
                ->with('error', 'Anda sudah pernah mengajukan lamaran untuk lowongan ini.');
        }

        $validated = $request->validate([
            'pesan_lamaran' => ['nullable', 'string'],
        ], [
            'pesan_lamaran.string' => 'Pesan lamaran harus berupa teks.',
        ]);

        DB::transaction(function () use ($validated, $user, $job) {
            Lamaran::create([
                'id_lowongan' => $job->id_lowongan,
                'id_pencari' => $user->id_pengguna,
                'pesan_lamaran' => $validated['pesan_lamaran'] ?? null,
                // status_lamaran dan status_pekerjaan
                // menggunakan nilai default dari database.
            ]);
        });

        return redirect()->route('jobs.apply', $job->id_lowongan)
            ->with('success', 'Lamaran pekerjaan berhasil dikirimkan!');
    }

    private function availableVacancy(int|string $id): JobVacancy
    {
        return JobVacancy::query()
            ->where('status_lowongan', 'aktif')
            ->whereHas('employer', function ($employers): void {
                $employers->where('role', 'penyedia_pekerjaan')
                    ->where('status_akun', 'aktif');
            })
            ->findOrFail($id);
    }
}
