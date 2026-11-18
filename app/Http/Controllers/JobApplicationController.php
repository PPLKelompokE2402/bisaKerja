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
        // Memastikan lowongan benar-benar tersedia
        $job = Job::findOrFail($id);

        // Fitur pendukung: Jika belum ada session login (karena modul login web SCRUM-51 belum di-merge),
        // gunakan akun demo Pencari Kerja agar pengujian form di browser dapat langsung dicoba.
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

        // Menampilkan form pengajuan lamaran dengan data lowongan dari database
        return view('jobs.apply', compact('job', 'existingApplication'));
    }

    /**
     * Memproses penyimpanan pengajuan lamaran dan pengunggahan berkas CV (opsional).
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

        // Memastikan lowongan yang dilamar tersedia di database
        $job = Job::findOrFail($id);

        // Mencegah pengajuan ganda untuk lowongan yang sama
        $alreadyApplied = JobApplication::where('user_id', $user->id)
            ->where('job_id', $job->id)
            ->exists();

        if ($alreadyApplied) {
            return redirect()->back()->with('error', 'Anda sudah pernah mengajukan lamaran untuk lowongan pekerjaan ini.');
        }

        // Validasi data pelamar dan berkas CV (CV bersifat opsional)
        $validated = $request->validate([
            'applicant_name' => ['required', 'string', 'max:255'],
            'date_of_birth'  => ['required', 'date', 'before_or_equal:today'],
            'age'            => ['required', 'integer', 'min:15', 'max:99'],
            'residence'      => ['required', 'string', 'max:255'],
            'skills'         => ['required', 'string'],
            'cv'             => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
        ], [
            'applicant_name.required'       => 'Nama lengkap wajib diisi.',
            'date_of_birth.required'        => 'Tanggal lahir wajib diisi.',
            'date_of_birth.before_or_equal' => 'Tanggal lahir tidak boleh berupa tanggal di masa depan.',
            'age.required'                  => 'Umur wajib diisi.',
            'age.integer'                   => 'Umur harus berupa angka bilangan bulat.',
            'age.min'                       => 'Umur minimal adalah 15 tahun.',
            'age.max'                       => 'Umur maksimal adalah 99 tahun.',
            'residence.required'            => 'Tempat tinggal wajib diisi.',
            'skills.required'               => 'Keahlian wajib diisi.',
            'cv.file'                       => 'Berkas CV yang diunggah tidak valid.',
            'cv.mimes'                      => 'Format berkas CV yang diperbolehkan adalah PDF, DOC, atau DOCX.',
            'cv.max'                        => 'Ukuran berkas CV tidak boleh melebihi 2MB.',
        ]);

        // Penanganan berkas CV opsional ke private storage
        $cvPath = null;
        if ($request->hasFile('cv')) {
            $file = $request->file('cv');
            $safeFileName = 'cv_user_' . $user->id . '_job_' . $job->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $cvPath = $file->storeAs('cv_applications', $safeFileName, 'local');
        }

        // Transaksi penyimpanan data pengajuan lamaran ke database
        DB::beginTransaction();
        try {
            JobApplication::create([
                'user_id'        => $user->id,
                'job_id'         => $job->id,
                'applicant_name' => $validated['applicant_name'],
                'date_of_birth'  => $validated['date_of_birth'],
                'age'            => $validated['age'],
                'residence'      => $validated['residence'],
                'skills'         => $validated['skills'],
                'cv_path'        => $cvPath,
                'status'         => 'pending',
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            // Menghapus file jika penyimpanan database gagal (mencegah orphaned files)
            if ($cvPath && Storage::disk('local')->exists($cvPath)) {
                Storage::disk('local')->delete($cvPath);
            }

            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem saat memproses lamaran Anda. Silakan coba lagi.');
        }

        return redirect()->route('jobs.apply', $job->id)->with('success', 'Lamaran pekerjaan berhasil dikirimkan!');
    }
}
