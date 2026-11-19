<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use App\Models\Lamaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

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

        $age = $user->tanggal_lahir?->age;

        // Cek apakah pengguna sudah pernah melamar lowongan ini.
        $existingApplication = Lamaran::where('id_pencari', $user->id_pengguna)
            ->where('id_lowongan', $job->id_lowongan)
            ->first();

        return view('jobs.apply', compact('job', 'existingApplication', 'user', 'age'));
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
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
            'tempat_tinggal' => ['required', 'string', 'max:1000'],
            'keahlian' => ['required', 'string', 'max:2000'],
            'cv' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
            'pesan_lamaran' => ['required', 'string', 'max:3000'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.max' => 'Nama lengkap maksimal 100 karakter.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh melebihi hari ini.',
            'tempat_tinggal.required' => 'Tempat tinggal wajib diisi.',
            'tempat_tinggal.max' => 'Tempat tinggal maksimal 1000 karakter.',
            'keahlian.required' => 'Keahlian wajib diisi.',
            'keahlian.max' => 'Keahlian maksimal 2000 karakter.',
            'pesan_lamaran.required' => 'Pesan lamaran wajib diisi.',
            'pesan_lamaran.max' => 'Pesan lamaran maksimal 3000 karakter.',
            'cv.mimes' => 'CV harus berformat PDF, DOC, atau DOCX.',
            'cv.max' => 'Ukuran CV maksimal 2 MB.',
        ]);

        $storedCvPath = null;
        $cvFile = $request->file('cv');

        if ($cvFile !== null) {
            $storedCvPath = $cvFile->store('cv', 'local');

            if ($storedCvPath === false) {
                throw new RuntimeException('CV gagal disimpan. Silakan coba kembali.');
            }
        }

        try {
            DB::transaction(function () use ($validated, $storedCvPath, $user, $job): void {
                $profile = [
                    'nama' => $validated['nama_lengkap'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'alamat' => $validated['tempat_tinggal'],
                    'keahlian' => $validated['keahlian'],
                ];

                if ($storedCvPath !== null) {
                    $profile['cv'] = $storedCvPath;
                }

                /** @var User $user */
                $user->update($profile);
                Lamaran::create([
                    'id_lowongan' => $job->id_lowongan,
                    'id_pencari' => $user->id_pengguna,
                    'pesan_lamaran' => $validated['pesan_lamaran'],
                    // status_lamaran dan status_pekerjaan menggunakan nilai default database.
                ]);
            });
        } catch (Throwable $exception) {
            if ($storedCvPath !== null) {
                $disk = Storage::disk('local');

                if ($disk->exists($storedCvPath) && ! $disk->delete($storedCvPath)) {
                    Log::warning('Gagal menghapus CV sementara setelah pengajuan lamaran gagal.', [
                        'path' => $storedCvPath,
                    ]);
                }
            }

            throw $exception;
        }

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
