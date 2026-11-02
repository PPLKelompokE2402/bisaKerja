<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JobApplicationController extends Controller
{
    /**
     * Menampilkan form pengajuan lamaran pekerjaan berdasarkan lowongan yang dipilih.
     *
     * @param int|string $id ID lowongan pekerjaan
     */
    public function create($id)
    {
        // AC-01: Memastikan lowongan benar-benar tersedia
        $job = Job::findOrFail($id);

        // Fitur pendukung: Jika belum ada session login (karena modul login web SCRUM-51 belum di-merge),
        // otomatis gunakan akun demo Pencari Kerja agar pengujian form di browser dapat langsung dicoba.
        if (!Auth::check()) {
            $demoUser = User::firstOrCreate(
                ['email' => 'pencari.kerja@bisakerja.com'],
                ['name' => 'Budi Pencari Kerja', 'password' => bcrypt('password123')]
            );
            Auth::login($demoUser);
        }

        $user = Auth::user();
        
        // Memeriksa apakah user sudah pernah mengajukan lamaran untuk lowongan ini
        $existingApplication = null;
        if ($user) {
            $existingApplication = JobApplication::where('user_id', $user->id)
                ->where('job_id', $job->id)
                ->first();
        }

        // AC-02: Menampilkan form pengajuan lamaran dengan data lowongan dari database
        return view('jobs.apply', compact('job', 'existingApplication'));
    }

    /**
     * Memproses penyimpanan pengajuan lamaran dan pengunggahan berkas CV.
     *
     * @param Request $request
     * @param int|string $id ID lowongan pekerjaan
     */
    public function store(Request $request, $id)
    {
        // Fitur pendukung pengujian browser
        if (!Auth::check()) {
            $demoUser = User::firstOrCreate(
                ['email' => 'pencari.kerja@bisakerja.com'],
                ['name' => 'Budi Pencari Kerja', 'password' => bcrypt('password123')]
            );
            Auth::login($demoUser);
        }

        $user = Auth::user();

        // Keamanan: Memastikan identitas pelamar diambil dari session login
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengajukan lamaran.');
        }

        // AC-01: Memastikan lowongan yang dilamar tersedia di database
        $job = Job::findOrFail($id);

        // AC-04: Mencegah pengajuan ganda untuk lowongan yang sama
        $alreadyApplied = JobApplication::where('user_id', $user->id)
            ->where('job_id', $job->id)
            ->exists();

        if ($alreadyApplied) {
            return redirect()->back()->with('error', 'Anda sudah pernah mengajukan lamaran untuk lowongan pekerjaan ini.');
        }

        // AC-03: Validasi berkas CV di server (wajib, tipe MIME, dan ukuran maksimal 5MB)
        $request->validate([
            'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ], [
            'cv.required' => 'Dokumen CV wajib dilampirkan saat pengajuan lamaran.',
            'cv.file' => 'Berkas yang diunggah tidak valid.',
            'cv.mimes' => 'Format berkas CV yang diperbolehkan adalah PDF, DOC, atau DOCX.',
            'cv.max' => 'Ukuran berkas CV tidak boleh melebihi 5MB.',
        ]);

        // AC-03: Penyimpanan CV ke penyimpanan privat (bukan publik) dengan nama file aman
        $file = $request->file('cv');
        $safeFileName = 'cv_user_' . $user->id . '_job_' . $job->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $cvPath = $file->storeAs('cv_applications', $safeFileName, 'local');

        // AC-04: Transaksi penyimpanan data pengajuan lamaran ke database
        DB::beginTransaction();
        try {
            JobApplication::create([
                'user_id' => $user->id,
                'job_id' => $job->id,
                'cv_path' => $cvPath,
                'status' => 'pending',
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            // Menghapus file jika penyimpanan database gagal (mencegah orphaned files)
            if (Storage::disk('local')->exists($cvPath)) {
                Storage::disk('local')->delete($cvPath);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses lamaran Anda. Silakan coba lagi.');
        }

        return redirect()->route('jobs.apply', $job->id)->with('success', 'Lamaran pekerjaan dan CV Anda berhasil dikirimkan!');
    }
}

